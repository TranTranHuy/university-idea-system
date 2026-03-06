@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-calendar-event text-danger me-2"></i> Manage Academic Deadlines</h5>
            <small class="text-muted">Set or extend the closure dates for Ideas and Comments.</small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Academic Year</th>
                            <th>Start Date</th>
                            <th>Idea Deadline (Closure Date)</th>
                            <th>Comment Deadline (Final Closure)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($years as $year)
                        <tr>
                            <td class="fw-bold">{{ $year->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($year->start_date)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">
                                    {{ \Carbon\Carbon::parse($year->closure_date)->format('d/m/Y H:i') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-1">
                                    {{ \Carbon\Carbon::parse($year->final_closure_date)->format('d/m/Y H:i') }}
                                </span>
                            </td>
                            <td>
                                @if(now() < $year->start_date)
                                    <span class="badge bg-info text-dark"><i class="bi bi-clock-history"></i> Upcoming</span>
                                @elseif(now() > $year->final_closure_date)
                                    <span class="badge bg-secondary">Closed</span>
                                @elseif(now() > $year->closure_date)
                                    <span class="badge bg-warning text-dark">Comments Only</span>
                                @else
                                    <span class="badge bg-success">Open</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDeadlineModal{{ $year->id }}">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>

                                <div class="modal fade" id="editDeadlineModal{{ $year->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('qam.deadlines.update', $year->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title fw-bold">Edit Deadlines: {{ $year->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info">
                                                        <strong>Start Date:</strong> {{ \Carbon\Carbon::parse($year->start_date)->format('d/m/Y H:i') }}<br>
                                                        <small>(Only Administrators can modify the Start Date or Name)</small>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Idea Submission Deadline</label>
                                                        <input type="datetime-local" name="closure_date" class="form-control" value="{{ $year->closure_date }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Comment & Interaction Deadline</label>
                                                        <input type="datetime-local" name="final_closure_date" class="form-control" value="{{ $year->final_closure_date }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No Academic Years created by Admin yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $years->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
