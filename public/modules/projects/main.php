<?php

/**
 * poemei.com
 * projects
 * A running list of projects that I am working on
*/
declare(strict_types=1);

// Parse action from URL: "/home", "/home/<action>"
$path  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$parts = array_values(array_filter(explode('/', trim($path, '/'))));
// after "/home/..." the action (if any) is the next segment
$route = $parts[1] ?? '';

?>
<div class="container">
<?php
function home() {
?>
  <section>
    <h1>Projects</h1>
    <p>Every project that I am working on.</p>
  </section>
  
  <section>
  <h2>STN-Labz</h2>
  <p>My LLC Company that focuses on sovereignty as a a neccessity, not a luxury. On the web @ <a href="https://www.stn-labz.com" target="_blank">stn-labz.com</a>.</p>
  <p>Under Stn-Labz and all are my original creations
    <ul>
      <li>Secure Rapspberry Pi Operating Systems
        <ul>
          <li><a href="/projects/pi-os">Raspberry Pi Operating System</a></li>
          <li><a href="/projects/ai-os">Raspberry Pi AI Operating System</a></li>
          <br>
        </ul>
      </li>
      <li>World-Wide Rapid <a href="/projects/block-chain">Block Chain</a></li>
      <li>Raspberry Pi <a href="/projects/router">Firewall/Gateway</a> Systems</li>
      <li><a href="/projects/sentinel"><strong>Sentinel</strong></a> (OS and PHP Plugin)</li>
      <li>The Stn-Labz API @ <a href="https://api.stn-labz.com/v1/events" target="_blank">STN API</a></li>
      <li><a href="/projects/devbot"><strong>DevBot</strong></a> a PHP driven developers companion</li>
      <br>
      <li>The D Series
      <ul>
        <li><a href="/projects/rictusd"><strong>RictusD</strong></a> a complience enforcement AI</li>
        <li><a href="/projects/digitd"><strong>DigitD</strong></a> a company wide AI</li>
        <li>Secure <a href="/projects/dnsd">DNSD Systems</a></li>
      </li>
      </ul>
      <br>
      <li>The <a href="/projects/chaos-cms">Chaos CMS</a> Projects</li>
    </ul>
    </p>
  </section>
<?php
}

function devbot() {
    echo '<small><em><a href="/projects">Projects</a> >> <strong>DevBot</em></small>';
    echo '<section>';
    echo '<h2>DevBot</h2>';
    echo '<p>The PHP Developers companion.</p>';
    echo '<p>DevBot is the reporting layer of the STN-Labz ecosystem. It passively scans projects, reads plugin signals, monitors file activity, and records development conditions. Each run produces structured reports that help track progress, identify changes, and document the state of the system over time. DevBot acts as a transparent observer — a diagnostic and historical tool that keeps clear, consistent insight into ongoing work without ever interfering with it.</p>';
    echo '</section>';
}

function block_chain() {
    echo '<small><em><a href="/projects">Projects</a> >> <strong>Block Chain</em></small>';
    echo '<section>';
    echo '<h2>Block Chain</h2>';
    echo '<p>Decentralized Immediate Security Threat Chain for the Sentinel Threat Network (<strong>STN</strong>).</p>';
    echo '<p>The root of operations in the STN-Labz Ecosystem, as threat data is gathered from our various projects, it all gets pooled into our <strong>Decentralized Threat Block Chain</strong>. Written in Go, deployed rapidly, world-wide. Sentinel OS and PHP implementations and API data is all derived from this project.</p>';
    echo '</section>';
}

