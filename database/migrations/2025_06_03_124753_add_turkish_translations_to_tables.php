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
			Schema::table('authors', function (Blueprint $table) {
				$table->string('name_tr')->nullable()->after('name');
				$table->text('biography_tr')->nullable()->after('biography');
			});

			Schema::table('books', function (Blueprint $table) {
				$table->string('name_tr')->nullable()->after('name');
				$table->text('description_tr')->nullable()->after('description');
				// excerpt_tr is not requested, but if needed, add it similarly
			});

			Schema::table('series', function (Blueprint $table) {
				$table->string('name_tr')->nullable()->after('name');
			});

			Schema::table('subjects', function (Blueprint $table) {
				$table->string('name_tr')->nullable()->after('name');
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::table('authors', function (Blueprint $table) {
				$table->dropColumn(['name_tr', 'biography_tr']);
			});

			Schema::table('books', function (Blueprint $table) {
				$table->dropColumn(['name_tr', 'description_tr']);
			});

			Schema::table('series', function (Blueprint $table) {
				$table->dropColumn('name_tr');
			});

			Schema::table('subjects', function (Blueprint $table) {
				$table->dropColumn('name_tr');
			});
		}
	};
