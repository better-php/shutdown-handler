<?php
declare(strict_types=1);

namespace BetterPhp\ShutdownHandler;

use BetterPhp\ShutdownHandler\Contract\ShutdownHandler;
use Closure;
use LogicException;
use function spl_object_id;

final class Manager
{
    private static Manager $instance;

    /**
     * Registered callables.
     *
     * @var array<string,ManagedHandler>
     */
    private array $handlers = [];

    private function __construct()
    {
        $this->registerShutdownFunction();
    }

    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function creatHandler(?callable $callback, bool $register = false): ShutdownHandler
    {
        $handler = new ManagedHandler($this, $callback);
        if ($register) {
            if (null === $callback) {
                throw new LogicException('Cannot register a handler without a callback.');
            }
            $this->registerHandler($handler);
        }

        return $handler;
    }

    public function registerHandler(ShutdownHandler $handler): void
    {
        $this->handlers[spl_object_id($handler)] = $handler;
    }

    public function deregisterHandler(ShutdownHandler $handler): void
    {
        $id = spl_object_id($handler);
        if (isset($this->handlers[$id])) {
            unset($this->handlers[$id]);
        }
    }

    /**
     * Real shutdown handler.
     *
     * Called by PHP's shutdown handler. Run through the registered handlers and run them.
     */
    private function shutdown(): void
    {
        foreach ($this->handlers as $handler) {
            $handler->deregister();
            $handler->run();
        }
    }

    /**
     * Register PHP shutdown handler
     */
    private function registerShutdownFunction(): void
    {
        // Just use PHP's default shutdown handler.
        register_shutdown_function(Closure::fromCallable([$this, 'shutdown']));
    }
}
