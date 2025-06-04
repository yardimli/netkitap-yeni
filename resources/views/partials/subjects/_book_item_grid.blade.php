<div class="product-card">
	
	<div class="product-list-content">
		<div class="card-image" style="max-width:200px; margin-right: 15px;">
			<img src="{{ $book->cover_image_path ? asset('storage/' . $book->cover_image_path) : asset('images/placeholder_cover.jpg') }}" alt="{{ $book->name_tr ?? $book->name }}">
		</div>
		<div class="product-card--body"  style="text-align: left;">
			<div class="product-header">
				<a href="{{-- {{ $book->author ? route('authors.show', $book->author->id) : '#' }} --}}" class="author">
					{{ $book->author ? ($book->author->name_tr ?? $book->author->name) : 'Bilinmiyor' }}
				</a>
				<h3><a href="{{-- {{ route('books.show', $book->id) }} --}}" tabindex="0">
						{{ !empty($book->name_tr) ? $book->name_tr : $book->name }}
						@if(!empty($book->subtitle_tr) || !empty($book->subtitle))
							<small class="d-block text-muted">{{ !empty($book->subtitle_tr) ? $book->subtitle_tr : $book->subtitle }}</small>
						@endif
					</a></h3>
			</div>
			<article class="d-none d-sm-block">
				<h2 class="sr-only">Kitap Özeti</h2>
				<p>{{ Str::limit(strip_tags(!empty($book->description_tr) ? $book->description_tr : $book->description), 450) }}</p>
			</article>

			{{-- <div class="rating-block my-2"> --}}
			{{-- Placeholder for rating --}}
			{{-- <span class="fas fa-star star_on"></span> ... --}}
			{{-- </div> --}}
			<div class="btn-block mt-3">
				<a href="{{-- {{ route('books.show', $book->id) }} --}}" class="btn btn--primary">Detayları Gör</a>
				{{-- <a href="#" class="btn btn-outlined ms-2">Hemen Oku</a> --}}
				{{-- <a href="#" class="card-link ms-2"><i class="fas fa-download"></i> İndir</a> --}}
			</div>
		</div>
	</div>
</div>
{{--
    NOTE: You'll need to define routes for 'authors.show' and 'books.show'
    and replace the commented-out href attributes with these routes.
    Example: route('books.show', $book)
--}}
