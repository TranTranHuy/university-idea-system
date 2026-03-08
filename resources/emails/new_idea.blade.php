<!DOCTYPE html>
<html>
<head>
    <title>New Idea Notification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">

    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="color: #0d6efd;">University Idea System</h2>
    </div>

    <p>Hello <strong>QA Coordinator</strong>,</p>
    <p>A new idea has just been submitted to your department. Here are the details:</p>

    <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #0d6efd; border-radius: 5px; margin-bottom: 25px;">
        <h3 style="margin-top: 0; color: #333;">{{ $idea->title }}</h3>

        <p style="margin: 5px 0;"><strong>Category:</strong> {{ $idea->category->name ?? 'N/A' }}</p>

        <p style="margin: 5px 0;"><strong>Author:</strong>
            @if($idea->is_anonymous)
    <span class="text-muted fst-italic"><i class="bi bi-incognito"></i> Anonymous</span>

    {{-- ĐOẠN LOGIC DÀNH RIÊNG CHO ADMIN HIỂN THỊ TÊN THẬT --}}
    @auth
        @php
            $roleName = Auth::user()->role->role_name ?? Auth::user()->role;
            $isAdmin = in_array($roleName, ['Administrator', 'Admin', 'admin']);
        @endphp
        @if($isAdmin)
            <div class="text-danger mt-1" style="font-size: 0.8rem;">
                <i class="bi bi-eye-fill"></i> Real Author: <strong>{{ $idea->user->full_name ?? $idea->user->name }}</strong>
            </div>
        @endif
    @endauth
    {{-- KẾT THÚC LOGIC ADMIN --}}

@else
    {{-- Nếu không ẩn danh thì hiện tên bình thường --}}
    <span class="fw-bold text-dark">{{ $idea->user->full_name ?? $idea->user->name }}</span>
@endif
        </p>

        <p style="margin: 5px 0;"><strong>Submitted at:</strong> {{ $idea->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <p>Please log in to the system to review this idea and take necessary actions.</p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ route('ideas.show', $idea->id) }}" style="display: inline-block; padding: 12px 25px; background-color: #0d6efd; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
            View Idea Details
        </a>
    </div>

    <hr style="border: 0; border-top: 1px solid #eee; margin-top: 40px; margin-bottom: 20px;">

    <p style="font-size: 0.85em; color: #777; text-align: center;">
        This is an automated email from the University Idea System.<br>
        Please do not reply directly to this email.
    </p>

</body>
</html>
