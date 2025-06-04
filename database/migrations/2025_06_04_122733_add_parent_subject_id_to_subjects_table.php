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
			Schema::table('subjects', function (Blueprint $table) {
				$table->foreignId('parent_subject_id')
					->nullable()
					->after('name_tr') // Or any other preferred position
					->constrained('parent_subjects')
					->onDelete('set null'); // Or 'cascade' if you prefer deleting child subjects
			});
		}

		/**
		 * Reverse the migrations.
		 */
		public function down(): void
		{
			Schema::table('subjects', function (Blueprint $table) {
				// Drop foreign key first by convention: table_column_foreign
				$table->dropForeign(['parent_subject_id']);
				$table->dropColumn('parent_subject_id');
			});
		}
	};
