<?php

	namespace App\Models;

	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Str; // Import Str

	class Subject extends Model
	{
		use HasFactory;

		protected $fillable = [
			'name',
			'name_tr',
			'parent_subject_id',
			'slug', // Add slug to fillable
		];

		// Automatically generate slug
		protected static function booted()
		{
			static::creating(function ($subject) {
				$subject->slug = self::createUniqueSlug($subject->name_tr ?: $subject->name);
			});

			static::updating(function ($subject) {
				if ($subject->isDirty('name_tr') || $subject->isDirty('name')) {
					// Only regenerate slug if name_tr or name changes
					// and the slug wasn't manually changed to something different.
					$expectedOldSlug = Str::slug($subject->getOriginal('name_tr') ?: $subject->getOriginal('name'), '-');
					if (empty($subject->slug) || $subject->slug === $expectedOldSlug || $subject->getOriginal('slug') === $subject->slug) {
						$subject->slug = self::createUniqueSlug($subject->name_tr ?: $subject->name, $subject->id);
					}
				}
			});
		}

		/**
		 * Create a unique slug.
		 *
		 * @param string $name
		 * @param int|null $ignoreId
		 * @return string
		 */
		private static function createUniqueSlug(string $name, ?int $ignoreId = null): string
		{
			$slug = Str::slug($name, '-');
			$originalSlug = $slug;
			$count = 1;

			$query = static::where('slug', $slug);
			if ($ignoreId) {
				$query->where('id', '!=', $ignoreId);
			}

			while ($query->exists()) {
				$slug = $originalSlug . '-' . $count++;
				// Re-query for the new slug
				$query = static::where('slug', $slug);
				if ($ignoreId) {
					$query->where('id', '!=', $ignoreId);
				}
			}
			return $slug;
		}


		/**
		 * Get the route key for the model.
		 *
		 * @return string
		 */
		public function getRouteKeyName()
		{
			return 'slug';
		}

		public function books()
		{
			return $this->belongsToMany(Book::class);
		}

		public function parentSubject()
		{
			return $this->belongsTo(ParentSubject::class, 'parent_subject_id');
		}
	}
