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
    Route::get('/forgot-password', App\Livewire\Auth\ForgotPassword::class)->name('password.request');
    Route::get('/reset-password', App\Livewire\Auth\ResetPassword::class)->name('password.reset');
});

Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Authenticated routes
Route::middleware(['auth', 'company', 'subscription'])->group(function () {
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

    // Admin — Gestores (company_admin + manager) e Admin do Sistema
    Route::middleware('role:gestor')->prefix('admin')->group(function () {
        Route::get('/', App\Livewire\Admin\AdminDashboard::class)->name('admin.dashboard');
        Route::get('/courses', App\Livewire\Admin\CourseManagement::class)->name('admin.courses');
        Route::get('/users', App\Livewire\Admin\UserManagement::class)->name('admin.users');
        Route::get('/reports', App\Livewire\Admin\ReportsPage::class)->name('admin.reports');
        Route::get('/diagnostics', App\Livewire\Admin\AdminDiagnostics::class)->name('admin.diagnostics');
    });

    // Plataforma — exclusivo platform_admin (criação e gestão de ferramentas)
    Route::middleware('role:platform_admin')->prefix('platform')->name('platform.')->group(function () {
        Route::prefix('diagnostics')->name('diagnostics.')->group(function () {
            Route::get('/', App\Livewire\Platform\Diagnostics\ToolIndex::class)->name('index');
            Route::get('/create', App\Livewire\Platform\Diagnostics\ToolForm::class)->name('create');
            Route::get('/{tool}/edit', App\Livewire\Platform\Diagnostics\ToolForm::class)->name('edit');
            Route::get('/{tool}/questions', App\Livewire\Platform\Diagnostics\QuestionManager::class)->name('questions');

            // Fila de revisão de relatórios + editor
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', App\Livewire\Platform\Diagnostics\ReportQueue::class)->name('index');
                Route::get('/{report}/edit', App\Livewire\Platform\Diagnostics\ReportEditor::class)->name('edit');
                Route::get('/{report}/pdf', [App\Http\Controllers\DiagnosticReportPdfController::class, 'downloadReport'])->name('pdf');
            });
        });
    });

    // Diagnósticos
    Route::prefix('diagnostics')->name('diagnostics.')->group(function () {
        Route::get('/', App\Livewire\Diagnostics\DiagnosticIndex::class)->name('index');
        Route::get('/responder/{assessment}', App\Livewire\Diagnostics\DiagnosticTake::class)->name('take');
        Route::get('/resultado/{assessment}', App\Livewire\Diagnostics\DiagnosticResult::class)->name('result');
        Route::get('/plano/{assessment}', App\Livewire\Diagnostics\ActionPlan::class)->name('action-plan');
        Route::get('/resultado/{assessment}/pdf', [App\Http\Controllers\DiagnosticReportPdfController::class, 'downloadAssessment'])->name('result.pdf');
        // Painel de resultados (gestor: empresa; admin: todas as empresas)
        Route::get('/{tool}/painel', App\Livewire\Diagnostics\DiagnosticPanel::class)->name('panel');
    });

    // Comunidade
    Route::get('/forum', App\Livewire\Forum\ForumIndex::class)->name('forum');

    // Biblioteca
    Route::get('/library', App\Livewire\Library\LibraryIndex::class)->name('library');

    // SaaS
    Route::get('/plans', App\Livewire\Saas\PlansPage::class)->name('plans');
    Route::get('/billing', App\Livewire\Saas\BillingPage::class)->name('billing');
});

// Asaas Webhook (sem auth — Asaas chama este endpoint)
Route::post('/webhooks/asaas', [App\Http\Controllers\AsaasWebhookController::class, 'handle'])
    ->name('webhooks.asaas');

// Cursos Personalizados (acesso público para demonstração)
Route::get('/curso/onboarding-levemente', function () {
    return view('cursos.levemente.onboarding');
})->name('curso.levemente.onboarding');

Route::get('/curso/levemente-guia-completo', function () {
    return view('cursos.levemente.guia-completo');
})->name('curso.levemente.guia-completo');
