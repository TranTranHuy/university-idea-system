<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="container">
        <div class="form-section">
            <h2>Forgot Password</h2>
            <p>Enter your email address to reset your password.</p>

            @if($errors->any())
                <div style="color: red; margin-bottom: 10px;">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="john.doe@gmail.com" required>
                </div>

                <button type="submit" class="btn-submit">Continue</button>

                <div class="footer-text" style="margin-top: 20px;">
                    Remember your password? <a href="{{ route('login') }}">Back to Login</a>
                </div>
            </form>
        </div>

        <div class="image-section">
            <img src="https://st.depositphotos.com/18722762/51522/v/450/depositphotos_515228796-stock-illustration-online-registration-sign-login-account.jpg">
        </div>
    </div>
</body>
</html>
