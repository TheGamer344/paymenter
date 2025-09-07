@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <h1 class="text-2xl font-semibold">Account Access</h1>

    <form method="POST" action="{{ route('user-access.store') }}" class="space-y-4 bg-white p-4 rounded shadow">
        @csrf
        <div>
            <label class="block text-sm font-medium">Invite Email</label>
            <input type="email" name="email" class="mt-1 w-full border rounded p-2" required />
        </div>
        <div>
            <label class="block text-sm font-medium">Permissions (comma separated)</label>
            <input type="text" name="permissions_list" class="mt-1 w-full border rounded p-2" placeholder="invoices.view,services.view" />
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Send Invitation</button>
    </form>

    <div class="grid md:grid-cols-2 gap-6">
        <div>
            <h2 class="font-semibold mb-2">People you invited</h2>
            <ul class="divide-y bg-white rounded shadow">
                @forelse($granted as $g)
                    <li class="p-3 text-sm flex justify-between">
                        <span>{{ $g->email }} @if($g->isAccepted()) <span class="text-green-600">(accepted)</span>@else <span class="text-gray-500">(pending)</span>@endif</span>
                        <span class="text-xs text-gray-400">{{ $g->permissions ? implode(',',$g->permissions) : '—' }}</span>
                    </li>
                @empty
                    <li class="p-3 text-sm text-gray-500">None</li>
                @endforelse
            </ul>
        </div>
        <div>
            <h2 class="font-semibold mb-2">Accounts you can access</h2>
            <form method="POST" action="{{ route('user-access.switch') }}" class="space-y-2 bg-white p-3 rounded shadow">
                @csrf
                <select name="owner_id" class="w-full border rounded p-2">
                    <option value="self" @if(!session('acting_owner_id')) selected @endif>My own account</option>
                    @foreach($shared as $s)
                        <option value="{{ $s->owner_user_id }}" @if(session('acting_owner_id') == $s->owner_user_id) selected @endif>{{ $s->owner->name }} ({{ $s->owner->email }})</option>
                    @endforeach
                </select>
                <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Switch</button>
            </form>
        </div>
    </div>
</div>
@endsection
