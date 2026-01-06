<?php
declare(strict_types=1);

/**
 * Chaos CMS DB — Sentinel Plugin (DB-compliant)
 * Path: /public/plugins/sentinel/plugin.php
 *
 * REQUIREMENT:
 * - This file MUST return a hooks/manifest array for the DB plugin loader.
 *
 * Notes:
 * - No meta injection (per your directive).
 * - Safe if API key missing.
 * - Uses local JSON files under /public/plugins/sentinel/data/
 */

final class SentinelPlugin
{
    /** number of events from same IP before auto-block */
    private const THRESHOLD_BLOCK_COUNT = 5;

    private array $config = [];
    private string $api_base = '';
    private string $local_data_file = '';
    private string $threat_log_file = '';
    private array $allow_list = [];

    public function __construct()
    {
        $this->local_data_file  = __DIR__ . '/data/sentinel_local.json';
        $this->threat_log_file  = __DIR__ . '/data/sentinel_threats.json';

        $this->loadConfig();
        $this->initializeLocalData();
        $this->loadAllowList();

        // Core behavior: inspect request + (optional) sync intel
        $this->inspectRequest();
        $this->syncThreatIntel();
    }

    private function loadConfig(): void
    {
        $cfgFile = __DIR__ . '/data/sentinel_config.json';

        $default = [
            'api_key'         => '',
            'api_base'        => 'https://api.stn-labz.com',
            'site_id'         => 'site_' . bin2hex(random_bytes(8)),
            'update_interval' => 1800,
            'log_threats'     => true,
            'block_threats'   => true,
            // meta injection removed; keep key but default false for safety
            'meta_tag'        => false,
            'auto_update'     => true,
            'debug'           => false,
            'allow_list'      => [],
            'threat_categories' => [
                'wp_admin_scan'    => [
                    '/wp-admin', '/wp-admin/', '/wp-login.php',
                    '/xmlrpc.php', '/wp-content', '/wp-includes', '/administrator'
                ],
                'config_scan'      => ['/wp-config.php', '/config.xml', '/configuration.php'],
                'sql_injection'    => ['UNION SELECT', 'DROP TABLE', ' OR 1=1 ', 'INSERT INTO', 'DELETE FROM'],
                'xss_attempt'      => ['<script>', 'javascript:', 'onload=', 'alert(', 'document.cookie'],
                'bot_probe'        => ['/phpmyadmin', '/.env', '/.env.backup', '/.git', '/backup', '/adminer.php'],
                'file_inclusion'   => ['./', '/etc/passwd', 'C:\\Windows\\'],
                'command_injection'=> ['; ls', '| cat', '`id`', '$(whoami)'],
            ],
        ];

        if (!is_file($cfgFile)) {
            @mkdir(dirname($cfgFile), 0775, true);
            @file_put_contents(
                $cfgFile,
                json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
            @chmod($cfgFile, 0664);
            $this->config = $default;
        } else {
            $raw = (string) @file_get_contents($cfgFile);
            $j   = json_decode($raw, true);
            $this->config = is_array($j) ? array_replace_recursive($default, $j) : $default;
        }

        $this->api_base = rtrim((string)($this->config['api_base'] ?? ''), '/');
    }

    private function initializeLocalData(): void
    {
        if (is_file($this->local_data_file)) {
            return;
        }

        @mkdir(dirname($this->local_data_file), 0775, true);

        $initial = [
            'threat_patterns' => [],
            'blocklists' => [
                'ips' => [],
                'user_agents' => [],
                'asns' => [],
                'ranges' => [],
                'countries' => [],
            ],
            'known_threats' => [],
            'global_patterns' => [],
            'last_sync' => null,
            'data_version' => '0.0.0',
            'sync_hash' => '',
            'install_date' => gmdate('c'),
        ];

        @file_put_contents(
            $this->local_data_file,
            json_encode($initial, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        @chmod($this->local_data_file, 0664);
    }

    private function loadAllowList(): void
    {
        $this->allow_list = [];
        $list = $this->config['allow_list'] ?? [];
        if (is_array($list)) {
            foreach ($list as $v) {
                $s = trim((string)$v);
                if ($s !== '') {
                    $this->allow_list[] = $s;
                }
            }
        }
    }

    private function inspectRequest(): void
    {
        $ip   = $this->getClientIP();
        $ua   = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
        $uri  = (string)($_SERVER['REQUEST_URI'] ?? '/');
        $path = (string)(parse_url($uri, PHP_URL_PATH) ?? '/');

        // allow list short-circuit
        foreach ($this->allow_list as $allowed) {
            if ($allowed === $ip) {
                return;
            }
        }

        $hit = $this->classifyThreat($path, $ua);
        if ($hit === null) {
            return;
        }

        $event = [
            'ts'       => gmdate('c'),
            'ip'       => $ip,
            'ua'       => $ua,
            'path'     => $path,
            'category' => $hit,
            'domain'   => $this->getCurrentDomain(),
        ];

        $this->logThreatLocally($event);

        if (!empty($this->config['block_threats'])) {
            if ($this->shouldBlock($ip)) {
                $this->blockRequest($event);
            }
        }
    }

    private function classifyThreat(string $path, string $ua): ?string
    {
        $cats = $this->config['threat_categories'] ?? [];
        if (!is_array($cats)) {
            return null;
        }

        foreach ($cats as $cat => $needles) {
            if (!is_array($needles)) {
                continue;
            }
            foreach ($needles as $n) {
                $needle = (string)$n;
                if ($needle === '') {
                    continue;
                }

                // path-based match first
                if (stripos($path, $needle) !== false) {
                    return (string)$cat;
                }

                // UA match for obvious bots (optional)
                if ($cat === 'bot_probe' && $ua !== '' && stripos($ua, $needle) !== false) {
                    return (string)$cat;
                }
            }
        }

        return null;
    }

    private function shouldBlock(string $ip): bool
    {
        if (!is_file($this->threat_log_file)) {
            return false;
        }

        $raw = (string)@file_get_contents($this->threat_log_file);
        $log = json_decode($raw, true);
        $log = is_array($log) ? $log : [];

        $count = 0;
        foreach ($log as $row) {
            if (is_array($row) && (string)($row['ip'] ?? '') === $ip) {
                $count++;
            }
        }

        return $count >= self::THRESHOLD_BLOCK_COUNT;
    }

    private function syncThreatIntel(): void
    {
        // keep it KISS: if api not configured, do nothing
        if ($this->api_base === '' || empty($this->config['api_key'])) {
            return;
        }

        // placeholder for your existing intel sync logic
        // (you already had this working in your Sentinel build; leaving minimal + non-breaking)
        return;
    }

    private function logThreatLocally(array $event): void
    {
        if (empty($this->config['log_threats'])) {
            return;
        }

        $log = [];
        if (is_file($this->threat_log_file)) {
            $raw = (string)@file_get_contents($this->threat_log_file);
            $log = json_decode($raw, true);
            $log = is_array($log) ? $log : [];
        }

        $log[] = $event;

        if (count($log) > 5000) {
            $log = array_slice($log, -5000);
        }

        @mkdir(dirname($this->threat_log_file), 0775, true);
        @file_put_contents(
            $this->threat_log_file,
            json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        @chmod($this->threat_log_file, 0664);
    }

    private function blockRequest(array $event): void
    {
        if (!headers_sent()) {
            http_response_code(404);
            header('X-Sentinel: soft-block');
        }
        echo 'Not Found';
        exit;
    }

    private function getClientIP(): string
    {
        $candidates = [
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR',
        ];

        foreach ($candidates as $h) {
            $v = (string)($_SERVER[$h] ?? '');
            if ($v === '') {
                continue;
            }
            if (strpos($v, ',') !== false) {
                $v = trim(explode(',', $v)[0]);
            }
            if (filter_var($v, FILTER_VALIDATE_IP)) {
                return $v;
            }
        }

        return (string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    private function getCurrentDomain(): string
    {
        $h = (string)($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'unknown');
        return preg_replace('/:\d+$/', '', $h);
    }
}

/**
 * DB Plugin Loader Contract:
 * This file must return an array.
 */
return [
    'slug'        => 'sentinel',
    'name'        => 'Sentinel',
    'version'     => '1.8.3',
    'author'      => 'stn-labz',
    'description' => 'Request firewall + threat intel sync (lean).',
    'has_admin'   => (int) (is_file(__DIR__ . '/admin/main.php') ? 1 : 0),

    // Called by the plugin loader at runtime
    'boot' => static function (): void {
        static $sentinel = null;
        if ($sentinel === null) {
            $sentinel = new SentinelPlugin();
        }
    },
];

