<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'name_tr',
		'subtitle',
		'subtitle_tr',
		'author_id',
		'series_id',
		'series_number',
		'cover_image_path',
		'description',
		'description_tr',
		'excerpt',
		'price', // Added for demonstration
		'old_price', // Added for demonstration
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

	// Helper for discount percentage (optional)
	public function getDiscountPercentageAttribute()
	{
		if ($this->old_price && $this->price < $this->old_price) {
			return round((($this->old_price - $this->price) / $this->old_price) * 100);
		}
		return 0;
	}
}
