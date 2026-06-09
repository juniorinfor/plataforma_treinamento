<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Challenge;
use App\Models\Level;
use Illuminate\Database\Seeder;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLevels();
        $this->seedBadges();
        $this->seedChallenges();
    }

    // ── Níveis 1-10 ───────────────────────────────────────────────────

    private function seedLevels(): void
    {
        $levels = [
            [1,  'Novato',          0,      99,     '#9CA3AF'],
            [2,  'Aprendiz',        100,    299,    '#6EE7B7'],
            [3,  'Explorador',      300,    599,    '#34D399'],
            [4,  'Praticante',      600,    999,    '#10B981'],
            [5,  'Competente',      1000,   1499,   '#3B82F6'],
            [6,  'Especialista',    1500,   2199,   '#6366F1'],
            [7,  'Expert',          2200,   2999,   '#8B5CF6'],
            [8,  'Mestre',          3000,   4199,   '#EC4899'],
            [9,  'Grão-Mestre',     4200,   5999,   '#F59E0B'],
            [10, 'Lendário',        6000,   999999, '#EF4444'],
        ];

        foreach ($levels as [$num, $name, $min, $max, $color]) {
            Level::updateOrCreate(
                ['level_number' => $num],
                ['name' => $name, 'min_xp' => $min, 'max_xp' => $max, 'color' => $color]
            );
        }
    }

    // ── Badges da plataforma ─────────────────────────────────────────

    private function seedBadges(): void
    {
        $badges = [
            [
                'name'            => 'Primeira Aula',
                'slug'            => 'primeira-aula',
                'description'     => 'Você completou sua primeira aula. O início de uma grande jornada!',
                'category'        => 'learning',
                'criteria_type'   => 'lessons_total',
                'criteria_config' => ['count' => 1],
                'xp_reward'       => 10,
                'rarity'          => 'common',
                'color'           => '#10B981',
            ],
            [
                'name'            => 'Maratonista',
                'slug'            => 'maratonista',
                'description'     => 'Completou 10 aulas. A consistência é o seu superpoder.',
                'category'        => 'learning',
                'criteria_type'   => 'lessons_total',
                'criteria_config' => ['count' => 10],
                'xp_reward'       => 25,
                'rarity'          => 'uncommon',
                'color'           => '#3B82F6',
            ],
            [
                'name'            => 'Mestre do Conhecimento',
                'slug'            => 'mestre-do-conhecimento',
                'description'     => 'Completou 50 aulas. Um verdadeiro mestre!',
                'category'        => 'learning',
                'criteria_type'   => 'lessons_total',
                'criteria_config' => ['count' => 50],
                'xp_reward'       => 100,
                'rarity'          => 'rare',
                'color'           => '#6366F1',
            ],
            [
                'name'            => 'Sequência de 7 Dias',
                'slug'            => 'streak-7',
                'description'     => 'Estudou 7 dias seguidos. Disciplina em ação!',
                'category'        => 'streak',
                'criteria_type'   => 'streak_days',
                'criteria_config' => ['count' => 7],
                'xp_reward'       => 50,
                'rarity'          => 'uncommon',
                'color'           => '#F59E0B',
            ],
            [
                'name'            => 'Implacável',
                'slug'            => 'streak-30',
                'description'     => '30 dias consecutivos de estudo. Lendário!',
                'category'        => 'streak',
                'criteria_type'   => 'streak_days',
                'criteria_config' => ['count' => 30],
                'xp_reward'       => 200,
                'rarity'          => 'epic',
                'color'           => '#EF4444',
            ],
            [
                'name'            => 'Diagnóstico Revelado',
                'slug'            => 'diagnostico-revelado',
                'description'     => 'Completou seu primeiro diagnóstico organizacional. Autoconhecimento é poder!',
                'category'        => 'diagnostic',
                'criteria_type'   => 'diagnostic_complete',
                'criteria_config' => ['count' => 1],
                'xp_reward'       => 75,
                'rarity'          => 'rare',
                'color'           => '#8B5CF6',
            ],
            [
                'name'            => 'Estudante Dedicado',
                'slug'            => 'estudante-dedicado',
                'description'     => 'Concluiu seu primeiro curso completo. Da primeira aula ao certificado!',
                'category'        => 'learning',
                'criteria_type'   => 'course_complete',
                'criteria_config' => ['count' => 1],
                'xp_reward'       => 50,
                'rarity'          => 'uncommon',
                'color'           => '#10B981',
            ],
            [
                'name'            => 'Mil Pontos',
                'slug'            => 'mil-pontos',
                'description'     => 'Acumulou 1.000 XP. Você está evoluindo!',
                'category'        => 'xp',
                'criteria_type'   => 'xp_total',
                'criteria_config' => ['count' => 1000],
                'xp_reward'       => 50,
                'rarity'          => 'rare',
                'color'           => '#F59E0B',
            ],
        ];

        foreach ($badges as $data) {
            Badge::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['company_id' => null, 'is_active' => true])
            );
        }
    }

    // ── Desafios da plataforma ────────────────────────────────────────

    private function seedChallenges(): void
    {
        $challenges = [
            // ── Diários ─────────────────────────────────────────────
            [
                'title'           => 'Conclua 3 aulas hoje',
                'description'     => 'Siga o ritmo e complete 3 aulas neste dia.',
                'type'            => 'daily',
                'criteria_type'   => 'lesson_today',
                'criteria_config' => ['count' => 3],
                'xp_reward'       => 30,
            ],
            [
                'title'           => 'Conclua 1 aula hoje',
                'description'     => 'Qualquer aula conta — o importante é não parar!',
                'type'            => 'daily',
                'criteria_type'   => 'lesson_today',
                'criteria_config' => ['count' => 1],
                'xp_reward'       => 15,
            ],
            [
                'title'           => 'Mantenha sua sequência',
                'description'     => 'Estude hoje para não quebrar seu streak.',
                'type'            => 'daily',
                'criteria_type'   => 'streak_min',
                'criteria_config' => ['count' => 1],
                'xp_reward'       => 10,
            ],

            // ── Semanais ─────────────────────────────────────────────
            [
                'title'           => 'Conclua 15 aulas esta semana',
                'description'     => 'Três aulas por dia — você consegue!',
                'type'            => 'weekly',
                'criteria_type'   => 'lesson_week',
                'criteria_config' => ['count' => 15],
                'xp_reward'       => 100,
            ],
            [
                'title'           => 'Complete um diagnóstico',
                'description'     => 'Realize ou reveja um diagnóstico organizacional esta semana.',
                'type'            => 'weekly',
                'criteria_type'   => 'diagnostic_complete',
                'criteria_config' => ['count' => 1],
                'xp_reward'       => 75,
            ],
            [
                'title'           => 'Streak de 5 dias',
                'description'     => 'Estude por 5 dias consecutivos esta semana.',
                'type'            => 'weekly',
                'criteria_type'   => 'streak_min',
                'criteria_config' => ['count' => 5],
                'xp_reward'       => 50,
            ],
        ];

        foreach ($challenges as $data) {
            Challenge::updateOrCreate(
                ['title' => $data['title'], 'company_id' => null],
                array_merge($data, [
                    'company_id' => null,
                    'badge_id'   => null,
                    'is_active'  => true,
                    'starts_at'  => null,
                    'ends_at'    => null,
                ])
            );
        }
    }
}
