@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center fw-bold mb-4" style="font-family: 'Poppins', sans-serif; color: #2c3e50;">
        🐾 Bài Viết Mới Về Thú Cưng 🐶🐱
    </h1>

    <!-- Bộ lọc danh mục -->
    <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
        <a href="{{ route('post.index') }}"
            class="btn {{ request('category_id') ? 'btn-outline-secondary' : 'btn-primary' }}">
            Tất cả
        </a>
        @foreach($categories as $category)
        <a href="{{ route('post.index', ['category_id' => $category->id]) }}"
            class="btn {{ request('category_id') == $category->id ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $category->name }}
        </a>
        @endforeach
    </div>

    <!-- Danh sách bài viết -->
    <div class="row g-4">
        @foreach($posts as $post)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                <div style="height: 160px; overflow: hidden;">
                    <img src="{{ asset('storage/' . $post->image) }}" class="w-100 h-100" alt="{{ $post->title }}"
                        style="object-fit: cover;">
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-success fw-bold">{{ $post->title }}</h5>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-calendar-event"></i> {{ $post->created_at->format('d/m/Y') }}
                    </p>
                    <a href="{{ route('post.show', $post->id) }}" class="btn btn-success btn-sm mt-auto">
                        📖 Đọc bài viết
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-5">
        {{ $posts->links() }}
    </div>
</div>
@endsection
@push('styles')
<style>
.card-title {
    font-size: 1.2rem;
}

.btn {
    text-transform: capitalize;
    font-weight: 600;
    border-radius: 30px;
}
</style>
@endpush