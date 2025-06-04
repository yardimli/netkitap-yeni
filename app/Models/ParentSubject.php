<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;

	class ParentSubject extends Model
	{
		use HasFactory;

		protected $fillable = [
			'name',
			'name_tr',
		];

		public function subjects()
		{
			return $this->hasMany(Subject::class);
		}
	}
