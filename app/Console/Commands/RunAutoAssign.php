<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Assignment\Enums\SolveMode;
use App\Domain\Assignment\Services\CommitAssignment;
use App\Domain\Assignment\Services\RunAssignment;
use App\Models\AcademicSession;
use App\Models\User;
use Illuminate\Console\Command;

class RunAutoAssign extends Command
{
    protected $signature = 'assign:run
        {--mode=fill_only : fill_only atau rebalance}
        {--commit : Terus sahkan cadangan (lalai: draf sahaja)}
        {--limit=5 : Bilangan sebab contoh untuk dipaparkan}';

    protected $description = 'Jalankan auto assign untuk sesi aktif dan papar cadangan penempatan.';

    public function handle(RunAssignment $run, CommitAssignment $commit): int
    {
        $mode = SolveMode::tryFrom((string) $this->option('mode'));

        if ($mode === null) {
            $this->error('Mode tidak sah. Guna fill_only atau rebalance.');

            return self::FAILURE;
        }

        $session = AcademicSession::active();
        $user = User::query()->orderBy('id')->firstOrFail();

        $this->info("Sesi {$session->name} · mod {$mode->value}");

        $draft = $run($session->id, $mode, $user->id);
        $stats = $draft->stats;

        $this->table(
            ['Ditempatkan', 'Dipindah', 'Kekal', 'Disemat', 'Tidak dapat ditempatkan', 'Pelanggaran', 'Masa (ms)'],
            [[
                $stats['place'] ?? 0,
                $stats['move'] ?? 0,
                $stats['keep'] ?? 0,
                $stats['pinned'] ?? 0,
                $stats['unplaceable'] ?? 0,
                $stats['violations'] ?? 0,
                $draft->duration_ms,
            ]],
        );

        $samples = $draft->items()
            ->whereIn('action', ['place', 'move'])
            ->with('student:id,name')
            ->limit((int) $this->option('limit'))
            ->get();

        foreach ($samples as $item) {
            $this->line("  <fg=cyan>{$item->student->name}</> — {$item->reason_text}");
        }

        foreach ($draft->items()->where('action', 'unplaceable')->with('student:id,name')->limit(5)->get() as $item) {
            $this->line("  <fg=red>{$item->student->name}</> — {$item->reason_text}");
        }

        if (! $this->option('commit')) {
            $this->comment("Draf #{$draft->id} disimpan. Guna --commit untuk mengesahkan.");

            return self::SUCCESS;
        }

        $result = $commit($draft, $user->id);
        $this->info("Disahkan: {$result['applied']} penempatan, {$result['skipped']} dilangkau.");

        return self::SUCCESS;
    }
}
