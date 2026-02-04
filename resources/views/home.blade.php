@extends('layouts.master')

@section('content')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
    [x-cloak] { display: none !important; }
    body { background-color: #f0f2f5; }
    .idea-card-square { min-height: 380px; height: auto; width: 100%; display: flex; flex-direction: column; transition: transform 0.2s; background: white; }
    .idea-card-square:hover { transform: translateY(-5px); }
    .idea-content-box { overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; }
    .expanded-box { -webkit-line-clamp: unset !important; overflow-y: visible; }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Header tạo Idea --}}
            <div class="card mb-5 border-0 shadow-sm p-3 rounded-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                        {{ substr(Auth::user()->full_name ?? 'U', 0, 1) }}
                    </div>
                    <a href="{{ route('ideas.create') }}" class="btn btn-light w-100 text-start rounded-pill text-muted shadow-none py-2 px-4">
                        Do you have any new ideas?
                    </a>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-3">
                @forelse($ideas as $idea)
                    <div class="col">
                        {{-- Khởi tạo AlpineJS cho mỗi card --}}
                        <div class="card h-100 border-0 shadow-sm idea-card-square rounded-4" x-data="{ showLoginAlert: false }">
                            <div class="card-body d-flex flex-column p-3">
                                {{-- User Info --}}
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        <img src="https://ui-avatars.com/api/?name={{ $idea->is_anonymous ? 'A' : ($idea->user->full_name ?? 'U') }}&background=random" class="rounded-circle me-2" width="35" height="35">
                                        <div class="text-truncate">
                                            <strong class="d-block small text-truncate">{{ $idea->is_anonymous ? 'Anonymous' : ($idea->user->full_name ?? 'Không tên') }}</strong>
                                            <small class="text-muted" style="font-size: 0.7rem;">{{ $idea->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-info-subtle text-info rounded-pill px-2 py-1 align-self-start" style="font-size: 0.65rem;">{{ $idea->category->name ?? 'Chung' }}</span>
                                </div>

                                <h6 class="fw-bold text-dark text-truncate mb-1">{{ $idea->title }}</h6>

                                {{-- Content --}}
                                <div x-data="{ expanded: false }" class="flex-grow-1 mb-2">
                                    <div class="text-secondary small idea-content-box" :class="expanded ? 'expanded-box' : ''" style="white-space: pre-line; font-size: 0.85rem;">
                                        <span x-show="!expanded">{{ Str::limit($idea->content, 90) }}</span>
                                        <span x-show="expanded" x-cloak>{{ $idea->content }}</span>
                                    </div>
                                    @if(strlen($idea->content) > 90)
                                        <button @click="expanded = !expanded" class="btn btn-link p-0 fw-bold text-decoration-none small" style="font-size: 0.75rem;">
                                            <span x-show="!expanded">See more</span>
                                            <span x-show="expanded" x-cloak>Less</span>
                                        </button>
                                    @endif
                                </div>

                                {{-- Interaction Buttons --}}
                                <div class="d-flex align-items-center gap-3 border-top pt-2 mb-2">
                                    @auth
                                        <a href="{{ route('idea.like', ['id' => $idea->id, 'type' => 1]) }}" class="text-decoration-none d-flex align-items-center {{ $idea->likes->where('user_id', Auth::id())->where('type', 1)->first() ? 'text-primary' : 'text-muted' }}">
                                            <i class="bi bi-hand-thumbs-up{{ $idea->likes->where('user_id', Auth::id())->where('type', 1)->first() ? '-fill' : '' }} me-1"></i>
                                            <span class="small">{{ $idea->likes->where('type', 1)->count() }}</span>
                                        </a>

                                        <a href="{{ route('idea.like', ['id' => $idea->id, 'type' => 0]) }}" class="text-decoration-none d-flex align-items-center {{ $idea->likes->where('user_id', Auth::id())->where('type', 0)->first() ? 'text-danger' : 'text-muted' }}">
                                            <i class="bi bi-hand-thumbs-down{{ $idea->likes->where('user_id', Auth::id())->where('type', 0)->first() ? '-fill' : '' }} me-1"></i>
                                            <span class="small">{{ $idea->likes->where('type', 0)->count() }}</span>
                                        </a>

                                        <button class="btn btn-link p-0 text-decoration-none text-muted d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#comments-{{ $idea->id }}">
                                            <i class="bi bi-chat-dots me-1"></i>
                                            <span class="small">{{ $idea->comments->count() }}</span>
                                        </button>
                                    @else
                                        {{-- Guest Buttons: Bấm vào bất kỳ cái nào cũng hiện Alert --}}
                                        <button type="button" @click="showLoginAlert = true" class="btn btn-link p-0 text-decoration-none text-muted d-flex align-items-center border-0">
                                            <i class="bi bi-hand-thumbs-up me-1"></i>
                                            <span class="small">{{ $idea->likes->where('type', 1)->count() }}</span>
                                        </button>

                                        <button type="button" @click="showLoginAlert = true" class="btn btn-link p-0 text-decoration-none text-muted d-flex align-items-center border-0">
                                            <i class="bi bi-hand-thumbs-down me-1"></i>
                                            <span class="small">{{ $idea->likes->where('type', 0)->count() }}</span>
                                        </button>

                                        <button type="button" @click="showLoginAlert = true" class="btn btn-link p-0 text-decoration-none text-muted d-flex align-items-center border-0">
                                            <i class="bi bi-chat-dots me-1"></i>
                                            <span class="small">{{ $idea->comments->count() }}</span>
                                        </button>
                                    @endauth
                                </div>

                                {{-- Unified Alert: Chỉ có một thanh thông báo duy nhất --}}
                                <div x-show="showLoginAlert" x-transition x-cloak class="mb-2">
                                    <div class="py-2 px-3 bg-white rounded-3 border d-flex align-items-center justify-content-between shadow-sm border-primary">
                                        <small class="text-muted" style="font-size: 0.65rem;">
                                            <i class="bi bi-info-circle me-1 text-primary"></i> Login to interact or comment
                                        </small>
                                        <div class="d-flex align-items-center">
                                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-pill py-0 px-3 me-2" style="font-size: 0.65rem;">Login</a>
                                            <button type="button" @click="showLoginAlert = false" class="btn-close" style="font-size: 0.5rem;"></button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Comments Section --}}
                                <div class="collapse" id="comments-{{ $idea->id }}">
                                    <div class="mt-2 p-2 bg-light rounded" style="max-height: 150px; overflow-y: auto;">
                                        @foreach($idea->comments as $comment)
                                            <div class="small mb-2 border-bottom pb-1">
                                                <strong style="font-size: 0.7rem;">{{ $comment->is_anonymous ? 'Anonymous' : ($comment->user->full_name ?? 'User') }}:</strong>
                                                <span style="font-size: 0.75rem;">{{ $comment->content }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    @auth
                                        @php
                                            // Mặc định là cho phép comment
                                            $canComment = true;

                                            // Kiểm tra logic:
                                            // 1. Idea có thuộc năm học nào không?
                                            // 2. Nếu có, ngày hiện tại đã vượt quá hạn đóng comment (Final Closure Date) chưa?
                                            if ($idea->academicYear && now() > $idea->academicYear->final_closure_date) {
                                                $canComment = false;
                                            }
                                        @endphp

                                        @if($canComment)
                                            {{-- TRƯỜNG HỢP 1: Còn hạn -> Hiển thị Form nhập bình thường --}}
                                            <form action="{{ route('comments.store', $idea->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                <div class="input-group input-group-sm mb-1">
                                                    <input type="text" name="content" class="form-control shadow-none" placeholder="Write a comment..." required>
                                                    <button class="btn btn-primary px-2" type="submit">Send</button>
                                                </div>
                                                <div class="form-check form-switch mt-1">
                                                    <input class="form-check-input" type="checkbox" name="is_anonymous" id="anon-{{ $idea->id }}" value="1" role="switch">
                                                    <label class="form-check-label text-muted" for="anon-{{ $idea->id }}" style="font-size: 0.7rem; cursor: pointer;">Anonymous comment</label>
                                                </div>
                                            </form>
                                        @else
                                            {{-- TRƯỜNG HỢP 2: Hết hạn -> Ẩn Form, hiện thông báo khóa --}}
                                            <div class="mt-2 p-2 bg-light text-center rounded border">
                                                <small class="text-danger fw-bold d-flex align-items-center justify-content-center gap-2">
                                                    <i class="bi bi-lock-fill"></i>
                                                    Comments are closed for this semester.
                                                </small>
                                            </div>
                                        @endif
                                    @endauth
                                    {{-- ĐÃ XÓA PHẦN @else THANH LOGIN CỐ ĐỊNH Ở ĐÂY --}}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">No ideas have been shared yet.</p>
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
