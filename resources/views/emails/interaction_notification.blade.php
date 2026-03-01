<!DOCTYPE html>
<html>
<body>
    <h2>Chào {{ $idea->user->full_name }},</h2>
    <p><strong>{{ $sender->full_name }}</strong> vừa <strong>{{ $actionType }}</strong> ý tưởng của bạn.</p>

    <p><strong>Tiêu đề ý tưởng:</strong> {{ $idea->title }}</p>

    @if($content)
        <p><strong>Nội dung bình luận:</strong> "{{ $content }}"</p>
    @endif

    <p>Vui lòng đăng nhập hệ thống để xem chi tiết.</p>
    <p>Trân trọng,<br>Hệ thống UIS</p>
</body>
</html>
