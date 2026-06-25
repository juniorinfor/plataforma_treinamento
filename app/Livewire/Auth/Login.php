<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:6')]
    public string $senha = '';

    public bool $lembrar = false;
    public string $erro = '';

    public function login(): void
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->senha], $this->lembrar)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                $this->erro = 'Sua conta está desativada. Entre em contato com o administrador.';
                return;
            }

            $user->update(['last_login_at' => now()]);
            session()->regenerate();

            $this->redirect(route($user->homeRoute()), navigate: true);
        } else {
            $this->erro = 'Email ou senha incorretos.';
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
