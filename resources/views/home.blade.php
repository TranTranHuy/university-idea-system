@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                        {{ substr(Auth::user()->full_name ?? 'U', 0, 1) }}
                    </div>
                    <a href="{{ route('ideas.create') }}" class="btn btn-light w-100 text-start rounded-pill text-muted shadow-none">
                        Bạn có idea gì mới, {{ Auth::user()->full_name ?? '...' }}?
                    </a>
                </div>
            </div>

            @forelse($ideas as $idea)
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ $idea->is_anonymous ? 'A' : ($idea->user->full_name ?? 'U') }}&background=random" class="rounded-circle me-2" width="40">
                                <div>
                                    <strong class="d-block">{{ $idea->is_anonymous ? 'Người dùng ẩn danh' : ($idea->user->full_name ?? 'Không tên') }}</strong>
                                    <small class="text-muted">{{ $idea->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <span class="badge bg-info-subtle text-info rounded-pill px-3">{{ $idea->category->name ?? 'Chung' }}</span>
                        </div>

                        <h5 class="fw-bold">{{ $idea->title }}</h5>
                        <p class="text-secondary">{{ $idea->content }}</p>

                        @if($idea->document)
                            <div class="mt-3 p-2 border rounded bg-light d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-arrow-down text-primary fs-4 me-2"></i>
                                    <div>
                                        <div class="small fw-bold text-dark text-truncate" style="max-width: 250px;">
                                            {{ basename($idea->document) }}
                                        </div>
                                        <small class="text-muted">Tài liệu đính kèm</small>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $idea->document) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-primary px-3 shadow-sm">
                                    <i class="bi bi-eye"></i> Xem / Tải về
                                </a>
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-4 border-top pt-2 mt-3">
    <form action="{{ route('ideas.like', ['id' => $idea->id, 'type' => 1]) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-link p-0 text-decoration-none d-flex align-items-center {{ Auth::user() && $idea->likes->where('user_id', Auth::id())->where('type', 1)->first() ? 'text-primary' : 'text-muted' }}">
            <i class="bi bi-hand-thumbs-up{{ Auth::user() && $idea->likes->where('user_id', Auth::id())->where('type', 1)->first() ? '-fill' : '' }} me-1"></i>
            <span>{{ $idea->likes->where('type', 1)->count() }}</span>
        </button>
    </form>

    <form action="{{ route('ideas.like', ['id' => $idea->id, 'type' => 0]) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-link p-0 text-decoration-none d-flex align-items-center {{ Auth::user() && $idea->likes->where('user_id', Auth::id())->where('type', 0)->first() ? 'text-danger' : 'text-muted' }}">
            <i class="bi bi-hand-thumbs-down{{ Auth::user() && $idea->likes->where('user_id', Auth::id())->where('type', 0)->first() ? '-fill' : '' }} me-1"></i>
            <span>{{ $idea->likes->where('type', 0)->count() }}</span>
        </button>
    </form>

    <button class="btn btn-link p-0 text-decoration-none text-muted d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#comments-{{ $idea->id }}">
        <i class="bi bi-chat-dots me-1"></i>
        <span>{{ $idea->comments->count() }}</span>
    </button>
</div>

                        <div class="collapse mt-3" id="comments-{{ $idea->id }}">
                            <hr>
                            <div class="comment-list mb-3">
                                @foreach($idea->comments as $comment)
                                    <div class="d-flex mb-2">
                                        <img src="https://ui-avatars.com/api/?name={{ $comment->user->full_name ?? 'Người dùng đã bị xóa' }}&size=30" class="rounded-circle me-2" width="30" height="30">
                                        <div class="bg-light rounded p-2 flex-grow-1">
                                            <strong class="small d-block">{{ $comment->user->full_name ?? 'Người dùng ẩn danh' }}</strong>
                                            <span class="small text-secondary">{{ $comment->content }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <form action="{{ route('ideas.comment', $idea->id) }}" method="POST">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="text" name="content" class="form-control" placeholder="Viết bình luận..." required>
                                    <button class="btn btn-primary" type="submit">Gửi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">Chưa có ý tưởng nào được chia sẻ.</p>
            @endforelse

            <div class="d-flex justify-content-center">
                {{ $ideas->links() }}
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-white fw-bold border-0 py-3">Bộ lọc</div>
                <div class="list-group list-group-flush border-top">
                    <a href="{{ url('/?sort=latest') }}" class="list-group-item list-group-item-action">Mới nhất</a>
                    <a href="{{ url('/?sort=popular') }}" class="list-group-item list-group-item-action">Phổ biến nhất</a>
                    <a href="{{ url('/?sort=trending') }}" class="list-group-item list-group-item-action">Xu hướng</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
