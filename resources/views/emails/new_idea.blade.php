<!DOCTYPE html>
<html>
<head>
    <title>New Idea Notification</title>
</head>
<body>
    <h1>Hello Coordinator,</h1>
    <p>A new idea has been submitted to your department.</p>

    <p><strong>Title:</strong> {{ $idea->title }}</p>
    <p><strong>Category:</strong> {{ $idea->category->name ?? 'None' }}</p>

    <p>Please login to the system to review it.</p>

    <p>Thank you,<br>
    UIS System</p>
</body>
</html>
