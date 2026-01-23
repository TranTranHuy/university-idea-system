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
            <img src="{{ asset('images/signup_image_3d.png') }}" alt="Sign Up Illustration">
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
                @csrf <div class="input-group">
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
                    <span class="toggle-password" onclick="togglePwd('password')">👁️</span>
                    <div class="error-msg" id="passError" style="display:none; color:red">Mật khẩu phải 8 ký tự, chữ cái đầu viết hoa và có số.</div>
                </div>

                <div class="input-group" style="margin-top: 15px;">
                    <label>Vai trò & Phòng ban</label>
                    <div style="display: flex; gap: 10px;">
                        <select name="role_id" class="input-style" style="width: 50%; padding: 10px;">
                            <option value="2">Nhân viên</option>
                            <option value="1">Admin</option>
                        </select>
                        <select name="department_id" class="input-style" style="width: 50%; padding: 10px;">
                            <option value="1">Phòng IT</option>
                            <option value="2">Phòng HR</option>
                        </select>
                    </div>
                </div>

                <div class="options">
                    <label>
                        <input type="checkbox" name="is_agreed_terms" id="is_agreed_terms" required>
                        I agree to all the Terms and Privacy Policies
                    </label>
                </div>

                <button type="submit" class="btn-submit">Create account</button>

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

            // Validate Password (Logic cũ)
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

            return isValid;
        }
    </script>
</body>
</html>
