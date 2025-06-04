<!DOCTYPE html>
<html lang="tr"> <head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>@yield('title', 'NetKitap - Kitap Mağazası')</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Use Minified Plugins Version For Fast Page Load -->
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/plugins.css') }}" />
	<link rel="stylesheet" type="text/css" media="screen" href="{{ asset('css/main.css') }}" />
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('/images/favicon.ico') }}">
	@stack('styles')
</head>
<body>
<div class="site-wrapper" id="top">
	@include('partials._header')
	
	@yield('content')
	
	@include('partials._modal_quick_view')
	
	@include('partials._footer')
</div>

<!-- Use Minified Plugins Version For Fast Page Load -->
<script src="{{ asset('js/plugins.js') }}"></script>
<script src="{{ asset('js/ajax-mail.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
@stack('scripts')
</body>
</html>
