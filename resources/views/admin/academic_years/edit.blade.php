<h1>Edit Academic Year: {{ $academicYear->name }}</h1>

{{-- Hiển thị lỗi Validate --}}
@if ($errors->any())
    <div style="color: red; border: 1px solid red; padding: 10px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Form Cập nhật --}}
<form action="{{ route('admin.academic-years.update', $academicYear->id) }}" method="POST">
    @csrf
    @method('PUT') {{-- Bắt buộc phải có để Laravel hiểu đây là lệnh Update --}}
    
    <div>
        <label>Tên kỳ học:</label>
        {{-- Dùng $academicYear->name để hiện dữ liệu cũ --}}
        <input type="text" name="name" value="{{ old('name', $academicYear->name) }}">
    </div>
    <br>

    <div>
        <label>Ngày bắt đầu:</label>
        <input type="date" name="start_date" value="{{ old('start_date', $academicYear->start_date) }}">
    </div>
    <br>

    <div>
        <label>Deadline 1 (Nộp bài):</label>
        <input type="date" name="closure_date" value="{{ old('closure_date', $academicYear->closure_date) }}">
    </div>
    <br>

    <div>
        <label>Deadline 2 (Đóng comment):</label>
        <input type="date" name="final_closure_date" value="{{ old('final_closure_date', $academicYear->final_closure_date) }}">
    </div>
    <br>

    <button type="submit">Cập nhật thay đổi</button>
    <a href="{{ route('admin.academic-years.index') }}">Hủy</a>
</form>