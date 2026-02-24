@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex gap-2 mb-4">
        <a href="{{ route('qam.ideas.export') }}" class="btn btn-success text-white shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i> Download Report (CSV)
        </a>

        <a href="{{ route('qam.download.all') }}" class="btn btn-dark shadow-sm">
            <i class="bi bi-file-earmark-zip-fill"></i> Download All Attachments (ZIP)
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Create New Category</h5>
                    <form action="{{ route('qam.categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Facilities, IT..." required>
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
                    <div class="table-responsive">
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
                                            <form action="{{ route('qam.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Manage Ideas & Attachments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Idea ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Files</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ideas as $idea)
                                <tr>
                                    <td>#{{ $idea->id }}</td>
                                    <td>{{ Str::limit($idea->title, 50) }}</td>
                                    <td>{{ $idea->is_anonymous ? 'Anonymous' : ($idea->user->full_name ?? 'Unknown') }}</td>
                                    <td>
                                        @if($idea->document)
                                            <span class="badge bg-info text-dark">
                                                {{ is_array($idea->document) ? count($idea->document) : count(json_decode($idea->document, true)) }} file(s)
                                            </span>
                                        @else
                                            <span class="text-muted">No file</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($idea->document)
                                            <a href="{{ route('qam.download.single', $idea->id) }}" class="btn btn-sm btn-success">
                                                <i class="bi bi-download"></i> Download ZIP
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-secondary disabled">No Files</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $ideas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
