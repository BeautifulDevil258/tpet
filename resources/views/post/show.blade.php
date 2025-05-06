@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Nội dung bài viết -->
        <div class="col-lg-8">
            <article class="card border-0 shadow-sm rounded-4 mb-4">
                @if($post->image)
                <div class="text-center mb-4">
                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}"
                        class="img-fluid rounded shadow-sm" style="max-height: 350px; width: auto;">
                </div>
                @endif
                <div class="card-body px-4 py-4">
                    <h1 class="fw-bold mb-3 fs-3">{{ $post->title }}</h1>
                    <div class="text-muted mb-3 small">
                        📂
                        <a href="{{ route('posts.index', ['category_id' => $post->category_id]) }}"
                            class="text-decoration-none fw-semibold text-dark">
                            {{ $post->category->name }}
                        </a>
                        &nbsp; | 🕒 {{ $post->created_at->format('d/m/Y') }}
                    </div>

                    <hr>

                    <div class="post-content mt-4" style="line-height: 1.8;">
                        {!! $post->content !!}
                    </div>
                </div>
            </article>
        </div>

        <!-- Bài viết liên quan -->
        <div class="col-lg-4">
            <h4 class="mb-4 fw-semibold">📰 Bài viết liên quan</h4>

            @foreach($relatedPosts as $related)
            <a href="{{ route('post.show', $related->id) }}" class="text-decoration-none text-dark">
                <div class="card mb-3 border-0 shadow-sm rounded-3 hover-shadow">
                    <div class="row g-0">
                        <div class="col-4">
                            <img src="{{ asset('storage/' . $related->image) }}"
                                class="img-fluid rounded-start h-100 object-fit-cover" alt="{{ $related->title }}">
                        </div>
                        <div class="col-8">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1 fw-semibold">{{ $related->title }}</h6>
                                <p class="text-muted small mb-0">{{ $related->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Làm ảnh bài viết chính vừa gọn vừa đẹp */
.ratio-16x9 {
    max-height: 350px;
}

/* Hover card nhỏ */
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    transition: 0.3s ease;
}

/* Ảnh nhỏ ở sidebar liên quan */
.object-fit-cover {
    object-fit: cover;
    height: 100px;
    width: 100%;
}
</style>
@endpush