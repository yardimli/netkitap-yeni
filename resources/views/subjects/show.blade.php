@extends('layouts.app')

@section('title', ($currentSubject->name_tr ?? $currentSubject->name) . ' - E-Kitaplar - NetKitap')

@section('content')
	@include('partials.subjects._breadcrumb', ['currentSubject' => $currentSubject])
	
	<main class="inner-page-sec-padding-bottom">
		<div class="container">
			<div class="row">
				<div class="col-lg-9 order-lg-2">
					@include('partials.subjects._toolbar', ['books' => $books, 'currentSubjectName' => ($currentSubject->name_tr ?? $currentSubject->name)]) {{-- Removed 'currentView' => $currentView --}}
					
					<div class="shop-product-wrap list with-pagination row space-db--30 shop-border"> {{-- Hardcoded 'list', removed {{ $currentView ?? 'list' }} --}}
						@forelse($books as $book)
							{{-- Removed PHP block for $itemClass logic --}}
							<div class="col-12 product-card-wrapper"> {{-- Hardcoded 'col-12' --}}
								@include('partials.subjects._book_item_grid', ['book' => $book])
							</div>
						@empty
							<div class="col-12">
								<div class="alert alert-info text-center">
									Bu kategoride henüz e-kitap bulunmamaktadır.
								</div>
							</div>
						@endforelse
					</div>
					
					@include('partials.subjects._pagination', ['paginator' => $books])
				</div>
				<div class="col-lg-3 mt--40 mt-lg--0 order-lg-1">
					@include('partials.subjects._sidebar', ['allParentSubjects' => $allParentSubjects, 'currentSubject' => $currentSubject])
				</div>
			</div>
		</div>
	</main>
@endsection

@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			// Sidebar category toggle (Existing code)
			const parentCategoryNames = document.querySelectorAll('.sidebar-menu--shop .parent-category-name');
			parentCategoryNames.forEach(nameElement => {
				if (nameElement.classList.contains('has-children-indicator')) {
					nameElement.addEventListener('click', function(e) {
						e.preventDefault();
						const subMenu = this.nextElementSibling;
						if (subMenu && subMenu.classList.contains('inner-cat-items')) {
							const parentLi = this.closest('li');
							if (subMenu.style.display === 'block') {
								subMenu.style.display = 'none';
								parentLi.classList.remove('is-active-parent');
							} else {
								subMenu.style.display = 'block';
								parentLi.classList.add('is-active-parent');
							}
						}
					});
				}
			});
			
			// Auto-expand current subject's parent in sidebar
			const activeParentLi = document.querySelector('.sidebar-menu--shop li.is-active-parent > ul.inner-cat-items');
			if(activeParentLi) {
				activeParentLi.style.display = 'block';
			}
			
			// Quick view modal AJAX (Existing code)
			const quickViewButtons = document.querySelectorAll('.quick-view-btn');
			const quickModalElement = document.getElementById('quickModal');
			if (quickModalElement) {
				const quickModal = new bootstrap.Modal(quickModalElement);
				quickViewButtons.forEach(button => {
					button.addEventListener('click', function () {
						const bookId = this.dataset.bookId;
						fetch(`/api/books/${bookId}`)
							.then(response => {
								if (!response.ok) throw new Error('Book not found or API error');
								return response.json();
							})
							.then(data => {
								populateQuickViewModal(data.book);
								quickModal.show();
							})
							.catch(error => {
								console.error('Error fetching book details for quick view:', error);
								// Optionally show a static/error message in modal or just log
								// For example, to show the modal even on error with placeholders:
								// populateQuickViewModal(null); // Or some default error state
								// quickModal.show();
							});
					});
				});
			}
		});
		
		function populateQuickViewModal(book) {
			// Default placeholder image if no book data or image path
			const placeholderImage = `{{ asset('images/placeholder_cover.jpg') }}`;
			const coverImage = book && book.cover_image_path ? `/storage/${book.cover_image_path}` : placeholderImage;
			
			document.getElementById('quickViewMainImage').src = coverImage;
			document.getElementById('quickViewNavImage1').src = coverImage; // Assuming only one nav image shown, same as main
			
			if (book) {
				document.getElementById('quickViewBookTitle').textContent = book.name_tr || book.name || 'Başlık Yok';
				const authorLink = document.querySelector('#quickViewBookAuthor a');
				if(book.author) {
					authorLink.textContent = book.author.name_tr || book.author.name;
					// authorLink.href = `/authors/${book.author.id}`; // Uncomment and adjust if author pages exist
				} else {
					authorLink.textContent = 'Bilinmiyor';
					// authorLink.href = '#';
				}
				
				// Example for other fields (uncomment and adjust if these elements exist and data is available)
				// document.getElementById('quickViewBookPublisher').textContent = book.publisher ? book.publisher.name : 'N/A';
				// document.getElementById('quickViewBookLanguage').textContent = book.language || 'Türkçe';
				// document.getElementById('quickViewBookPages').textContent = book.page_count || 'N/A';
				document.getElementById('quickViewBookDescription').innerHTML = book.excerpt || book.description_tr || book.description || 'Açıklama bulunmamaktadır.';
				
				document.getElementById('quickViewBookPriceNew').textContent = `₺${parseFloat(book.price || 0).toFixed(2)}`;
				const oldPriceEl = document.getElementById('quickViewBookPriceOld');
				if (book.old_price && parseFloat(book.old_price) > parseFloat(book.price || 0)) {
					oldPriceEl.textContent = `₺${parseFloat(book.old_price).toFixed(2)}`;
					oldPriceEl.style.display = 'inline';
				} else {
					oldPriceEl.style.display = 'none';
				}
				
				// const detailsButton = document.querySelector('#quickModal .product-actions-ebook-modal .btn-primary');
				// if(detailsButton && book.slug_or_id){ // Assuming you have a slug or id for book detail route
				//     detailsButton.href = `/kitap/${book.slug_or_id}`;
				// }
			} else {
				// Reset to placeholders if book data is null (e.g., on error)
				document.getElementById('quickViewBookTitle').textContent = 'Örnek E-Kitap Başlığı';
				document.querySelector('#quickViewBookAuthor a').textContent = 'Yazar Adı';
				// document.querySelector('#quickViewBookAuthor a').href = '#';
				document.getElementById('quickViewBookDescription').innerHTML = 'E-kitap açıklaması buraya gelecek.';
				document.getElementById('quickViewBookPriceNew').textContent = '₺0.00';
				document.getElementById('quickViewBookPriceOld').style.display = 'none';
			}
		}
		
		// Removed function updateSidebarLinksPreferredView and related view mode logic
	</script>
@endpush
