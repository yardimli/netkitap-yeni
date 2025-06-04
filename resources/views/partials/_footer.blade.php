<!--================================= Footer Area ===================================== -->
<footer class="site-footer">
	<div class="container">
		<div class="row justify-content-between section-padding">
			<div class=" col-xl-3 col-lg-4 col-sm-6">
				<div class="single-footer pb--40">
					<div class="brand-footer footer-title">
						<img src="{{ asset('/images/netkitap.png') }}" alt="NetKitap Footer Logo">
					</div>
					<div class="footer-contact">
						<p><span class="label">E-posta:</span><span class="text">destek@netkitap.com</span></p>
						<p>NetKitap, binlerce e-kitaba anında erişim sunar.</p> {{-- Added tagline --}}
					</div>
				</div>
			</div>
			<div class=" col-xl-3 col-lg-2 col-sm-6">
				<div class="single-footer pb--40">
					<div class="footer-title">
						<h3>Bilgilendirme</h3>
					</div>
					<ul class="footer-list normal-list">
						<li><a href="#">Yeni Çıkan E-Kitaplar</a></li> {{-- Update link --}}
						<li><a href="#">Çok Okunan E-Kitaplar</a></li> {{-- Update link --}}
						<li><a href="#">Kategoriler</a></li> {{-- Update link --}}
						<li><a href="#">Bize Ulaşın</a></li>
					</ul>
				</div>
			</div>
			<div class=" col-xl-3 col-lg-2 col-sm-6">
				<div class="single-footer pb--40">
					<div class="footer-title">
						<h3>Ekstralar</h3>
					</div>
					<ul class="footer-list normal-list">
						<li><a href="#">Hakkımızda</a></li>
						<li><a href="#">Gizlilik Politikası</a></li> {{-- Added --}}
						<li><a href="#">Kullanım Şartları</a></li> {{-- Added --}}
						<li><a href="#">Site Haritası</a></li>
					</ul>
				</div>
			</div>
			<div class=" col-xl-3 col-lg-4 col-sm-6">
				<div class="footer-title">
					<h3>Bülten Aboneliği</h3>
				</div>
				<div class="newsletter-form mb--30">
					<form action="#"> {{-- Update action if needed --}}
						<input type="email" class="form-control" placeholder="E-posta Adresinizi Girin...">
						<button class="btn btn--primary w-100">Abone Ol</button>
					</form>
				</div>
				<div class="social-block">
					<h3 class="title">BAĞLANTIDA KALIN</h3>
					<ul class="social-list list-inline">
						<li class="single-social facebook"><a href="#"><i class="ion ion-social-facebook"></i></a></li>
						<li class="single-social twitter"><a href="#"><i class="ion ion-social-twitter"></i></a></li>
						<li class="single-social google"><a href="#"><i class="ion ion-social-googleplus-outline"></i></a></li>
						<li class="single-social youtube"><a href="#"><i class="ion ion-social-youtube"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="container">
			<p class="copyright-text">Telif Hakkı © {{ date('Y') }} <a href="{{ route('home') }}" class="author">NetKitap</a>. Tüm Hakları Saklıdır. E-Kitap Platformu.</p>
		</div>
	</div>
</footer>
