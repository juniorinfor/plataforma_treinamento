<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ReportsPage extends Component
{
    public function render()
    {
        return view('livewire.admin.reports-page');
    }
}
