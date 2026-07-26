<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * PHP Hooks Class (Modified)
 *
 * @link https://github.com/voku/php-hooks/blob/master/src/voku/helper/Hooks.php
 */
class Hooks
{
    /**
     * Registered callbacks grouped by hook tag, then by ascending priority, then by callback ID.
     *
     * @var array<string, array<int, array<string, array{function: \Closure, accepted_args: int}>>>
     */
    protected array $filters = [];

    /**
     * Cache flag for hook tags whose callbacks have already been sorted by ascending priority.
     *
     * This was previously named `merged_filters`, but nothing is merged here. The
     * actual callbacks still live in `$filters`; this array only records whether
     * `$filters[$tag]` has already been sorted with `ksort()`.
     *
     * Lifecycle:
     * - `add_filter()`, `remove_filter()`, and `remove_all_filters()` unset the tag
     *   to mark it as needing a fresh sort.
     * - `apply_filters()`, `apply_filters_ref_array()`, `do_action()`, and
     *   `do_action_ref_array()` sort `$filters[$tag]` once, then set the tag to `true`.
     * - Subsequent executions skip sorting until that tag is mutated again.
     *
     * This avoids re-sorting the same hook on every execution while keeping
     * callback order correct after mutations.
     *
     * @var array<string, bool>
     */
    protected array $sorted_filters = [];

    /**
     * Number of times each action hook has fired.
     *
     * @var array<string, int>
     */
    protected array $actions = [];

    /**
     * Stack of currently executing filter and action tags.
     *
     * @var list<string>
     */
    protected array $current_filter = [];

    /**
     * Registered shortcode handlers keyed by shortcode tag.
     *
     * @var array<string, \Closure>
     */
    public static array $shortcode_tags = [];

    /**
     * Default priority
     */
    const int PRIORITY_NEUTRAL = 50;

    /**
     * This class is not allowed to call from outside: private!
     */
    protected function __construct()
    {
    }

    /**
     * Prevent the object from being cloned.
     */
    protected function __clone()
    {
    }

    /**
     * Avoid serialization.
     */
    public function __wakeup(): void
    {
    }

    /**
     * Get the singleton instance.
     *
     * @return static
     */
    public static function getInstance(): static
    {
        // PHP's static variables are initialized only once.
        // This is the core of the Singleton pattern.
        static $instance;

        // If the instance hasn't been created yet, create it.
        // The null coalescing operator or direct assignment would also work here,
        // but the explicit `if (null === $instance)` is a very common and clear pattern for singletons.
        if (null === $instance) {
            $instance = new static(); // Use `new static()` for late static binding.
        }

        return $instance;
    }

    /**
     * FILTERS
     */

    /**
     * Add a callback to a filter hook.
     *
     * Lower priority values run earlier.
     */
    public function add_filter(
        string $tag,
        callable $callback,
        int $priority = self::PRIORITY_NEUTRAL,
        int $accepted_args = 1
    ): bool {
        $idx = $this->_filter_build_unique_id($callback);

        // Initialize array structures if they don't exist to prevent errors.
        // PHP's array behavior handles this implicitly, but explicit initialization
        // can sometimes make code clearer for complex structures.
        // However, for direct assignment like $this->filters[$tag][$priority][$idx],
        // PHP will create the necessary arrays automatically.
        // So, no changes are strictly needed here for modernization.

        $this->filters[$tag][$priority][$idx] = [
            'function' => \Closure::fromCallable($callback),
            'accepted_args' => $accepted_args,
        ];

        // Unset merged filters for this tag, forcing a re-sort on the next application.
        unset($this->sorted_filters[$tag]);

        return true;
    }

    /**
     * Remove a callback from a filter hook.
     */
    public function remove_filter(
        string $tag,
        callable $callback,
        int $priority = self::PRIORITY_NEUTRAL
    ): bool {
        $uniqueId = $this->_filter_build_unique_id($callback);

        if (!isset($this->filters[$tag][$priority][$uniqueId])) {
            return false;
        }

        // Remove the specific function from the filter.
        unset($this->filters[$tag][$priority][$uniqueId]);

        // If, after removing, there are no more functions at this priority for this tag,
        // remove the priority array itself to keep the structure clean.
        if (empty($this->filters[$tag][$priority])) {
            unset($this->filters[$tag][$priority]);
        }

        // Unset the merged filters for this tag to force a re-sort on the next application.
        unset($this->sorted_filters[$tag]);

        return true;
    }

