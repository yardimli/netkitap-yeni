<div class="site-header header-3 d-none d-lg-block">
	<div class="header-middle pt--10 pb--10">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-3">
					<a href="{{ url('/') }}" class="site-brand">
						<img src="{{ asset('/images/netkitap.png') }}" alt="NetKitap Logo">
					</a>
				</div>
				<div class="col-lg-5">
					<div class="header-search-block">
						<input type="text" placeholder="Mağazada ara...">
						<button>Ara</button>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="main-navigation flex-lg-right">
						{{-- Removed cart widget and login block (moved login/register to top) --}}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="header-bottom">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-3">
					<nav class="category-nav ">
						<div>
							<a href="javascript:void(0)" class="category-trigger"><i class="fa fa-bars"></i>Kategorilere
								Göz At</a>
							@if(isset($allParentSubjects) && $allParentSubjects->count() > 0)
								<ul class="category-menu">
									@foreach($allParentSubjects as $parentSubject)
										<li
											class="cat-item {{ $parentSubject->subjects->count() > 0 ? 'has-children' : '' }} {{ $loop->index > 7 && $loop->remaining > 1 ? 'hidden-menu-item' : '' }}">
											<a href="javascript:void(0);">{{-- Parent subjects are not clickable as per requirement --}}
												{{ $parentSubject->name_tr ?? $parentSubject->name }}
											</a>
											@if($parentSubject->subjects->count() > 0)
												<ul class="sub-menu">
													@foreach($parentSubject->subjects as $subject)
														<li>
															<a href="{{ route('subjects.show', $subject) }}">
																{{ $subject->name_tr ?? $subject->name }}
															</a>
														</li>
													@endforeach
												</ul>
											@endif
										</li>
									@endforeach
									@if(isset($allParentSubjects) && $allParentSubjects->count() > 8)
										{{-- Example threshold --}}
										<li class="cat-item"><a href="#" class="js-expand-hidden-menu">Daha Fazla Kategori</a></li>
									@endif
								</ul>
							@else
								<p>Kategori bulunamadı.</p>
							@endif
						</div>
					</nav>
				</div>
				<div class="col-lg-9"> {{-- Adjusted width as phone support removed --}}
					<div class="main-navigation flex-lg-right">
						<ul class="main-menu menu-right li-last-0">
							<li class="menu-item">
								<a href="{{ url('/') }}">Ana Sayfa</a>
							</li>
							<li class="menu-item">
								<a href="#">Gözat</a> {{-- Link to shop/browse page --}}
							</li>
							<li class="menu-item">
								<a href="#">Blog</a> {{-- Link to blog page --}}
							</li>
							{{-- Removed compare, wishlist, my account dropdown, contact, checkout --}}
							@guest
								<li class="menu-item"><a href="{{ route('login') }}"><i class="icons-left fas fa-user"></i> Giriş
										Yap</a></li>
								<li class="menu-item"><a href="{{ route('register') }}"><i class="icons-left fas fa-user-plus"></i>
										Kayıt Ol</a></li>
							@else
								<li class="menu-item">
									<a href="{{ route('dashboard') }}"><i class="icons-left fas fa-user-circle"></i> Hesabım</a>
								</li>
								<li class="menu-item">
									<form method="POST" action="{{ route('logout') }}">
										@csrf
										<a href="{{ route('logout') }}"
										   onclick="event.preventDefault(); this.closest('form').submit();">
											<i class="icons-left fas fa-sign-out-alt"></i> Çıkış Yap
										</a>
									</form>
								</li>
							@endguest
						
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="site-mobile-menu">
	<header class="mobile-header d-block d-lg-none pt--10 pb-md--10">
		<div class="container">
			<div class="row align-items-sm-end align-items-center">
				<div class="col-md-4 col-7">
					<a href="{{ url('/') }}" class="site-brand">
						<img src="{{ asset('/images/netkitap.png') }}" alt="NetKitap Logo">
					</a>
				</div>
				<div class="col-md-5 order-3 order-md-2">
					<nav class="category-nav ">
						<div>
							<a href="javascript:void(0)" class="category-trigger"><i class="fa fa-bars"></i>Kategorilere
								Göz At</a>
							@if(isset($allParentSubjects) && $allParentSubjects->count() > 0)
								<ul class="category-menu">
									@foreach($allParentSubjects as $parentSubject)
										<li
											class="cat-item {{ $parentSubject->subjects->count() > 0 ? 'has-children' : '' }} {{ $loop->index > 7 && $loop->remaining > 1 ? 'hidden-menu-item' : '' }}">
											<a href="javascript:void(0);">{{-- Parent subjects are not clickable as per requirement --}}
												{{ $parentSubject->name_tr ?? $parentSubject->name }}
											</a>
											@if($parentSubject->subjects->count() > 0)
												<ul class="sub-menu">
													@foreach($parentSubject->subjects as $subject)
														<li>
															<a href="{{ route('subjects.show', $subject) }}">
																{{ $subject->name_tr ?? $subject->name }}
															</a>
														</li>
													@endforeach
												</ul>
											@endif
										</li>
									@endforeach
									@if(isset($allParentSubjects) && $allParentSubjects->count() > 8)
										{{-- Example threshold --}}
										<li class="cat-item"><a href="#" class="js-expand-hidden-menu">Daha Fazla Kategori</a></li>
									@endif
								</ul>
							@else
								<p>Kategori bulunamadı.</p>
							@endif
						</div>
					</nav>
				</div>
				<div class="col-md-3 col-5 order-md-3 text-right">
					<div class="mobile-header-btns header-top-widget">
						<ul class="header-links">
							{{-- Removed cart link --}}
							<li class="sin-link">
								<a href="javascript:" class="link-icon hamburgur-icon off-canvas-btn"><i class="ion-navicon"></i></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</header>
	<!--Off Canvas Navigation Start-->
	<aside class="off-canvas-wrapper">
		<div class="btn-close-off-canvas">
			<i class="ion-android-close"></i>
		</div>
		<div class="off-canvas-inner">
			<!-- search box start -->
			<div class="search-box offcanvas">
				<form>
					<input type="text" placeholder="Burada Ara">
					<button class="search-btn"><i class="ion-ios-search-strong"></i></button>
				</form>
			</div>
			<!-- search box end -->
			<!-- mobile menu start -->
			<div class="mobile-navigation">
				<!-- mobile menu navigation start -->
				<nav class="off-canvas-nav">
					<ul class="mobile-menu main-mobile-menu">
						<li><a href="{{ url('/') }}">Ana Sayfa</a></li>
						<li><a href="#">Gözat</a></li>
						<li><a href="#">Blog</a></li>
						@guest
							<li><a href="{{ route('login') }}">Giriş Yap</a></li>
							<li><a href="{{ route('register') }}">Kayıt Ol</a></li>
						@else
							<li><a href="{{ route('dashboard') }}">Hesabım</a></li>
							<li>
								<form method="POST" action="{{ route('logout') }}">
									@csrf
									<a href="{{ route('logout') }}"
									   onclick="event.preventDefault(); this.closest('form').submit();">
										Çıkış Yap
									</a>
								</form>
							</li>
						@endguest
					</ul>
				</nav>
				<!-- mobile menu navigation end -->
			</div>
			<!-- mobile menu end -->
			{{-- Removed currency, lang, my account from off-canvas nav block 2 --}}
			<div class="off-canvas-bottom">
				<div class="contact-list mb--10">
					<a href="tel:1234578790220" class="sin-contact"><i class="fas fa-mobile-alt"></i>(12345) 78790220</a>
					<a href="mailto:ornek@netkitap.com" class="sin-contact"><i class="fas fa-envelope"></i>ornek@netkitap.com</a>
				</div>
				<div class="off-canvas-social">
					<a href="#" class="single-icon"><i class="fab fa-facebook-f"></i></a>
					<a href="#" class="single-icon"><i class="fab fa-twitter"></i></a>
					<a href="#" class="single-icon"><i class="fas fa-rss"></i></a>
					<a href="#" class="single-icon"><i class="fab fa-youtube"></i></a>
					<a href="#" class="single-icon"><i class="fab fa-instagram"></i></a>
				</div>
			</div>
		</div>
	</aside>
	<!--Off Canvas Navigation End-->
