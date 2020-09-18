<?php

declare(strict_types=1);

namespace BetterPhp\ShutdownHandler;

use Closure;

final class ManagedHandler implements Contract\ShutdownHandler
{
    private ?Closure $callback;
    private Manager $manager;

    public function __construct(Manager $manager, ?callable $callback = null)
    {
        if (null !== $callback) {
            $this->callback = Closure::fromCallable($callback);
        } else {
            $this->callback = null;
        }

        $this->manager = $manager;
    }

    public function run(): void
    {
        $this->manager->deregisterHandler($this);
        if (null !== $this->callback) {
            ($this->callback)();
        }
    }

    public function register(callable $callable): void
    {
        $this->callback = Closure::fromCallable($callable);
        $this->manager->registerHandler($this);
    }

    public function deregister(): void
    {
        $this->callback = null;
        $this->manager->deregisterHandler($this);
    }
}
