<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'name_tr', // Added
		'born_death',
		'image_path',
		'biography',
		'biography_tr', // Added
	];

	public function books()
	{
		return $this->hasMany(Book::class);
	}
}
