<?php
/**
 * Hook Manager
 *
 * Manages WordPress actions and filters in a clean, organized way.
 *
 * @package    CbParallax
 * @subpackage Core\Hooks
 * @author     CB Parallax Team
 * @license    GPL-2.0-or-later
 * @since      1.0.0
 */

declare(strict_types=1);

namespace CbParallax\Core\Hooks;

// Exit if accessed directly
if (!defined('WPINC')) {
    wp_die('Direct access not permitted.');
}

/**
 * Hook Manager Class
 */
class HookManager
{
    /**
     * Registered actions
     *
     * @var array<string, array>
     */
    private array $actions = [];

    /**
     * Registered filters
     *
     * @var array<string, array>
     */
    private array $filters = [];

    /**
     * Add an action hook
     *
     * @param string   $tag      Hook name
     * @param callable $callback Callback function
     * @param int      $priority Priority (default: 10)
     * @param int      $acceptedArgs Number of arguments (default: 1)
     * @return self
     */
    public function addAction(
        string $tag,
        callable $callback,
        int $priority = 10,
        int $acceptedArgs = 1
    ): self {
        add_action($tag, $callback, $priority, $acceptedArgs);

        $this->actions[$tag][] = [
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $acceptedArgs,
        ];

        return $this;
    }

    /**
     * Add a filter hook
     *
     * @param string   $tag      Hook name
     * @param callable $callback Callback function
     * @param int      $priority Priority (default: 10)
     * @param int      $acceptedArgs Number of arguments (default: 1)
     * @return self
     */
    public function addFilter(
        string $tag,
        callable $callback,
        int $priority = 10,
        int $acceptedArgs = 1
    ): self {
        add_filter($tag, $callback, $priority, $acceptedArgs);

        $this->filters[$tag][] = [
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $acceptedArgs,
        ];

        return $this;
    }

    /**
     * Remove an action hook
     *
     * @param string   $tag      Hook name
     * @param callable $callback Callback function
     * @param int      $priority Priority (default: 10)
     * @return bool True if removed, false otherwise
     */
    public function removeAction(
        string $tag,
        callable $callback,
        int $priority = 10
    ): bool {
        $removed = remove_action($tag, $callback, $priority);

        if ($removed && isset($this->actions[$tag])) {
            $this->actions[$tag] = array_filter(
                $this->actions[$tag],
                function ($action) use ($callback, $priority) {
                    return !($action['callback'] === $callback && $action['priority'] === $priority);
                }
            );
        }

        return $removed;
    }

    /**
     * Remove a filter hook
     *
     * @param string   $tag      Hook name
     * @param callable $callback Callback function
     * @param int      $priority Priority (default: 10)
     * @return bool True if removed, false otherwise
     */
    public function removeFilter(
        string $tag,
        callable $callback,
        int $priority = 10
    ): bool {
        $removed = remove_filter($tag, $callback, $priority);

        if ($removed && isset($this->filters[$tag])) {
            $this->filters[$tag] = array_filter(
                $this->filters[$tag],
                function ($filter) use ($callback, $priority) {
                    return !($filter['callback'] === $callback && $filter['priority'] === $priority);
                }
            );
        }

        return $removed;
    }

    /**
     * Check if an action is registered
     *
     * @param string $tag Hook name
     * @return bool
     */
    public function hasAction(string $tag): bool
    {
        return isset($this->actions[$tag]) && count($this->actions[$tag]) > 0;
    }

    /**
     * Check if a filter is registered
     *
     * @param string $tag Hook name
     * @return bool
     */
    public function hasFilter(string $tag): bool
    {
        return isset($this->filters[$tag]) && count($this->filters[$tag]) > 0;
    }

    /**
     * Get all registered actions
     *
     * @return array<string, array>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Get all registered filters
     *
     * @return array<string, array>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get actions for a specific tag
     *
     * @param string $tag Hook name
     * @return array
     */
    public function getActionsForTag(string $tag): array
    {
        return $this->actions[$tag] ?? [];
    }

    /**
     * Get filters for a specific tag
     *
     * @param string $tag Hook name
     * @return array
     */
    public function getFiltersForTag(string $tag): array
    {
        return $this->filters[$tag] ?? [];
    }

    /**
     * Remove all hooks (actions and filters)
     *
     * @return void
     */
    public function removeAll(): void
    {
        foreach ($this->actions as $tag => $actions) {
            foreach ($actions as $action) {
                remove_action($tag, $action['callback'], $action['priority']);
            }
        }

        foreach ($this->filters as $tag => $filters) {
            foreach ($filters as $filter) {
                remove_filter($tag, $filter['callback'], $filter['priority']);
            }
        }

        $this->actions = [];
        $this->filters = [];
    }
}
