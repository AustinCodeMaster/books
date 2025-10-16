<?php
require_once '../conf.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .verification-input {
            width: 50px;
            height: 50px;
            text-align: center;
            margin: 0 5px;
            font-size: 24px;
        }
        .step-2 {
            display: none;
        }
        .alert {
            display: none;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <!-- Step 1: Login Form -->
        <div class="form-container step-1">
            <h3 class="text-center mb-4">Login</h3>
            <div class="alert alert-danger" id="loginError"></div>
            <form id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>

        <!-- Step 2: 2FA Verification -->
        <div class="form-container step-2">
            <h3 class="text-center mb-4">2FA Verification</h3>
            <div class="alert alert-danger" id="verificationError"></div>
            <p class="text-center">Enter the 6-digit code sent to your email</p>
            <form id="verificationForm">
                <div class="d-flex justify-content-center mb-4">
                    <input type="text" class="form-control verification-input" maxlength="1" required>
                    <input type="text" class="form-control verification-input" maxlength="1" required>
                    <input type="text" class="form-control verification-input" maxlength="1" required>
                    <input type="text" class="form-control verification-input" maxlength="1" required>
                    <input type="text" class="form-control verification-input" maxlength="1" required>
                    <input type="text" class="form-control verification-input" maxlength="1" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const verificationForm = document.getElementById('verificationForm');
            const step1 = document.querySelector('.step-1');
            const step2 = document.querySelector('.step-2');
            const loginError = document.getElementById('loginError');
            const verificationError = document.getElementById('verificationError');

            // Handle login form submission
            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                try {
                    const response = await fetch('2fa_login.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ email, password })
                    });

                    const data = await response.json();
                    if (data.success) {
                        step1.style.display = 'none';
                        step2.style.display = 'block';
                    } else {
                        loginError.style.display = 'block';
                        loginError.textContent = data.message || 'Login failed';
                    }
                } catch (error) {
                    loginError.style.display = 'block';
                    loginError.textContent = 'An error occurred. Please try again.';
                }
            });

            // Handle 2FA code verification
            const verificationInputs = document.querySelectorAll('.verification-input');
            verificationInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    if (this.value && index < verificationInputs.length - 1) {
                        verificationInputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !this.value && index > 0) {
                        verificationInputs[index - 1].focus();
                    }
                });
            });

            verificationForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                let code = '';
                verificationInputs.forEach(input => {
                    code += input.value;
                });

                try {
                    const response = await fetch('verify_2fa.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ code })
                    });

                    const data = await response.json();
                    if (data.success) {
                        window.location.href = '../Layouts/dashboard.php';
                    } else {
                        verificationError.style.display = 'block';
                        verificationError.textContent = data.message || 'Invalid verification code';
                    }
                } catch (error) {
                    verificationError.style.display = 'block';
                    verificationError.textContent = 'An error occurred. Please try again.';
                }
            });
        });
    </script>
</body>
</html><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="<?php echo "Welcome"; ?>/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<?php
class forms
{
    public function signup()
    {
?>

<body>
        <form method="post" action="/bookstore/Global/validateForm.php" id="signUp">
            <div class="container-fluid">
                <h2>Sign Up</h2>
                <p>Sign Up to make online purchases, view books and make reservations</p>
                <div class="mb-3">
                    <label for="fullname">Full name:</label>
                    <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Wesley Ogam" aria-label="Fullname" aria-describedby="basic-addon1">
                </div>
                <div class="mb-3">
                    <label for="email">Email: </label>
                    <input type="text" id="email" name="email" class="form-control" required aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <div class="mb-3">
                    <label for="password">Password: </label>
                    <input type="text" id="password" name="password" class="form-control" required aria-label="Username" aria-describedby="basic-addon1">
                </div>
                <div class="mb-3">
                    <?php $this->submit_button("Sign Up", "signup"); ?> <a href="#">Already have an account? Log in</a>
                </div>
            </div>
        </form>
</body>

    <?php
    }

    private function submit_button($value, $name)
    {
    ?>
        <button type="submit" class="btn btn-primary" name="<?php echo $name; ?>" value="<?php echo $value; ?>"><?php echo $value ?></button>
    <?php
    }
    public function signin()
    {
    ?>
        <form method="get" action="/bookstore/Global/validateForm.php" id="signin">
            <div class="container-fluid">
                <h2>Sign in</h2>
                <p>Glad to see you back.</p>
                <div class="mb-3">
                    <label for="fullname">Fullname: </label>
                    <input type="text" id="fullname" name="fullname" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="password">Password:</label>
                    <input type="password" name="password" required class="form-control">
                </div>
                <!--<div class="mb-3">
                    <input type="submit" value="log in"><a href="#signUp">Don't have an account? Sign up</a>
                </div>-->
                <div class="mb-3">
                    <?php $this->submit_button("Sign In", "signin"); ?> <a href="#">Don't have an account, Sign up</a>
                </div>


        </form>

        </div>

<?php
    }
}