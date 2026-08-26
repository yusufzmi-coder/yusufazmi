<?php

declare(strict_types=1);

use App\Enums\Gender;
use App\Imports\StudentImporter;
use App\Models\AcademicSession;
use App\Models\Family;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    // Each test gets its own disk. Without this, uploads pile up in real storage and
    // stagedUpload() would pick a file left behind by an earlier test.
    Storage::fake('local');

    $this->seed(RoleSeeder::class);
    AcademicSession::factory()->active()->create();
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
    $this->actingAs($this->user);
});

function csvFile(string $contents, string $name = 'pelajar.csv'): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'imp').'.csv';
    file_put_contents($path, $contents);

    return new UploadedFile($path, $name, 'text/csv', null, true);
}

/**
 * The preview step parks the upload on disk and hands its path to the page. Reading
 * it back from storage keeps these tests independent of how Inertia serialises props.
 *
 * @return array{path: string, nama_fail: string, mapping: array<string, string|null>}
 */
function stagedUpload(array $headers): array
{
    $path = collect(Storage::files('imports'))->sortDesc()->first();

    expect($path)->not->toBeNull();

    return [
        'path' => $path,
        'nama_fail' => 'pelajar.csv',
        'mapping' => app(StudentImporter::class)->guessMapping($headers),
    ];
}

it('guesses the column mapping from common Malay headers', function () {
    $mapping = app(StudentImporter::class)->guessMapping(['Kod', 'Nama', 'Jantina', 'Tahun', 'Keluarga']);

    expect($mapping['student_code'])->toBe('Kod')
        ->and($mapping['name'])->toBe('Nama')
        ->and($mapping['gender'])->toBe('Jantina')
        ->and($mapping['year_level'])->toBe('Tahun')
        ->and($mapping['family_name'])->toBe('Keluarga')
        ->and($mapping['phone'])->toBeNull();
});

it('previews a file without writing any students', function () {
    $csv = "Kod,Nama,Jantina,Tahun\nP-001,Ahmad,Lelaki,4\nP-002,Aminah,Perempuan,5\n";

    $this->post('/pelajar/import/pratonton', ['fail' => csvFile($csv)])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('pelajar/import')
            ->where('preview.jumlah_baris', 2)
            ->where('preview.sah', 2)
            ->has('preview.ralat', 0)
            ->etc()
        );

    expect(Student::count())->toBe(0);
});

it('reports bad rows with their line numbers instead of guessing', function () {
    $csv = "Kod,Nama,Jantina,Tahun\n"
        ."P-001,Ahmad,Lelaki,4\n"
        .",Tiada Kod,Lelaki,4\n"
        ."P-003,Salah Tahun,Lelaki,99\n";

    $this->post('/pelajar/import/pratonton', ['fail' => csvFile($csv)])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('preview.sah', 1)
            ->has('preview.ralat', 2)
            // Header is line 1, so the first data row is line 2.
            ->where('preview.ralat.0.baris', 3)
            ->where('preview.ralat.1.baris', 4)
            ->etc()
        );
});

it('accepts the many ways a sheet writes gender', function () {
    $csv = "Kod,Nama,Jantina,Tahun\n"
        ."P-1,A,Lelaki,4\nP-2,B,L,4\nP-3,C,Male,4\n"
        ."P-4,D,Perempuan,4\nP-5,E,P,4\nP-6,F,female,4\n";

    $this->post('/pelajar/import/pratonton', ['fail' => csvFile($csv)])
        ->assertInertia(fn (Assert $page) => $page->where('preview.sah', 6)->etc());
});

it('accepts behaviour written as a Malay label or a number', function () {
    $csv = "Kod,Nama,Jantina,Tahun,Tingkah Laku\n"
        ."P-1,A,L,4,Bermasalah\nP-2,B,L,4,3\nP-3,C,L,4,\n";

    $this->post('/pelajar/import/pratonton', ['fail' => csvFile($csv)])
        ->assertInertia(fn (Assert $page) => $page->where('preview.sah', 3)->etc());
});

