@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold"><i class="bi bi-bar-chart-fill text-primary me-2"></i> System Statistics</h3>
        <span class="badge bg-primary fs-6 py-2 px-3">Total Ideas: {{ $totalIdeas }}</span>
    </div>

    {{-- PHẦN 1: THỐNG KÊ THEO PHÒNG BAN --}}
    <div class="card shadow-sm border-0 rounded-4 mb-5">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h5 class="fw-bold mb-0"><i class="bi bi-building me-2 text-success"></i> Department Performance</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Department Name</th>
                            <th class="text-center">Total Ideas</th>
                            <th class="text-center">Contributors</th>
                            <th class="w-25">Percentage (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departmentsData as $dept)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $dept->name }}</td>
                                <td class="text-center h5 mb-0"><span class="badge bg-primary bg-opacity-10 text-primary">{{ $dept->ideas_count }}</span></td>
                                <td class="text-center h5 mb-0"><span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-people-fill"></i> {{ $dept->contributors }}</span></td>
                                <td class="pe-4">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 10px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $dept->percentage }}%"></div>
                                        </div>
                                        <span class="ms-3 fw-bold text-muted small" style="min-width: 40px;">{{ $dept->percentage }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No departments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PHẦN 2: BÁO CÁO NGOẠI LỆ (EXCEPTION REPORTS) --}}
    <h4 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i> Exception Reports</h4>
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white pt-3 pb-0 border-bottom">
            <ul class="nav nav-tabs border-0" id="exceptionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-secondary" data-bs-toggle="tab" data-bs-target="#no-comments" type="button">
                        <i class="bi bi-chat-square-text"></i> Ideas without Comments ({{ $ideasWithoutComments->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-secondary" data-bs-toggle="tab" data-bs-target="#anon-ideas" type="button">
                        <i class="bi bi-incognito"></i> Anonymous Ideas ({{ $anonymousIdeas->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-secondary" data-bs-toggle="tab" data-bs-target="#anon-comments" type="button">
                        <i class="bi bi-mask"></i> Anonymous Comments ({{ $anonymousComments->count() }})
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="exceptionTabsContent">

                {{-- Tab 1: Không bình luận --}}
                <div class="tab-pane fade show active" id="no-comments">
                    <ul class="list-group list-group-flush">
                        @forelse($ideasWithoutComments as $idea)
                            <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold"><a href="{{ route('ideas.show', $idea->id) }}" class="text-decoration-none">{{ $idea->title }}</a></h6>
                                    <small class="text-muted">By {{ $idea->user->full_name ?? 'Unknown' }} | Dept: {{ $idea->department->department_name ?? 'N/A' }} | {{ $idea->created_at->format('d/m/Y') }}</small>
                                </div>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">0 Comments</span>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">Tất cả ý tưởng đều đã được bình luận! Tuyệt vời!</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Tab 2: Ý tưởng ẩn danh --}}
                <div class="tab-pane fade" id="anon-ideas">
                    <ul class="list-group list-group-flush">
                        @forelse($anonymousIdeas as $idea)
                            <li class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-bold"><a href="{{ route('ideas.show', $idea->id) }}" class="text-decoration-none text-dark">{{ $idea->title }}</a></h6>
                                    <small class="text-danger"><i class="bi bi-eye-fill"></i> Real Author: <strong>{{ $idea->user->full_name ?? 'Unknown' }}</strong></small>
                                </div>
                                <small class="text-muted">{{ $idea->created_at->format('d/m/Y') }}</small>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">Không có ý tưởng ẩn danh nào.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Tab 3: Bình luận ẩn danh --}}
                <div class="tab-pane fade" id="anon-comments">
                    <ul class="list-group list-group-flush">
                        @forelse($anonymousComments as $comment)
                            <li class="list-group-item p-3">
                                <div class="mb-1 text-secondary fst-italic">"{{ Str::limit($comment->content, 100) }}"</div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-danger"><i class="bi bi-eye-fill"></i> Real Commenter: <strong>{{ $comment->user->full_name ?? 'Unknown' }}</strong></small>
                                    <a href="{{ route('ideas.show', $comment->idea_id) }}" class="btn btn-sm btn-outline-secondary py-0" style="font-size: 0.75rem;">View Idea</a>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">Không có bình luận ẩn danh nào.</li>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link { border: none; border-bottom: 3px solid transparent; padding: 15px 20px; transition: 0.3s; }
    .nav-tabs .nav-link.active { border-bottom: 3px solid #0d6efd; background: transparent; color: #0d6efd !important; }
    .nav-tabs .nav-link:hover:not(.active) { border-bottom: 3px solid #dee2e6; }
</style>
@endsection
