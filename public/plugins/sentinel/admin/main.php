<?php
/**
 * Sentinel — Admin Console (Chaos CMS)
 * Path: /public/plugins/sentinel/admin/main.php
 *
 * Standalone:
 * - NO flash(), NO jread(), NO jwrite() required.
 * - Only uses local helpers in this file (sentinel_*).
 * - Reads/writes:
 *     /public/plugins/sentinel/data/sentinel_config.json
 *     /public/plugins/sentinel/data/sentinel_local.json
 *     /public/plugins/sentinel/data/sentinel_threats.json
 */

// ---------------------------------------------------------------------
// Local helpers (no collisions)
// ---------------------------------------------------------------------

//echo '<link rel="stylesheet" href="/public/plugins/sentinel/assets/css/admin.css">';

/**
 * HTML escape.
 */
function sentinel_h(string $s): string
{
    if (function_exists('e')) {
        return e($s);
    }
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Read JSON file to array.
 */
function sentinel_read_json(string $file, $default)
{
    if (!is_file($file)) {
        return $default;
    }
    $raw = @file_get_contents($file);
    if ($raw === false || $raw === '') {
        return $default;
    }
    $j = json_decode($raw, true);
    return is_array($j) ? $j : $default;
}

/**
 * Write array to JSON.
 */
function sentinel_write_json(string $file, $data): bool
{
    $dir = dirname($file);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $tmp = $file . '.tmp';
    $ok  = @file_put_contents(
        $tmp,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
    if ($ok === false) {
        return false;
    }
    @chmod($tmp, 0664);
    return @rename($tmp, $file);
}

/**
 * Log a manual threat-shaped event (for manual blocks).
 */
function sentinel_log_manual_event(string $ip, array $config, string $category = 'manual_block'): void
{
    $pluginRoot = rtrim(dirname(__DIR__), '/\\');
    $eventsFile = $pluginRoot . '/data/sentinel_threats.json';

    $log = sentinel_read_json($eventsFile, []);
    if (!is_array($log)) {
        $log = [];
    }

    $siteId = (string)($config['site_id'] ?? '');
    if ($siteId === '') {
        $siteId = 'site_manual';
    }

    $domain = isset($_SERVER['HTTP_HOST'])
        ? preg_replace('/:\d+$/', '', (string)$_SERVER['HTTP_HOST'])
        : ((string)($_SERVER['SERVER_NAME'] ?? 'unknown'));

    $event = [
        'id'              => 'threat_' . bin2hex(random_bytes(8)),
        'site_id'         => $siteId,
        'timestamp'       => gmdate('c'),
        'threat_category' => $category,
        'matched_pattern' => 'admin_manual',
        'confidence'      => 'high',
        'source'          => 'admin',
        'ip_address'      => $ip,
        'user_agent'      => '(manual admin action)',
        'request_url'     => '/admin?action=plugin_admin&slug=sentinel',
        'request_method'  => 'MANUAL',
        'headers'         => [],
        'get_params'      => [],
        'post_params'     => [],
        'domain'          => $domain,
    ];

    $log[] = $event;
    sentinel_write_json($eventsFile, $log);
}

// ---------------------------------------------------------------------
// Paths / data
// ---------------------------------------------------------------------

$pluginRoot = rtrim(dirname(__DIR__), '/\\');             // /public/plugins/sentinel
$dataDir    = $pluginRoot . '/data';

$configFile = $dataDir . '/sentinel_config.json';
$localFile  = $dataDir . '/sentinel_local.json';
$eventsFile = $dataDir . '/sentinel_threats.json';

$config = sentinel_read_json($configFile, []);
$local  = sentinel_read_json($localFile,  []);
$events = sentinel_read_json($eventsFile, []);

if (!is_array($events)) {
    $events = [];
}
if (!isset($local['blocklists']) || !is_array($local['blocklists'])) {
    $local['blocklists'] = [];
}
if (!isset($local['blocklists']['ips']) || !is_array($local['blocklists']['ips'])) {
    $local['blocklists']['ips'] = [];
}
if (!isset($config['allow_list']) || !is_array($config['allow_list'])) {
    $config['allow_list'] = [];
}
if (!isset($config['allow_list']['ips']) || !is_array($config['allow_list']['ips'])) {
    $config['allow_list']['ips'] = [];
}

// Normalized lists
$blockedIps = array_values(array_unique(array_map('strval', $local['blocklists']['ips'])));
$allowedIps = array_values(array_unique(array_map('strval', $config['allow_list']['ips'])));

// Tabs
$tabs = ['dashboard', 'events', 'blocked', 'allowed', 'tools'];
$tab  = isset($_GET['tab']) ? (string)$_GET['tab'] : 'dashboard';
if (!in_array($tab, $tabs, true)) {
    $tab = 'dashboard';
}

// Messages
$notice = '';
$error  = '';

// ---------------------------------------------------------------------
// POST actions (block / allow / unblock / unallow / resync)
// ---------------------------------------------------------------------

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $op = (string)($_POST['op'] ?? '');

    switch ($op) {
        case 'block_ip': {
            $ip = trim((string)($_POST['ip'] ?? ''));
            if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP)) {
                $error = 'Provide a valid IPv4 or IPv6 to block.';
                $tab   = 'blocked';
                break;
            }

            if (!in_array($ip, $blockedIps, true)) {
                $blockedIps[] = $ip;
                $local['blocklists']['ips'] = $blockedIps;
                sentinel_write_json($localFile, $local);

                // manual threat event
                sentinel_log_manual_event($ip, $config, 'manual_block');
            }

            $notice = 'Blocked IP ' . $ip . '.';
            $tab    = 'blocked';
            break;
        }

        case 'unblock_ip': {
            $ip = trim((string)($_POST['ip'] ?? ''));
            if ($ip === '') {
                $error = 'Missing IP.';
                $tab   = 'blocked';
                break;
            }

            $blockedIps = array_values(array_filter(
                $blockedIps,
                static fn($v) => $v !== $ip
            ));
            $local['blocklists']['ips'] = $blockedIps;
            sentinel_write_json($localFile, $local);

            $notice = 'Unblocked IP ' . $ip . '.';
            $tab    = 'blocked';
            break;
        }

        case 'allow_ip': {
            $ip = trim((string)($_POST['ip'] ?? ''));
            if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP)) {
                $error = 'Provide a valid IPv4 or IPv6 to allow.';
                $tab   = 'allowed';
                break;
            }

            if (!in_array($ip, $allowedIps, true)) {
                $allowedIps[] = $ip;
                $config['allow_list']['ips'] = $allowedIps;
                sentinel_write_json($configFile, $config);
            }

            $notice = 'Allowed IP ' . $ip . '.';
            $tab    = 'allowed';
            break;
        }

        case 'unallow_ip': {
            $ip = trim((string)($_POST['ip'] ?? ''));
            if ($ip === '') {
                $error = 'Missing IP.';
                $tab   = 'allowed';
                break;
            }

            $allowedIps = array_values(array_filter(
                $allowedIps,
                static fn($v) => $v !== $ip
            ));
            $config['allow_list']['ips'] = $allowedIps;
            sentinel_write_json($configFile, $config);

            $notice = 'Removed allowed IP ' . $ip . '.';
            $tab    = 'allowed';
            break;
        }

        case 'force_resync': {
            // Just mark a timestamp; runtime plugin will do real sync
            $local['last_manual_resync'] = gmdate('c');
            sentinel_write_json($localFile, $local);
            $notice = 'Re-sync flagged. Sentinel runtime will refresh intel.';
            $tab    = 'tools';
            break;
        }

        default:
            // nothing
            break;
    }

    // reload to reflect any changes
    $config = sentinel_read_json($configFile, $config);
    $local  = sentinel_read_json($localFile,  $local);
    $events = sentinel_read_json($eventsFile, $events);
    if (!is_array($events)) {
        $events = [];
    }
    if (!isset($local['blocklists']['ips']) || !is_array($local['blocklists']['ips'])) {
        $local['blocklists']['ips'] = [];
    }
    if (!isset($config['allow_list']['ips']) || !is_array($config['allow_list']['ips'])) {
        $config['allow_list']['ips'] = [];
    }

    $blockedIps = array_values(array_unique(array_map('strval', $local['blocklists']['ips'])));
    $allowedIps = array_values(array_unique(array_map('strval', $config['allow_list']['ips'])));
}

