<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('user_accesses', function (Blueprint $table) {
			$table->id();
			$table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
			// invited_user_id is nullable until the invited user registers / accepts
			$table->foreignId('invited_user_id')->nullable()->constrained('users')->nullOnDelete();
			$table->string('email'); // email invitation sent to (unique per owner?)
			$table->string('token')->unique();
			$table->json('permissions')->nullable();
			$table->timestamp('accepted_at')->nullable();
			$table->timestamps();
			$table->softDeletes();

			$table->unique(['owner_user_id', 'email']);
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('user_accesses');
	}
};

