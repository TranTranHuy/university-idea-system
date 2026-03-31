<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="container">
        <div class="image-section">
            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=800&auto=format&fit=crop" alt="Team Illustration" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
        </div>

        <div class="form-section">
            <h2>Sign up</h2>
            <p>Let's get you all set up so you can access your personal account.</p>

            @if($errors->any())
                <div class="alert-error" style="color: red; margin-bottom: 10px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="registerForm" action="{{ route('register') }}" method="POST" onsubmit="return validateForm()">
                @csrf
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" id="full_name" placeholder="John Doe" value="{{ old('full_name') }}" required>
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" id="email" placeholder="john.doe@gmail.com" value="{{ old('email') }}" required>
                    <div class="error-msg" id="emailError" style="display:none; color:red">Email không hợp lệ.</div>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePwd('password')" style="cursor: pointer;">👁️</span>
                    <div class="error-msg" id="passError" style="display:none; color:red">Mật khẩu phải có ít nhất 8 ký tự, chữ cái đầu viết hoa và có số.</div>
                </div>

                <div class="input-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePwd('password_confirmation')" style="cursor: pointer;">👁️</span>
                    <div class="error-msg" id="confirmError" style="display:none; color:red">Mật khẩu xác nhận không khớp!</div>
                </div>

                <div class="form-check mt-3" style="margin-top: 15px;">
                    <input type="checkbox" name="agree" id="agree" required class="form-check-input">
                    <label class="form-check-label" for="agree">
                        I agree to all the
                        <a href="{{ route('terms.index') }}" target="_blank" class="text-primary fw-bold text-decoration-none">
                            Terms
                        </a>
                        and
                        <a href="{{ route('privacy.index') }}" target="_blank" class="text-primary fw-bold text-decoration-none">
                            Privacy Policies
                        </a>
                    </label>
                </div>

                <button type="submit" class="btn-submit" style="margin-top: 20px;">Create account</button>

                <div class="footer-text">
                    Already have an account? <a href="{{ route('login') }}">Login</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePwd(id) {
            var x = document.getElementById(id);
            x.type = (x.type === "password") ? "text" : "password";
        }

        function validateForm() {
            let isValid = true;

            // Validate Email
            const email = document.getElementById('email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('emailError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('emailError').style.display = 'none';
            }

            // Validate Password
            const password = document.getElementById('password').value;
            const hasNumber = /\d/.test(password);
            const isLongEnough = password.length >= 8;
            const isFirstUpper = /^[A-Z]/.test(password);

            if (!isLongEnough || !isFirstUpper || !hasNumber) {
                document.getElementById('passError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('passError').style.display = 'none';
            }

            // Kiểm tra mật khẩu xác nhận (Thêm mới để người dùng biết ngay lập tức)
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            if (password !== passwordConfirmation && password !== '') {
                document.getElementById('confirmError').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('confirmError').style.display = 'none';
            }

            return isValid;
        }
    </script>
</body>
</html>
