<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;

	class Book extends Model
	{
		use HasFactory;

		protected $fillable = [
			'name',
			'author_id',
			'series_id',
			'cover_image_path',
			'description',
			'excerpt',
		];

		public function author()
		{
			return $this->belongsTo(Author::class);
		}

		public function series()
		{
			return $this->belongsTo(Series::class);
		}

		public function subjects()
		{
			return $this->belongsToMany(Subject::class);
		}
	}