    /**
     * Remove callbacks from a filter hook.
     *
     * When `$priority` is `false`, every callback for the hook is removed.
     */
    public function remove_all_filters(string $tag, int|false $priority = false): bool
    {
        // Mark the hook as unsorted so it will be re-sorted on the next execution.
        unset($this->sorted_filters[$tag]);

        // If the tag itself doesn't exist in filters, there's nothing to remove.
        // Return true as it's effectively "removed".
        if (!isset($this->filters[$tag])) {
            return true;
        }

        // If a specific priority is provided and exists for this tag, remove only that priority's hooks.
        if (false !== $priority && isset($this->filters[$tag][$priority])) {
            unset($this->filters[$tag][$priority]);
        } else {
            // If no priority is specified (false) or the specified priority doesn't exist,
            // remove all hooks for the entire tag.
            unset($this->filters[$tag]);
        }

        return true;
    }

    /**
     * Determine whether a filter hook has any callbacks.
     *
     * @return int|bool Returns `true` when any callback exists and no callback is provided.
     * Returns the callback priority when a specific callback is found, or `false` otherwise.
     */
    public function has_filter(string $tag, ?callable $callback = null): int|bool
    {
        // Check if the tag exists in the filters array.
        $has = isset($this->filters[$tag]);

        // If no specific function to check, or if the tag itself has no filters,
        // simply return whether the tag exists.
        if (null === $callback || !$has) {
            return $has;
        }

        $uniqueId = $this->_filter_build_unique_id($callback);

        // Iterate through priorities for the given tag to find the function.
        // Using foreach directly on the priorities array is cleaner.
        foreach ($this->filters[$tag] as $priority => $callbacks) {
            // Check if the unique ID exists within the callbacks for this priority.
            if (isset($callbacks[$uniqueId])) {
                return $priority; // Return the priority if found.
            }
        }

        // If the function was not found at any priority.
        return false;
    }

    /**
     * Apply a filter hook to a value.
     */
    public function apply_filters(string $tag, mixed $value, mixed ...$args): mixed
    {
        // Combine the initial value with the rest of the arguments
        $allArgs = [$value, ...$args];

        // Do 'all' actions first
        if (isset($this->filters['all'])) {
            $this->current_filter[] = $tag;
            // Assuming _call_all_hook exists and is correctly defined elsewhere
            // It needs to accept an array of arguments, similar to how it was used previously
            $this->_call_all_hook($allArgs);
            \array_pop($this->current_filter);
        }

        // If no filters for the specific tag, return the original value.
        if (!isset($this->filters[$tag])) {
            return $value;
        }

        // Add the current tag to the filter stack for specific tag filters
        $this->current_filter[] = $tag;

        // Sort filters by priority (assumed integer keys for priority)
        if (!isset($this->sorted_filters[$tag])) {
            // ksort sorts by key, assuming filter priorities are the keys.
            \ksort($this->filters[$tag]);
            $this->sorted_filters[$tag] = true;
        }

        // Process filters
        foreach ($this->filters[$tag] as $priority => $callbacks) {
            foreach ($callbacks as $callbackData) {
                // Using null coalesce operator for cleaner checks
                if ($callbackData['function'] !== null) {
                    // Prepare arguments for the callable.
                    // The first argument is always the $value being filtered.
                    // Subsequent arguments are the ones passed after $value to apply_filters.
                    $callableArgs = \array_slice($allArgs, 0, (int)$callbackData['accepted_args']);
                    $callableArgs[0] = $value; // Ensure the first argument is the current $value

                    $callback = $callbackData['function'];
                    $value = $callback(...$callableArgs);
                }
            }
        }

        // Pop the current filter tag from the stack
        \array_pop($this->current_filter);

        return $value;
    }