it('catches a code duplicated inside the file itself', function () {
    $csv = "Kod,Nama,Jantina,Tahun\nP-001,Ahmad,L,4\nP-001,Kembar,L,4\n";

    $this->post('/pelajar/import/pratonton', ['fail' => csvFile($csv)])
        ->assertInertia(fn (Assert $page) => $page
            ->where('preview.sah', 1)
            ->has('preview.ralat', 1)
            ->etc()
        );
});

it('imports the valid rows and links siblings by family name', function () {
    $csv = "Kod,Nama,Jantina,Tahun,Keluarga\n"
        ."P-001,Ahmad,L,4,Keluarga Abdullah\n"
        ."P-002,Aminah,P,5,Keluarga Abdullah\n"
        ."P-003,Solo,L,3,\n";

    $this->post('/pelajar/import/pratonton', ['fail' => csvFile($csv)])->assertOk();

    $this->post('/pelajar/import', stagedUpload(['Kod', 'Nama', 'Jantina', 'Tahun', 'Keluarga']))
        ->assertRedirect(route('pelajar.index'));

    expect(Student::count())->toBe(3)
        ->and(Family::count())->toBe(1);

    $ahmad = Student::where('student_code', 'P-001')->first();
    $aminah = Student::where('student_code', 'P-002')->first();
    $solo = Student::where('student_code', 'P-003')->first();

    expect($ahmad->family_id)->toBe($aminah->family_id)
        ->and($solo->family_id)->toBeNull()
        ->and($ahmad->gender)->toBe(Gender::Lelaki)
        ->and($aminah->gender)->toBe(Gender::Perempuan);
});

it('updates an existing code rather than duplicating it', function () {
    Student::factory()->create([
        'student_code' => 'P-001',
        'name' => 'Nama Lama',
        'year_level' => 1,
    ]);

    $csv = "Kod,Nama,Jantina,Tahun\nP-001,Nama Betul,L,6\n";

    $this->post('/pelajar/import/pratonton', ['fail' => csvFile($csv)])->assertOk();
    $this->post('/pelajar/import', stagedUpload(['Kod', 'Nama', 'Jantina', 'Tahun']));

    expect(Student::count())->toBe(1)
        ->and(Student::first()->name)->toBe('Nama Betul')
        ->and(Student::first()->year_level)->toBe(6);
});

it('skips invalid rows on commit instead of aborting the whole import', function () {
    $csv = "Kod,Nama,Jantina,Tahun\nP-001,Baik,L,4\nP-002,Rosak,L,99\n";

    $this->post('/pelajar/import/pratonton', ['fail' => csvFile($csv)])->assertOk();

    $this->post('/pelajar/import', stagedUpload(['Kod', 'Nama', 'Jantina', 'Tahun']))
        ->assertSessionHas('success', fn (string $m): bool => str_contains($m, 'dilangkau'));

    expect(Student::count())->toBe(1);
});

it('deletes the uploaded file once the import is committed', function () {
    $csv = "Kod,Nama,Jantina,Tahun\nP-001,Ahmad,L,4\n";

    $this->post('/pelajar/import/pratonton', ['fail' => csvFile($csv)])->assertOk();

    $staged = stagedUpload(['Kod', 'Nama', 'Jantina', 'Tahun']);
    expect(Storage::exists($staged['path']))->toBeTrue();

    $this->post('/pelajar/import', $staged);

    expect(Storage::exists($staged['path']))->toBeFalse();
});

it('rejects a file that is not a spreadsheet', function () {
    $path = tempnam(sys_get_temp_dir(), 'bad').'.pdf';
    file_put_contents($path, '%PDF-1.4');

    $this->post('/pelajar/import/pratonton', [
        'fail' => new UploadedFile($path, 'nota.pdf', 'application/pdf', null, true),
    ])->assertSessionHasErrors('fail');
});
