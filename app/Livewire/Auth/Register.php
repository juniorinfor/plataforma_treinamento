<?php

namespace App\Livewire\Auth;

use App\Enums\SubscriptionStatus;
use App\Models\Company;
use App\Models\Level;
use App\Models\Plan;
use App\Models\Streak;
use App\Models\User;
use App\Models\UserPoints;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    #[Rule('required|min:3')]
    public string $empresa = '';

    #[Rule('required|min:3')]
    public string $nome = '';

    #[Rule('required|email|unique:users,email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $senha = '';

    #[Rule('required|same:senha')]
    public string $senha_confirmation = '';

    public function register(): void
    {
        $this->validate();

        DB::transaction(function () {
            $plan = Plan::where('slug', 'starter')->first();

            $company = Company::create([
                'name' => $this->empresa,
                'slug' => Str::slug($this->empresa) . '-' . Str::random(4),
                'plan_id' => $plan?->id,
                'subscription_status' => SubscriptionStatus::Trial->value,
                'trial_ends_at' => now()->addDays(14),
                'max_users' => $plan?->max_users ?? 25,
                'is_active' => true,
            ]);

            $user = User::create([
                'company_id' => $company->id,
                'name' => $this->nome,
                'email' => $this->email,
                'password' => bcrypt($this->senha),
                'role' => 'company_admin',
                'is_active' => true,
            ]);

            $novatoLevel = Level::where('level_number', 1)->first();
            UserPoints::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'total_xp' => 0,
                'current_level_id' => $novatoLevel?->id,
            ]);
            Streak::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
            ]);

            Auth::login($user);
        });

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
