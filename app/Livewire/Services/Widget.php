<?php

namespace App\Livewire\Services;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Widget extends Component
{
    use WithPagination;

    public $status = null;

    protected function baseUser()
    {
        $user = Auth::user();
        $actingOwner = session('acting_owner_id');
        if ($actingOwner && (int)$actingOwner !== (int)$user->id) {
            $shared = $user->sharedAccounts()->where('owner_user_id', $actingOwner)->first();
            if ($shared) {
                return $shared->owner;
            }
        }
        return $user;
    }

    public function render()
    {
        $userContext = $this->baseUser();
        $query = $userContext->services();

        if ($this->status) {
            $query->where('status', $this->status);
        } else {
            $query->where('status', '!=', 'cancelled');
        }

        return view('services.widget', [
            'services' => $query->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => 'Services',
        ]);
    }
}
