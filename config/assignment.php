<?php

declare(strict_types=1);
use App\Domain\Assignment\Rules\FirmTeacherForBehaviourRule;
use App\Domain\Assignment\Rules\GenderBalanceRule;
use App\Domain\Assignment\Rules\MaxClassSizeRule;
use App\Domain\Assignment\Rules\RoomCapacityRule;
use App\Domain\Assignment\Rules\SiblingPolicyRule;
use App\Domain\Assignment\Rules\YearLevelMatchRule;

return [
    /*
     | Rule types available to the engine. An explicit list, not a reflection scan:
     | what is registered is exactly what someone wrote down here.
     |
     | Adding a new rule type is four steps and no migration:
     |   1. implement HardConstraint or SoftPreference
     |   2. add the class below
     |   3. add Malay strings under lang/ms/assignment.php
     |   4. add a config form in the `ruleConfigForms` map on the frontend
     */
    'rule_types' => [
        YearLevelMatchRule::class,        // always on
        RoomCapacityRule::class,          // always on
        MaxClassSizeRule::class,
        SiblingPolicyRule::class,
        FirmTeacherForBehaviourRule::class,
        GenderBalanceRule::class,
    ],

    'solver' => [
        // Stickiness. Small enough that a genuinely better class still wins, large
        // enough that equal-scoring alternatives never shuffle an existing roster.
        'incumbent_bonus' => 0.25,

        'local_search' => [
            'max_passes' => 6,
            'time_budget_ms' => 1500,
            'epsilon' => 0.001,
        ],

        // Above this many students, run the solve on the queue instead of inline.
        'sync_max_students' => 800,

        // A preview older than this can no longer be committed.
        'draft_ttl_minutes' => 30,
    ],
];
