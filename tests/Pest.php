<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature');

// Feature tests hit a real PostgreSQL database: the schema leans on partial
// unique indexes, generated columns and advisory locks, none of which SQLite has.
pest()->use(RefreshDatabase::class)->in('Feature');

// Unit tests for the assignment engine are pure — they build Snapshot objects by
// hand and never touch the database, which is what keeps them millisecond-fast.

// Engine unit tests need the container (the rule registry is resolved from it) but
// never a database connection.
pest()->extend(TestCase::class)->in('Unit');
