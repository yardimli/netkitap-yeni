<div class="modal fade modal-quick-view" id="quickModal" tabindex="-1" role="dialog" aria-labelledby="quickModal" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
			<div class="product-details-modal">
				<div class="row">
					<div class="col-lg-5">
						<!-- Product Details Slider Big Image-->
						<div class="product-details-slider sb-slick-slider arrow-type-two" data-slick-setting='{
                            "slidesToShow": 1,
                            "arrows": false,
                            "fade": true,
                            "draggable": false,
                            "swipe": false,
                            "asNavFor": ".product-slider-nav"
                        }'>
							<div class="single-slide">
								<img src="{{ asset('images/placeholder_cover.jpg') }}" alt="E-Kitap Kapağı 1" id="quickViewMainImage">
							</div>
							{{-- Add more slides if you have multiple images for a book, otherwise one is enough for placeholder --}}
						</div>
						<!-- Product Details Slider Nav -->
						<div class="mt--30 product-slider-nav sb-slick-slider arrow-type-two" data-slick-setting='{
                            "infinite":true,
                            "autoplay": true,
                            "autoplaySpeed": 8000,
                            "slidesToShow": 3, "arrows": true, "prevArrow":{"buttonClass": "slick-prev","iconClass":"fa fa-chevron-left"}, "nextArrow":{"buttonClass": "slick-next","iconClass":"fa fa-chevron-right"},
                            "asNavFor": ".product-details-slider",
                            "focusOnSelect": true
                        }'>
							<div class="single-slide">
								<img src="{{ asset('images/placeholder_cover.jpg') }}" alt="E-Kitap Nav 1" id="quickViewNavImage1">
							</div>
							{{-- Add more nav slides if needed --}}
						</div>
					</div>
					<div class="col-lg-7 mt--30 mt-lg--30">
						<div class="product-details-info pl-lg--30 ">
							<h3 class="product-title" id="quickViewBookTitle">Örnek E-Kitap Başlığı</h3>
							<p class="author" id="quickViewBookAuthor">Yazar: <a href="#">Yazar Adı</a></p>
							<ul class="list-unstyled">
								<li>Yayıncı: <a href="#" class="list-value font-weight-bold" id="quickViewBookPublisher"> Yayıncı Adı</a></li>
								<li>Dil: <span class="list-value" id="quickViewBookLanguage"> Türkçe</span></li>
								<li>Sayfa Sayısı: <span class="list-value" id="quickViewBookPages"> 250</span></li>
							</ul>
							<div class="price-block">
								<span class="price-new" id="quickViewBookPriceNew">₺0.00</span>
								<del class="price-old" id="quickViewBookPriceOld" style="display:none;">₺0.00</del>
							</div>
							<div class="rating-widget">
								{{-- Ratings can be dynamic too --}}
								<div class="rating-block">
									<span class="fas fa-star star_on"></span>
									<span class="fas fa-star star_on"></span>
									<span class="fas fa-star star_on"></span>
									<span class="fas fa-star"></span>
									<span class="fas fa-star"></span>
								</div>
								<div class="review-widget">
									<a href="" id="quickViewBookReviewsLink">(0 Yorum)</a> <span>|</span>
									<a href="">Yorum Yaz</a>
								</div>
							</div>
							<article class="product-details-article">
								<h4 class="sr-only">E-Kitap Özeti</h4>
								<p id="quickViewBookDescription">E-kitap açıklaması buraya gelecek. Kısa ve ilgi çekici bir özet.</p>
							</article>
							<div class="product-actions-ebook-modal mt-3">
								<a href="#" class="btn btn-outlined--primary">Hemen Oku</a>
								<a href="#" class="btn btn-outlined--primary ms-2">İndir</a>
								<a href="#" class="btn btn-primary ms-2">Detayları Gör</a> {{-- Link to full product page --}}
							</div>
						</div>
					</div>
				</div>
			</div>
			{{-- Footer of modal for social share can remain as is or be removed if not needed --}}
		</div>
	</div>
</div>
<style>
    .product-actions-ebook-modal .btn {
        padding: 8px 15px;
    }
</style>
