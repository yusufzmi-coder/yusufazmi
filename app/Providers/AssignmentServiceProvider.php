<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Assignment\AssignmentSolver;
use App\Domain\Assignment\RuleTypeRegistry;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AssignmentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RuleTypeRegistry::class, fn (Application $app): RuleTypeRegistry => new RuleTypeRegistry(
            config('assignment.rule_types', []),
            $app,
        ));

        $this->app->bind(AssignmentSolver::class, function (Application $app): AssignmentSolver {
            $search = config('assignment.solver.local_search');

            return new AssignmentSolver(
                registry: $app->make(RuleTypeRegistry::class),
                incumbentBonus: (float) config('assignment.solver.incumbent_bonus', 0.25),
                maxPasses: (int) ($search['max_passes'] ?? 6),
                timeBudgetMs: (int) ($search['time_budget_ms'] ?? 1500),
                epsilon: (float) ($search['epsilon'] ?? 0.001),
            );
        });
    }
}
