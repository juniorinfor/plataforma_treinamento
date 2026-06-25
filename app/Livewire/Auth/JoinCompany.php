<?php

namespace App\Livewire\Auth;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Streak;
use App\Models\User;
use App\Models\UserPoints;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Auto-cadastro de colaborador via link da empresa (token).
 * Página pública (guest): o colaborador entra como `employee` na empresa.
 */
#[Layout('components.layouts.auth')]
#[Title('Cadastro de colaborador')]
class JoinCompany extends Component
{
    public string $token = '';
    public ?Company $company = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->company = Company::where('invite_token', $token)
            ->where('allow_self_registration', true)
            ->where('is_active', true)
            ->first();
    }

    public function register()
    {
        // Link inválido / desativado
        if (!$this->company) {
            $this->addError('email', 'Este link de cadastro é inválido ou foi desativado.');
            return;
        }

        // Limite de colaboradores do plano
        if (!$this->company->hasCapacity()) {
            $this->addError('email', 'Esta empresa atingiu o limite de colaboradores. Fale com o gestor.');
            return;
        }

        $this->validate([
            'name'     => ['required', 'string', 'min:3', 'max:120'],
            'email'    => ['required', 'email', 'max:160', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique'       => 'Este e-mail já está cadastrado. Tente fazer login.',
            'password.confirmed' => 'A confirmação de senha não confere.',
        ]);

        $user = User::create([
            'company_id' => $this->company->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'password'   => Hash::make($this->password),
            'role'       => UserRole::Employee,
            'is_active'  => true,
        ]);

        // Inicializa gamificação (com company_id)
        UserPoints::create(['user_id' => $user->id, 'company_id' => $this->company->id]);
        Streak::create(['user_id' => $user->id, 'company_id' => $this->company->id, 'current_streak' => 0, 'longest_streak' => 0]);

        Auth::login($user);

        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.join-company');
    }
}
