<?php
declare(strict_types=1);

/**
 * Router
 *
 * Routes requests to controllers/methods in a strict and secure way.
 * - No silent fallback to index()
 * - Strict controller/method name validation
 * - Blocks magic/underscore methods
 * - Throws internally, responds with controlled 404 externally (Strategy D)
 *
 * @package STN-Labz
 */

final class router
{
    private string $controllersDir = '/controllers';
    private string $defaultController = 'home';
    private string $defaultMethod = 'index';

    public function run(): void
    {
        try {
            $this->dispatch();
        } catch (Throwable $e) {
            $this->logException($e);
            $this->respond404();
        }
    }

    private function dispatch(): void
    {
        $url = $this->get_url();

        /*
        -------------------------------------------------------
        ADMIN MODULE HANDOFF
        -------------------------------------------------------
        /admin/{module}
        routes to admin controller which loads module controllers
        -------------------------------------------------------
        */

        if (($url[0] ?? '') === 'admin') {

            require_once APPROOT . $this->controllersDir . '/admin.php';

            if (!class_exists('admin', false)) {
                throw new RuntimeException('Admin controller not found.');
            }

            $controller = new admin();

            if (!method_exists($controller, 'index')) {
                throw new RuntimeException('Admin controller missing index().');
            }

            $controller->index($url);
            return;
        }

        /*
        -------------------------------------------------------
        AUTH SHORTCUTS
        -------------------------------------------------------
        */

        if (($url[0] ?? '') === 'login') {
            $url = ['auth', 'login'];
        } elseif (($url[0] ?? '') === 'logout') {
            $url = ['auth', 'logout'];
        }

        $controllerName = $this->normalizeControllerName($url[0] ?? $this->defaultController);
        $methodName     = $this->normalizeMethodName($url[1] ?? $this->defaultMethod);

        $params = array_slice($url, 2);

        if ($this->controllerFileExists($controllerName)) {
            $this->dispatchController($controllerName, $methodName, $params);
            return;
        }

        $this->dispatchPageFallback($controllerName);
    }

    private function dispatchController(string $controllerName, string $methodName, array $params): void
    {
        $controllerFile = $this->controllerFilePath($controllerName);

        require_once $controllerFile;

        if (!class_exists($controllerName, false)) {
            throw new RuntimeException('Controller class not found after require: ' . $controllerName);
        }

        if (class_exists('controller', false) && !is_subclass_of($controllerName, 'controller')) {
            throw new RuntimeException('Controller does not extend base controller: ' . $controllerName);
        }

        $controller = new $controllerName();

        if ($this->isDeniedMethod($methodName)) {
            throw new RuntimeException('Denied method requested: ' . $controllerName . '::' . $methodName);
        }

        if (!method_exists($controller, $methodName)) {
            throw new RuntimeException('Method not found: ' . $controllerName . '::' . $methodName);
        }

        $ref = new ReflectionMethod($controller, $methodName);

        if (!$ref->isPublic()) {
            throw new RuntimeException('Non-public method requested: ' . $controllerName . '::' . $methodName);
        }

        $required = $ref->getNumberOfRequiredParameters();
        $total    = $ref->getNumberOfParameters();

        if ($total === 1) {
            $controller->{$methodName}($params);
            return;
        }

        $argc = count($params);

        if ($argc < $required || $argc > $total) {
            throw new RuntimeException(
                'Parameter mismatch for ' . $controllerName . '::' . $methodName .
                ' (given ' . $argc . ', required ' . $required . ', total ' . $total . ')'
            );
        }

        $controller->{$methodName}(...$params);
    }

    private function dispatchPageFallback(string $slug): void
    {
        if (!$this->isValidName($slug)) {
            throw new RuntimeException('Invalid slug for page fallback.');
        }

        $pageController = 'page';

        if (!$this->controllerFileExists($pageController)) {
            throw new RuntimeException('Page controller not available for fallback routing.');
        }

        require_once $this->controllerFilePath($pageController);

        if (!class_exists($pageController, false)) {
            throw new RuntimeException('Page controller class not found after require.');
        }

        $page = new page();

        if (!method_exists($page, 'index')) {
            throw new RuntimeException('Page controller missing index().');
        }

        $ref = new ReflectionMethod($page, 'index');

        if (!$ref->isPublic()) {
            throw new RuntimeException('Page index() not public.');
        }

        $total = $ref->getNumberOfParameters();

        if ($total === 1) {
            $page->index($slug);
            return;
        }

        $page->index([$slug]);
    }

    private function controllerFilePath(string $controllerName): string
    {
        return APPROOT . $this->controllersDir . '/' . $controllerName . '.php';
    }

    private function controllerFileExists(string $controllerName): bool
    {
        return is_file($this->controllerFilePath($controllerName));
    }

    private function normalizeControllerName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = str_replace('-', '_', $name);

        if (!$this->isValidName($name)) {
            throw new RuntimeException('Invalid controller name.');
        }

        return $name;
    }

    private function normalizeMethodName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = str_replace('-', '_', $name);

        if (!$this->isValidName($name)) {
            throw new RuntimeException('Invalid method name.');
        }

        return $name;
    }

    private function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[a-z0-9_]+$/', $name);
    }

    private function isDeniedMethod(string $methodName): bool
    {
        if ($methodName === '') {
            return true;
        }

        if (str_starts_with($methodName, '__')) {
            return true;
        }

        if (str_starts_with($methodName, '_')) {
            return true;
        }

        return false;
    }

    private function get_url(): array
    {
        if (!isset($_GET['url'])) {
            return [];
        }

        $raw = (string) $_GET['url'];
        $raw = trim($raw);
        $raw = rtrim($raw, '/');

        if ($raw === '') {
            return [];
        }

        $parts = explode('/', $raw);
        $out = [];

        foreach ($parts as $p) {
            $p = rawurldecode($p);
            $p = strtolower(trim($p));
            $p = str_replace('-', '_', $p);
            $out[] = $p;
        }

        return $out;
    }

    private function respond404(): void
    {
        if (!headers_sent()) {
            http_response_code(404);
        }

        $errController = 'error_handler';

        if ($this->controllerFileExists($errController)) {

            require_once $this->controllerFilePath($errController);

            if (class_exists($errController, false)) {

                $c = new error_handler();

                if (method_exists($c, 'index')) {

                    try {

                        $ref = new ReflectionMethod($c, 'index');

                        if ($ref->isPublic()) {
                            $c->index(['404']);
                            exit;
                        }

                    } catch (Throwable) {}

                }

            }

        }

        echo '404 Not Found';
        exit;
    }

    private function logException(Throwable $e): void
    {
        error_log('[router] ' . $e->getMessage());
    }
}
