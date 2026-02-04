@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- 1. NÚT QUAY LẠI --}}
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary mb-3 border-0">
                <i class="bi bi-arrow-left"></i> Back to Dashboard
            </a>

            {{-- 2. NỘI DUNG IDEA --}}
            <div class="card shadow-sm rounded-4 border-0 mb-4">
                <div class="card-body p-4">
                    {{-- Category Badge --}}
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-2 px-3 py-2 rounded-pill">
                        {{ $idea->category->name ?? 'Uncategorized' }}
                    </span>

                    {{-- Tiêu đề --}}
                    <h2 class="fw-bold text-dark mt-2">{{ $idea->title }}</h2>

                    {{-- Tác giả & Ngày tháng --}}
                    <div class="d-flex align-items-center text-muted small mb-4 mt-3">
                        @if($idea->is_anonymous)
                            <div class="avatar bg-secondary text-white rounded-circle me-2 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-incognito"></i>
                            </div>
                            <span class="me-3">Anonymous</span>
                        @else
                            <div class="avatar bg-primary text-white rounded-circle me-2 d-flex justify-content-center align-items-center" style="width: 32px; height: 32px; font-weight: bold;">
                                {{ substr($idea->user->full_name ?? 'U', 0, 1) }}
                            </div>
                            <span class="me-3 fw-bold">{{ $idea->user->full_name ?? $idea->user->name }}</span>
                        @endif

                        <i class="bi bi-clock me-1"></i> {{ $idea->created_at->format('d M, Y H:i') }}
                    </div>

                    {{-- Nội dung chính --}}
                    <div class="idea-content fs-5 text-secondary" style="white-space: pre-line;">
                        {{ $idea->content }}
                    </div>

                    <hr class="my-4">

                    {{-- 3. KHU VỰC TƯƠNG TÁC (LIKE / DISLIKE) --}}
                    <div class="d-flex gap-2">
                        {{-- Nút Like --}}
                        <a href="{{ route('idea.like', ['id' => $idea->id, 'type' => 'like']) }}" class="btn btn-outline-success rounded-pill px-4">
                            <i class="bi bi-hand-thumbs-up-fill"></i> Like
                            <span class="fw-bold ms-1">{{ $idea->likes_count ?? 0 }}</span>
                        </a>

                        {{-- Nút Dislike --}}
                        <a href="{{ route('idea.like', ['id' => $idea->id, 'type' => 'dislike']) }}" class="btn btn-outline-danger rounded-pill px-4">
                            <i class="bi bi-hand-thumbs-down-fill"></i> Dislike
                            <span class="fw-bold ms-1">{{ $idea->dislikes_count ?? 0 }}</span>
                        </a>

                        <span class="ms-auto text-muted align-self-center">
                            <i class="bi bi-eye"></i> {{ $idea->views ?? 0 }} Views
                        </span>
                    </div>
                </div>
            </div>

            {{-- 4. KHU VỰC BÌNH LUẬN --}}
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-chat-dots me-2"></i>Comments</h5>
                </div>
                <div class="card-body">
                    {{-- Form viết bình luận --}}
                    <form action="{{ route('comments.store', $idea->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="content" class="form-control rounded-pill bg-light" placeholder="Write a comment..." required>
                            <button class="btn btn-primary rounded-pill ms-2 px-4" type="submit">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>

                    {{-- Danh sách bình luận cũ --}}
                    <div class="comment-list">
                        @forelse($idea->comments as $comment)
                            <div class="d-flex mb-3">
                                <div class="avatar bg-light text-secondary rounded-circle me-2 d-flex justify-content-center align-items-center border" style="width: 40px; height: 40px;">
                                    {{ substr($comment->user->full_name ?? 'U', 0, 1) }}
                                </div>
                                <div class="bg-light p-3 rounded-4 w-100">
                                    <div class="d-flex justify-content-between">
                                        <strong class="small">{{ $comment->user->full_name ?? $comment->user->name }}</strong>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0 mt-1 text-dark">{{ $comment->content }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-muted py-3">No comments yet. Be the first to discuss!</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
