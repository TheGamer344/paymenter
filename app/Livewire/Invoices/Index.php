<?php

namespace App\Livewire\Invoices;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

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
        return view('invoices.index', [
            'invoices' => $userContext->invoices()->orderBy('id', 'desc')->paginate(config('settings.pagination')),
        ])->layoutData([
            'title' => __('invoices.invoices'),
            'sidebar' => true,
        ]);
    }
}