</div>
<div class="sticky-init fixed-header common-sticky">
	<div class="container d-none d-lg-block">
		<div class="row align-items-center">
			<div class="col-lg-4">
				<a href="{{ url('/') }}" class="site-brand">
					<img src="{{ asset('/images/netkitap.png') }}" alt="NetKitap Logo">
				</a>
			</div>
			<div class="col-lg-8">
				<div class="main-navigation flex-lg-right">
					<ul class="main-menu menu-right ">
						<li class="menu-item">
							<a href="{{ url('/') }}">Ana Sayfa</a>
						</li>
						<li class="menu-item">
							<a href="#">Gözat</a>
						</li>
						<li class="menu-item">
							<a href="#">Blog</a>
						</li>
						@guest
							<li class="menu-item"><a href="{{ route('login') }}">Giriş Yap</a></li>
							<li class="menu-item"><a href="{{ route('register') }}">Kayıt Ol</a></li>
						@else
							<li class="menu-item has-children">
								<a href="javascript:void(0)">{{ Auth::user()->name }} <i class="fas fa-chevron-down dropdown-arrow"></i></a>
								<ul class="sub-menu">
									<li><a href="{{ route('dashboard') }}">Hesabım</a></li>
									<li>
										<form method="POST" action="{{ route('logout') }}">
											@csrf
											<a href="{{ route('logout') }}"
											   onclick="event.preventDefault(); this.closest('form').submit();">
												Çıkış Yap
											</a>
										</form>
									</li>
								</ul>
							</li>
						@endguest
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
