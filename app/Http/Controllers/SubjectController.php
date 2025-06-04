<?php namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Subject;
use App\Models\ParentSubject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
	private function getSharedViewData()
	{
		return [
			'allParentSubjects' => ParentSubject::with(['subjects' => function ($query) {
				$query->withCount('books')->orderBy('name_tr')->orderBy('name');
			}])->orderBy('name_tr')->orderBy('name')->get()
		];
	}

	public function show(Request $request, Subject $subject)
	{
		$subject->load('parentSubject');
		$perPage = $request->input('perPage', 15); // Default to 15
		$sortBy = $request->input('sortBy', 'created_at');
		$sortDir = $request->input('sortDir', 'desc');
		// $currentView = $request->input('view', 'list'); // Default to 'list' view - REMOVED
		// Always use list view
		// $currentView = 'list'; // This variable is not strictly needed anymore if hardcoded in blade

		$allowedSortBy = ['name', 'price', 'created_at', 'default'];
		if (!in_array($sortBy, $allowedSortBy) || $sortBy === 'default') {
			$sortBy = 'created_at';
		}

		$booksQuery = $subject->books()->with(['author', 'series']);

		if ($sortBy === 'name') {
			$booksQuery->orderBy('name_tr', $sortDir)->orderBy('name', $sortDir);
		} elseif ($sortBy === 'price') {
			$booksQuery->orderBy('price', $sortDir);
		} else {
			$booksQuery->orderBy($sortBy, $sortDir);
		}

		$books = $booksQuery->paginate($perPage)->withQueryString();
		$currentSubject = $subject;
		$sharedData = $this->getSharedViewData();

		return view('subjects.show', array_merge(
			compact('books', 'currentSubject'), // Pass currentView REMOVED
			$sharedData
		));
	}
}
