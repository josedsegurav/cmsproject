<?php
    session_start();

    require('connect.php');
    require('utils/functions.php');

    unsetRedirectSessions();

    if(empty($_SESSION['user']) || ($_SESSION['user']['role'] !== "admin")){
        header("Location: /webdev2/project/login");
    }

    $_SESSION['createUser'] = true;

    $title = "Add User";

    function filterInput() {
        if (
            $_POST && 
            !empty($_POST['username']) &&
            !empty($_POST['fname']) && 
            !empty($_POST['lname']) && 
            !empty($_POST['email']) && 
            filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) && 
            isset($_POST['role']) &&
            !empty($_POST['password']) &&
            !empty($_POST['confirmPassword']) &&
            !(trim($_POST['username']) == '') && 
            !(trim($_POST['fname']) == '') &&
            !(trim($_POST['lname']) == '') &&
            !(trim($_POST['email']) == '') &&
            !(trim($_POST['password']) == '') && 
            !(trim($_POST['confirmPassword']) == '')){
            return true;
        }else{
            return false;
        }
    }

    $passwordMatchError = false;
    $userError = false;
    $emailError = false;

    if(filterInput()){

        $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirmPassword = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $user_query = "SELECT * FROM serverside.users WHERE username = :username";
        // A PDO::Statement is prepared from the query.
        $userStatement = $db->prepare($user_query);
        $userStatement->bindValue(':username', $username, PDO::PARAM_STR);
        // Execution on the DB server.
        $userStatement->execute();
        $userData = $userStatement->fetch();

        $email_query = "SELECT * FROM serverside.users WHERE email = :email";
        // A PDO::Statement is prepared from the query.
        $emailStatement = $db->prepare($email_query);
        $emailStatement->bindValue(':email', $email, PDO::PARAM_STR);
        // Execution on the DB server.
        $emailStatement->execute();
        $emailData = $emailStatement->fetch();

        if($userData){
            $userError = true;
        }elseif($emailData){
            $emailError = true;
        }else{
            if($password === $confirmPassword){
                $hashPassword = password_hash($password, PASSWORD_DEFAULT);
    
                $signup_query = "INSERT INTO serverside.users (name, lastname, email, username, role, password) 
                        VALUES (:fname, :lname, :email, :username, :role, :password)";
                // A PDO::Statement is prepared from the query.
                $signupStatement = $db->prepare($signup_query);
                $signupStatement->bindValue(':fname', $fname, PDO::PARAM_STR);
                $signupStatement->bindValue(':lname', $lname, PDO::PARAM_STR);
                $signupStatement->bindValue(':email', $email, PDO::PARAM_STR);
                $signupStatement->bindValue(':username', $username, PDO::PARAM_STR);
                $signupStatement->bindValue(':role', $role, PDO::PARAM_STR);
                $signupStatement->bindValue(':password', $hashPassword, PDO::PARAM_STR);
                
                // Execution on the DB server.
                $success = $signupStatement->execute();
    
                if(!$success){
                    $signUpError = true;
                }else{
                    header("Location: /webdev2/project/dashboard/users");
                    exit();
                }
            }else{
                $passwordMatchError = true;
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
            <div class="col-md-8 col-lg-6">
                <div class="bg-white shadow-sm p-4">
                    
                        <h2 class="card-title text-center mb-4">Create New <span class="text-warning">User</span></h2>

                        <?php if($userError): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>Username already in use, please choose
                            another.
                        </div>
                        <?php endif ?>

                        <?php if($emailError): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>Email already in use, please choose another.
                        </div>
                        <?php endif ?>

                        <?php if($passwordMatchError): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>Passwords don't match, please try again.
                        </div>
                        <?php endif ?>

                        <form method="post">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="fname" name="fname" value="<?= isset($_POST['fname']) ? $_POST['fname'] : '' ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lname" name="lname" value="<?= isset($_POST['lname']) ? $_POST['lname'] : '' ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= isset($_POST['username']) ? $_POST['username'] : '' ?>" required>
                            </div>

                            <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="" disabled selected>- Choose a Role-</option>
                                            <option value="admin">admin</option>
                                            <option value="user">user</option>
                                        </select>
                                </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                                    required>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" id="adduser" class="btn btn-primary">Create User</button>
                            </div>
                        </form>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <?php include('footer.php'); ?>
</body>

</html>