    /**
     * Apply a filter hook using a pre-built argument array.
     *
     * @param array<int, mixed> $args
     */
    public function apply_filters_ref_array(string $tag, array $args): mixed
    {
        // Do 'all' actions first
        if (isset($this->filters['all'])) {
            $this->current_filter[] = $tag;
            // In apply_filters_ref_array, $args already contains the full set of arguments for the filter callbacks.
            // The 'all' hook often receives the tag as its first argument, then the original args.
            // Assuming _call_all_hook expects ($tag, ...$originalArgs).
            // If _call_all_hook always expects func_get_args() of the calling function, then `func_get_args()` here is correct.
            // Given the original function used func_get_args(), we'll retain that for direct compatibility for the 'all' hook.
            $all_hook_args = \func_get_args(); // This will be ($tag, $args_array)
            $this->_call_all_hook($all_hook_args);
            \array_pop($this->current_filter);
        }

        if (!isset($this->filters[$tag])) {
            return $args[0]; // If no specific tag filters, return the original first argument.
        }

        // Add the current tag to the filter stack for specific tag filters
        $this->current_filter[] = $tag;

        // Sort filters by priority (assumed integer keys for priority)
        if (!isset($this->sorted_filters[$tag])) {
            \ksort($this->filters[$tag]);
            $this->sorted_filters[$tag] = true;
        }

        // Process filters
        foreach ($this->filters[$tag] as $priority => $callbacks) {
            foreach ($callbacks as $callbackData) {
                if ($callbackData['function'] !== null) {
                    // Call the filter function.
                    // $args is already the array of arguments, so slice it directly.
                    // The result of the filter is assigned back to $args[0].
                    $callback = $callbackData['function'];
                    $args[0] = $callback(...\array_slice($args, 0, (int)$callbackData['accepted_args']));
                }
            }
        }

        // Pop the current filter tag from the stack
        \array_pop($this->current_filter);

        return $args[0];
    }

    /**
     * Add a callback to an action hook.
     *
     * Lower priority values run earlier.
     */
    public function add_action(
        string $tag,
        callable $callback,
        int $priority = self::PRIORITY_NEUTRAL,
        int $accepted_args = 1
    ): bool {
        // This function simply acts as a wrapper for add_filter,
        // as actions are essentially filters that don't modify a value.
        return $this->add_filter($tag, $callback, $priority, $accepted_args);
    }

    /**
     * Determine whether an action hook has any callbacks.
     *
     * @return bool|int Returns `true` when any callback exists and no callback is provided.
     * Returns the callback priority when a specific callback is found, or `false` otherwise.
     */
    public function has_action(string $tag, ?callable $callback = null): mixed
    {
        return $this->has_filter($tag, $callback);
    }

    /**
     * Remove a callback from an action hook.
     */
    public function remove_action(
        string $tag,
        callable $callback,
        int $priority = self::PRIORITY_NEUTRAL
    ): bool {
        return $this->remove_filter($tag, $callback, $priority);
    }

    /**
     * Remove callbacks from an action hook.
     *
     * When `$priority` is `false`, every callback for the hook is removed.
     */
    public function remove_all_actions(string $tag, int|false $priority = false): bool
    {
        return $this->remove_all_filters($tag, $priority);
    }

