<?php

/**
 * Chaos MVC manifesto page.
 */

require_once APPROOT . '/views/inc/head.php';
?>

<?php /* [AI:GPT-5 | 2026-08-24 08:00:00 UTC] */ ?>

<main class="container py-4 py-md-5">
    <header
        class="row justify-content-center text-center py-5"
        aria-labelledby="manifesto-heading"
    >
        <div class="col-lg-9 col-xl-8">
            <p class="text-uppercase fw-bold text-primary mb-2">
                The Chaos MVC Manifesto
            </p>

            <h1
                id="manifesto-heading"
                class="display-3 fw-bold mb-3"
            >
                Architecture Without the Shadows
            </h1>

            <p class="lead text-muted mb-0">
                Clear execution. Visible structure. A framework that expects
                developers to understand and own the systems they build.
            </p>
        </div>
    </header>

    <section
        class="row justify-content-center py-5 border-top"
        aria-labelledby="framework-heading"
    >
        <div class="col-lg-9">
            <p class="text-uppercase fw-bold text-primary mb-2">
                01 — Ownership
            </p>

            <h2
                id="framework-heading"
                class="display-6 fw-bold"
            >
                Stop fighting the framework.
            </h2>

            <p class="fs-5">
                Modern development too often buries straightforward logic
                beneath layers of abstraction and bloat. Chaos MVC takes the
                opposite approach. It is built for developers who want to
                understand their stack, trace its behavior, and remain
                responsible for the application they ship.
            </p>

            <p class="fs-5 mb-0">
                The framework supports your work. It does not ask you to
                surrender your judgment to it.
            </p>
        </div>
    </section>

    <section
        class="row justify-content-center py-5 border-top"
        aria-labelledby="wires-heading"
    >
        <div class="col-lg-9">
            <p class="text-uppercase fw-bold text-primary mb-2">
                02 — Transparency
            </p>

            <h2
                id="wires-heading"
                class="display-6 fw-bold"
            >
                We don’t hide the wires.
            </h2>

            <p class="fs-5">
                Transparency is an architectural requirement, not a slogan.
                From the Example Module to the Developer Portal, Chaos MVC
                exposes the structure developers need to understand how the
                system operates.
            </p>

            <div
                class="border-start border-primary border-4 ps-4 py-2 mt-4"
            >
                <p class="fs-4 fw-semibold mb-1">
                    Request → Controller → Model → View → Response
                </p>

                <p class="text-muted mb-0">
                    A predictable execution path in which every step can be
                    located, read, and traced.
                </p>
            </div>
        </div>
    </section>

    <section
        class="py-5 border-top"
        aria-labelledby="discipline-heading"
    >
        <div class="row justify-content-center mb-4">
            <div class="col-lg-9">
                <p class="text-uppercase fw-bold text-primary mb-2">
                    03 — Discipline
                </p>

                <h2
                    id="discipline-heading"
                    class="display-6 fw-bold"
                >
                    Discipline is your freedom.
                </h2>

                <p class="fs-5 text-muted mb-0">
                    Clear rules prevent the framework from decaying into a
                    collection of exceptions.
                </p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="row g-4">
                    <div class="col-md-4">
                        <article class="h-100 border rounded-3 p-4">
                            <p
                                class="small fw-bold text-primary mb-2"
                            >
                                CONSISTENCY
                            </p>

                            <h3 class="h4">Lowercase or leave</h3>

                            <p class="text-muted mb-0">
                                Files, classes, and controllers use lowercase
                                naming. Filesystem sanity matters more than
                                creative capitalization.
                            </p>
                        </article>
                    </div>

                    <div class="col-md-4">
                        <article class="h-100 border rounded-3 p-4">
                            <p
                                class="small fw-bold text-primary mb-2"
                            >
                                TRACEABILITY
                            </p>

                            <h3 class="h4">Annotation protocol</h3>

                            <p class="text-muted mb-0">
                                AI-assisted changes are marked. Core changes
                                are signed. The history of a decision remains
                                visible in the code it affected.
                            </p>
                        </article>
                    </div>

                    <div class="col-md-4">
                        <article class="h-100 border rounded-3 p-4">
                            <p
                                class="small fw-bold text-primary mb-2"
                            >
                                FOCUS
                            </p>

                            <h3 class="h4">Zero bloat</h3>

                            <p class="text-muted mb-0">
                                If a capability is not essential to the
                                framework, it stays out of Core and belongs in
                                an optional module or project-specific code.
                            </p>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section
        class="row justify-content-center py-5 border-top"
        aria-labelledby="magic-heading"
    >
        <div class="col-lg-9">
            <p class="text-uppercase fw-bold text-primary mb-2">
                04 — Determinism
            </p>

            <h2
                id="magic-heading"
                class="display-6 fw-bold"
            >
                No magic. No nonsense.
            </h2>

            <p class="fs-5">
                Chaos MVC does not depend on telemetry, hidden tracking, or
                automatic behavior that conceals the state of your application.
                Important operations should be explicit, inspectable, and based
                on the real condition of the system.
            </p>

            <p class="fs-5 mb-0">
                We would rather diagnose and harden a schema deliberately than
                trust automation that cannot explain what it changed or why.
            </p>
        </div>
    </section>

    <section
        class="row justify-content-center py-5 border-top"
        aria-labelledby="competency-heading"
    >
        <div class="col-lg-9">
            <p class="text-uppercase fw-bold text-primary mb-2">
                05 — Craft
            </p>

            <h2
                id="competency-heading"
                class="display-6 fw-bold"
            >
                Competency is the watchword.
            </h2>

            <p class="fs-5">
                Chaos MVC is for developers who respect the craft enough to
                keep their work precise. The framework provides a clear
                architecture; the developer brings the judgment and skill
                required to use it well.
            </p>

            <p class="fs-5 mb-0">
                The goal is an ecosystem in which code is readable, execution
                is predictable, and developers can confidently explain the
                systems they maintain.
            </p>
        </div>
    </section>

    <footer class="row justify-content-center py-5 border-top">
        <div class="col-lg-9">
            <div
                class="bg-dark text-white rounded-3 p-4 p-md-5 text-center"
            >
                <p
                    class="text-uppercase small fw-bold text-white-50 mb-2"
                >
                    The standard
                </p>

                <p class="display-6 fw-bold mb-0">
                    Discipline over convenience.<br>
                    Every single time.
                </p>
            </div>
        </div>
    </footer>
</main>

<?php /* [End AI:GPT-5] */ ?>

<?php require_once APPROOT . '/views/inc/foot.php'; ?>