// ---------------------------------------------------------------------
// Stats for Dashboard
// ---------------------------------------------------------------------

$totalEvents  = count($events);
$blockedCount = count($blockedIps);
$allowedCount = count($allowedIps);

$siteId = (string)($config['site_id'] ?? '');
if ($siteId === '') {
    $siteId = 'site-unknown';
}

$dataVersion = isset($local['data_version'])
    ? (string)$local['data_version']
    : '0.0.0';

$lastSeenTs = null;
$ipCounts   = [];
$catCounts  = [];

foreach ($events as $ev) {
    if (!is_array($ev)) {
        continue;
    }

    $ip = (string)($ev['ip_address'] ?? '');
    if ($ip !== '') {
        $ipCounts[$ip] = ($ipCounts[$ip] ?? 0) + 1;
    }

    $cat = (string)($ev['threat_category'] ?? '');
    if ($cat !== '') {
        $catCounts[$cat] = ($catCounts[$cat] ?? 0) + 1;
    }

    $ts = (string)($ev['timestamp'] ?? '');
    if ($ts !== '') {
        $t = strtotime($ts);
        if ($t && ($lastSeenTs === null || $t > $lastSeenTs)) {
            $lastSeenTs = $t;
        }
    }
}

arsort($ipCounts);
arsort($catCounts);

