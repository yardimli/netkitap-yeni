<?php

	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;

	return new class extends Migration
	{
		/**
		 * Run the migrations.
		 */
		public function up(): void
		{
			Schema::table('books', function (Blueprint $table) {
				$table->string('subtitle')->nullable()->after('name_tr');
				$table->string('subtitle_tr')->nullable()->after('subtitle');
				$table->integer('series_number')->unsigned()->nullable()->after('series_id');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::table('books', function (Blueprint $table) {
				$table->dropColumn(['subtitle', 'subtitle_tr', 'series_number']);
			});
		}
	};
