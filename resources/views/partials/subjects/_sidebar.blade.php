<div class="inner-page-sidebar">
	<div class="single-block">
		<h3 class="sidebar-title">Kategoriler</h3>
		@if($allParentSubjects && $allParentSubjects->count() > 0)
			<ul class="sidebar-menu--shop">
				@foreach($allParentSubjects as $parentSubject)
					<li class="{{ (isset($currentSubject) && $currentSubject && $currentSubject->parent_subject_id == $parentSubject->id) ? 'is-active-parent' : '' }}">
						<a href="javascript:void(0);" class="parent-category-name {{ $parentSubject->subjects->count() > 0 ? 'has-children-indicator' : '' }}">
							{{ $parentSubject->name_tr ?? $parentSubject->name }}
						</a>
						@if($parentSubject->subjects->count() > 0)
							<ul class="inner-cat-items" style="{{ (isset($currentSubject) && $currentSubject && $currentSubject->parent_subject_id == $parentSubject->id) ? 'display:block;' : '' }}">
								@foreach($parentSubject->subjects as $subject)
									<li>
										{{-- MODIFIED LINE BELOW: Removed 'view' => 'list' from route parameters --}}
										<a href="{{ route('subjects.show', ['subject' => $subject]) }}" class="subject-category-link {{ (isset($currentSubject) && $currentSubject && $currentSubject->id == $subject->id) ? 'active' : '' }}">
											{{ $subject->name_tr ?? $subject->name }} <span>({{ $subject->books_count }})</span>
										</a>
									</li>
								@endforeach
							</ul>
						@endif
					</li>
				@endforeach
			</ul>
		@else
			<p>Kategori bulunamadı.</p>
		@endif
	</div>
	<div class="single-block">
		<a href="#" class="promo-image sidebar">
			<img src="{{ asset('images/others/home-side-promo.jpg') }}" alt="Yan Panel Promosyon">
			{{-- Ensure 'images/others/home-side-promo.jpg' exists in public/images/others/ --}}
		</a>
	</div>
</div>
<style>
    .sidebar-menu--shop .parent-category-name {
        font-weight: bold;
        cursor: pointer;
    }
    .sidebar-menu--shop .inner-cat-items {
        padding-left: 15px;
        display: none; /* Initially hidden, shown by JS or if parent is active */
    }
    .sidebar-menu--shop li.is-active-parent > .inner-cat-items {
        display: block;
    }
    .sidebar-menu--shop .inner-cat-items a.active {
        color: var(--primary-color); /* Or your theme's active color */
        font-weight: bold;
    }
</style>
