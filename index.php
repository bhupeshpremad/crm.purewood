<?php
session_start();
include './config/config.php';
include './core/auth.php';
include './include/inc/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $auth = new Auth();

    // Debug BASE_URL
    error_log('BASE_URL: ' . BASE_URL);

    if ($auth->login('superadmin', $email, $password)) {
        $_SESSION['user_type'] = 'superadmin';
        $_SESSION['user_email'] = $email;
        header('Location: ' . BASE_URL . 'superadmin/superadmin_dashboard.php');
        exit();
    } elseif ($auth->login('salesadmin', $email, $password)) {
        $_SESSION['user_type'] = 'salesadmin';
        $_SESSION['user_email'] = $email;
        header('Location: ' . BASE_URL . 'salesadmin/salesadmin_dashboard.php');
        exit();
    } elseif ($auth->login('accounts', $email, $password)) {
        $_SESSION['user_type'] = 'accounts';
        $_SESSION['user_email'] = $email;
        header('Location: ' . BASE_URL . 'accountsadmin/accounts_dashboard.php');
        exit();
    } elseif ($auth->login('operation', $email, $password)) {
        $_SESSION['user_type'] = 'operation';
        $_SESSION['user_email'] = $email;
        header('Location: ' . BASE_URL . 'operationadmin/operation_dashboard.php');
        exit();
    } elseif ($auth->login('production', $email, $password)) {
        $_SESSION['user_type'] = 'production';
        $_SESSION['user_email'] = $email;
        header('Location: ' . BASE_URL . 'productionadmin/production_dashboard.php');
        exit();
    } else {
        $error = 'Invalid email or password.';
    }
}
?>

<div class="container-fluid bg-gradient-primary">
    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center align-items-center mt-5">
    
            <div class="col-xl-10 col-lg-12 col-md-9 mt-5">
    
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row align-items-center">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image text-center">
                               <img src="./assets/images/Purewood-Joey Logo.png" alt="" class="img-fluid" style="width: 70%; height: auto;">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <?php if ($error): ?>
                                        <div aria-live="polite" aria-atomic="true" style="position: relative; min-height: 100px;">
                                            <div style="position: absolute; top: 0; right: 0; z-index: 1080;">
                                                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
                                                    <div class="toast-header bg-danger text-white">
                                                        <strong class="mr-auto">Error</strong>
                                                        <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="toast-body">
                                                        <?php echo htmlspecialchars($error); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function () {
                                                $('.toast').toast('show');
                                            });
                                        </script>
                                    <?php endif; ?>
                                    <form class="user" method="POST" action="">
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address..." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                        <hr>
                                    </form>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="register.html">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
            </div>
    
        </div>
    </div>

</div>