    /**
     * Execute an action hook.
     */
    public function do_action(string $tag, mixed $arg = '', mixed ...$additionalArgs): bool
    {
        // Initialize actions array if it's not already an array.
        // PHP 8.4 allows for clearer type handling.
        if (!\is_array($this->actions)) {
            $this->actions = [];
        }

        // Increment or initialize the action count for the given tag.
        $this->actions[$tag] = ($this->actions[$tag] ?? 0) + 1;

        // Do 'all' actions first.
        if (isset($this->filters['all'])) {
            $this->current_filter[] = $tag;
            // Collect all arguments passed to do_action for the 'all' hook.
            // This includes $tag, $arg, and any $additionalArgs.
            $all_hook_args = \func_get_args();
            // Assuming _call_all_hook exists and handles these arguments.
            $this->_call_all_hook($all_hook_args);
        }

        // If no specific actions are registered for this tag, and 'all' hooks were handled,
        // then pop the current filter and return false.
        if (!isset($this->filters[$tag])) {
            if (isset($this->filters['all'])) {
                \array_pop($this->current_filter);
            }
            return false;
        }

        // If 'all' hooks weren't processed (meaning `filters['all']` wasn't set),
        // then add the current tag to the filter stack now.
        // If 'all' was handled, it would have been pushed and popped, so push it again.
        $this->current_filter[] = $tag;

        // Prepare arguments for the action callbacks.
        $args_for_callbacks = [];

        // Handle the first argument ($arg).
        // The original logic checks for a specific case: an array containing a single object by reference.
        // This maintains that exact behavior for compatibility.
        if (\is_array($arg) && isset($arg[0]) && \is_object($arg[0]) && 1 === \count($arg)) {
            $args_for_callbacks[] = &$arg[0]; // Retain reference behavior
        } else {
            $args_for_callbacks[] = $arg;
        }

        // Append any additional arguments passed via variadic syntax.
        // Using `...$additionalArgs` makes this much cleaner than `func_get_arg` loop.
        if (!empty($additionalArgs)) {
            $args_for_callbacks = \array_merge($args_for_callbacks, $additionalArgs);
        }

        // Sort actions by priority (assuming integer keys for priority).
        if (!isset($this->sorted_filters[$tag])) {
            \ksort($this->filters[$tag]); // Note: 'filters' array is used for actions too, per original code.
            $this->sorted_filters[$tag] = true;
        }

        // Process actions.
        foreach ($this->filters[$tag] as $priority => $callbacks) {
            foreach ($callbacks as $callbackData) {
                if ($callbackData['function'] !== null) {
                    // Call the action function.
                    // Actions typically don't return a value that modifies $arg, unlike filters.
                    $callback = $callbackData['function'];
                    $callback(...\array_slice($args_for_callbacks, 0, (int)$callbackData['accepted_args']));
                }
            }
        }

        // Pop the current filter tag from the stack.
        \array_pop($this->current_filter);

        return true;
    }

    /**
     * Execute an action hook using a pre-built argument array.
     *
     * @param array<int, mixed> $args
     */
    public function do_action_ref_array(string $tag, array $args): bool
    {
        // Initialize actions array if it's not already an array.
        if (!\is_array($this->actions)) {
            $this->actions = [];
        }

        // Increment or initialize the action count for the given tag.
        $this->actions[$tag] = ($this->actions[$tag] ?? 0) + 1;

        // Do 'all' actions first.
        if (isset($this->filters['all'])) {
            $this->current_filter[] = $tag;
            // For `do_action_ref_array`, `func_get_args()` provides `($tag, $args_array)`.
            // This array is passed to `_call_all_hook` to maintain original behavior.
            $all_hook_args = \func_get_args();
            $this->_call_all_hook($all_hook_args);
        }

        // If no specific actions are registered for this tag, return false.
        if (!isset($this->filters[$tag])) {
            // If 'all' actions were processed, pop the current filter.
            if (isset($this->filters['all'])) {
                \array_pop($this->current_filter);
            }
            return false;
        }

        // If 'all' hooks weren't handled, or were pushed and popped, push the current tag now.
        $this->current_filter[] = $tag;

        // Sort actions by priority (assuming integer keys for priority).
        if (!isset($this->sorted_filters[$tag])) {
            \ksort($this->filters[$tag]); // Using 'filters' array for actions, as in original.
            $this->sorted_filters[$tag] = true;
        }

        // Process actions.
        foreach ($this->filters[$tag] as $priority => $callbacks) {
            foreach ($callbacks as $callbackData) {
                if ($callbackData['function'] !== null) {
                    // Call the action function. `array_slice` ensures only accepted arguments are passed.
                    $callback = $callbackData['function'];
                    $callback(...\array_slice($args, 0, (int)$callbackData['accepted_args']));
                }
            }
        }

        // Pop the current filter tag from the stack.
        \array_pop($this->current_filter);

        return true;
    }

    /**
     * Get the number of times an action hook has fired.
     */
    public function did_action(string $tag): int
    {
        // Use the null coalescing operator to return 0 if $this->actions is not an array
        // or if the specific $tag doesn't exist within it. This is more concise.
        return $this->actions[$tag] ?? 0;
    }

    /**
     * Get the current filter or action name.
     */
    public function current_filter(): string
    {
        // Use array_key_last to get the last element of the array.
        // This is generally preferred over end() as it does not manipulate the internal array pointer,
        // which can prevent unexpected side effects if the array pointer is relied upon elsewhere.
        // If the array is empty, array_key_last returns null, so we coalesce to an empty string.
        $lastTag = \array_key_last($this->current_filter);

        return ($lastTag !== null) ? $this->current_filter[$lastTag] : '';
    }

