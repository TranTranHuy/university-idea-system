@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="d-flex gap-2 mb-4">
        <a href="{{ route('qam.ideas.export') }}" class="btn btn-success text-white shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i> Download Report (CSV)
        </a>

        {{-- --- DROPDOWN CHỌN NĂM HỌC ĐỂ TẢI ZIP --- --}}
        <div class="dropdown">
            <button class="btn btn-dark shadow-sm dropdown-toggle" type="button" id="dropdownZip" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-file-earmark-zip-fill"></i> Download ZIP by Academic Year
            </button>
            <ul class="dropdown-menu shadow" aria-labelledby="dropdownZip">
                <li><h6 class="dropdown-header">Select Academic Year</h6></li>
                @forelse($academicYears as $year)
                    <li>
                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="{{ route('qam.qa.download_zip_by_year', $year->id) }}">
                            <span>{{ $year->name }}</span>
                            @if(now() <= $year->final_closure_date)
                                <span class="badge bg-success bg-opacity-10 text-success ms-3">Active</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary ms-3">Closed</span>
                            @endif
                        </a>
                    </li>
                @empty
                    <li><span class="dropdown-item text-muted text-center">No academic years available</span></li>
                @endforelse
            </ul>
        </div>
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

                                            {{-- KIỂM TRA ĐIỀU KIỆN CHẶN XÓA --}}
                                            @if($cat->ideas()->exists())
                                                {{-- Nếu đã có Idea dùng, disable nút và hiện tooltip --}}
                                                <button type="button" class="btn btn-sm btn-secondary opacity-50" title="Cannot be deleted because Idea is already using it." style="cursor: not-allowed;" onclick="alert('Danh mục này đang được sử dụng bởi các ý tưởng, không thể xóa!')">
                                                    Delete
                                                </button>
                                            @else
                                                {{-- Nếu chưa có Idea nào, gọi hàm mở Modal --}}
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="openDeleteModal({{ $cat->id }}, '{{ $cat->name }}')">
                                                    Delete
                                                </button>
                                            @endif
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
                                    <td>{{ \Illuminate\Support\Str::limit($idea->title, 50) }}</td>
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
                                                <i class="bi bi-download"></i> ZIP
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

<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the category <strong id="categoryNameToDelete" class="text-dark"></strong>?
                <br>
                <span class="text-muted" style="font-size: 0.9em;">This action cannot be undone.</span>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCategoryForm" method="POST" class="m-0 p-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(id, name) {
        // 1. Điền tên Category vào dòng chữ trong Modal
        document.getElementById('categoryNameToDelete').innerText = "'" + name + "'";

        // 2. Thay đổi cái route Xóa cho đúng với ID của Category
        let form = document.getElementById('deleteCategoryForm');
        let route = "{{ route('qam.categories.destroy', ':id') }}";
        form.action = route.replace(':id', id);

        // 3. Gọi Modal của Bootstrap hiện lên
        let deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
        deleteModal.show();
    }
</script>
@endsection
