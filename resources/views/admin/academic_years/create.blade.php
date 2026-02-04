{{-- Lưu ý: Nếu file layout của bạn tên là 'master.blade.php' nằm ngay trong views thì để là 'master' --}}
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
                <div class="card-header bg-primary text-white py-3 px-4">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-calendar-plus me-2"></i> Create New Academic Year</h4>
                </div>

                <div class="card-body p-5">

                    {{-- Hiển thị thông báo lỗi chung (Nếu có) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.academic-years.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Academic Year Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   placeholder="e.g. Spring 2024"
                                   value="{{ old('name') }}" required>
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
                                       value="{{ old('start_date') }}" required>
                            </div>
                            <div class="form-text">The day the semester officially begins.</div>
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
                                           value="{{ old('closure_date') }}" required>
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
                                           value="{{ old('final_closure_date') }}" required>
                                </div>
                                <div class="form-text small">All interactions (likes/comments) are disabled.</div>
                                @error('final_closure_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                                <i class="bi bi-save me-2"></i> Save Academic Year
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