    /**
     * Build a stable unique ID for a registered callable.
     */
    private function _filter_build_unique_id(callable $function): string
    {
        return match (true) {
            \is_string($function) => $function,
            \is_array($function) => $this->buildArrayCallableUniqueId($function),
            $function instanceof \Closure => (string)\spl_object_id($function),
            \is_object($function) => \spl_object_id($function) . '::__invoke',
            default => throw new \LogicException('Unsupported callable type.'),
        };
    }

    /**
     * Build a stable unique ID for an array callable.
     *
     * @param array{0: object|string, 1: string} $callable
     */
    private function buildArrayCallableUniqueId(array $callable): string
    {
        [$target, $method] = $callable;

        if (\is_object($target)) {
            return \spl_object_id($target) . '::' . $method;
        }

        return $target . '::' . $method;
    }

    /**
     * Execute the callbacks registered on the special `all` hook.
     *
     * @param array<int, mixed> $args
     */
    public function _call_all_hook(array $args): void
    {
        // Ensure 'all' filters exist before attempting to iterate.
        // While the calling functions typically check, this adds an extra layer of safety.
        if (!isset($this->filters['all'])) {
            return;
        }

        // Iterate through all registered 'all' hooks.
        // Assuming filters['all'] is structured as [priority => [callbackData1, callbackData2, ...]]
        // ksort is typically applied by apply_filters/do_action when filters are merged.
        // If _call_all_hook can be called directly without prior sorting, you might want to ksort here as well.
        // For now, mirroring the original's assumption that it's already sorted if needed.
        foreach ($this->filters['all'] as $priority => $callbacks) {
            foreach ($callbacks as $callbackData) {
                if ($callbackData['function'] !== null) {
                    // Call the function with all provided arguments.
                    // The 'all' hook typically receives the raw arguments of the original hook.
                    $callback = $callbackData['function'];
                    $callback(...$args);
                }
            }
        }
    }

    /**
     * Register a shortcode handler.
     */
    public function add_shortcode(string $tag, callable $func): bool
    {
        if (\is_callable($func)) {
            self::$shortcode_tags[$tag] = \Closure::fromCallable($func);

            return true;
        }

        return false;
    }

    /**
     * Remove a shortcode handler.
     */
    public function remove_shortcode(string $tag): bool
    {
        if (isset(self::$shortcode_tags[$tag])) {
            unset(self::$shortcode_tags[$tag]);

            return true;
        }

        return false;
    }

    /**
     * Remove all registered shortcode handlers.
     */
    public function remove_all_shortcodes(): bool
    {
        self::$shortcode_tags = [];

        return true;
    }

    /**
     * Determine whether a shortcode tag is registered.
     */
    public function shortcode_exists(string $tag): bool
    {
        return \array_key_exists($tag, self::$shortcode_tags);
    }

