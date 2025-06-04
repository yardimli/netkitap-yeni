<section class="section-margin">
	<div class="container">
		<div class="section-title section-title--bordered">
			<h2>{{ $title }}</h2>
		</div>
		@if(isset($books) && $books->count() > 0)
			<div class="product-slider sb-slick-slider slider-border-single-row" data-slick-setting='{
            "autoplay": true,
            "autoplaySpeed": 8000,
            "slidesToShow": 5,
            "dots":true
        }' data-slick-responsive='[
            {"breakpoint":1200, "settings": {"slidesToShow": 4} },
            {"breakpoint":992, "settings": {"slidesToShow": 3} },
            {"breakpoint":768, "settings": {"slidesToShow": 2} },
            {"breakpoint":480, "settings": {"slidesToShow": 1} },
            {"breakpoint":320, "settings": {"slidesToShow": 1} }
        ]'>
				@foreach($books as $book)
					<div class="single-slide">
						<div class="product-card">
							<div class="product-header">
								<a href="{{ $book->author ? '#' : '#' }}" class="author">
									{{ $book->author ? ($book->author->name_tr ?? $book->author->name) : 'Bilinmiyor' }}
								</a>
								<h3><a style="min-height:40px;" href="{{ route('home') }}">
										{{ !empty($book->name_tr) ? $book->name_tr : $book->name }}
									</a></h3>
{{--								@if(!empty($book->subtitle_tr) || $book->subtitle)--}}
{{--									<h4 class="product-subtitle">{{ !empty($book->subtitle_tr) ? $book->subtitle_tr : $book->subtitle }}</h4>--}}
{{--								@endif--}}
							</div>
							<div class="product-card--body">
								<div class="card-image" style="min-height: 350px;">
									<img src="{{ $book->cover_image_path ? asset('storage/' . $book->cover_image_path) : asset('images/placeholder_cover.jpg') }}" alt="{{ $book->name_tr ?? $book->name }}" style="max-width: 90%; margin: 0 auto; display: block;">
									<div class="hover-contents">
										<a href="{{ route('home') }}" class="hover-image"> {{-- Replace with actual book detail route --}}
											{{-- Assuming a different hover image is not available, use same image or a generic one --}}
											<img src="{{ $book->cover_image_path ? asset('storage/' . $book->cover_image_path) : asset('images/placeholder_cover.jpg') }}" alt="{{ $book->name_tr ?? $book->name }} Hover">
										</a>
										<div class="hover-btns">
											<a href="#" data-bs-toggle="modal" data-bs-target="#quickModal" class="single-btn quick-view-btn" data-book-id="{{ $book->id }}"> {{-- Add data-book-id for JS --}}
												<i class="fas fa-eye"></i>
											</a>
										</div>
									</div>
								</div>
								<div class="product-actions-ebook">
{{--									<a href="#" class="btn btn-outlined--primary btn-sm">Hemen Oku</a>--}}
{{--									<a href="#" class="btn btn-outlined--primary btn-sm">İndir</a>--}}
								</div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		@else
			<p>Bu kategoride henüz e-kitap bulunmamaktadır.</p>
		@endif
	</div>
</section>
<style>
    .product-subtitle {
        font-size: 0.8rem;
        color: #666;
        margin-top: -8px;
        margin-bottom: 5px;
        height: 2.4em; /* Approx 2 lines */
        line-height: 1.2em;
        overflow: hidden;
    }
    .product-actions-ebook {
        margin-top: 10px;
        display: flex;
        justify-content: space-around;
    }
    .product-actions-ebook .btn-sm {
        padding: 5px 10px;
        font-size: 0.8rem;
    }
</style>
