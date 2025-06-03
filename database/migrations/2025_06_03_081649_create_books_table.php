<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration
	{
		public function up()
		{
			Schema::create('books', function (Blueprint $table) {
				$table->id();
				$table->string('name');
				$table->foreignId('author_id')->nullable()->constrained('authors')->onDelete('set null');
				$table->foreignId('series_id')->nullable()->constrained('series')->onDelete('set null');
				$table->string('cover_image_path')->nullable(); // Local path after download
				$table->text('description')->nullable();
				$table->text('excerpt')->nullable();
				$table->timestamps();

				// Optional: Add a unique constraint if a book name + author should be unique
				// $table->unique(['name', 'author_id']);
			});
		}

		public function down()
		{
			Schema::dropIfExists('books');
		}
	};
