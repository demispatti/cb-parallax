<?php
/**
 * Dependency Injection Container
 *
 * A simple but powerful dependency injection container for managing
 * plugin services and dependencies.
 *
 * @package    CbParallax
 * @subpackage Core
 * @author     CB Parallax Team
 * @license    GPL-2.0-or-later
 * @since      1.0.0
 */

declare(strict_types=1);

namespace CbParallax\Core;

// Exit if accessed directly
if (!defined('WPINC')) {
    wp_die('Direct access not permitted.');
}

/**
 * Dependency Injection Container
 */
class Container
{
    /**
     * Container bindings
     *
     * @var array<string, callable>
     */
    private array $bindings = [];

    /**
     * Singleton instances
     *
     * @var array<string, object>
     */
    private array $instances = [];

    /**
     * Register a binding in the container
     *
     * @param string   $id       Service identifier
     * @param callable $concrete Factory function
     * @return void
     */
    public function bind(string $id, callable $concrete): void
    {
        $this->bindings[$id] = $concrete;
    }

    /**
     * Register a singleton binding
     *
     * @param string   $id       Service identifier
     * @param callable $concrete Factory function
     * @return void
     */
    public function singleton(string $id, callable $concrete): void
    {
        $this->bind($id, function ($container) use ($id, $concrete) {
            if (!isset($this->instances[$id])) {
                $this->instances[$id] = $concrete($container);
            }

            return $this->instances[$id];
        });
    }

    /**
     * Register an existing instance
     *
     * @param string $id       Service identifier
     * @param object $instance Service instance
     * @return void
     */
    public function instance(string $id, object $instance): void
    {
        $this->instances[$id] = $instance;
    }

    /**
     * Resolve a service from the container
     *
     * @param string $id Service identifier
     * @return mixed
     * @throws \RuntimeException If service is not bound
     */
    public function get(string $id)
    {
        // Check if we have an instance
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // Check if we have a binding
        if (!isset($this->bindings[$id])) {
            // Try auto-wiring
            if (class_exists($id)) {
                return $this->autoWire($id);
            }

            throw new \RuntimeException(
                sprintf('Service "%s" is not bound in the container.', $id)
            );
        }

        // Resolve the binding
        return $this->bindings[$id]($this);
    }

    /**
     * Check if a service is bound
     *
     * @param string $id Service identifier
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]) || isset($this->instances[$id]);
    }

    /**
     * Auto-wire a class by analyzing constructor dependencies
     *
     * @param string $className Fully qualified class name
     * @return object
     * @throws \ReflectionException If class doesn't exist
     * @throws \RuntimeException If dependencies can't be resolved
     */
    private function autoWire(string $className): object
    {
        $reflectionClass = new \ReflectionClass($className);

        if (!$reflectionClass->isInstantiable()) {
            throw new \RuntimeException(
                sprintf('Class "%s" is not instantiable.', $className)
            );
        }

        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return new $className();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type === null || $type instanceof \ReflectionUnionType) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new \RuntimeException(
                    sprintf(
                        'Cannot auto-wire parameter "$%s" in class "%s".',
                        $parameter->getName(),
                        $className
                    )
                );
            }

            $typeName = $type->getName();

            // For built-in types, try to get default value
            if ($type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new \RuntimeException(
                    sprintf(
                        'Cannot auto-wire built-in type "%s" for parameter "$%s" in class "%s".',
                        $typeName,
                        $parameter->getName(),
                        $className
                    )
                );
            }

            // Resolve the dependency
            $dependencies[] = $this->get($typeName);
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    /**
     * Call a callable with dependency injection
     *
     * @param callable $callable Callable to invoke
     * @param array    $parameters Additional parameters
     * @return mixed
     * @throws \ReflectionException
     */
    public function call(callable $callable, array $parameters = [])
    {
        if (is_array($callable)) {
            [$class, $method] = $callable;
            $reflection = new \ReflectionMethod($class, $method);
        } elseif (is_object($callable) && !$callable instanceof \Closure) {
            $reflection = new \ReflectionMethod($callable, '__invoke');
        } else {
            $reflection = new \ReflectionFunction($callable);
        }

        $dependencies = [];
        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();

            // Check if parameter is provided
            if (isset($parameters[$name])) {
                $dependencies[] = $parameters[$name];
                continue;
            }

            $type = $parameter->getType();

            if ($type === null || $type instanceof \ReflectionUnionType || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new \RuntimeException(
                    sprintf(
                        'Cannot resolve parameter "$%s".',
                        $parameter->getName()
                    )
                );
            }

            $dependencies[] = $this->get($type->getName());
        }

        return $reflection->invokeArgs(
            is_object($callable) && !$callable instanceof \Closure ? $callable : null,
            $dependencies
        );
    }

    /**
     * Remove a binding or instance
     *
     * @param string $id Service identifier
     * @return void
     */
    public function remove(string $id): void
    {
        unset($this->bindings[$id], $this->instances[$id]);
    }

    /**
     * Clear all bindings and instances
     *
     * @return void
     */
    public function clear(): void
    {
        $this->bindings = [];
        $this->instances = [];
    }

    /**
     * Get all registered service identifiers
     *
     * @return array<string>
     */
    public function getBindings(): array
    {
        return array_unique(array_merge(
            array_keys($this->bindings),
            array_keys($this->instances)
        ));
    }
}
