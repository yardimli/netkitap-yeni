<?php

	namespace App\Http\View\Composers;

	use App\Models\ParentSubject;
	use Illuminate\View\View;

	class HeaderComposer
	{
		public function compose(View $view)
		{
			if (!isset($view->getData()['allParentSubjects'])) {
				$allParentSubjects = ParentSubject::with(['subjects' => function($query){
					$query->orderBy('name_tr')->orderBy('name'); // Sort subjects
				}])
					->orderBy('name_tr')->orderBy('name') // Sort parent subjects
					->get();
				$view->with('allParentSubjects', $allParentSubjects);
			}
		}
	}
