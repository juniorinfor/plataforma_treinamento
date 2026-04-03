<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Authenticated routes
Route::middleware(['auth', 'company'])->group(function () {
    // Student
    Route::get('/dashboard', App\Livewire\Dashboard\StudentDashboard::class)->name('dashboard');
    Route::get('/courses', App\Livewire\Courses\CourseIndex::class)->name('courses.index');
    Route::get('/courses/{slug}', App\Livewire\Courses\CourseShow::class)->name('courses.show');
    Route::get('/progress-map', App\Livewire\Learning\ProgressMap::class)->name('progress-map');
    Route::get('/lesson/{id}', App\Livewire\Learning\LessonViewer::class)->name('lesson.view');
    Route::get('/quiz/{id}', App\Livewire\Learning\QuizPlayer::class)->name('quiz.play');

    // Gamification
    Route::get('/leaderboard', App\Livewire\Gamification\LeaderboardPage::class)->name('leaderboard');
    Route::get('/badges', App\Livewire\Gamification\BadgesPage::class)->name('badges');
    Route::get('/challenges', App\Livewire\Gamification\ChallengesPage::class)->name('challenges');
    Route::get('/certificates', App\Livewire\Gamification\CertificatesPage::class)->name('certificates');

    // Admin
    Route::middleware('role:company_admin,manager')->prefix('admin')->group(function () {
        Route::get('/', App\Livewire\Admin\AdminDashboard::class)->name('admin.dashboard');
        Route::get('/courses', App\Livewire\Admin\CourseManagement::class)->name('admin.courses');
        Route::get('/users', App\Livewire\Admin\UserManagement::class)->name('admin.users');
        Route::get('/reports', App\Livewire\Admin\ReportsPage::class)->name('admin.reports');
    });

    // SaaS
    Route::get('/plans', App\Livewire\Saas\PlansPage::class)->name('plans');
});
