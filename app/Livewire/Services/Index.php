<?php

namespace App\Livewire\Services;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $status = null;

    protected function baseUser()
    {
        $user = Auth::user();
        $actingOwner = session('acting_owner_id');
        if ($actingOwner && (int)$actingOwner !== (int)$user->id) {
            // ensure user still has access; fallback to own
            $shared = $user->sharedAccounts()->where('owner_user_id', $actingOwner)->first();
            if ($shared) {
                return $shared->owner; // owner relationship loaded in view elsewhere if needed
            }
        }
        return $user;
    }

    public function render()
    {
        $userContext = $this->baseUser();
        $query = $userContext->services()->orderBy('created_at', 'desc');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('services.index', [
            'services' => $query->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => 'Services',
            'sidebar' => true,
        ]);
    }
}
