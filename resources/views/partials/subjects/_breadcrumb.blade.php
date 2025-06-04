<section class="breadcrumb-section">
    <h2 class="sr-only">Site Breadcrumb</h2>
    <div class="container">
        <div class="breadcrumb-contents">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Ana Sayfa</a></li>
                    @if(isset($currentSubject) && $currentSubject)
                        @if($currentSubject->parentSubject)
                        {{-- Parent subject is not directly clickable to filter, but can be part of breadcrumb --}}
                        <li class="breadcrumb-item"><a href="javascript:void(0)">{{ $currentSubject->parentSubject->name_tr ?? $currentSubject->parentSubject->name }}</a></li>
                        @endif
                        <li class="breadcrumb-item active">{{ $currentSubject->name_tr ?? $currentSubject->name }}</li>
                    @else
                        <li class="breadcrumb-item active">Tüm Kitaplar</li>
                    @endif
                </ol>
            </nav>
        </div>
    </div>
</section>
