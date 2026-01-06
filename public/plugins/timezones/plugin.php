<?php
declare(strict_types=1);

/**
 * Chaos CMS DB Plugin: Timezones
 * Contract: plugin.php returns ['init'=>callable|null,'routes'=>callable|null,'shutdown'=>callable|null]
 *
 * IMPORTANT: No output at file scope. No output inside init except slot registration.
 */

return [
    'init' => static function (db $db): void {

        // ------------------------------------------------------------
        // Slot registry (define once, no output)
        // ------------------------------------------------------------
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
                    return (int)($a['priority'] ?? 10) <=> (int)($b['priority'] ?? 10);
                });

                foreach ($items as $it) {
                    $cb = $it['cb'] ?? null;
                    if (is_callable($cb)) {
                        $cb();
                    }
                }
            }
        }

        // ------------------------------------------------------------
        // Register output into a slot (ONLY renders when slot called)
        // ------------------------------------------------------------
        plugin_register_slot('home_time', static function (): void {

            $utcIso = gmdate('Y-m-d H:i:s') . 'Z';

            // Inline CSS (centered like your layout)
            echo '<style>
.tzbar{
  max-width:960px;
  margin: 10px auto 0 auto;
  padding: 10px 14px;
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 12px;
  background: rgba(0,0,0,.20);
  backdrop-filter: blur(8px);
}
.tzbar-inner{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:14px;
  flex-wrap:wrap;
}
.tzbar-item{ display:flex; flex-direction:column; gap:2px; min-width:180px; }
.tzbar-label{ font-size:.75rem; letter-spacing:.14em; text-transform:uppercase; opacity:.75; }
.tzbar-time{ font-size:.95rem; font-weight:600; opacity:.95; }
.tzbar-sep{ width:1px; height:34px; background: rgba(255,255,255,.14); }
.tzbar-local-row{ display:flex; align-items:baseline; gap:10px; flex-wrap:wrap; }
.tzbar-meta{ font-size:.80rem; opacity:.75; white-space:nowrap; }
@media (max-width:520px){
  .tzbar-item{ min-width:unset; width:100%; }
  .tzbar-sep{ display:none; }
  .tzbar-inner{ justify-content:flex-start; }
}
</style>';

            // HTML
            echo '<section class="tzbar" data-utc="' . htmlspecialchars($utcIso, ENT_QUOTES, 'UTF-8') . '">';
            echo '  <div class="tzbar-inner">';
            echo '    <div class="tzbar-item">';
            echo '      <div class="tzbar-label">UTC</div>';
            echo '      <div class="tzbar-time" id="tzbar-utc">' . htmlspecialchars($utcIso, ENT_QUOTES, 'UTF-8') . '</div>';
            echo '    </div>';
            echo '    <div class="tzbar-sep"></div>';
            echo '    <div class="tzbar-item tzbar-local">';
            echo '      <div class="tzbar-label" id="tzbar-local-label">Local</div>';
            echo '      <div class="tzbar-local-row">';
            echo '        <div class="tzbar-time" id="tzbar-local">—</div>';
            echo '        <div class="tzbar-meta" id="tzbar-meta"></div>';
            echo '      </div>';
            echo '    </div>';
            echo '  </div>';
            echo '</section>';

            // Inline JS (ticks both UTC and Local)
            echo '<script>
(function(){
  function pad(n){return String(n).padStart(2,"0");}
  function parseUtcStamp(s){
    var m=String(s||"").match(/^(\\d{4})-(\\d{2})-(\\d{2})\\s+(\\d{2}):(\\d{2}):(\\d{2})Z$/);
    if(!m) return null;
    return new Date(Date.UTC(+m[1],+m[2]-1,+m[3],+m[4],+m[5],+m[6]));
  }
  function fmtUtc(d){
    return d.getUTCFullYear()+"-"+pad(d.getUTCMonth()+1)+"-"+pad(d.getUTCDate())+" "+
           pad(d.getUTCHours())+":"+pad(d.getUTCMinutes())+":"+pad(d.getUTCSeconds())+"Z";
  }
  function isoWeek(d){
    const date=new Date(Date.UTC(d.getFullYear(),d.getMonth(),d.getDate()));
    const day=date.getUTCDay()||7;
    date.setUTCDate(date.getUTCDate()+4-day);
    const yearStart=new Date(Date.UTC(date.getUTCFullYear(),0,1));
    const weekNo=Math.ceil((((date-yearStart)/86400000)+1)/7);
    return {week:weekNo,year:date.getUTCFullYear()};
  }

  var el=document.querySelector(".tzbar");
  if(!el) return;

  var utcStart=parseUtcStamp(el.getAttribute("data-utc"));
  var startMs=Date.now();

  function tick(){
    // UTC ticking
    var utcEl=document.getElementById("tzbar-utc");
    if(utcEl && utcStart){
      var elapsed=Date.now()-startMs;
      var d=new Date(utcStart.getTime()+elapsed);
      utcEl.textContent=fmtUtc(d);
    }

    // Local time
    var now=new Date();
    var local=now.getFullYear()+"-"+pad(now.getMonth()+1)+"-"+pad(now.getDate())+" "+
              pad(now.getHours())+":"+pad(now.getMinutes())+":"+pad(now.getSeconds());
    var localEl=document.getElementById("tzbar-local");
    if(localEl) localEl.textContent=local;

    // Local day/week string (inline, right of local)
    var days=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    var wk=isoWeek(now);
    var meta="Today is "+days[now.getDay()]+", in week "+wk.week+" of "+wk.year;
    var metaEl=document.getElementById("tzbar-meta");
    if(metaEl) metaEl.textContent=meta;
  }

  tick();
  setInterval(tick,1000);
})();
</script>';
        }, 10);
    },

    'routes' => null,
    'shutdown' => null,
];

