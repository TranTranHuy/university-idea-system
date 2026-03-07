<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="container">
        <div class="form-section">
            <h2>Reset Password</h2>
            <p>Create a new password for your account.</p>

            @if($errors->any())
                <div style="color: red; margin-bottom: 10px;">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ $email }}" required readonly style="background-color: #f0f0f0;">
                </div>

                <div class="input-group">
                    <label>New Password</label>
                    <input type="password" name="password" id="newPass" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePwd('newPass')">👁️</span>
                </div>

                <button type="submit" class="btn-submit">Save Password</button>
            </form>
        </div>

        <div class="image-section">
            <img src="https://st.depositphotos.com/18722762/51522/v/450/depositphotos_515228796-stock-illustration-online-registration-sign-login-account.jpg">
        </div>
    </div>

    <script>
        function togglePwd(id) {
            var x = document.getElementById(id);
            x.type = (x.type === "password") ? "text" : "password";
        }
    </script>
</body>
</html>
