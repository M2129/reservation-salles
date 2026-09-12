<?php

declare(strict_types=1);

namespace App;

use App\Exception\ReservationInvalideException;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\View\ViewRenderer;
use Closure;
use FastRoute\Dispatcher;
use Throwable;

final class Application
{
    /** @param Closure(string): object $controllerResolver */
    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly ViewRenderer $view,
        private readonly Closure $controllerResolver,
    ) {
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $routeInfo = $this->dispatcher->dispatch($method, $uri);

        try {
            $body = match ($routeInfo[0]) {
                Dispatcher::NOT_FOUND => $this->error('404', 404),
                Dispatcher::METHOD_NOT_ALLOWED => $this->methodNotAllowed($routeInfo[1]),
                Dispatcher::FOUND => $this->invoke($routeInfo[1], $routeInfo[2]),
                default => $this->error('500', 500),
            };
        } catch (ReservationInvalideException|SalleIndisponibleException|ReservationIntrouvableException $exception) {
            $body = $this->error('404', 404);
        } catch (Throwable) {
            $body = $this->error('500', 500);
        }

        echo $body;
    }

    /** @param array{0: string, 1: string} $handler @param array<string, string> $parameters */
    private function invoke(array $handler, array $parameters): string
    {
        [$controllerClass, $method] = $handler;
        $controller = ($this->controllerResolver)($controllerClass);
        $arguments = match (true) {
            $method === 'store' => [$_POST],
            $method === 'update' => [(int) $parameters['id'], $_POST],
            $method === 'cancel' => [(int) $parameters['id']],
            $method === 'index' && $controllerClass === \App\Controller\SalleController::class => [$_GET['q'] ?? null],
            $method === 'index' => [$_GET],
            isset($parameters['id']) => [(int) $parameters['id']],
            default => [],
        };

        return $controller->{$method}(...$arguments);
    }

    /** @param list<string> $allowedMethods */
    private function methodNotAllowed(array $allowedMethods): string
    {
        http_response_code(405);
        header('Allow: ' . implode(', ', $allowedMethods));

        return $this->view->render('error/405');
    }

    private function error(string $view, int $status): string
    {
        http_response_code($status);

        return $this->view->render('error/' . $view);
    }
}