    /**
     * Determine whether the given content contains a shortcode tag.
     */
    public function has_shortcode(string $content, string $tag): bool
    {
        if (false === \strpos($content, '[')) {
            return false;
        }

        if ($this->shortcode_exists($tag)) {
            \preg_match_all('/' . $this->get_shortcode_regex() . '/s', $content, $matches, PREG_SET_ORDER);
            if (empty($matches)) {
                return false;
            }

            foreach ($matches as $shortcode) {
                if ($tag === $shortcode[2]) {
                    return true;
                }

                if (!empty($shortcode[5]) && $this->has_shortcode($shortcode[5], $tag)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Replace shortcode tags in content with their rendered output.
     */
    public function do_shortcode(string $content): string
    {
        if (empty(self::$shortcode_tags) || !\is_array(self::$shortcode_tags)) {
            return $content;
        }

        $pattern = $this->get_shortcode_regex();

        return \preg_replace_callback(
            "/$pattern/s",
            [
                $this,
                '_do_shortcode_tag',
            ],
            $content
        );
    }

    /**
     * Build the regular expression used to locate registered shortcodes.
     */
    public function get_shortcode_regex(): string
    {
        $tagnames = \array_keys(self::$shortcode_tags);
        $tagregexp = \implode('|', \array_map('preg_quote', $tagnames));

        // WARNING! Do not change this regex without changing __do_shortcode_tag() and __strip_shortcode_tag()
        // Also, see shortcode_unautop() and shortcode.js.
        return
            '\\[' // Opening bracket
            . '(\\[?)' // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "($tagregexp)" // 2: Shortcode name
            . '(?![\\w-])' // Not followed by word character or hyphen
            . '(' // 3: Unroll the loop: Inside the opening shortcode tag
            . '[^\\]\\/]*' // Not a closing bracket or forward slash
            . '(?:'
            . '\\/(?!\\])' // A forward slash not followed by a closing bracket
            . '[^\\]\\/]*' // Not a closing bracket or forward slash
            . ')*?'
            . ')'
            . '(?:'
            . '(\\/)' // 4: Self closing tag ...
            . '\\]' // ... and closing bracket
            . '|'
            . '\\]' // Closing bracket
            . '(?:'
            . '(' // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            . '[^\\[]*+' // Not an opening bracket
            . '(?:'
            . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            . '[^\\[]*+' // Not an opening bracket
            . ')*+'
            . ')'
            . '\\[\\/\\2\\]' // Closing shortcode tag
            . ')?'
            . ')'
            . '(\\]?)'; // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
    }

    /**
     * Render a single shortcode regex match.
     *
     * @param array<int, string> $m
     */
    private function _do_shortcode_tag(array $m): mixed
    {
        // allow [[foo]] syntax for escaping a tag
        if ($m[1] == '[' && $m[6] == ']') {
            return \substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = $this->shortcode_parse_atts($m[3]);
        $shortcode = self::$shortcode_tags[$tag];

        // enclosing tag - extra parameter
        if (isset($m[5])) {
            return $m[1] . $shortcode($attr, $m[5], $tag) . $m[6];
        }

        // self-closing tag
        return $m[1] . $shortcode($attr, null, $tag) . $m[6];
    }

    /**
     * Parse shortcode attributes from raw shortcode text.
     *
     * @return array<int|string, string>|string
     */
    public function shortcode_parse_atts(string $text): array|string
    {
        $atts = [];
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text = \preg_replace("/[\x{00a0}\x{200b}]+/u", ' ', $text);
        $matches = [];
        if (\preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                if (!empty($m[1])) {
                    $atts[\strtolower($m[1])] = \stripcslashes($m[2]);
                } elseif (!empty($m[3])) {
                    $atts[\strtolower($m[3])] = \stripcslashes($m[4]);
                } elseif (!empty($m[5])) {
                    $atts[\strtolower($m[5])] = \stripcslashes($m[6]);
                } elseif (isset($m[7]) && $m[7] !== '') {
                    $atts[] = \stripcslashes($m[7]);
                } elseif (isset($m[8])) {
                    $atts[] = \stripcslashes($m[8]);
                }
            }
        } else {
            $atts = \ltrim($text);
        }

        return $atts;
    }

    /**
     * Merge user-supplied shortcode attributes with defaults.
     *
     * @param array<string, mixed> $pairs
     * @param array<int|string, mixed> $atts
     * @return array<string, mixed>
     */
    public function shortcode_atts(array $pairs, array $atts, string $shortcode = ''): array
    {
        $atts = (array)$atts;
        $out = [];
        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $atts)) {
                $out[$name] = $atts[$name];
            } else {
                $out[$name] = $default;
            }
        }

        /**
         * Filter the resolved shortcode attributes.
         *
         * This hook is available only when `$shortcode` is provided.
         */
        if ($shortcode) {
            $out = $this->apply_filters(
                "shortcode_atts_{$shortcode}",
                $out,
                $pairs,
                $atts
            );
        }

        return $out;
    }

    /**
     * Remove shortcode tags from content.
     */
    public function strip_shortcodes(string $content): string
    {
        if (empty(self::$shortcode_tags) || !\is_array(self::$shortcode_tags)) {
            return $content;
        }

        $pattern = $this->get_shortcode_regex();

        return preg_replace_callback(
            "/$pattern/s",
            [
                $this,
                '_strip_shortcode_tag',
            ],
            $content
        );
    }

    /**
     * Remove a single shortcode regex match from content.
     *
     * @param array<int, string> $m
     */
    private function _strip_shortcode_tag(array $m): string
    {
        // allow [[foo]] syntax for escaping a tag
        if ($m[1] == '[' && $m[6] == ']') {
            return substr($m[0], 1, -1);
        }

        return $m[1] . $m[6];
    }

}
