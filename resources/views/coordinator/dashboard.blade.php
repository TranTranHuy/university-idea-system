@extends('layouts.master')

@section('content')
<div class="container py-5">

    <div class="row mb-4 align-items-center">
        {{-- <div class="col-md-8">
            <h2 class="fw-bold text-primary"><i class="bi bi-speedometer2"></i> Coordinator Dashboard</h2>
            <p class="text-muted">Overview of ideas in your Department.</p>
        </div>
        <div class="col-md-4 text-md-end"> --}}
            {{-- Nút Export CSV --}}
            {{-- <a href="{{ route('coordinator.export') }}" class="btn btn-success text-white shadow-sm fw-bold">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export Report (CSV)
            </a> --}}
            <div class="d-flex justify-content-between align-items-center mb-1">

        {{-- Phần Tiêu đề bên trái --}}
        <div>
            <h2 class="fw-bold text-primary"><i class="bi bi-speedometer2"></i> Coordinator Dashboard</h2>
            <p class="text-muted mb-0">Overview of ideas in your Department.</p>
        </div>

        {{-- Phần Nút bấm bên phải (Dạt sát góc phải) --}}
        <div class="d-flex gap-2">
            {{-- Nút Download ZIP --}}
            <a href="{{ route('coordinator.download.zip') }}" class="btn btn-primary text-white shadow-sm fw-bold text-nowrap px-3">
                <i class="bi bi-file-earmark-zip-fill me-1"></i> Download Zip
            </a>

            {{-- Nút Export CSV --}}
            <a href="{{ route('coordinator.export') }}" class="btn btn-success text-white shadow-sm fw-bold text-nowrap px-3">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
            </a>
        </div>

    </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10 h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <i class="bi bi-lightbulb fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-1">Total Ideas</h6>
                        <h2 class="fw-bold text-dark mb-0">{{ $totalIdeas }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10 h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle p-3 me-3">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-uppercase text-muted fw-bold mb-1">Active Contributors</h6>
                        <h2 class="fw-bold text-dark mb-0">{{ $activeContributors }}</h2>
                        <small class="text-success">Staff submitted at least 1 idea</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4">
            <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2"></i> Latest Submissions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary text-uppercase small fw-bold">
                        <tr>
                            <th class="ps-4 py-3">Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Submitted Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ideas as $idea)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('ideas.show', $idea->id) }}" class="fw-bold text-decoration-none text-dark">
                                        {{ Str::limit($idea->title, 40) }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">
                                        {{ $idea->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td>
                                    @if($idea->is_anonymous)
                                        <span class="text-muted fst-italic"><i class="bi bi-incognito"></i> Anonymous</span>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-secondary text-white rounded-circle me-2 d-flex justify-content-center align-items-center" style="width: 30px; height: 30px; font-size: 12px;">
    {{ substr($idea->user->full_name, 0, 1) }}
</div>

{{ $idea->user->full_name }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $idea->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('ideas.show', $idea->id) }}" class="btn btn-sm btn-outline-primary shadow-sm">
                                        View Detail <i class="bi bi-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No ideas submitted in your department yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center py-4">
                {{ $ideas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
