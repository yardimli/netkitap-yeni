@if ($paginator->hasPages())
	<div class="row pt--30">
		<div class="col-md-12">
			<nav aria-label="Sayfalar">
				{{ $paginator->links() }} {{-- This will use Bootstrap 5 styled pagination by default due to AppServiceProvider change --}}
			</nav>
		</div>
	</div>
@endif
