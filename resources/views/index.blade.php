@extends('layouts.app')

@section('title', 'NetKitap - E-Kitap Mağazası')

@section('content')
	@include('partials.home._hero')
	@include('partials.home._category_gallery')
	
	@include('partials.home._book_slider_partial', ['books' => $actionAdventureBooks, 'title' => 'AKSİYON VE MACERA E-KİTAPLARI'])
	@include('partials.home._book_slider_partial', ['books' => $scienceFictionBooks, 'title' => 'BİLİM KURGU E-KİTAPLARI'])
	@include('partials.home._book_slider_partial', ['books' => $classicFictionBooks, 'title' => 'KLASİK KURGU E-KİTAPLARI'])
	
	@include('partials.home._book_slider_partial', ['books' => $biographyBooks, 'title' => 'BİYOGRAFİ E-KİTAPLARI'])
	@include('partials.home._book_slider_partial', ['books' => $childrenBooks, 'title' => 'ÇOCUK E-KİTAPLARI'])
	
	
	@include('partials.home._features')
	@include('partials.home._promotion_one')
@endsection

@push('scripts')
	{{-- Add script for dynamic quick view modal if needed --}}
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const quickViewButtons = document.querySelectorAll('.quick-view-btn');
			const quickModal = new bootstrap.Modal(document.getElementById('quickModal')); // Ensure Bootstrap JS is loaded
			
			quickViewButtons.forEach(button => {
				button.addEventListener('click', function () {
					const bookId = this.dataset.bookId;
					// TODO: AJAX call to fetch book details using bookId
					// For now, it will just open the modal with static/placeholder content
					// Example: fetch(`/api/books/${bookId}`) .then(response => response.json()) .then(data => { populateModal(data); quickModal.show(); });
					
					// Placeholder: Update modal title (rest would need more detailed DOM manipulation)
					const modalTitle = document.querySelector('#quickModal .product-title');
					if (modalTitle) {
						// This is a very basic update. A full implementation would update all fields.
						// For a full dynamic modal, you'd fetch book data and populate all elements inside the modal.
						// The current static modal content will show.
					}
					// quickModal.show(); // Modal is already shown by data-bs-toggle
				});
			});
			
			function populateModal(bookData) {
				// This function would update the modal's content with bookData
				// Example:
				// document.querySelector('#quickModal .product-title').textContent = bookData.name_tr;
				// document.querySelector('#quickModal .product-details-slider .single-slide img').src = `/storage/${bookData.cover_image_path}`;
				// ... and so on for all other details (price, description, author, etc.)
				console.log('Populate modal with:', bookData); // Placeholder
			}
		});
	</script>
@endpush
