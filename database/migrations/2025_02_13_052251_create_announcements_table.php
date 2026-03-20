<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void {
		Schema::create('announcements', function (Blueprint $table) {
			$table->id();
			$table->string('title');
			$table->string('image_path')->nullable();
			$table->boolean('status')->default(true);
			$table->timestamps();
		});

		Schema::create('announcement_industry', function (Blueprint $table) {
			$table->id();
			$table->foreignId('announcement_id')->constrained()->onDelete('cascade');
			$table->foreignId('industry_id')->constrained()->onDelete('cascade');
			$table->timestamps();
		});

		Schema::create('announcement_dealer', function (Blueprint $table) {
			$table->id();
			$table->foreignId('announcement_id')->constrained()->onDelete('cascade');
			$table->foreignId('dealer_id')->constrained()->onDelete('cascade');
			$table->timestamps();
		});

		Schema::create('announcement_manufacturer', function (Blueprint $table) {
			$table->id();
			$table->foreignId('announcement_id')->constrained()->onDelete('cascade');
			$table->foreignId('manufacturer_id')->constrained()->onDelete('cascade');
			$table->timestamps();
		});
	}

	public function down(): void {
		Schema::dropIfExists('announcement_dealer');
		Schema::dropIfExists('announcement_manufacturer');
		Schema::dropIfExists('announcement_industry');
		Schema::dropIfExists('announcements');
	}
};