function sentinel() {
    echo '<small><em><a href="/projects">Projects</a> >> <strong>Sentinel</em></small>';
    echo '<section>';
    echo '<h2>Sentinel</h2>';
    echo '<p>Security at multiple levels.</p>';
    echo '<p><strong>As an OS</strong><br>Sentinel OS is a stripped-down, security-focused operating system built for Raspberry Pi environments that need hardened, autonomous protection without external dependencies. It boots fast, runs lean, and provides real-time network and host monitoring through an integrated Sentinel daemon that watches traffic, system behavior, and service integrity. Designed for internal infrastructure, Sentinel OS can operate independently of cloud services, push alerts only when real threats occur, and remain fully manageable inside closed networks. Its purpose is simple: give small labs and private networks the power of enterprise-grade intrusion detection with zero complexity, zero bloat, and absolute control.</p>';
     echo '<p><strong>As a PHP Plugin</strong><br>The Sentinel PHP Plugin is a lightweight security module for websites that provides threat detection, integrity checking, and behavioral monitoring directly inside the website environment. It ties into the website without adding framework bloat and reports only actionable security events such as intrusion attempts, suspicious traffic, malformed requests, and DDoS patterns. Built with KISS principles and strict PSR compliance, the plugin integrates seamlessly with existing the Chaos CMS workflows, allowing operators to track vulnerability signals, review logs, and maintain site health from within the admin panel. Sentinel for PHP extends the same protective philosophy of Sentinel OS into the web stack: small, focused, transparent, and fully controlled by the operator.</p>';
    echo '</section>';
}

function pi_os() {
    echo '<small><em><a href="/projects">Projects</a> >> <strong>Pi OS</em></small>';
    echo '<section>';
    echo '<h2>Pi OS</h2>';
    echo '<p>a very small (3.6GB) Linux Headless Operating System for the Raspberry Pi 5 and up, based on Ubuntu/Debian with its own Repository for updating. This OS Is great for internal web servers, NAT/Routing, Storage (NAS) Servers and run flawlessly on edge type systems.</p>';
    echo '</section>';
}

function ai_os() {
    echo '<small><em><a href="/projects">Projects</a> >> <strong>AI OS</em></small>';
    echo '<section>';
    echo '<h2>AI OS</h2>';
    echo '<p>a very small (3.6GB) Linux Headless Operating System for the Raspberry Pi 5 and up that utilizes the Hailo 8/10 AI Hat+, based on Raspian/Debian that does not have its own repository at this current time. If you are AI enthusiast on edge type systems and have a Hailo AI Hat+ installed, this OS is for you.</p>';
    echo '</section>';
}

function router() {
    echo '<small><em><a href="/projects">Projects</a> >> <strong>Pi Router</em></small>';
    echo '<section>';
    echo '<h2>Pi Router/Gateway</h2>';
    echo '<p>The STN-Labz Raspberry Pi Router Initiative is a forward-leaning effort to transform Raspberry Pi 5 hardware into a fully customized, security-focused network platform powered by STN-OS (<a href="/projects/pi-os">Pi-OS</a>). Instead of relying on bloated, generic distributions, the project builds a lean, hardened, purpose-built OS that strips away unnecessary components while preserving complete control for the operator. The initiative blends traditional Unix principles with modern threat-intelligence tooling, ensuring every Pi-based router can function as a lightweight gateway. At its core, it represents STN-Labz’s broader philosophy: owning the stack from silicon to software, engineering efficient systems by hand, and deploying practical, innovative technology that respects sovereignty, simplicity, and long-term resilience.</p>';
    echo '</section>';
}

function rictus() {
    echo '<small><em><a href="/projects">Projects</a> >> <strong>RictusD</em></small>';
    echo '<section>';
    echo '<h2>RictusD</h2>';
    echo '<p>Complience Enforcement.</p>';
    echo '<p>RictusD is the silent, surgical enforcer of the STN-Labz ecosystem — a non-conversational, operator-controlled system whose sole purpose is to scan, validate, and structurally correct project directories without altering logic, behavior, or architecture. It never acts on its own, never anticipates tasks, never interacts with users, and operates only within explicitly authorized paths. Bound by strict sovereignty, transparency, and minimalism rules, RictusD performs predictable, deterministic actions such as fixing JSON, restoring required paths, enforcing lowercase naming, and applying strict typing, while logging every change it makes. It is the system’s uncompromising compliance engine: precise, controlled, unopinionated, and utterly obedient.</p>';
    echo '</section>';
}

