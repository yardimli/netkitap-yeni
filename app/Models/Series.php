<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'name_tr', // Added
	];

	// If you want to prevent Eloquent from managing created_at and updated_at
	// public $timestamps = false; // But we defined them in migration, so keep true

	public function books()
	{
		return $this->hasMany(Book::class);
	}
}
