<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Mail\UserInviteMail;
use App\Models\Streak;
use App\Models\User;
use App\Models\UserPoints;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Colaboradores')]
class UserManagement extends Component
{
    use WithPagination;

    // ── Filtros ───────────────────────────────────────────────────────
    public string $search     = '';
    public string $filterDept = '';
    public string $filterRole = '';

    // ── Modal de convite ──────────────────────────────────────────────
    public bool   $showInvite   = false;
    public string $inviteName   = '';
    public string $inviteEmail  = '';
    public string $inviteRole   = 'employee';
    public string $inviteDept   = '';
    public bool   $sendEmail    = true;

    // ── Exibição da senha temporária ──────────────────────────────────
    public bool   $showCreated      = false;
    public string $createdName      = '';
    public string $createdEmail     = '';
    public string $createdPassword  = '';

    // ── Reset de filtros ao paginar ───────────────────────────────────
    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingFilterDept(): void { $this->resetPage(); }
    public function updatingFilterRole(): void { $this->resetPage(); }

    // ── Helpers de role ───────────────────────────────────────────────
    public function roleOptions(): array
    {
        return UserRole::companyRoles();
    }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function departments(): \Illuminate\Support\Collection
    {
        return \App\Models\Department::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function company(): ?\App\Models\Company
    {
        return auth()->user()->company;
    }

    #[Computed]
    public function users()
    {
        $cid = auth()->user()->company_id;

        return User::where('company_id', $cid)
            ->when($this->search, fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterDept, fn ($q) =>
                $q->whereHas('departments', fn ($q2) =>
                    $q2->where('departments.id', $this->filterDept)
                )
            )
            ->when($this->filterRole, fn ($q) =>
                $q->where('role', $this->filterRole)
            )
            ->with([
                'points:user_id,total_xp',
                'points.currentLevel:id,level_number',
                'enrollments:id,user_id,completed_at',
                'latestDiagnosticAssessment.tool:id,name,code,color',
            ])
            ->orderBy('name')
            ->paginate(15);
    }

    // ── Ações ─────────────────────────────────────────────────────────

    public function openInvite(): void
    {
        $this->resetInviteForm();
        $this->showInvite = true;
    }

    // ── Link de auto-cadastro ─────────────────────────────────────────

    public function toggleSelfRegistration(): void
    {
        $company = auth()->user()->company;
        if (!$company) {
            return;
        }

        $enabled = !$company->allow_self_registration;

        // Gera o token na primeira ativação
        if ($enabled && !$company->invite_token) {
            $company->invite_token = Str::random(48);
        }

        $company->allow_self_registration = $enabled;
        $company->save();

        unset($this->company);
    }

    public function regenerateLink(): void
    {
        $company = auth()->user()->company;
        if (!$company) {
            return;
        }

        $company->update(['invite_token' => Str::random(48)]);
        unset($this->company);

        session()->flash('status', 'Link de cadastro regenerado. O link anterior deixou de funcionar.');
    }

    public function createUser(): void
    {
        $this->validate([
            'inviteName'  => 'required|min:3|max:120',
            'inviteEmail' => 'required|email|unique:users,email',
            'inviteRole'  => 'required|in:employee,company_admin,manager',
        ], [
            'inviteEmail.unique' => 'Este e-mail já está cadastrado na plataforma.',
        ]);

        $company   = auth()->user()->company;
        $tempPass  = Str::random(10);

        $user = User::create([
            'company_id' => $company->id,
            'name'       => $this->inviteName,
            'email'      => $this->inviteEmail,
            'password'   => Hash::make($tempPass),
            'role'       => UserRole::from($this->inviteRole),
            'is_active'  => true,
        ]);

        // Vincula ao departamento se selecionado
        if ($this->inviteDept) {
            $user->departments()->sync([$this->inviteDept]);
        }

        // Inicializa gamificação
        UserPoints::create(['user_id' => $user->id, 'company_id' => $company->id]);
        Streak::create(['user_id' => $user->id, 'company_id' => $company->id, 'current_streak' => 0, 'longest_streak' => 0]);

        // Envia e-mail de boas-vindas (silencia erro se SMTP não configurado)
        if ($this->sendEmail) {
            try {
                Mail::to($user->email)->send(new UserInviteMail($user, $tempPass, $company->name));
            } catch (\Throwable) {
                // SMTP não configurado — mostra a senha na tela
            }
        }

        unset($this->users);

        $this->showInvite      = false;
        $this->showCreated     = true;
        $this->createdName     = $user->name;
        $this->createdEmail    = $user->email;
        $this->createdPassword = $tempPass;

        $this->resetInviteForm();
    }

    public function toggleActive(int $userId): void
    {
        $user = User::where('company_id', auth()->user()->company_id)
            ->findOrFail($userId);

        // Não pode desativar a si mesmo
        if ($user->id === auth()->id()) {
            return;
        }

        $user->update(['is_active' => !$user->is_active]);
        unset($this->users);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function resetInviteForm(): void
    {
        $this->inviteName  = '';
        $this->inviteEmail = '';
        $this->inviteRole  = 'employee';
        $this->inviteDept  = '';
        $this->sendEmail   = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.user-management');
    }
}
