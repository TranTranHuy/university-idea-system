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
                        <div class="me-3">
                            {{-- LOGIC ADMIN NHÌN THẤU BÀI VIẾT ẨN DANH --}}
                            @if($idea->is_anonymous)
                                <span class="text-muted fst-italic"><i class="bi bi-incognito"></i> Anonymous</span>
                                @auth
                                    @if(Auth::user()->role_id == 1)
                                        <div class="text-danger mt-1" style="font-size: 0.8rem;">
                                            <i class="bi bi-eye-fill"></i> Real Author: <strong>{{ $idea->user->full_name ?? $idea->user->name ?? 'Unknown' }}</strong>
                                        </div>
                                    @endif
                                @endauth
                            @else
                                <span class="fw-bold text-dark">{{ $idea->user->full_name ?? $idea->user->name ?? 'Unknown' }}</span>
                            @endif
                            {{-- KẾT THÚC LOGIC ADMIN --}}
                        </div>

                        <div class="ms-auto">
                            <i class="bi bi-clock me-1"></i> {{ $idea->created_at->format('d M, Y H:i') }}
                        </div>
                    </div>

                    {{-- Nội dung chính --}}
                    <div class="idea-content fs-5 text-secondary" style="white-space: pre-line;">
                        {{ $idea->content }}
                    </div>

                    {{-- 👇 CHÈN THÊM KHỐI HIỂN THỊ FILE ĐÍNH KÈM Ở ĐÂY 👇 --}}
                    @php
                        $documents = is_string($idea->document) ? json_decode($idea->document, true) : $idea->document;
                    @endphp
                    @if(!empty($documents) && is_array($documents))
                        <div class="mt-4 p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-paperclip me-1"></i> Attached Documents</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($documents as $file)
                                    <a href="{{ asset('storage/' . $file) }}" download class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-arrow-down-fill text-primary"></i>
                                        <span>{{ basename($file) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    {{-- 👆 KẾT THÚC KHỐI HIỂN THỊ FILE 👆 --}}

                    <hr class="my-4">

                    {{-- 3. KHU VỰC TƯƠNG TÁC (LIKE / DISLIKE) --}}
                    <div class="d-flex gap-2 align-items-center">
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
                    <h5 class="mb-0 fw-bold"><i class="bi bi-chat-dots me-2"></i>Comments ({{ $idea->comments->count() }})</h5>
                </div>
                <div class="card-body">

                    {{-- Kiểm tra đăng nhập và hạn chót bình luận --}}
                    @auth
                        @php
                            $canComment = true;
                            if ($idea->academicYear && now() > $idea->academicYear->final_closure_date) {
                                $canComment = false;
                            }
                        @endphp

                        @if($canComment)
                            {{-- Form viết bình luận --}}
                            <form action="{{ route('comments.store', $idea->id) }}" method="POST" class="mb-4">
                                @csrf
                                <div class="input-group mb-2">
                                    <input type="text" name="content" class="form-control rounded-pill bg-light" placeholder="Write a comment..." required>
                                    <button class="btn btn-primary rounded-pill ms-2 px-4" type="submit">
                                        <i class="bi bi-send-fill"></i>
                                    </button>
                                </div>
                                <div class="form-check form-switch ms-2">
                                    <input class="form-check-input" type="checkbox" name="is_anonymous" id="anon-comment-{{ $idea->id }}" value="1" role="switch">
                                    <label class="form-check-label text-muted small" for="anon-comment-{{ $idea->id }}" style="cursor: pointer;">Comment anonymously</label>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-warning text-center py-2 mb-4" role="alert">
                                <i class="bi bi-lock-fill"></i> Comments are closed for this semester.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info py-2 mb-4 text-center" role="alert">
                            <i class="bi bi-info-circle me-1"></i> <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Login</a> to leave a comment.
                        </div>
                    @endauth

                    {{-- Danh sách bình luận cũ --}}
                    <div class="comment-list">
                        @forelse($idea->comments as $comment)
                            <div class="d-flex mb-3">
                                {{-- Avatar (Ẩn danh thì hiện chữ A) --}}
                                <div class="avatar bg-light text-secondary rounded-circle me-2 d-flex justify-content-center align-items-center border" style="width: 40px; height: 40px; font-weight: bold;">
                                    {{ $comment->is_anonymous ? 'A' : substr($comment->user->full_name ?? $comment->user->name ?? 'U', 0, 1) }}
                                </div>

                                <div class="bg-light p-3 rounded-4 w-100">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            {{-- LOGIC ADMIN NHÌN THẤU BÌNH LUẬN ẨN DANH --}}
                                            @if($comment->is_anonymous)
                                                <strong class="small text-muted fst-italic"><i class="bi bi-incognito"></i> Anonymous</strong>
                                                @auth
                                                    @if(Auth::user()->role_id == 1)
                                                        <span class="text-danger ms-1 d-block d-md-inline" style="font-size: 0.75rem;" title="Admin View">
                                                            (<i class="bi bi-eye-fill"></i> Real: {{ $comment->user->full_name ?? $comment->user->name ?? 'Unknown' }})
                                                        </span>
                                                    @endif
                                                @endauth
                                            @else
                                                <strong class="small text-dark">{{ $comment->user->full_name ?? $comment->user->name ?? 'User' }}</strong>
                                            @endif
                                            {{-- KẾT THÚC LOGIC ADMIN --}}
                                        </div>
                                        <small class="text-muted text-nowrap ms-2" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-0 mt-2 text-dark">{{ $comment->content }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-chat-square-text fs-2 d-block mb-2 text-light"></i>
                                No comments yet. Be the first to discuss!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
