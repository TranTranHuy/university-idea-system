<h1>Danh sách Năm học (Academic Years)</h1>

<a href="{{ route('admin.academic-years.create') }}">Creating New Academic Year</a>

@if(session('success'))
    <div style="color: green; font-weight: bold; margin: 10px 0;">
        {{ session('success') }}
    </div>
@endif

<table border="1" cellpadding="10" style="border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Start Date</th>
            <th>Closure Date (Idea)</th>
            <th>Final Closure Date (Comment)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($years as $year)
        <tr>
            <td>{{ $year->id }}</td>
            <td>{{ $year->name }}</td>
            <td>{{ $year->start_date }}</td>
            <td>{{ $year->closure_date }}</td>
            <td>{{ $year->final_closure_date }}</td>
            <td>
                {{-- Nút sửa --}}
                <a href="{{ route('admin.academic-years.edit', $year->id) }}">Edit</a>
                
                {{-- Nút xóa --}}
                <form action="{{ route('admin.academic-years.destroy', $year->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Chắc chắn xóa?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>