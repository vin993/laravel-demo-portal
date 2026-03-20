<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up() {
		// Marketing Material Groups Table
		Schema::create('marketing_material_groups', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->text('description')->nullable();
			$table->foreignId('created_by')->constrained('users');
			$table->timestamps();
			$table->softDeletes();
		});

		// Marketing Materials Table
		Schema::create('marketing_materials', function (Blueprint $table) {
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

		// Using shorter names for pivot tables
		Schema::create('mm_group_pivot', function (Blueprint $table) {
			$table->id();
			$table->foreignId('material_id')->constrained('marketing_materials')->onDelete('cascade');
			$table->foreignId('group_id')->constrained('marketing_material_groups')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['material_id', 'group_id'], 'mm_group_unique');
		});

		Schema::create('mm_tags', function (Blueprint $table) {
			$table->id();
			$table->string('name')->unique();
			$table->timestamps();
			$table->softDeletes();
		});

		Schema::create('mm_tag_pivot', function (Blueprint $table) {
			$table->id();
			$table->foreignId('material_id')->constrained('marketing_materials')->onDelete('cascade');
			$table->foreignId('tag_id')->constrained('mm_tags')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['material_id', 'tag_id'], 'mm_tag_unique');
		});

		Schema::create('mm_industry_pivot', function (Blueprint $table) {
			$table->id();
			$table->foreignId('material_id')->constrained('marketing_materials')->onDelete('cascade');
			$table->foreignId('industry_id')->constrained('industries')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['material_id', 'industry_id'], 'mm_industry_unique');
		});

		Schema::create('mm_dealer_pivot', function (Blueprint $table) {
			$table->id();
			$table->foreignId('material_id')->constrained('marketing_materials')->onDelete('cascade');
			$table->foreignId('dealer_id')->constrained('dealers')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['material_id', 'dealer_id'], 'mm_dealer_unique');
		});

		Schema::create('mm_manufacturer_pivot', function (Blueprint $table) {
			$table->id();
			$table->foreignId('material_id')->constrained('marketing_materials')->onDelete('cascade');
			$table->foreignId('manufacturer_id')->constrained('manufacturers')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['material_id', 'manufacturer_id'], 'mm_manufacturer_unique');
		});

		Schema::create('mm_company_pivot', function (Blueprint $table) {
			$table->id();
			$table->foreignId('material_id')->constrained('marketing_materials')->onDelete('cascade');
			$table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['material_id', 'company_id'], 'mm_company_unique');
		});

		Schema::create('mm_likes', function (Blueprint $table) {
			$table->id();
			$table->foreignId('material_id')->constrained('marketing_materials')->onDelete('cascade');
			$table->foreignId('user_id')->constrained('users');
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['material_id', 'user_id'], 'mm_likes_unique');
		});

		Schema::create('mm_views', function (Blueprint $table) {
			$table->id();
			$table->foreignId('material_id')->constrained('marketing_materials')->onDelete('cascade');
			$table->foreignId('user_id')->constrained('users');
			$table->string('ip_address', 45)->nullable();
			$table->timestamps();
		});
	}

	public function down() {
		Schema::dropIfExists('mm_dealer_pivot');
		Schema::dropIfExists('mm_industry_pivot');
		Schema::dropIfExists('mm_tag_pivot');
		Schema::dropIfExists('mm_group_pivot');
		Schema::dropIfExists('mm_tags');
		Schema::dropIfExists('marketing_materials');
		Schema::dropIfExists('marketing_material_groups');
		Schema::dropIfExists('mm_company_pivot');
		Schema::dropIfExists('mm_likes');
		Schema::dropIfExists('mm_views');
	}
};
