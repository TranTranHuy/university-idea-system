@extends('layouts.master')

@section('content')
<div class="container py-5">
    
    {{-- HEADER BAR: Tiêu đề bên trái, Nút chức năng bên phải --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h3 class="fw-bold text-dark mb-0">Category Management</h3>
            <p class="text-muted small mb-0">Manage idea categories and download reports.</p>
        </div>

        <div class="d-flex gap-2">
            {{-- 1. Nút tải báo cáo CSV --}}
            <a href="{{ route('qam.ideas.export') }}" class="btn btn-outline-success shadow-sm fw-bold">
                <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i> Export CSV
            </a>

            {{-- 2. Dropdown tải ZIP theo năm --}}
            <div class="dropdown">
                <button class="btn btn-success shadow-sm fw-bold dropdown-toggle" type="button" id="downloadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-file-earmark-zip-fill me-1"></i> Download ZIP
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="downloadDropdown">
                    <li><h6 class="dropdown-header text-uppercase small fw-bold text-muted">Select Academic Year</h6></li>
                    
                    @foreach($academicYears as $year)
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center py-2" 
                               href="{{ route('qam.qa.download_zip_by_year', $year->id) }}">
                                
                                <span class="fw-bold text-dark">{{ $year->name }}</span>
                                
                                {{-- Badge trạng thái --}}
                                @if(now() <= $year->final_closure_date)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill ms-3">Active</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill ms-3">Closed</span>
                                @endif
                            </a>
                        </li>
                    @endforeach

                    @if($academicYears->isEmpty())
                        <li><span class="dropdown-item text-muted text-center py-2">No data available</span></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Create New Category</h5>
                    <form action="{{ route('qam.categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Category</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Existing Categories</h5>
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $cat)
                            <tr>
                                <td>#{{ $cat->id }}</td>
                                <td class="fw-bold">{{ $cat->name }}</td>
                                <td>{{ $cat->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('qam.categories.edit', $cat->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('qam.categories.destroy', $cat->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                    </div> </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