function digit() {
    echo '<small><em><a href="/projects">Projects</a> >> <strong>DigitD</em></small>';
    echo '<section>';
    echo '<h2>DigitD</h2>';
    echo '<p>Company Wide Assistant.</p>';
    echo '<p>DigitD is the sovereign, in-house intelligence engine of STN-Labz—built to think, guide, reason, orchestrate, and evolve entirely within the companies infrastructure. She isn’t a cloud-bound chatbot or a toy assistant; she is a locally running, policy-bound cognitive system capable of chatting, interpreting intent, spawning hundreds of concurrent agents, managing tasks, ingesting knowledge, tracking doctrine, learning from workflows, and expanding her capabilities through modular Go code. Every part of her behavior is transparent, logged, observable, and shaped by the STN-Labz Ethics & Charter, with <a href="/projects/rictusd">RictusD</a> serving as her enforcing counterpart.</p>';
    echo '<p>DigitD grows by absorbing input, notes, patterns from STN-Labz codebases, policies, architectural decisions, system behavior, authorized external documentation, and real operational events. Over time she forms a structured, internal understanding of projects, languages, tools, and doctrines—becoming the mind and voice of HQ while remaining completely compliant to company doctrine.</p>';
    echo '</section>';
}

function dns_d() {
    echo '<small><em><a href="/projects">Projects</a> >> <strong>DNSD</em></small>';
    echo '<section>';
    echo '<h2>DNSD</h2>';
    echo '<p>Secure DNS.</p>';
    echo '<p>The STN-Labz DNSD is a lightweight, high-performance authoritative DNS server written in Go and engineered for total independence from legacy DNS stacks like BIND. It loads all of its zones from clean, human-readable JSON, serves them with speed and accuracy, and avoids unnecessary complexity while still supporting core features like SOA, NS, A, TXT, and secure future expansion for AXFR and TSIG replication. DNSD is designed to run as a hardened system service with minimal privileges, binding cleanly to port 53 without the usual Ubuntu resolver chaos, and giving you full sovereignty over internal and external domains alike. It’s built to scale into a true STN-Labz product: modular, predictable, and capable of powering an entire DNS cluster with the same simplicity and control that defines the rest of your ecosystem.</p>';
    echo '</section>';
}

function chaos_cms() {
    echo '<small><em><a href="/projects">Projects</a> >> <strong>Chaos CMS</em></small>';
    echo '<section>';
    echo '<h2>Chaos CMS</h2>';
    echo '<p>Clarity over magic!</p>';
    echo '<p><a href="/changelog">Dev Log</a><br><a href="/roadmap">Dev Map</a></p>';
    echo '<p>The Chaos CMS is a lightweight, developer-focused content management system built on a strict KISS philosophy, avoiding frameworks, avoiding bloat, and keeping everything human-readable, transparent, and under the operator’s full control. Its core is intentionally simple: a clean bootstrap, a predictable router, JSON and Markdown rendering, a small set of core modules, and a minimal theme system that an end-user can actually understand without needing to learn an entire software ecosystem. Chaos CMS favors clarity over magic — no hidden layers, no dependency stacks, no composer forests. What you see is exactly what runs.</p>';
    echo '<p>The system is designed for sovereignty and flexibility. Modules, pages, themes, and plugins live in clean directory structures, can be dropped in or removed easily, and the database version introduces structured support for modules, plugins, themes, users, and internal logging without sacrificing the simplicity that defines the project. Whether running flat-file JSON builds or the newer MySQL-driven variant, Chaos CMS maintains the same philosophy: everything should be editable, transparent, portable, and easy for a developer to extend without fighting the system. It’s a CMS built for builders — not for vendors, trends, or over-engineered “enterprise” patterns.</p>';
    echo '</section>';
}

/* --- dispatch --- */
switch ($route) {
    case '':
    case 'home':
        home();
        break;
    // DevBot
    case 'devbot':
        devbot();
        break;
    // OS
    case 'ai-os':
        ai_os();
        break;
     case 'pi-os':
        pi_os();
        break;
    // Router
    case 'router':
        router();
        break;
    // Sentinel
    case 'sentinel':
        sentinel();
        break;
    // RictusD
    case 'rictusd':
        rictus();
        break;
    // DigitD
    case 'digitd':
        digit();
        break;
    // DNSD
    case 'dnsd':
        dns_d();
        break;
    // Chaos CMS
    case 'chaos-cms':
        chaos_cms();
        break;
    // Block Chain
    case 'block-chain':
        block_chain();
        break;

    default:
        http_response_code(404);
        echo '<div class="container my-4"><div class="alert alert-secondary">Not found: '
            . htmlspecialchars($route, ENT_QUOTES, 'UTF-8')
            . '</div></div>';
        break;
}
?>
</div>



