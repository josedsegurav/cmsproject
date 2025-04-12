<?php

session_start();
    require('connect.php');

    $title = "Login";

    $signUpSuccess = false;

    if(isset($_SESSION['signUpMessage'])){
    $registered = $_SESSION['signUpMessage'];
    $signUpSuccess = true;
    unset($_SESSION['signUpMessage']);
}

$logOutSuccess = false;
if(isset($_SESSION['loggedOutMessage'])){
    $loggedOut = $_SESSION['loggedOutMessage'];
    $logOutSuccess = true;
    unset($_SESSION['loggedOutMessage']);
}

if(isset($_SESSION['loginRquestItem'])){
    $item_id = $_SESSION['item_id'];
    $slug = $_SESSION['slug'];
}

    if(isset($_SESSION['user'])){
        header("Location: dashboard");
    }

    function filterInput() {
        if (
            $_POST && 
            !empty($_POST['username']) && 
            !empty($_POST['password']) &&
            !(trim($_POST['username']) == '') &&
            !(trim($_POST['password']) == '')){
            return true;
        }else{
            return false;
        }
    }

    $passwordError = false;
    $usernameError = false;

    if(filterInput()){

        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $login_query = "SELECT * FROM serverside.users WHERE username = :username";
        // A PDO::Statement is prepared from the query.
        $loginStatement = $db->prepare($login_query);
        $loginStatement->bindValue(':username', $username, PDO::PARAM_STR);
        // Execution on the DB server.
        $loginStatement->execute();
        $loginData = $loginStatement->fetch();

        if(!$loginData){
            $usernameError = true;
        }else{
            if(password_verify($password, $loginData['password'])){
                $_SESSION['user'] = [
                    "user_id" => $loginData['user_id'],
                    "username" => $loginData['username'],
                    "role" => $loginData['role'],
                    "name" => $loginData['name'],
                    "lastname" => $loginData['lastname'],
                    "email" => $loginData['email']
                ];
                $_SESSION['loggedMessage'] = "You have successfully logged in!";
                
                if (isset($_SESSION['loginRquestItem'])){
                    unset($_SESSION['loginRquestItem']);
                    header("Location: items/$item_id/$slug");
                }else{
                    
                    header("Location: dashboard");
                }
                
            }else{
                $passwordError = true;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<?php include('htmlHead.php'); ?>

<body>
    <?php include('nav.php'); ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="card-title text-center mb-4">Log In <span class="text-warning">Account</span></h2>

                        <?php if($passwordError): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>Invalid password, please try again.
                        </div>
                        <?php endif ?>

                        <?php if($usernameError): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>Username doesn't exist, please try again.
                        </div>
                        <?php endif ?>

                        <form method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" autofocus required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">Log In</button>
                            </div>

                            <p class="text-center mt-3 mb-0">
                                Don't have an account? <a href="signup" class="text-primary">Sign Up</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if($signUpSuccess): ?>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-white text-dark border-bottom border-warning">
                <strong class="me-auto">
                    <i class="fas fa-check-circle me-2 text-warning"></i>
                    <span>Interior<span class="text-warning">Items</span></span>
                </strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-check text-primary me-2"></i>
                    <?= $registered ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>
    <?php if($logOutSuccess): ?>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-white text-dark border-bottom border-warning">
                <strong class="me-auto">
                    <i class="fas fa-check-circle me-2 text-warning"></i>
                    <span>Interior<span class="text-warning">Items</span></span>
                </strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-check text-primary me-2"></i>
                    
                    <?= $loggedOut ?>
                    
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>
    <!-- Footer -->
    <?php if($signUpSuccess || $logOutSuccess): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var successToast = new bootstrap.Toast(document.getElementById('successToast'), {
            delay: 5000
        });
        successToast.show();
    });
    </script>
    <?php endif ?>
    
    <?php include('footer.php'); ?>
</body>

</html>