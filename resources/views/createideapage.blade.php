@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-4">Submit Your Idea</h3>

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

                    <form action="{{ route('ideas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

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
                            <textarea name="description" class="form-control" rows="6" placeholder="Describe your idea in detail..." required>{{ old('content') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Attachment (Optional)</label>
                            <input type="file" name="document" class="form-control">
                            <small class="text-muted">Accepted: pdf, docx, jpg, png (max 2MB)</small>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" name="terms" class="form-check-input" id="checkTerms" required>
                            <label class="form-check-label small" for="checkTerms">I agree to the Terms and Conditions</label>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">SUBMIT IDEA</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Xử lý đổi tên hiển thị thời gian thực khi gạt nút
    const anonymousSwitch = document.getElementById('anonymousSwitch');
    const nameLabel = document.getElementById('displayAuthor');
    const realName = "{{ auth()->user()->full_name }}";

    function updateName() {
        if(anonymousSwitch.checked) {
            nameLabel.innerText = "Anonymous (Người dùng ẩn danh)";
            nameLabel.classList.replace('text-success', 'text-secondary');
        } else {
            nameLabel.innerText = realName;
            nameLabel.classList.replace('text-secondary', 'text-success');
        }
    }

    anonymousSwitch.addEventListener('change', updateName);

    // Gọi hàm một lần khi load trang để giữ trạng thái đúng nếu có lỗi validation quay lại
    window.onload = updateName;
</script>
@endsection
