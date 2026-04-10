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

            {{-- Header tạo Idea (Chỉ hiện khi đã đăng nhập) --}}
            @auth
            <div class="card mb-4 border-0 shadow-sm p-3 rounded-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                        {{ Auth::user() ? substr(Auth::user()->full_name, 0, 1) : 'U' }}
                    </div>
                    <a href="{{ route('ideas.create') }}" class="btn btn-light w-100 text-start rounded-pill text-muted shadow-none py-2 px-4">
                        Do you have any new ideas?
                    </a>
                </div>
            </div>
            @endauth

            {{-- HEADER VÀ BỘ LỌC (FILTER DROPDOWN) --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">{{ $sortTitle ?? 'All Ideas' }}</h4>
                    <p class="text-muted small mb-0">Discover and engage with ideas from all departments.</p>
                </div>

                {{-- Nút Filter Dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle shadow-sm bg-white" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-funnel-fill me-1"></i> Sort By:
                        <span class="fw-bold text-dark">
                            @if(request('sort') == 'popular') Popular
                            @elseif(request('sort') == 'newest_comments') Newest Comments
                            @elseif(request('sort') == 'viewed') Viewed
                            @elseif(request('sort') == 'latest') Latest
                            @else Default
                            @endif
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="filterDropdown">
                        <li>
                            <a class="dropdown-item {{ request('sort') == 'default' || !request('sort') ? 'active bg-primary' : '' }}" href="{{ route('home', ['sort' => 'default']) }}">
                                <i class="bi bi-collection me-2"></i> Default
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item {{ request('sort') == 'latest' ? 'active bg-primary' : '' }}" href="{{ route('home', ['sort' => 'latest']) }}">
                                <i class="bi bi-clock-history me-2"></i> Latest Ideas
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request('sort') == 'popular' ? 'active bg-primary' : '' }}" href="{{ route('home', ['sort' => 'popular']) }}">
                                <i class="bi bi-fire text-danger me-2"></i> Most Popular
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request('sort') == 'newest_comments' ? 'active bg-primary' : '' }}" href="{{ route('home', ['sort' => 'newest_comments']) }}">
                                <i class="bi bi-chat-dots text-info me-2"></i> Newest Comments
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request('sort') == 'viewed' ? 'active bg-primary' : '' }}" href="{{ route('home', ['sort' => 'viewed']) }}">
                                <i class="bi bi-eye text-success me-2"></i> Most Viewed
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- DANH SÁCH IDEAS (DẠNG GRID CARD) --}}
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

                                {{-- Tiêu đề --}}
                                <a href="{{ route('ideas.show', $idea->id) }}" class="text-decoration-none text-dark">
                                    <h6 class="fw-bold text-truncate mb-1 text-primary-hover">{{ $idea->title }}</h6>
                                </a>

                                {{-- Content --}}
                                <div class="flex-grow-1 mb-2 d-flex flex-column">
                                    <a href="{{ route('ideas.show', $idea->id) }}" class="text-decoration-none text-secondary small mb-1 d-block" style="white-space: pre-line; font-size: 0.85rem;">
                                        {{ Str::limit($idea->content, 90) }}
                                    </a>

                                    <a href="{{ route('ideas.show', $idea->id) }}" class="p-0 fw-bold text-decoration-none small text-primary d-inline-block mt-auto" style="font-size: 0.75rem;">
                                        View details <i class="bi bi-arrow-right-short"></i>
                                    </a>
                                </div>

                                {{-- HIỂN THỊ FILE ĐÍNH KÈM --}}
                                @if($idea->document)
                                    @php
                                        $files = is_array($idea->document) ? $idea->document : json_decode($idea->document, true);
                                    @endphp
                                    @if(!empty($files))
                                        <div class="d-flex flex-wrap gap-1 mb-3">
                                            @foreach($files as $file)
                                                <a href="{{ asset('storage/' . $file) }}" download="{{ basename($file) }}"
                                                   class="badge bg-light text-secondary border text-decoration-none d-flex align-items-center px-2 py-1"
                                                   title="Click to download file">
                                                    <i class="bi bi-download me-1 text-primary"></i>
                                                    <span class="text-truncate" style="max-width: 150px; font-weight: normal; font-size: 0.75rem;">
                                                        {{ basename($file) }}
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                {{-- Interaction Buttons --}}
                                <div class="d-flex align-items-center gap-3 border-top pt-2 mb-2">
                                    @auth
                                        <a href="{{ route('idea.like', ['id' => $idea->id, 'type' => 'like']) }}"
                                        class="text-decoration-none d-flex align-items-center {{ $idea->likes->where('user_id', Auth::id())->where('type', 1)->first() ? 'text-success' : 'text-muted' }}">
                                            <i class="bi bi-hand-thumbs-up{{ $idea->likes->where('user_id', Auth::id())->where('type', 1)->first() ? '-fill' : '' }} me-1"></i>
                                            <span class="small">{{ $idea->likes->where('type', 1)->count() }}</span>
                                        </a>

                                        <a href="{{ route('idea.like', ['id' => $idea->id, 'type' => 'dislike']) }}"
                                        class="text-decoration-none d-flex align-items-center {{ $idea->likes->where('user_id', Auth::id())->where('type', 0)->first() ? 'text-danger' : 'text-muted' }}">
                                            <i class="bi bi-hand-thumbs-down{{ $idea->likes->where('user_id', Auth::id())->where('type', 0)->first() ? '-fill' : '' }} me-1"></i>
                                            <span class="small">{{ $idea->likes->where('type', 0)->count() }}</span>
                                        </a>

                                        <button class="btn btn-link p-0 text-decoration-none text-muted d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#comments-{{ $idea->id }}">
                                            <i class="bi bi-chat-dots me-1"></i>
                                            <span class="small">{{ $idea->comments->count() }}</span>
                                        </button>
                                    @else
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

                                    <div class="ms-auto text-muted small d-flex align-items-center" title="Views">
                                        <i class="bi bi-eye me-1"></i> {{ $idea->view_count ?? 0 }}
                                    </div>
                                </div>

                                {{-- Unified Alert --}}
                                <div x-show="showLoginAlert" x-transition x-cloak class="mb-2">
                                    <div class="py-2 px-3 bg-white rounded-3 border d-flex align-items-center justify-content-between shadow-sm border-primary">
                                        <small class="text-muted" style="font-size: 0.65rem;">
                                            <i class="bi bi-info-circle me-1 text-primary"></i> Login to interact
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
                                            $canComment = true;
                                            if ($idea->academicYear && now() > $idea->academicYear->final_closure_date) {
                                                $canComment = false;
                                            }
                                        @endphp

                                        @if($canComment)
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
                                            <div class="mt-2 p-2 bg-light text-center rounded border">
                                                <small class="text-danger fw-bold d-flex align-items-center justify-content-center gap-2">
                                                    <i class="bi bi-lock-fill"></i>
                                                    Comments are closed for this semester.
                                                </small>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="bg-white rounded-circle d-inline-flex justify-content-center align-items-center mb-3 shadow-sm" style="width: 80px; height: 80px;">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                        </div>
                        <h5 class="fw-bold text-muted">No ideas found.</h5>
                        <p class="text-muted small">Be the first to share your thoughts!</p>
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
