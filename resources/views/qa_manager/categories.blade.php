@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('qa_manager.ideas.index') }}" class="btn btn-dark shadow-sm">
            <i class="bi bi-kanban me-2"></i> Manage All Ideas
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Create New Category</h5>
                    <form action="{{ route('qa_manager.categories.store') }}" method="POST">
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
                                        <a href="{{ route('qa_manager.categories.edit', $cat->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('qa_manager.categories.destroy', $cat->id) }}" method="POST">
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
