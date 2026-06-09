<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Nova senha')]
class ResetPassword extends Component
{
    #[Url]
    public string $token = '';

    #[Url]
    public string $email = '';

    public string $senha              = '';
    public string $senha_confirmation = '';
    public string $error              = '';
    public bool   $done               = false;

    public function submit(): void
    {
        $this->validate([
            'email'              => 'required|email',
            'senha'              => 'required|min:8|confirmed',
            'senha_confirmation' => 'required',
        ], [
            'senha.confirmed' => 'A confirmação de senha não confere.',
            'senha.min'       => 'A senha deve ter pelo menos 8 caracteres.',
        ]);

        $status = Password::reset(
            [
                'email'                 => $this->email,
                'password'              => $this->senha,
                'password_confirmation' => $this->senha_confirmation,
                'token'                 => $this->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $this->done = true;
        } else {
            $this->error = match ($status) {
                Password::INVALID_TOKEN => 'Link expirado ou inválido. Solicite um novo.',
                Password::INVALID_USER  => 'Usuário não encontrado.',
                default                 => 'Não foi possível redefinir a senha. Tente novamente.',
            };
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
