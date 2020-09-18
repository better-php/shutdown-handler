<?php


namespace BetterPhp\ShutdownHandler\Contract;


interface ShutdownHandler
{
    public function register(callable $callable): void;
    public function deregister(): void;
}
