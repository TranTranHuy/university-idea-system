@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom-0">
            <h5 class="fw-bold mb-0"><i class="bi bi-kanban me-2 text-primary"></i>Quản lý tất cả ý tưởng</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4" style="font-size: 0.8rem;">NGƯỜI ĐĂNG</th>
                        <th style="font-size: 0.8rem;">NỘI DUNG Ý TƯỞNG</th>
                        <th style="font-size: 0.8rem;">DANH MỤC</th>
                        <th style="font-size: 0.8rem;">FILES</th>
                        <th class="text-end pe-4" style="font-size: 0.8rem;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ideas as $idea)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    {{ substr($idea->user->full_name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold small">{{ $idea->is_anonymous ? 'Anonymous' : ($idea->user->full_name ?? 'Không tên') }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">{{ $idea->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">{{ Str::limit($idea->title, 40) }}</div>
                            <div class="text-muted small text-truncate" style="max-width: 250px;">{{ Str::limit($idea->content, 60) }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill fw-normal">{{ $idea->category->name ?? 'Chung' }}</span>
                        </td>
                        <td>
                            @if($idea->document)
                                <span class="badge bg-info-subtle text-info"><i class="bi bi-paperclip"></i> {{ is_array($idea->document) ? count($idea->document) : 1 }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.ideas.destroy', $idea->id) }}" method="POST" onsubmit="return confirm('bạn có chắc muốn xóa ý tưởng này không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm border-0">
                                    <i class="bi bi-trash3"></i> Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Chưa có ý tưởng nào để quản lý.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-top-0 py-3">
            {{ $ideas->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
