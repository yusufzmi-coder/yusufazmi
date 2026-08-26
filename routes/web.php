<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrolmentController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RuleController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Admin\TimetableController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('home');

// Invitation-only: the callback refuses any Google account without an existing user.
Route::get('auth/google/redirect', [GoogleController::class, 'redirect'])
    ->middleware('guest')->name('auth.google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'callback'])
    ->middleware('guest')->name('auth.google.callback');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('jadual', [TimetableController::class, 'index'])->name('jadual.index');
    Route::get('jadual/kelas/{class}', [TimetableController::class, 'show'])->name('jadual.kelas');

    Route::post('jadual/kelas/{class}/pelajar', [EnrolmentController::class, 'store'])->name('enrolment.store');
    Route::patch('enrolan/{enrolment}', [EnrolmentController::class, 'update'])->name('enrolment.update');
    Route::patch('enrolan/{enrolment}/semat', [EnrolmentController::class, 'pin'])->name('enrolment.pin');
    Route::delete('enrolan/{enrolment}', [EnrolmentController::class, 'destroy'])->name('enrolment.destroy');

    // Import routes come before the {student} wildcard so "import" is not read as an id.
    Route::get('pelajar/import', [StudentImportController::class, 'create'])->name('pelajar.import.create');
    Route::post('pelajar/import/pratonton', [StudentImportController::class, 'preview'])->name('pelajar.import.preview');
    Route::post('pelajar/import/peta', [StudentImportController::class, 'remap'])->name('pelajar.import.remap');
    Route::post('pelajar/import', [StudentImportController::class, 'store'])->name('pelajar.import.store');

    Route::resource('pelajar', StudentController::class)
        ->parameters(['pelajar' => 'student'])
        ->except(['show'])
        ->names('pelajar');

    Route::resource('guru', TeacherController::class)
        ->parameters(['guru' => 'teacher'])
        ->except(['show'])
        ->names('guru');

    Route::resource('bilik', RoomController::class)
        ->parameters(['bilik' => 'room'])
        ->except(['show'])
        ->names('bilik');

    Route::resource('kelas', SchoolClassController::class)
        ->parameters(['kelas' => 'class'])
        ->except(['show'])
        ->names('kelas');
    Route::get('statistik', [StatisticsController::class, 'index'])->name('statistik.index');

    Route::get('eksport/jadual.pdf', [ExportController::class, 'timetablePdf'])->name('eksport.jadual');
    Route::get('eksport/kelas/{class}.pdf', [ExportController::class, 'classRosterPdf'])->name('eksport.kelas');
    Route::get('eksport/pelajar.csv', [ExportController::class, 'studentsCsv'])->name('eksport.pelajar');
    Route::get('eksport/statistik.csv', [ExportController::class, 'statisticsCsv'])->name('eksport.statistik');
    Route::get('log-aktiviti', [ActivityLogController::class, 'index'])->name('log-aktiviti.index');

    Route::get('pengguna', [UserController::class, 'index'])->name('pengguna.index');
    Route::post('pengguna', [UserController::class, 'store'])->name('pengguna.store');
    Route::delete('pengguna/{user}', [UserController::class, 'destroy'])->name('pengguna.destroy');

    Route::get('peraturan', [RuleController::class, 'index'])->name('peraturan.index');
    Route::patch('peraturan/{rule}', [RuleController::class, 'update'])->name('peraturan.update');

    Route::get('auto-assign', [AssignmentController::class, 'index'])->name('auto-assign.index');
    Route::post('auto-assign', [AssignmentController::class, 'store'])->name('auto-assign.store');
    Route::get('auto-assign/{run}', [AssignmentController::class, 'show'])->name('auto-assign.show');
    Route::post('auto-assign/{run}/sahkan', [AssignmentController::class, 'commit'])->name('auto-assign.commit');
    Route::post('auto-assign/{run}/buat-asal', [AssignmentController::class, 'revert'])->name('auto-assign.revert');
    Route::delete('auto-assign/{run}', [AssignmentController::class, 'destroy'])->name('auto-assign.destroy');

    Route::get('integrasi', [IntegrationController::class, 'index'])->name('integrasi.index');
    Route::patch('integrasi/{key}', [IntegrationController::class, 'update'])->name('integrasi.update');
    Route::post('integrasi/{key}/uji', [IntegrationController::class, 'test'])->name('integrasi.test');
});

require __DIR__.'/settings.php';
