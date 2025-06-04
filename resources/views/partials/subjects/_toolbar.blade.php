<div class="shop-toolbar with-sidebar mb--30">
	<div class="row align-items-center">
		{{-- Removed Product View Mode Section --}}
		{{-- <div class="col-lg-2 col-md-2 col-sm-6"> ... </div> --}}
		
		<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12"> {{-- Adjusted from col-xl-4 col-md-4 col-sm-6, removed mt--10 mt-sm--0 as it's first now --}}
			<span class="toolbar-status">
                @if($books->total() > 0)
					{{ $books->firstItem() }}-{{ $books->lastItem() }} arası. Toplam {{ $books->total() }} ({{ $books->lastPage() }} sayfa)
				@else
					{{ $currentSubjectName ?? 'Seçili kategoride' }} hiç ürün bulunamadı.
				@endif
            </span>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6 mt--10 mt-md--0"> {{-- Adjusted from col-lg-2 col-md-2 --}}
			<div class="sorting-selection">
				<label for="show-count" class="d-none d-sm-inline">Göster:</label>
				<select class="form-control nice-select sort-select" id="show-count" onchange="window.location.href = this.value;">
					@php $perPageOptions = [9, 15, 24, 30]; @endphp
					@foreach($perPageOptions as $option)
						<option value="{{ request()->fullUrlWithQuery(['perPage' => $option, 'page' => 1]) }}"
							{{ $books->perPage() == $option ? 'selected' : '' }}>{{ $option }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6 mt--10 mt-md--0 "> {{-- Adjusted from col-lg-4 col-md-4 --}}
			<div class="sorting-selection">
				<label for="sort-by" class="d-none d-sm-inline">Sırala:</label>
				<select class="form-control nice-select sort-select mr-0" id="sort-by" onchange="window.location.href = this.value;">
					<option value="{{ request()->fullUrlWithQuery(['sortBy' => 'default', 'sortDir' => 'desc', 'page' => 1]) }}"
						{{ (request('sortBy', 'default') == 'default' || (request('sortBy') == 'created_at' && request('sortDir') == 'desc')) ? 'selected' : '' }}>Yeni Eklenenler</option>
					<option value="{{ request()->fullUrlWithQuery(['sortBy' => 'name', 'sortDir' => 'asc', 'page' => 1]) }}"
						{{ request('sortBy') == 'name' && request('sortDir') == 'asc' ? 'selected' : '' }}>İsim (A - Z)</option>
					<option value="{{ request()->fullUrlWithQuery(['sortBy' => 'name', 'sortDir' => 'desc', 'page' => 1]) }}"
						{{ request('sortBy') == 'name' && request('sortDir') == 'desc' ? 'selected' : '' }}>İsim (Z - A)</option>
				</select>
			</div>
		</div>
	</div>
</div>
