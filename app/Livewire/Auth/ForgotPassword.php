<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Recuperar senha')]
class ForgotPassword extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    public bool   $sent  = false;
    public string $error = '';

    public function send(): void
    {
        $this->validate();
        $this->error = '';

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->sent = true;
        } else {
            // Não revela se o e-mail existe ou não — mensagem genérica
            $this->sent = true; // security: always show success to prevent user enumeration
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
