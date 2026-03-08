<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register an account</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="container">
        <div class="image-section">
            <img src="https://st.depositphotos.com/18722762/51522/v/450/depositphotos_515228796-stock-illustration-online-registration-sign-login-account.jpg">
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
                    <div class="error-msg" id="emailError" style="display:none; color:red">Invalid email.</div>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                    <span class="toggle-password" onclick="togglePwd('password')">👁️</span>
                    <div class="error-msg" id="passError" style="display:none; color:red">The password must be 8 characters long, with the first letter capitalized, and include numbers.</div>
                </div>

                {{-- <div class="input-group" style="margin-top: 15px;">
                    <label>Roles and Departments</label>
                    <div style="display: flex; gap: 10px;">
                        <select name="role_id" class="input-style" style="width: 50%; padding: 10px;">
                            <option value="2">Employee</option>
                            <option value="1">Admin</option>
                        </select>
                        <select name="department_id" class="input-style" style="width: 50%; padding: 10px;">
                            <option value="1">IT Department</option>
                            <option value="2">HR Department</option>
                        </select>
                    </div>
                </div> --}}
                {{-- Khối chọn Department --}}
<div style="margin-bottom: 1.2rem;">
    <label for="department_id" style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: #4b5563;">
        Department
    </label>

    <select id="department_id" name="department_id" required
            style="width: 100%; padding: 0.6rem 1rem; border: 1px solid #d1d5db; border-radius: 0.5rem; outline: none; background-color: #fff; font-size: 0.9rem; color: #374151; cursor: pointer;">
        <option value="" disabled selected>Select your department</option>
        @foreach ($departments as $department)
            <option value="{{ $department->id }}">{{ $department->department_name }}</option>
        @endforeach
    </select>
</div>

                <div style="margin-top:15px; font-size:14px; color:#666;">
    <input type="checkbox" name="agree" id="agree" required style="margin-right:6px;">
    <label for="agree">
        I agree to the
<a href="{{ route('terms.index') }}" style="color:#2f80ed; font-weight:600; text-decoration:none;">
    Terms of Service
</a>
and
<a href="{{ route('privacy.index') }}" style="color:#2f80ed; font-weight:600; text-decoration:none;">
    Privacy Policy
</a>
    </label>
</div>

                <button type="submit" class="btn-submit" style="margin-top:15px;">Create account</button>

                <div class="footer-text">
    Already have an account?
    <a href="{{ route('login') }}" style="color:#2f80ed; font-weight:600; text-decoration:none;">
        Login
    </a>
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
