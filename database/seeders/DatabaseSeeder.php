<?php

namespace Database\Seeders;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\Badge;
use App\Models\Category;
use App\Models\Company;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\Level;
use App\Models\Module;
use App\Models\Plan;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\Streak;
use App\Models\User;
use App\Models\UserPoints;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Plans
        $starter = Plan::create([
            'name' => 'Starter', 'slug' => 'starter',
            'description' => 'Ideal para pequenas equipes',
            'price_monthly' => 99, 'price_yearly' => 990,
            'max_users' => 25, 'max_courses' => 10, 'max_storage_mb' => 1024,
            'features' => ['courses', 'quizzes', 'certificates', 'basic_reports'],
            'sort_order' => 1,
        ]);
        $pro = Plan::create([
            'name' => 'Professional', 'slug' => 'professional',
            'description' => 'Para empresas em crescimento',
            'price_monthly' => 299, 'price_yearly' => 2990,
            'max_users' => 100, 'max_courses' => 999, 'max_storage_mb' => 10240,
            'features' => ['courses', 'quizzes', 'certificates', 'advanced_reports', 'gamification', 'custom_branding'],
            'sort_order' => 2,
        ]);
        Plan::create([
            'name' => 'Enterprise', 'slug' => 'enterprise',
            'description' => 'Solução completa sem limites',
            'price_monthly' => 699, 'price_yearly' => 6990,
            'max_users' => 99999, 'max_courses' => 99999, 'max_storage_mb' => 102400,
            'features' => ['courses', 'quizzes', 'certificates', 'advanced_reports', 'gamification', 'custom_branding', 'api_access', 'priority_support', 'sso'],
            'sort_order' => 3,
        ]);

        // Levels
        $levels = [
            ['name' => 'Novato', 'level_number' => 1, 'min_xp' => 0, 'max_xp' => 99, 'color' => '#9CA3AF'],
            ['name' => 'Aprendiz', 'level_number' => 2, 'min_xp' => 100, 'max_xp' => 299, 'color' => '#10B981'],
            ['name' => 'Estudante', 'level_number' => 3, 'min_xp' => 300, 'max_xp' => 599, 'color' => '#3B82F6'],
            ['name' => 'Praticante', 'level_number' => 4, 'min_xp' => 600, 'max_xp' => 999, 'color' => '#6366F1'],
            ['name' => 'Competente', 'level_number' => 5, 'min_xp' => 1000, 'max_xp' => 1499, 'color' => '#8B5CF6'],
            ['name' => 'Habilidoso', 'level_number' => 6, 'min_xp' => 1500, 'max_xp' => 2099, 'color' => '#A855F7'],
            ['name' => 'Experiente', 'level_number' => 7, 'min_xp' => 2100, 'max_xp' => 2999, 'color' => '#EC4899'],
            ['name' => 'Especialista', 'level_number' => 8, 'min_xp' => 3000, 'max_xp' => 4499, 'color' => '#F43F5E'],
            ['name' => 'Mestre', 'level_number' => 9, 'min_xp' => 4500, 'max_xp' => 6499, 'color' => '#EAB308'],
            ['name' => 'Lenda', 'level_number' => 10, 'min_xp' => 6500, 'max_xp' => null, 'color' => '#F59E0B'],
        ];
        foreach ($levels as $level) {
            Level::create($level);
        }
        $novatoLevel = Level::where('level_number', 1)->first();

        // Badges
        $badges = [
            ['name' => 'Primeiro Passo', 'slug' => 'primeiro-passo', 'description' => 'Complete sua primeira aula', 'category' => 'course', 'rarity' => 'common', 'xp_reward' => 5, 'color' => '#9CA3AF', 'criteria_type' => 'lessons_completed', 'criteria_config' => ['count' => 1]],
            ['name' => 'Estudante Dedicado', 'slug' => 'estudante-dedicado', 'description' => 'Complete 10 aulas', 'category' => 'course', 'rarity' => 'uncommon', 'xp_reward' => 15, 'color' => '#10B981', 'criteria_type' => 'lessons_completed', 'criteria_config' => ['count' => 10]],
            ['name' => 'Maratonista', 'slug' => 'maratonista', 'description' => 'Complete 5 cursos', 'category' => 'course', 'rarity' => 'rare', 'xp_reward' => 50, 'color' => '#3B82F6', 'criteria_type' => 'courses_completed', 'criteria_config' => ['count' => 5]],
            ['name' => 'Mente Brilhante', 'slug' => 'mente-brilhante', 'description' => '3 quizzes perfeitos seguidos', 'category' => 'quiz', 'rarity' => 'epic', 'xp_reward' => 75, 'color' => '#8B5CF6', 'criteria_type' => 'perfect_quizzes_streak', 'criteria_config' => ['count' => 3]],
            ['name' => 'Inabalável', 'slug' => 'inabalavel', 'description' => 'Passe um quiz sem perder corações', 'category' => 'quiz', 'rarity' => 'rare', 'xp_reward' => 30, 'color' => '#3B82F6', 'criteria_type' => 'quiz_full_hearts', 'criteria_config' => ['count' => 1]],
            ['name' => 'Chama Acesa', 'slug' => 'chama-acesa', 'description' => 'Mantenha uma sequência de 7 dias', 'category' => 'streak', 'rarity' => 'uncommon', 'xp_reward' => 20, 'color' => '#F97316', 'criteria_type' => 'streak_days', 'criteria_config' => ['count' => 7]],
            ['name' => 'Inferno', 'slug' => 'inferno', 'description' => 'Mantenha uma sequência de 30 dias', 'category' => 'streak', 'rarity' => 'legendary', 'xp_reward' => 100, 'color' => '#F59E0B', 'criteria_type' => 'streak_days', 'criteria_config' => ['count' => 30]],
            ['name' => 'Explorador', 'slug' => 'explorador', 'description' => 'Inscreva-se em 10 cursos', 'category' => 'course', 'rarity' => 'uncommon', 'xp_reward' => 15, 'color' => '#10B981', 'criteria_type' => 'enrollments_count', 'criteria_config' => ['count' => 10]],
        ];
        foreach ($badges as $badge) {
            Badge::create($badge);
        }

        // Demo Company
        $company = Company::create([
            'name' => 'TechCorp Brasil',
            'slug' => 'techcorp',
            'email' => 'admin@techcorp.com',
            'phone' => '(11) 99999-0000',
            'document' => '12.345.678/0001-90',
            'plan_id' => $pro->id,
            'subscription_status' => 'active',
            'max_users' => 100,
            'is_active' => true,
        ]);

        // Platform Admin
        User::create([
            'name' => 'Admin Plataforma',
            'email' => 'admin@trainup.com',
            'password' => bcrypt('password'),
            'role' => 'platform_admin',
            'is_active' => true,
        ]);

        // Company Admin
        $admin = User::create([
            'company_id' => $company->id,
            'name' => 'Carlos Silva',
            'email' => 'carlos@techcorp.com',
            'password' => bcrypt('password'),
            'role' => 'company_admin',
            'is_active' => true,
        ]);

        // Employees
        $employees = [];
        $names = [
            'Ana Oliveira', 'Bruno Santos', 'Camila Lima', 'Diego Ferreira',
            'Elena Costa', 'Felipe Souza', 'Gabriela Almeida', 'Hugo Ribeiro',
        ];
        foreach ($names as $i => $name) {
            $employees[] = User::create([
                'company_id' => $company->id,
                'name' => $name,
                'email' => Str::slug(explode(' ', $name)[0]) . '@techcorp.com',
                'password' => bcrypt('password'),
                'role' => 'employee',
                'is_active' => true,
            ]);
        }

        // Create user points for all company users
        foreach (array_merge([$admin], $employees) as $i => $user) {
            $xp = rand(0, 3500);
            $level = Level::where('min_xp', '<=', $xp)->orderBy('min_xp', 'desc')->first();
            UserPoints::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'total_xp' => $xp,
                'current_level_id' => $level->id,
                'weekly_xp' => rand(0, 200),
                'monthly_xp' => rand(0, 800),
            ]);
            Streak::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'current_streak' => rand(0, 15),
                'longest_streak' => rand(5, 30),
                'last_activity_date' => now()->subDays(rand(0, 2)),
            ]);
        }

        // Categories
        $catTech = Category::create(['company_id' => null, 'name' => 'Tecnologia', 'slug' => 'tecnologia', 'icon' => 'computer-desktop', 'color' => '#3B82F6']);
        $catSeg = Category::create(['company_id' => null, 'name' => 'Segurança', 'slug' => 'seguranca', 'icon' => 'shield-check', 'color' => '#EF4444']);
        $catLid = Category::create(['company_id' => null, 'name' => 'Liderança', 'slug' => 'lideranca', 'icon' => 'user-group', 'color' => '#8B5CF6']);
        Category::create(['company_id' => null, 'name' => 'Compliance', 'slug' => 'compliance', 'icon' => 'clipboard-document-check', 'color' => '#10B981']);
        Category::create(['company_id' => null, 'name' => 'Vendas', 'slug' => 'vendas', 'icon' => 'chart-bar', 'color' => '#F59E0B']);

        // Demo Courses
        $course1 = Course::create([
            'company_id' => null, 'created_by' => $admin->id,
            'title' => 'Segurança da Informação Básica',
            'slug' => 'seguranca-informacao-basica',
            'description' => 'Aprenda os fundamentos de segurança da informação, proteção de dados e boas práticas para manter sua empresa segura.',
            'short_description' => 'Fundamentos de segurança digital',
            'category_id' => $catSeg->id,
            'difficulty' => 'beginner', 'estimated_hours' => 4,
            'is_published' => true, 'is_platform_course' => true,
            'published_at' => now(), 'xp_reward' => 100,
        ]);

        $mod1 = Module::create(['course_id' => $course1->id, 'title' => 'Introdução à Segurança Digital', 'sort_order' => 1]);
        $mod2 = Module::create(['course_id' => $course1->id, 'title' => 'Senhas e Autenticação', 'sort_order' => 2]);
        $mod3 = Module::create(['course_id' => $course1->id, 'title' => 'Phishing e Engenharia Social', 'sort_order' => 3]);

        $lesson1 = Lesson::create(['module_id' => $mod1->id, 'title' => 'O que é Segurança da Informação?', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 10, 'xp_reward' => 10]);
        LessonContent::create(['lesson_id' => $lesson1->id, 'type' => 'heading', 'content' => 'O que é Segurança da Informação?', 'sort_order' => 1]);
        LessonContent::create(['lesson_id' => $lesson1->id, 'type' => 'text', 'content' => '<p>Segurança da informação é a prática de proteger informações contra acesso não autorizado, uso, divulgação, interrupção, modificação ou destruição. Em um mundo cada vez mais digital, proteger dados é fundamental para qualquer organização.</p><p>Os três pilares da segurança da informação são:</p><ul><li><strong>Confidencialidade</strong>: Garantir que informações sejam acessíveis apenas por pessoas autorizadas</li><li><strong>Integridade</strong>: Assegurar que os dados não sejam alterados de forma não autorizada</li><li><strong>Disponibilidade</strong>: Garantir que as informações estejam disponíveis quando necessário</li></ul>', 'sort_order' => 2]);

        Lesson::create(['module_id' => $mod1->id, 'title' => 'Ameaças Comuns no Ambiente Corporativo', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 15, 'xp_reward' => 10]);
        Lesson::create(['module_id' => $mod2->id, 'title' => 'Criando Senhas Fortes', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 10, 'xp_reward' => 10]);
        Lesson::create(['module_id' => $mod2->id, 'title' => 'Autenticação de Dois Fatores', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 10, 'xp_reward' => 10]);

        $quizLesson = Lesson::create(['module_id' => $mod2->id, 'title' => 'Quiz: Senhas e Autenticação', 'type' => 'quiz', 'sort_order' => 3, 'duration_minutes' => 5, 'xp_reward' => 25]);
        $quiz = Quiz::create(['lesson_id' => $quizLesson->id, 'title' => 'Quiz: Senhas e Autenticação', 'passing_score' => 70, 'hearts_enabled' => true, 'hearts_count' => 5, 'xp_reward' => 25]);

        $q1 = Question::create(['quiz_id' => $quiz->id, 'type' => 'multiple_choice', 'content' => 'Qual das alternativas abaixo é considerada uma senha forte?', 'explanation' => 'Senhas fortes combinam letras maiúsculas, minúsculas, números e caracteres especiais.', 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q1->id, 'content' => '123456', 'is_correct' => false, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q1->id, 'content' => 'senha123', 'is_correct' => false, 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $q1->id, 'content' => 'M@r1a#2024!Segura', 'is_correct' => true, 'sort_order' => 3]);
        QuestionOption::create(['question_id' => $q1->id, 'content' => 'qwerty', 'is_correct' => false, 'sort_order' => 4]);

        $q2 = Question::create(['quiz_id' => $quiz->id, 'type' => 'true_false', 'content' => 'A autenticação de dois fatores (2FA) adiciona uma camada extra de segurança além da senha.', 'explanation' => 'Correto! O 2FA exige um segundo fator de verificação, como um código no celular.', 'sort_order' => 2]);
        QuestionOption::create(['question_id' => $q2->id, 'content' => 'Verdadeiro', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q2->id, 'content' => 'Falso', 'is_correct' => false, 'sort_order' => 2]);

        Lesson::create(['module_id' => $mod3->id, 'title' => 'Identificando Emails de Phishing', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 15, 'xp_reward' => 10]);
        Lesson::create(['module_id' => $mod3->id, 'title' => 'Como se Proteger de Golpes', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 10, 'xp_reward' => 10]);

        // Course 2
        $course2 = Course::create([
            'company_id' => null, 'created_by' => $admin->id,
            'title' => 'Liderança e Gestão de Equipes',
            'slug' => 'lideranca-gestao-equipes',
            'description' => 'Desenvolva habilidades de liderança, comunicação e gestão para liderar equipes de alto desempenho.',
            'short_description' => 'Habilidades de liderança moderna',
            'category_id' => $catLid->id,
            'difficulty' => 'intermediate', 'estimated_hours' => 6,
            'is_published' => true, 'is_platform_course' => true,
            'published_at' => now(), 'xp_reward' => 150,
        ]);
        $mod4 = Module::create(['course_id' => $course2->id, 'title' => 'Fundamentos da Liderança', 'sort_order' => 1]);
        Module::create(['course_id' => $course2->id, 'title' => 'Comunicação Eficaz', 'sort_order' => 2]);
        Module::create(['course_id' => $course2->id, 'title' => 'Gestão de Conflitos', 'sort_order' => 3]);
        Lesson::create(['module_id' => $mod4->id, 'title' => 'O que faz um bom líder?', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 15, 'xp_reward' => 10]);
        Lesson::create(['module_id' => $mod4->id, 'title' => 'Estilos de Liderança', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 20, 'xp_reward' => 10]);

        // Course 3 - Company specific
        $course3 = Course::create([
            'company_id' => $company->id, 'created_by' => $admin->id,
            'title' => 'Onboarding TechCorp',
            'slug' => 'onboarding-techcorp',
            'description' => 'Conheça a TechCorp, nossa cultura, processos e ferramentas internas.',
            'short_description' => 'Integração de novos colaboradores',
            'category_id' => $catTech->id,
            'difficulty' => 'beginner', 'estimated_hours' => 2,
            'is_published' => true, 'is_mandatory' => true,
            'published_at' => now(), 'xp_reward' => 50,
        ]);
        $mod5 = Module::create(['course_id' => $course3->id, 'title' => 'Bem-vindo à TechCorp', 'sort_order' => 1]);
        Lesson::create(['module_id' => $mod5->id, 'title' => 'Nossa História', 'type' => 'text', 'sort_order' => 1, 'duration_minutes' => 10, 'xp_reward' => 10]);
        Lesson::create(['module_id' => $mod5->id, 'title' => 'Cultura e Valores', 'type' => 'text', 'sort_order' => 2, 'duration_minutes' => 10, 'xp_reward' => 10]);

        // Enrollments for demo
        foreach ($employees as $i => $emp) {
            Enrollment::create([
                'user_id' => $emp->id, 'course_id' => $course3->id,
                'progress_percentage' => rand(20, 100), 'status' => 'active',
            ]);
            if ($i < 5) {
                Enrollment::create([
                    'user_id' => $emp->id, 'course_id' => $course1->id,
                    'progress_percentage' => rand(0, 80), 'status' => 'active',
                ]);
            }
        }

        // Diagnostics module (Executive Mapping + demo). O IO provisório criado
        // aqui é substituído logo abaixo pelo AS SCORE® oficial.
        $this->call(DiagnosticSeeder::class);

        // Diagnóstico de Conformidade com a NR1 (3º banner)
        $this->call(Nr1DiagnosticSeeder::class);

        // AS SCORE® oficial — substitui o IO provisório por 5 índices + IOH + eNPS
        $this->call(AsScoreSeeder::class);

        // Gamification: níveis completos, badges e desafios reais
        $this->call(GamificationSeeder::class);

        // 3 cursos de soft skills da plataforma (conteúdo interativo com blocos)
        $this->call(SoftSkillsCoursesSeeder::class);
    }
}
