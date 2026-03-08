<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="container">
        <div class="form-section">
            <h2>Login</h2>
            <p>Login to access your travelwise account</p>

            @if(session('success'))
                <div style="color: green; margin-bottom: 10px;">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div style="color: red; margin-bottom: 10px;">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="john.doe@gmail.com" value="{{ old('email') }}" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" id="loginPass" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePwd('loginPass')">👁️</span>
                </div>

                <div class="options">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="{{ route('password.request') }}" style="color:#2f80ed; font-weight:600; text-decoration:none;">
    Forgot Password
</a>
                </div>

                <button type="submit" class="btn-submit">Login</button>

                <div class="footer-text">
    Don't have an account?
    <a href="{{ route('register') }}" style="color:#2f80ed; font-weight:600; text-decoration:none;">
        Sign up
    </a>
</div>
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
