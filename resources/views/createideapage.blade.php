@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-4">Submit Your Idea</h3>
                    {{-- --- HIỂN THỊ ACADEMIC YEAR --- --}}
                    @if(isset($currentYear))
                        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
                            <i class="bi bi-clock-history fs-3 me-3 text-primary"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-primary">Current Semester: {{ $currentYear->name }}</h6>
                                <small class="text-muted">
                                    Closure Date: <span class="fw-bold text-danger">{{ \Carbon\Carbon::parse($currentYear->closure_date)->format('d/m/Y') }}</span>
                                    (Ideas submitted after this date will be rejected)
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>System Closed:</strong> There is no active academic year at this moment. You cannot submit new ideas.
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('ideas.store') }}" method="POST" enctype="multipart/form-data" id="ideaForm">
                        @csrf
                        <fieldset {{ !isset($currentYear) ? 'disabled' : '' }}>
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 border rounded-3 bg-light">
                            <div>
                                <p class="mb-0 fw-bold">Posting as: <span id="displayAuthor" class="text-success">{{ auth()->user()->full_name }}</span></p>
                                <small class="text-muted">Enable to hide your identity from others</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_anonymous" id="anonymousSwitch" style="width: 50px; height: 25px;" {{ old('is_anonymous') ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Idea Title</label>
                            <input type="text" name="title" class="form-control form-control-lg" placeholder="Enter title" value="{{ old('title') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Choose Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="content" class="form-control" rows="6" placeholder="Describe your idea in detail..." required>{{ old('content') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Attachments (Select multiple times if needed)</label>
                            <input type="file" id="fileHelper" class="form-control shadow-none" multiple>

                            <div id="hiddenFilesContainer" style="display: none;"></div>

                            <div id="fileList" class="mt-3 d-flex flex-wrap gap-2">
                                </div>
                            <small class="text-muted d-block mt-2">Accepted: pdf, docx, jpg, png (max 2MB per file)</small>
                        </div>

                        <div class="form-check mt-3">
                            <input type="checkbox" name="agree" id="agree" required class="form-check-input">
                            <label class="form-check-label" for="agree">
                                I agree to all the
                                <a href="{{ route('terms.index') }}" target="_blank" class="text-primary fw-bold text-decoration-none">
                                    Terms
                                </a>
                                and
                                <a href="{{ route('privacy.index') }}" target="_blank" class="text-primary fw-bold text-decoration-none">
                                    Privacy Policies
                                </a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">SUBMIT IDEA</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Xử lý đổi tên hiển thị thời gian thực
    const anonymousSwitch = document.getElementById('anonymousSwitch');
    const nameLabel = document.getElementById('displayAuthor');
    const realName = "{{ auth()->user()->full_name }}";

    function updateName() {
        if(anonymousSwitch.checked) {
            nameLabel.innerText = "Anonymous (Anonymous)";
            nameLabel.classList.replace('text-success', 'text-secondary');
        } else {
            nameLabel.innerText = realName;
            nameLabel.classList.replace('text-secondary', 'text-success');
        }
    }
    anonymousSwitch.addEventListener('change', updateName);

    // 2. LOGIC QUẢN LÝ FILE CHỌN NHIỀU LẦN
    const fileHelper = document.getElementById('fileHelper');
    const fileList = document.getElementById('fileList');
    const ideaForm = document.getElementById('ideaForm');

    // Mảng ảo để lưu trữ tất cả file người dùng đã chọn qua các lần bấm
    let allFiles = [];

    fileHelper.addEventListener('change', function() {
        const newFiles = Array.from(this.files);

        // Cộng dồn vào danh sách hiện tại
        allFiles = [...allFiles, ...newFiles];

        renderFileList();
        this.value = ''; // Reset input helper để có thể chọn lại file cũ nếu muốn
    });

    function renderFileList() {
        fileList.innerHTML = '';
        allFiles.forEach((file, index) => {
            const badge = document.createElement('div');
            badge.className = 'badge bg-light text-dark border p-2 rounded-2 d-flex align-items-center gap-2 fw-normal';
            badge.style.fontSize = '0.75rem';

            badge.innerHTML = `
                <i class="bi bi-file-earmark-check text-primary"></i>
                <span class="text-truncate" style="max-width: 120px;">${file.name}</span>
                <i class="bi bi-x-circle-fill text-danger cursor-pointer" onclick="removeFile(${index})" style="cursor: pointer;"></i>
            `;
            fileList.appendChild(badge);
        });
    }

    // Hàm xóa file khỏi danh sách cộng dồn
    window.removeFile = function(index) {
        allFiles.splice(index, 1);
        renderFileList();
    };

    // Trước khi submit, đóng gói mảng allFiles vào một input file thực sự
    ideaForm.addEventListener('submit', function(e) {
        const container = document.getElementById('hiddenFilesContainer');
        container.innerHTML = ''; // Clear cũ

        const dataTransfer = new DataTransfer();
        allFiles.forEach(file => {
            dataTransfer.items.add(file);
        });

        const realInput = document.createElement('input');
        realInput.type = 'file';
        realInput.name = 'documents[]'; // Đặt đúng tên để Controller nhận diện
        realInput.multiple = true;
        realInput.files = dataTransfer.files;

        container.appendChild(realInput);
    });


    window.onload = updateName;
</script>
@endsection
