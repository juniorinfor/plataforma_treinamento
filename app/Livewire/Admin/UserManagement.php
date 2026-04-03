<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class UserManagement extends Component
{
    public function render()
    {
        $users = User::where('company_id', auth()->user()->company_id)->with('points')->get();
        return view('livewire.admin.user-management', ['users' => $users]);
    }
}
