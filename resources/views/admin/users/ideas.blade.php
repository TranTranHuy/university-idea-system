@extends('layouts.admin')

@section('admin_content')
<div class="container py-4">

    {{-- Phần Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
                <i class="bi bi-arrow-left me-1"></i> Back to User Management
            </a>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-lightbulb-fill text-warning me-2"></i> Ideas by {{ $user->full_name }}
            </h3>
            <p class="text-muted mb-0">Viewing all ideas submitted by this user, including anonymous posts.</p>
        </div>
    </div>

    {{-- Lưới hiển thị các Idea (Chia 3 cột trên màn hình lớn) --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

        @forelse($ideas as $idea)
        <div class="col">
            <div class="card border-0 shadow-sm h-100 rounded-4" style="transition: transform 0.2s;">
                <div class="card-body p-4">

                    {{-- Header của thẻ Card: Avatar + Tên + Category --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            {{-- Avatar --}}
                            @php
                                // Chỉnh màu Avatar ngẫu nhiên hoặc theo trạng thái ẩn danh
                                $bgColor = $idea->is_anonymous ? 'bg-secondary' : 'bg-primary';
                                $avatarText = $idea->is_anonymous ? 'A' : strtoupper(substr($user->full_name, 0, 2));
                            @endphp
                            <div class="{{ $bgColor }} text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-weight: bold;">
                                {{ $avatarText }}
                            </div>

                            {{-- Tên & Thời gian --}}
                            <div>
                                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                    {{-- Dù ẩn danh thì Admin vẫn thấy tên gốc, nhưng báo cho Admin biết bài này đang ẩn --}}
                                    @if($idea->is_anonymous)
                                        <span class="text-secondary">Anonymous</span>
                                        <span class="badge bg-danger bg-opacity-10 text-danger ms-2" style="font-size: 0.6rem;" title="Admin can see this is {{ $user->full_name }}">
                                            <i class="bi bi-incognito"></i> Real: {{ $user->full_name }}
                                        </span>
                                    @else
                                        {{ $user->full_name }}
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $idea->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        {{-- Badge Category --}}
                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 fw-medium">
                            {{ $idea->category->name ?? 'Category' }}
                        </span>
                    </div>

                    {{-- Body: Tiêu đề & Mô tả ngắn --}}
                    <h5 class="card-title fw-bold text-dark mt-4 mb-2">{{ $idea->title }}</h5>
                    <p class="card-text text-muted mb-2" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $idea->description }}
                    </p>
                    <a href="{{ route('ideas.show', $idea->id) }}" class="text-primary fw-bold text-decoration-none small">See more</a>
                </div>

                {{-- Footer: Lượt Like / Dislike / Comment --}}
                <div class="card-footer bg-white border-top-0 px-4 pb-4 pt-0">
                    <hr class="text-muted opacity-25 mt-0 mb-3">
                    <div class="d-flex text-muted gap-4">
                        <span class="d-flex align-items-center"><i class="bi bi-hand-thumbs-up me-2 fs-5"></i> 0</span>
                        <span class="d-flex align-items-center"><i class="bi bi-hand-thumbs-down me-2 fs-5"></i> 0</span>
                        <span class="d-flex align-items-center"><i class="bi bi-chat-dots me-2 fs-5"></i> 0</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        {{-- Trạng thái trống nếu user chưa đăng bài nào --}}
        <div class="col-12 w-100 text-center py-5">
            <i class="bi bi-lightbulb-off text-muted opacity-50 d-block mb-3" style="font-size: 4rem;"></i>
            <h5 class="text-muted fw-bold">No ideas found</h5>
            <p class="text-muted">This user hasn't posted any ideas yet.</p>
        </div>
        @endforelse

    </div>

    {{-- Phân trang --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $ideas->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection
