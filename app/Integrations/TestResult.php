<?php

declare(strict_types=1);

namespace App\Integrations;

final readonly class TestResult
{
    private function __construct(
        public bool $ok,
        public string $message,
    ) {}

    public static function ok(string $message): self
    {
        return new self(true, $message);
    }

    public static function failed(string $message): self
    {
        return new self(false, $message);
    }
}
