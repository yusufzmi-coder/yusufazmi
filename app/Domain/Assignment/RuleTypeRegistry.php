<?php

declare(strict_types=1);

namespace App\Domain\Assignment;

use App\Domain\Assignment\Contracts\RuleType;
use Illuminate\Contracts\Container\Container;

/**
 * Explicit list from config/assignment.php -- no reflection scanning, so what is
 * registered is exactly what someone wrote down.
 */
final class RuleTypeRegistry
{
    /** @var array<string, RuleType> */
    private array $types = [];

    /** @param list<class-string<RuleType>> $classes */
    public function __construct(array $classes, Container $container)
    {
        foreach ($classes as $class) {
            $this->types[$class::key()] = $container->make($class);
        }
    }

    public function get(string $key): RuleType
    {
        return $this->types[$key] ?? throw new UnknownRuleTypeException($key);
    }

    public function has(string $key): bool
    {
        return isset($this->types[$key]);
    }

    /** @return array<string, RuleType> */
    public function all(): array
    {
        return $this->types;
    }

    /**
     * Rule types the admin may create and toggle in the Peraturan panel.
     *
     * @return array<string, RuleType>
     */
    public function configurable(): array
    {
        return array_filter($this->types, static fn (RuleType $t): bool => $t->isConfigurable());
    }

    /**
     * Always-on physical constraints, enforced regardless of the rules table.
     *
     * @return array<string, RuleType>
     */
    public function alwaysOn(): array
    {
        return array_filter($this->types, static fn (RuleType $t): bool => ! $t->isConfigurable());
    }
}
