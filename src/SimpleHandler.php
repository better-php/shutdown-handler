<?php

declare(strict_types=1);

namespace BetterPhp\ShutdownHandler;

use Closure;
use function register_shutdown_function;

final class SimpleHandler implements Contract\ShutdownHandler
{
    private ?Closure $callback;

    private function __construct(?callable $callback)
    {
        $this->registerShutdownFunction();
        if (null !== $callback) {
            $this->callback = Closure::fromCallable($callback);
        } else {
            $this->callback = null;
        }
    }

    public function register(callable $callable): void
    {
        $this->callback = Closure::fromCallable($callable);
    }

    public function deregister(): void
    {
        $this->callback = null;
    }

    private function shutdown(): void
    {
        if (null !== $this->callback) {
            ($this->callback)();
        }
    }

    private function registerShutdownFunction(): void
    {
        // Just use PHP's default shutdown handler.
        register_shutdown_function(Closure::fromCallable([$this, 'shutdown']));
    }
}
