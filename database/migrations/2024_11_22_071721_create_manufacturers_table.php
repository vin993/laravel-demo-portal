<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up() {
		Schema::create('manufacturers', function (Blueprint $table) {
			$table->id();
			$table->string('name')->unique();
			$table->string('contact_person')->nullable();
			$table->string('email')->nullable();
			$table->string('phone')->nullable();
			$table->text('address')->nullable();
			$table->timestamps();
		});
		Schema::create('manufacturer_user', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->foreignId('manufacturer_id')->constrained()->onDelete('cascade');
			$table->timestamps();
		});
	}

	public function down(): void {
		Schema::dropIfExists('manufacturer_user');
		Schema::dropIfExists('manufacturers');
	}
};
