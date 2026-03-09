<!DOCTYPE html>
<html>
<body>
     <h2>Hello {{ $idea->user->full_name }},</h2>

    <p><strong>{{ $sender->full_name }}</strong> has just <strong>{{ $actionType }}</strong> your idea.</p>

    <p><strong>Idea Title:</strong> {{ $idea->title }}</p>

     @if($commentContent)
        <p><strong>Comment Content:</strong> "{{ $commentContent }}"</p>
    @endif

    <p>Please log in to the system to view details.</p>
    <p>Best regards,<br>UIMS System</p>
</body>
</html>
