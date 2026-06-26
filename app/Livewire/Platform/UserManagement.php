<?php

namespace App\Livewire\Platform;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Streak;
use App\Models\User;
use App\Models\UserPoints;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Usuários')]
class UserManagement extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $filterCompany = '';
    public string $filterRole    = '';

    // Modal de criação
    public bool   $showCreate   = false;
    public string $createName   = '';
    public string $createEmail  = '';
    public string $createRole   = 'employee';
    public ?int   $createCompany = null;

    // Exibição de senha criada
    public bool   $showCreated     = false;
    public string $createdName     = '';
    public string $createdEmail    = '';
    public string $createdPassword = '';

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingFilterCompany(): void { $this->resetPage(); }
    public function updatingFilterRole(): void    { $this->resetPage(); }

    #[Computed]
    public function companies()
    {
        return Company::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function users()
    {
        return User::with(['company:id,name'])
            ->withCount('enrollments')
            ->when($this->search, fn ($q) =>
                $q->where(fn ($q2) =>
                    $q2->where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterCompany, fn ($q) =>
                $q->where('company_id', $this->filterCompany)
            )
            ->when($this->filterRole, fn ($q) =>
                $q->where('role', $this->filterRole)
            )
            ->orderBy('name')
            ->paginate(20);
    }

    public function openCreate(): void
    {
        $this->reset('createName', 'createEmail', 'createRole', 'createCompany', 'showCreated');
        $this->showCreate = true;
    }

    public function createUser(): void
    {
        $this->validate([
            'createName'    => 'required|min:3|max:120',
            'createEmail'   => 'required|email|unique:users,email',
            'createRole'    => 'required|in:employee,company_admin,manager,platform_admin',
            'createCompany' => 'nullable|exists:companies,id',
        ]);

        $tempPass = Str::random(10);

        $user = User::create([
            'company_id' => $this->createCompany ?: null,
            'name'       => $this->createName,
            'email'      => $this->createEmail,
            'password'   => Hash::make($tempPass),
            'role'       => UserRole::from($this->createRole),
            'is_active'  => true,
        ]);

        // Gamificação (só se vinculado a empresa)
        if ($this->createCompany) {
            UserPoints::create(['user_id' => $user->id, 'company_id' => $this->createCompany]);
            Streak::create(['user_id' => $user->id, 'company_id' => $this->createCompany, 'current_streak' => 0, 'longest_streak' => 0]);
        }

        unset($this->users);
        $this->showCreate     = false;
        $this->showCreated    = true;
        $this->createdName    = $user->name;
        $this->createdEmail   = $user->email;
        $this->createdPassword = $tempPass;
    }

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->id === auth()->id()) {
            return;
        }
        $user->update(['is_active' => !$user->is_active]);
        unset($this->users);
    }

    public function render()
    {
        return view('livewire.platform.user-management');
    }
}
