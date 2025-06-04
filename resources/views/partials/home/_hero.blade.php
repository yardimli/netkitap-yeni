<section class="hero-area hero-slider-3">
	<div class="sb-slick-slider" data-slick-setting='{
        "autoplay": true,
        "autoplaySpeed": 8000,
        "slidesToShow": 1,
        "dots":true
    }'>
		<div class="single-slide bg-image bg-overlay--dark" data-bg="{{ asset('images/home-3-slider-1.jpg') }}">
			<div class="container">
				<div class="home-content text-center">
					<div class="row justify-content-end">
						<div class="col-lg-6">
							<h1>Binlerce E-Kitap</h1>
							<h2>Anında indirin veya web sitemizde okuyun.</h2>
							{{-- Link to a general shop/browse page --}}
							<a href="{{ route('home') }}#all-books" class="btn btn--yellow">Hemen Keşfet</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="single-slide bg-image bg-overlay--dark" data-bg="{{ asset('images/home-3-slider-2.jpg') }}">
			<div class="container">
				<div class="home-content pl--30"> {{-- Consider text-center if content is centered for this slide too --}}
					<div class="row">
						<div class="col-lg-6">
							<h1>Yeni Çıkanlar Burada!</h1>
							<h2>En son eklenen e-kitapları ilk siz okuyun.</h2>
							{{-- Link to a new releases page or filtered shop page --}}
							<a href="{{ route('home') }}#new-releases" class="btn btn--yellow">Yeni Gelenler</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
