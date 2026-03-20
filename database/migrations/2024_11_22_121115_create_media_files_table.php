<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up() {
		// Media Groups Table
		Schema::create('media_groups', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->text('description')->nullable();
			$table->foreignId('created_by')->constrained('users');
			$table->timestamps();
			$table->softDeletes();
		});

		// Media Files Table (same as before)
		Schema::create('media_files', function (Blueprint $table) {
			$table->id();
			$table->string('title');
			$table->text('description')->nullable();
			$table->string('file_path');
			$table->string('thumbnail_path')->nullable();
			$table->string('medium_path')->nullable();
			$table->enum('file_type', ['document', 'video', 'image']);
			$table->string('mime_type');
			$table->bigInteger('size');
			$table->bigInteger('thumbnail_size')->nullable();
			$table->integer('width')->nullable();
			$table->integer('height')->nullable();
			$table->foreignId('uploaded_by')->constrained('users');
			$table->boolean('is_featured')->default(false);
			$table->integer('sort_order')->default(0);
			$table->json('metadata')->nullable();
			$table->unsignedBigInteger('created_by')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});

		Schema::create('media_file_group', function (Blueprint $table) {
			$table->id();
			$table->foreignId('media_file_id')->constrained('media_files')->onDelete('cascade');
			$table->foreignId('media_group_id')->constrained('media_groups')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['media_file_id', 'media_group_id']);
		});

		Schema::create('media_tags', function (Blueprint $table) {
			$table->id();
			$table->string('name')->unique();
			$table->timestamps();
			$table->softDeletes();
		});

		Schema::create('media_file_tag', function (Blueprint $table) {
			$table->id();
			$table->foreignId('media_file_id')->constrained('media_files')->onDelete('cascade');
			$table->foreignId('media_tag_id')->constrained('media_tags')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['media_file_id', 'media_tag_id']);
		});

		Schema::create('media_file_industry', function (Blueprint $table) {
			$table->id();
			$table->foreignId('media_file_id')->constrained('media_files')->onDelete('cascade');
			$table->foreignId('industry_id')->constrained('industries')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['media_file_id', 'industry_id']);
		});

		Schema::create('media_file_dealer', function (Blueprint $table) {
			$table->id();
			$table->foreignId('media_file_id')->constrained('media_files')->onDelete('cascade');
			$table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['media_file_id', 'dealer_id']);
		});
		Schema::create('media_file_manufacturer', function (Blueprint $table) {
			$table->id();
			$table->foreignId('media_file_id')->constrained('media_files')->onDelete('cascade');
			$table->foreignId('manufacturer_id')->constrained('manufacturers')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['media_file_id', 'manufacturer_id']);
		});
		Schema::create('media_file_company', function (Blueprint $table) {
			$table->id();
			$table->foreignId('media_file_id')->constrained('media_files')->onDelete('cascade');
			$table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['media_file_id', 'company_id']);
		});

		Schema::create('media_comments', function (Blueprint $table) {
			$table->id();
			$table->foreignId('media_file_id')->constrained('media_files')->onDelete('cascade');
			$table->foreignId('user_id')->constrained('users');
			$table->text('comment');
			$table->boolean('is_approved')->default(true);
			$table->timestamps();
			$table->softDeletes();
		});

		Schema::create('media_likes', function (Blueprint $table) {
			$table->id();
			$table->foreignId('media_file_id')->constrained('media_files')->onDelete('cascade');
			$table->foreignId('user_id')->constrained('users');
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['media_file_id', 'user_id']);
		});

		Schema::create('media_views', function (Blueprint $table) {
			$table->id();
			$table->foreignId('media_file_id')->constrained('media_files')->onDelete('cascade');
			$table->foreignId('user_id')->constrained('users');
			$table->string('ip_address', 45)->nullable();
			$table->timestamps();
		});
	}

	public function down() {
		Schema::dropIfExists('media_file_dealer');
		Schema::dropIfExists('media_file_manufacturer');
		Schema::dropIfExists('media_file_industry');
		Schema::dropIfExists('media_file_tag');
		Schema::dropIfExists('media_file_group');
		Schema::dropIfExists('media_tags');
		Schema::dropIfExists('media_files');
		Schema::dropIfExists('media_groups');
		Schema::dropIfExists('media_file_company');
	}
};
