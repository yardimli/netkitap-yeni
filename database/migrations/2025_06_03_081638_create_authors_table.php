<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration
	{
		public function up()
		{
			Schema::create('authors', function (Blueprint $table) {
				$table->id();
				$table->string('name')->unique(); // Assuming author names are unique
				$table->string('born_death')->nullable();
				$table->string('image_path')->nullable(); // Local path after download
				$table->text('biography')->nullable();
				$table->timestamps();
			});
		}

		public function down()
		{
			Schema::dropIfExists('authors');
		}
	};