$topIps    = array_slice($ipCounts, 0, 5, true);
$topCats   = array_slice($catCounts, 0, 5, true);
$lastSeen  = $lastSeenTs ? gmdate('Y-m-d\TH:i:sP', $lastSeenTs) : 'never';

// recent events (sorted desc) - LIMIT 20
$eventsSorted = $events;
usort($eventsSorted, static function ($a, $b) {
    $ta = isset($a['timestamp']) ? strtotime((string)$a['timestamp']) : 0;
    $tb = isset($b['timestamp']) ? strtotime((string)$b['timestamp']) : 0;
    return $tb <=> $ta;
});
$recentEvents = array_slice($eventsSorted, 0, 20);

$baseUrl = '/admin?action=plugin_admin&amp;slug=sentinel';

?>
<div class="container my-3 sentinel-admin">
  <h2 class="h5 mb-2">Sentinel — Security Console</h2>

  <!-- TAB STRIP as BUTTONS -->
  <div class="mb-3">
    <div class="btn-group btn-group-sm" role="group" aria-label="Sentinel sections">
      <?php
        $tabLinks = [
          'dashboard' => 'Dashboard',
          'events'    => 'Events',
          'blocked'   => 'Blocked IPs',
          'allowed'   => 'Allowed IPs',
          'tools'     => 'Tools',
        ];
        foreach ($tabLinks as $key => $label):
          $href    = $baseUrl . '&amp;tab=' . $key;
          $classes = 'btn btn-sm ' . ($tab === $key ? 'btn-primary' : 'btn-outline-secondary');
      ?>
        <a href="<?= $href ?>" class="<?= $classes ?>"><?= sentinel_h($label) ?></a>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($notice !== ''): ?>
    <div class="alert alert-success"><?= sentinel_h($notice) ?></div>
  <?php endif; ?>
  <?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= sentinel_h($error) ?></div>
  <?php endif; ?>

  <?php if ($tab === 'dashboard'): ?>

    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <div class="card card-body">
          <div class="small text-muted">Total Events</div>
          <div class="fs-4 fw-semibold"><?= (int)$totalEvents ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-body">
          <div class="small text-muted">Blocked IPs</div>
          <div class="fs-4 fw-semibold"><?= (int)$blockedCount ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-body">
          <div class="small text-muted">Allowed IPs</div>
          <div class="fs-4 fw-semibold"><?= (int)$allowedCount ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card card-body">
          <div class="small text-muted">Last Seen (UTC)</div>
          <div class="fw-semibold small"><?= sentinel_h($lastSeen) ?></div>
          <div class="small text-muted mt-1">
            Site ID: <code><?= sentinel_h($siteId) ?></code><br>
            Data Version: <code><?= sentinel_h($dataVersion) ?></code>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <div class="card card-body">
          <div class="fw-semibold mb-2">Top IPs</div>
          <?php if (!$topIps): ?>
            <div class="text-muted small">No Sentinel events yet.</div>
          <?php else: ?>
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>IP Address</th>
                  <th class="text-end">Events</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($topIps as $ip => $count): ?>
                <tr>
                  <td><code><?= sentinel_h((string)$ip) ?></code></td>
                  <td class="text-end"><?= (int)$count ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card card-body">
          <div class="fw-semibold mb-2">Top Categories</div>
          <?php if (!$topCats): ?>
            <div class="text-muted small">No Sentinel events yet.</div>
          <?php else: ?>
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Category</th>
                  <th class="text-end">Events</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($topCats as $cat => $count): ?>
                <tr>
                  <td><?= sentinel_h((string)$cat) ?></td>
                  <td class="text-end"><?= (int)$count ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="card card-body mb-3">
      <div class="fw-semibold mb-2">Recent Activity</div>
      <?php if (!$recentEvents): ?>
        <div class="text-muted small">No recent Sentinel events.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>When (UTC)</th>
                <th>IP</th>
                <th>Category</th>
                <th>URL</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentEvents as $ev): ?>
                <?php $ip = (string)($ev['ip_address'] ?? ''); ?>
                <tr>
                  <td class="small text-muted"><?= sentinel_h((string)($ev['timestamp'] ?? '')) ?></td>
                  <td><code><?= sentinel_h($ip) ?></code></td>
                  <td><?= sentinel_h((string)($ev['threat_category'] ?? '')) ?></td>
                  <td class="small text-muted text-truncate" style="max-width:260px;">
                    <?= sentinel_h((string)($ev['request_url'] ?? '')) ?>
                  </td>
                  <td class="text-end">
                    <?php if ($ip === ''): ?>
                      <span class="text-muted small">—</span>
                    <?php elseif (in_array($ip, $blockedIps, true)): ?>
                      <span class="badge text-bg-danger">Blocked</span>
                    <?php elseif (in_array($ip, $allowedIps, true)): ?>
                      <span class="badge text-bg-success">Allowed</span>
                    <?php else: ?>
                      <form method="post" class="d-inline"
                            onsubmit="return confirm('Block IP <?= sentinel_h($ip) ?> from this event?');">
                        <input type="hidden" name="op" value="block_ip">
                        <input type="hidden" name="ip" value="<?= sentinel_h($ip) ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Block</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  <?php elseif ($tab === 'events'): ?>

    <div class="card card-body">
      <div class="fw-semibold mb-2">Events (latest first, max 20)</div>
      <?php if (!$recentEvents): ?>
        <div class="text-muted small">No Sentinel events recorded.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>When (UTC)</th>
                <th>IP</th>
                <th>Category</th>
                <th>URL</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentEvents as $ev): ?>
                <?php $ip = (string)($ev['ip_address'] ?? ''); ?>
                <tr>
                  <td class="small text-muted"><?= sentinel_h((string)($ev['timestamp'] ?? '')) ?></td>
                  <td><code><?= sentinel_h($ip) ?></code></td>
                  <td><?= sentinel_h((string)($ev['threat_category'] ?? '')) ?></td>
                  <td class="small text-muted text-truncate" style="max-width:260px;">
                    <?= sentinel_h((string)($ev['request_url'] ?? '')) ?>
                  </td>
                  <td class="text-end">
                    <?php if ($ip === ''): ?>
                      <span class="text-muted small">—</span>
                    <?php elseif (in_array($ip, $blockedIps, true)): ?>
                      <span class="badge text-bg-danger">Blocked</span>
                    <?php elseif (in_array($ip, $allowedIps, true)): ?>
                      <span class="badge text-bg-success">Allowed</span>
                    <?php else: ?>
                      <form method="post" class="d-inline"
                            onsubmit="return confirm('Block IP <?= sentinel_h($ip) ?> from this event?');">
                        <input type="hidden" name="op" value="block_ip">
                        <input type="hidden" name="ip" value="<?= sentinel_h($ip) ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Block</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  <?php elseif ($tab === 'blocked'): ?>

    <div class="card card-body mb-3">
      <div class="fw-semibold mb-2">Block an IP</div>
      <form method="post" class="row g-2 align-items-end">
        <input type="hidden" name="op" value="block_ip">
        <div class="col-md-6">
          <label class="form-label small mb-1">IP Address (IPv4 or IPv6)</label>
          <input type="text" name="ip" class="form-control form-control-sm"
                 placeholder="203.0.113.10 or 2a06:98c0:3600::103" required>
        </div>
        <div class="col-md-3">
          <button class="btn btn-sm btn-danger" type="submit">Block</button>
        </div>
      </form>
    </div>

    <div class="card card-body">
      <div class="fw-semibold mb-2">Blocked IPs</div>
      <?php if (!$blockedIps): ?>
        <div class="text-muted small">No IPs currently blocked in Sentinel local blocklist.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>IP Address</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($blockedIps as $ip): ?>
              <tr>
                <td><code><?= sentinel_h($ip) ?></code></td>
                <td class="text-end">
                  <form method="post" class="d-inline"
                        onsubmit="return confirm('Unblock <?= sentinel_h($ip) ?>?');">
                    <input type="hidden" name="op" value="unblock_ip">
                    <input type="hidden" name="ip" value="<?= sentinel_h($ip) ?>">
                    <button class="btn btn-sm btn-outline-secondary" type="submit">Unblock</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  <?php elseif ($tab === 'allowed'): ?>

    <div class="card card-body mb-3">
      <div class="fw-semibold mb-2">Allow / Whitelist an IP</div>
      <form method="post" class="row g-2 align-items-end">
        <input type="hidden" name="op" value="allow_ip">
        <div class="col-md-6">
          <label class="form-label small mb-1">IP Address (IPv4 or IPv6)</label>
          <input type="text" name="ip" class="form-control form-control-sm"
                 placeholder="Your IP, trusted upstream, etc." required>
        </div>
        <div class="col-md-3">
          <button class="btn btn-sm btn-success" type="submit">Allow</button>
        </div>
      </form>
    </div>

    <div class="card card-body">
      <div class="fw-semibold mb-2">Allowed / Whitelisted IPs</div>
      <?php if (!$allowedIps): ?>
        <div class="text-muted small">No allow-list IPs configured.</div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>IP Address</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($allowedIps as $ip): ?>
              <tr>
                <td><code><?= sentinel_h($ip) ?></code></td>
                <td class="text-end">
                  <form method="post" class="d-inline"
                        onsubmit="return confirm('Remove <?= sentinel_h($ip) ?> from allow-list?');">
                    <input type="hidden" name="op" value="unallow_ip">
                    <input type="hidden" name="ip" value="<?= sentinel_h($ip) ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  <?php elseif ($tab === 'tools'): ?>

    <div class="card card-body mb-3">
      <div class="fw-semibold mb-2">Maintenance</div>
      <form method="post" class="mb-2">
        <input type="hidden" name="op" value="force_resync">
        <button class="btn btn-sm btn-outline-primary" type="submit">Force Re-Sync</button>
      </form>
      <div class="small text-muted">
        Re-sync marks a request; the Sentinel runtime plugin performs actual API calls.
      </div>
    </div>

    <div class="card card-body">
      <div class="fw-semibold mb-2">Info</div>
      <div class="small">
        <div>Site ID: <code><?= sentinel_h($siteId) ?></code></div>
        <div>Data Version: <code><?= sentinel_h($dataVersion) ?></code></div>
        <div>Events Logged: <code><?= (int)$totalEvents ?></code></div>
      </div>
    </div>

  <?php endif; ?>
</div>

