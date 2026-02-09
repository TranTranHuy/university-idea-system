{{-- Lưu ý: Kiểm tra lại tên file layout của bạn (master hay layouts.master) --}}
@extends('layouts.master')

@section('content')
<div class="container py-5">

    <div class="mb-4">
        <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">

                <div class="card-header bg-warning text-dark py-3 px-4">
                    <h4 class="mb-0 fw-bold">
                        <i class="bi bi-pencil-square me-2"></i> Edit Academic Year
                    </h4>
                </div>

                <div class="card-body p-5">

                    {{-- Hiển thị lỗi Validate --}}
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.academic-years.update', $academicYear->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Bắt buộc để Laravel hiểu là Update --}}

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Academic Year Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   value="{{ old('name', $academicYear->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Start Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-calendar-check"></i></span>
                                <input type="date"
                                       name="start_date"
                                       class="form-control @error('start_date') is-invalid @enderror"
                                       value="{{ old('start_date', isset($academicYear->start_date) ? \Carbon\Carbon::parse($academicYear->start_date)->format('Y-m-d') : '') }}" required>
                            </div>
                            @error('start_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold text-danger">Closure Date (Idea Submission)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-warning bg-opacity-25 text-dark"><i class="bi bi-hourglass-split"></i></span>
                                    <input type="date"
                                           name="closure_date"
                                           class="form-control @error('closure_date') is-invalid @enderror"
                                           value="{{ old('closure_date', isset($academicYear->closure_date) ? \Carbon\Carbon::parse($academicYear->closure_date)->format('Y-m-d') : '') }}" required>
                                </div>
                                <div class="form-text small">Students cannot upload ideas after this date.</div>
                                @error('closure_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold text-danger">Final Closure Date (Comments)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-danger bg-opacity-25 text-danger"><i class="bi bi-slash-circle"></i></span>
                                    <input type="date"
                                           name="final_closure_date"
                                           class="form-control @error('final_closure_date') is-invalid @enderror"
                                           value="{{ old('final_closure_date', isset($academicYear->final_closure_date) ? \Carbon\Carbon::parse($academicYear->final_closure_date)->format('Y-m-d') : '') }}" required>
                                </div>
                                <div class="form-text small">All interactions are disabled.</div>
                                @error('final_closure_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.academic-years.index') }}" class="btn btn-light shadow-sm fw-bold">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-warning shadow-sm fw-bold text-dark px-4">
                                <i class="bi bi-check-circle-fill me-2"></i> Update Changes
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
