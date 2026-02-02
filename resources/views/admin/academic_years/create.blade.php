{{-- File này chỉ để test Logic, xấu cũng được --}}
<h1>Test Create Academic Year</h1>

{{-- 1. Hiển thị lỗi Validate (Quan trọng nhất) --}}
@if ($errors->any())
    <div style="color: red; border: 1px solid red; padding: 10px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 2. Hiển thị thông báo thành công --}}
@if (session('success'))
    <div style="color: green; font-weight: bold;">
        {{ session('success') }}
    </div>
@endif

{{-- 3. Form gửi dữ liệu --}}
<form action="{{ route('admin.academic-years.store') }}" method="POST">
    @csrf {{-- Đừng quên cái này, không có là lỗi 419 --}}
    
    <div>
        <label>Tên kỳ học:</label>
        <input type="text" name="name" value="{{ old('name') }}">
    </div>
    <br>

    <div>
        <label>Ngày bắt đầu:</label>
        <input type="date" name="start_date" value="{{ old('start_date') }}">
    </div>
    <br>

    <div>
        <label>Deadline 1 (Nộp bài):</label>
        <input type="date" name="closure_date" value="{{ old('closure_date') }}">
    </div>
    <br>

    <div>
        <label>Deadline 2 (Đóng comment):</label>
        <input type="date" name="final_closure_date" value="{{ old('final_closure_date') }}">
    </div>
    <br>

    <button type="submit">Lưu dữ liệu</button>
</form>