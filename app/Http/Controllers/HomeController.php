<?php

	namespace App\Http\Controllers;

	use App\Models\Book;
	use App\Models\ParentSubject;
	use Illuminate\Http\Request;

	class HomeController extends Controller
	{
		public function index()
		{
			// Fetch books for different sections
			// We'll assume ParentSubject 'name_tr' fields match these titles
			// or we'd use IDs or English names if 'name_tr' is not guaranteed unique for this purpose.

			$biographyBooks = $this->getBooksByParentSubjectNameTr('Tarih ve Biyografi', 'Biyografi'); // Assuming "Biyografi" might be a specific child subject or part of "Tarih ve Biyografi"
			$childrenBooks = $this->getBooksByParentSubjectNameTr('Çocuk Edebiyatı');
			$actionAdventureBooks = $this->getBooksByParentSubjectNameTr('Aksiyon ve Macera');
			$scienceFictionBooks = $this->getBooksByParentSubjectNameTr('Kurgu', 'Bilim Kurgu'); // Assuming "Bilim Kurgu" is a Subject under "Kurgu" ParentSubject
			$classicFictionBooks = $this->getBooksByParentSubjectNameTr('Kurgu', 'Klasik Kurgu'); // Assuming "Klasik Kurgu" is a Subject under "Kurgu" ParentSubject

			// For header categories
			$allParentSubjects = ParentSubject::with(['subjects' => function ($query) {
				$query->orderBy('name_tr');
			}])->orderBy('name_tr')->get();

			return view('index', compact(
				'biographyBooks',
				'childrenBooks',
				'actionAdventureBooks',
				'scienceFictionBooks',
				'classicFictionBooks',
				'allParentSubjects'
			));
		}

		/**
		 * Helper function to get books by ParentSubject name_tr.
		 * If childSubjectNameTr is provided, it further filters by a Subject's name_tr under that ParentSubject.
		 */
		private function getBooksByParentSubjectNameTr(string $parentSubjectNameTr, ?string $childSubjectNameTr = null, int $limit = 10)
		{
			$parentSubject = ParentSubject::where('name_tr', $parentSubjectNameTr)->first();

			if (!$parentSubject) {
				return collect(); // Return empty collection if parent subject not found
			}

			$query = Book::query()->with('author');

			if ($childSubjectNameTr) {
				$query->whereHas('subjects', function ($q) use ($parentSubject, $childSubjectNameTr) {
					$q->where('parent_subject_id', $parentSubject->id)
						->where('name_tr', $childSubjectNameTr);
				});
			} else {
				$query->whereHas('subjects.parentSubject', function ($q) use ($parentSubject) {
					$q->where('id', $parentSubject->id);
				});
			}

			return $query->latest()->take($limit)->get();
		}
	}
