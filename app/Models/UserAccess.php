<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A UserAccess record grants an invited user limited access to an owner's account resources.
 * Scopes and permissions are stored as a JSON array of strings (e.g. ["invoices.view","services.manage"].
 */
class UserAccess extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'owner_user_id',
		'invited_user_id',
		'email',
		'token',
		'accepted_at',
		'permissions',
	];

	protected $casts = [
		'permissions' => 'array',
		'accepted_at' => 'datetime',
	];

	public function owner()
	{
		return $this->belongsTo(User::class, 'owner_user_id');
	}

	public function invitedUser()
	{
		return $this->belongsTo(User::class, 'invited_user_id');
	}

	public function isAccepted(): bool
	{
		return !is_null($this->accepted_at);
	}
}

