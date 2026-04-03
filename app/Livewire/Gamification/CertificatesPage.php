<?php

namespace App\Livewire\Gamification;

use App\Models\Certificate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CertificatesPage extends Component
{
    public function render()
    {
        $certificates = Certificate::where('user_id', auth()->id())->with('course')->get();
        return view('livewire.gamification.certificates-page', ['certificates' => $certificates]);
    }
}
