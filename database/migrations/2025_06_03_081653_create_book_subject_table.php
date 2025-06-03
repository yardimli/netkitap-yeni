<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration
	{
		public function up()
		{
			Schema::create('book_subject', function (Blueprint $table) {
				$table->foreignId('book_id')->constrained('books')->onDelete('cascade');
				$table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
				$table->primary(['book_id', 'subject_id']);
				// No timestamps needed for a simple pivot unless you have extra data
			});
		}

		public function down()
		{
			Schema::dropIfExists('book_subject');
		}
	};
