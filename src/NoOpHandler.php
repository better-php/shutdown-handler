<?php

declare(strict_types=1);

namespace BetterPhp\ShutdownHandler;

final class NoOpHandler implements Contract\ShutdownHandler
{
    public function register(callable $callable): void
    {
    }

    public function deregister(): void
    {
    }
}
