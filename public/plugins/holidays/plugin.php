<?php

declare(strict_types=1);

/**
 * Chaos CMS DB Plugin: Holidays
 * - Registers a message in the 'home_time' slot.
 * - Outputs NOTHING unless the slot is called.
 */

require_once __DIR__ . '/core/holiday.php';

/**
 * Slot registry (only if core doesn't provide it yet).
 */
if (!function_exists('plugin_register_slot')) {
    function plugin_register_slot(string $slot, callable $cb, int $priority = 10): void
    {
        if (!isset($GLOBALS['CHAOS_PLUGIN_SLOTS']) || !is_array($GLOBALS['CHAOS_PLUGIN_SLOTS'])) {
            $GLOBALS['CHAOS_PLUGIN_SLOTS'] = [];
        }

        if (!isset($GLOBALS['CHAOS_PLUGIN_SLOTS'][$slot]) || !is_array($GLOBALS['CHAOS_PLUGIN_SLOTS'][$slot])) {
            $GLOBALS['CHAOS_PLUGIN_SLOTS'][$slot] = [];
        }

        $GLOBALS['CHAOS_PLUGIN_SLOTS'][$slot][] = [
            'priority' => $priority,
            'cb'       => $cb,
        ];
    }
}

if (!function_exists('plugin_slot')) {
    function plugin_slot(string $slot): void
    {
        $slots = $GLOBALS['CHAOS_PLUGIN_SLOTS'] ?? null;

        if (!is_array($slots) || !isset($slots[$slot]) || !is_array($slots[$slot])) {
            return;
        }

        $items = $slots[$slot];

        usort($items, static function (array $a, array $b): int {
            return (int) ($a['priority'] ?? 10) <=> (int) ($b['priority'] ?? 10);
        });

        foreach ($items as $it) {
            $cb = $it['cb'] ?? null;

            if (is_callable($cb)) {
                $cb();
            }
        }
    }
}

return [
    'init' => static function (db $db): void {
        plugin_register_slot('home_time', static function (): void {
            $h = new \plugins\holidays\holiday();
            $msg = $h->get_message(date('Y-m-d'));

            if ($msg === '') {
                return;
            }

            echo '<div class="tzbar-holiday">';
            echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
            echo '</div>';
        }, 30);
    },

    'routes' => null,
    'shutdown' => null,
];

