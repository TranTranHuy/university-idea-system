@extends('layouts.master')

@section('content')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
    [x-cloak] { display: none !important; }
    body { background-color: #f0f2f5; }

    .idea-card-square {
        min-height: 360px;
        height: auto;
        width: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s;
        background: white;
    }

    .idea-card-square:hover {
        transform: translateY(-5px);
    }

    .idea-content-box {
        overflow-y: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .expanded-box {
        -webkit-line-clamp: unset !important;
        overflow-y: visible;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card mb-5 border-0 shadow-sm p-3 rounded-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                        {{ substr(Auth::user()->full_name ?? 'U', 0, 1) }}
                    </div>
                    <a href="{{ route('ideas.create') }}" class="btn btn-light w-100 text-start rounded-pill text-muted shadow-none py-2 px-4">
                        Bạn có idea gì mới?
                    </a>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-3">
                @forelse($ideas as $idea)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm idea-card-square rounded-4">
                            <div class="card-body d-flex flex-column p-3">

                                <div class="d-flex justify-content-between mb-2">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        <img src="https://ui-avatars.com/api/?name={{ $idea->is_anonymous ? 'A' : ($idea->user->full_name ?? 'U') }}&background=random" class="rounded-circle me-2" width="35" height="35">
                                        <div class="text-truncate">
                                            <strong class="d-block small text-truncate">{{ $idea->is_anonymous ? 'Người dùng ẩn danh' : ($idea->user->full_name ?? 'Không tên') }}</strong>
                                            <small class="text-muted" style="font-size: 0.7rem;">{{ $idea->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-info-subtle text-info rounded-pill px-2 py-1 align-self-start" style="font-size: 0.65rem;">{{ $idea->category->name ?? 'Chung' }}</span>
                                </div>

                                <h6 class="fw-bold text-dark text-truncate mb-1">{{ $idea->title }}</h6>

                                <div x-data="{ expanded: false }" class="flex-grow-1 mb-2">
                                    <div class="text-secondary small idea-content-box" :class="expanded ? 'expanded-box' : ''" style="white-space: pre-line; font-size: 0.85rem;">
                                        <span x-show="!expanded">{{ Str::limit($idea->content, 90) }}</span>
                                        <span x-show="expanded" x-cloak>{{ $idea->content }}</span>
                                    </div>
                                    @if(strlen($idea->content) > 90)
                                        <button @click="expanded = !expanded" class="btn btn-link p-0 fw-bold text-decoration-none small" style="font-size: 0.75rem;">
                                            <span x-show="!expanded">Xem thêm</span>
                                            <span x-show="expanded" x-cloak>Thu gọn</span>
                                        </button>
                                    @endif
                                </div>

                                @if($idea->document)
                                    @php
                                        // Tự động ép kiểu về mảng để xử lý cho dù data là gì
                                        $files = is_array($idea->document) ? $idea->document : (json_decode($idea->document, true) ?: [$idea->document]);
                                    @endphp
                                    <div class="mt-auto mb-2 py-1 px-2 bg-light border rounded">
                                        <small class="text-muted d-block mb-1" style="font-size: 0.65rem;"><i class="bi bi-paperclip"></i> Tài liệu ({{ count((array)$files) }})</small>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach((array)$files as $index => $file)
                                                @if($file)
                                                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="badge bg-white text-primary border text-decoration-none py-1 px-2 shadow-sm" style="font-size: 0.65rem;">
                                                    File {{ $index + 1 }} <i class="bi bi-download ms-1"></i>
                                                </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-auto"></div>
                                @endif

                                <div class="d-flex align-items-center gap-3 border-top pt-2">
                                    <form action="{{ route('ideas.like', ['id' => $idea->id, 'type' => 1]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 text-decoration-none d-flex align-items-center {{ Auth::user() && $idea->likes->where('user_id', Auth::id())->where('type', 1)->first() ? 'text-primary' : 'text-muted' }}">
                                            <i class="bi bi-hand-thumbs-up{{ Auth::user() && $idea->likes->where('user_id', Auth::id())->where('type', 1)->first() ? '-fill' : '' }} me-1"></i>
                                            <span class="small">{{ $idea->likes->where('type', 1)->count() }}</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('ideas.like', ['id' => $idea->id, 'type' => 0]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 text-decoration-none d-flex align-items-center {{ Auth::user() && $idea->likes->where('user_id', Auth::id())->where('type', 0)->first() ? 'text-danger' : 'text-muted' }}">
                                            <i class="bi bi-hand-thumbs-down{{ Auth::user() && $idea->likes->where('user_id', Auth::id())->where('type', 0)->first() ? '-fill' : '' }} me-1"></i>
                                            <span class="small">{{ $idea->likes->where('type', 0)->count() }}</span>
                                        </button>
                                    </form>

                                    <button class="btn btn-link p-0 text-decoration-none text-muted d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#comments-{{ $idea->id }}">
                                        <i class="bi bi-chat-dots me-1"></i>
                                        <span class="small">{{ $idea->comments->count() }}</span>
                                    </button>
                                </div>

                                <div class="collapse" id="comments-{{ $idea->id }}">
                                    <div class="mt-2 p-2 bg-light rounded" style="max-height: 120px; overflow-y: auto;">
                                        @foreach($idea->comments as $comment)
                                            <div class="small mb-1 border-bottom pb-1">
                                                <strong style="font-size: 0.7rem;">{{ $comment->is_anonymous ? 'Ẩn danh' : ($comment->user->full_name ?? 'User') }}:</strong>
                                                <span style="font-size: 0.75rem;">{{ $comment->content }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <form action="{{ route('ideas.comment', $idea->id) }}" method="POST" class="mt-2 pb-2">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="content" class="form-control" placeholder="Viết bình luận..." required>
                                            <button class="btn btn-primary px-2" type="submit">Gửi</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Chưa có ý tưởng nào được chia sẻ.</p>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center my-5">
                {{ $ideas->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
