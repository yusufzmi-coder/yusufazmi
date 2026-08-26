<?php

declare(strict_types=1);

use App\Domain\Assignment\AssignmentSolver;
use App\Domain\Assignment\Enums\ItemAction;
use App\Domain\Assignment\ReasonRenderer;
use App\Domain\Assignment\Rules\FirmTeacherForBehaviourRule;
use App\Domain\Assignment\Rules\GenderBalanceRule;
use App\Domain\Assignment\Rules\SiblingPolicyRule;
use App\Domain\Assignment\RuleTypeRegistry;
use App\Enums\BehaviourLevel;
use App\Enums\Gender;
use Illuminate\Support\Facades\Lang;
use Tests\Support\SnapshotBuilder;

it('has a Malay translation for every reason code every rule can emit', function () {
    // Catches the classic regression: a rule type is added, the Malay string is not.
    $registry = app(RuleTypeRegistry::class);

    $emitted = [
        'padanan_tahun' => ['tahun_tidak_sepadan'],
        'kapasiti_bilik' => ['bilik_penuh'],
        'had_maksimum_kelas' => ['kelas_penuh'],
        'kelas_adik_beradik' => ['asing_dipenuhi', 'asing_gagal', 'bersama_dipenuhi', 'bersama_gagal'],
        'pelajar_bermasalah' => ['guru_tegas', 'guru_kurang_tegas', 'terlalu_ramai_bermasalah', 'tiada_guru'],
        'seimbangkan_jantina' => ['imbangan_baik', 'imbangan_terjejas'],
        'sistem' => ['kekal_di_kelas_asal', 'disematkan_oleh_admin', 'tiada_kelas_tahun'],
    ];

    foreach (array_keys($registry->all()) as $key) {
        expect(array_keys($emitted))->toContain($key);
    }

    foreach ($emitted as $ruleKey => $codes) {
        foreach ($codes as $code) {
            expect([$code => Lang::has("assignment.reasons.{$ruleKey}.{$code}", 'ms')])
                ->toBe([$code => true]);
        }
    }
});

it('renders a Malay sentence for every placement with no leaked translation keys', function () {
    $b = SnapshotBuilder::make()
        ->classes([
            '4 ALPHA' => ['capacity' => 4, 'firmness' => 5, 'teacher' => 'Cikgu Farah'],
            '4 BETA' => ['capacity' => 4, 'firmness' => 2, 'teacher' => 'Cikgu Amir'],
        ])
        ->family('Abdullah')
        ->student('Ahmad', family: 'Abdullah')
        ->student('Aminah', gender: Gender::Perempuan, family: 'Abdullah')
        ->student('Danish', behaviour: BehaviourLevel::Bermasalah)
        ->rule(SiblingPolicyRule::key(), weight: 80)
        ->rule(FirmTeacherForBehaviourRule::key(), weight: 90)
        ->rule(GenderBalanceRule::key(), weight: 40);

    $result = app(AssignmentSolver::class)->solve($b->build());
    $renderer = app(ReasonRenderer::class);

    foreach ($result->items as $item) {
        $sentence = $renderer->render(
            $item,
            $item->toClassId === null ? null : $b->className($item->toClassId),
            $item->fromClassId === null ? null : $b->className($item->fromClassId),
        );

        expect($sentence)->not->toBeEmpty()
            ->and($sentence)->not->toContain('assignment.reasons.')
            ->and($sentence)->not->toContain('assignment.sentence.');
    }
});

it('names the firm teacher in the reason for a problem student', function () {
    $b = SnapshotBuilder::make()
        ->classes([
            '4 ALPHA' => ['capacity' => 4, 'firmness' => 5, 'teacher' => 'Cikgu Farah'],
            '4 BETA' => ['capacity' => 4, 'firmness' => 1, 'teacher' => 'Cikgu Amir'],
        ])
        ->student('Danish', behaviour: BehaviourLevel::Bermasalah)
        ->rule(FirmTeacherForBehaviourRule::key(), weight: 100);

    $result = app(AssignmentSolver::class)->solve($b->build());
    $sentence = app(ReasonRenderer::class)->render($result->items[0], '4 ALPHA');

    expect($sentence)
        ->toContain('Diletakkan dalam 4 ALPHA')
        ->toContain('Cikgu Farah')
        ->toContain('pelajar bermasalah');
});

it('reports the trade-off when a soft rule has to be broken', function () {
    // Three siblings, one class with exactly three seats, policy "apart". They all
    // get placed -- and the sentence says so out loud rather than quietly omitting it.
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 3]])
        ->family('Abdullah')
        ->student('Ahmad', family: 'Abdullah')
        ->student('Aminah', gender: Gender::Perempuan, family: 'Abdullah')
        ->student('Amir', family: 'Abdullah')
        ->rule(SiblingPolicyRule::key(), weight: 100, config: ['default_policy' => 'apart']);

    $result = app(AssignmentSolver::class)->solve($b->build());
    $renderer = app(ReasonRenderer::class);

    $sentences = array_map(
        fn ($item) => $renderer->render($item, '4 ALPHA'),
        $result->items,
    );

    expect(implode(' | ', $sentences))->toContain('terpaksa sekelas dengan adik-beradik');
});

it('explains why an unplaceable student could not be placed', function () {
    $b = SnapshotBuilder::make()
        ->classes(['4 ALPHA' => ['capacity' => 1]])
        ->student('Ali')->student('Abu');

    $result = app(AssignmentSolver::class)->solve($b->build());
    $item = collect($result->items)->firstWhere('action', ItemAction::Unplaceable);

    $sentence = app(ReasonRenderer::class)->render($item, null);

    expect($sentence)
        ->toContain('Tidak dapat ditempatkan')
        ->toContain('penuh');
});
