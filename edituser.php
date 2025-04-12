<?php
    session_start();

    require('utils/functions.php');

    unsetRedirectSessions();
    $_SESSION['editUser'] = true;

    if(empty($_SESSION['user']) || ($_SESSION['user']['role'] !== "admin")){
        header("Location: login");
    }
    require('connect.php');

    $title = "Update User";

    if(isset($_GET['id'])){
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    $query =   "SELECT user_id, role, name, lastname, email, username, password, created_at 
            FROM serverside.users 
            WHERE user_id = :id";
    
    // A PDO::Statement is prepared from the query. 
    $statement = $db->prepare($query);
    // Bind the value of the id coming from the GET and sanitized into the query.
    $statement->bindValue(':id', $id, PDO::PARAM_INT);

    // Execution on the DB server.
    $statement->execute();

    // Get the data from the DB after the query was executed.
    $userData = $statement->fetch();

    $secondOption = "";

    if($userData['role'] === "admin"){
        $secondOption = "user";
    }else{
        $secondOption = "admin";
    }
    }

    $inputs = [
        'id' => filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT),
        'fname' => filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'lname' => filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        'role' => filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
        ];

    if(isset($_POST['changePassword'])){
        function filterInput() {
            if (
                $_POST && 
                !empty($_POST['username']) &&
                !empty($_POST['fname']) && 
                !empty($_POST['lname']) && 
                !empty($_POST['email']) &&
                isset($_POST['role']) &&
                !empty($_POST['password']) &&
                !empty($_POST['confirmPassword']) &&
                !(trim($_POST['username']) == '') && 
                !(trim($_POST['fname']) == '') &&
                !(trim($_POST['lname']) == '') &&
                !(trim($_POST['email']) == '') &&
                !(trim($_POST['password']) == '') && 
                !(trim($_POST['confirmPassword']) == '')){
                    
                    $inputs['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $inputs['confirmPassword'] = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                return true;
            }else{
                return false;
            }
        }
    }else{
        function filterInput() {
            if (
                $_POST && 
                !empty($_POST['username']) &&
                !empty($_POST['fname']) && 
                !empty($_POST['lname']) && 
                !empty($_POST['email']) &&
                isset($_POST['role']) &&
                !(trim($_POST['username']) == '') && 
                !(trim($_POST['fname']) == '') &&
                !(trim($_POST['lname']) == '') &&
                !(trim($_POST['email']) == '')){
                return true;
            }else{
                return false;
            }
        }
    }

    $passwordMatchError = false;
    $userError = false;
    $emailError = false;

    
        if(isset($_POST['edituser'])){
            if(isset($_POST['changePassword'])){
                if(filterInput()){

                    if($password === $confirmPassword){
                        $hashPassword = password_hash($inputs['password'], PASSWORD_DEFAULT);
                    
                        $signup_query = "UPDATE serverside.users 
                                        SET role = :role , name = :fname, lastname = :lname, email = :email, username = :username, password = :password 
                                        WHERE user_id = :user_id";
                        // A PDO::Statement is prepared from the query.
                        $signupStatement = $db->prepare($signup_query);
                        $signupStatement->bindValue(':user_id', $inputs['id'], PDO::PARAM_INT);
                        $signupStatement->bindValue(':role', $inputs['role'], PDO::PARAM_STR);
                        $signupStatement->bindValue(':fname', $inputs['fname'], PDO::PARAM_STR);
                        $signupStatement->bindValue(':lname', $inputs['lname'], PDO::PARAM_STR);
                        $signupStatement->bindValue(':email', $inputs['email'], PDO::PARAM_STR);
                        $signupStatement->bindValue(':username', $inputs['username'], PDO::PARAM_STR);
                        $signupStatement->bindValue(':password', $hashPassword, PDO::PARAM_STR);

                        // Execution on the DB server.
                        $success = $signupStatement->execute();
                    
                        header("Location: dashboard/users");
                        exit();
                    }else{
                        $passwordMatchError = true;
                    }  
            }
        }else{
                 
                $signup_query = "UPDATE serverside.users 
                SET role = :role , name = :fname, lastname = :lname, email = :email, username = :username 
                WHERE user_id = :user_id";
                // A PDO::Statement is prepared from the query.
                $signupStatement = $db->prepare($signup_query);
                $signupStatement->bindValue(':user_id', $inputs['id'], PDO::PARAM_INT);
                $signupStatement->bindValue(':role', $inputs['role'], PDO::PARAM_STR);
                $signupStatement->bindValue(':fname', $inputs['fname'], PDO::PARAM_STR);
                $signupStatement->bindValue(':lname', $inputs['lname'], PDO::PARAM_STR);
                $signupStatement->bindValue(':email', $inputs['email'], PDO::PARAM_STR);
                $signupStatement->bindValue(':username', $inputs['username'], PDO::PARAM_STR);
                // Execution on the DB server.
                $success = $signupStatement->execute();
            
                    header("Location: dashboard/users");
                    exit();   
            }        
        }

        if(isset($_POST['confirm'])){
            // Sanitizing id data into a number.
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        // SQL query
        $query = "DELETE FROM serverside.users WHERE user_id = :id";
        
        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is an int
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        
        // Execution on the DB server.
        $statement->execute();
        
        // Variable session message added with delete message.
        
        // Then it is redirected to index.php.
        header("Location: dashboard/users");
        
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
                <div class="shadow-sm bg-white p-4">

                    <h2 class="card-title text-center mb-4">Update<span class="text-warning">User</span></h2>

                    <?php if(filterInput()): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>There's an error in the form. You have to fill all
                        the information.
                    </div>
                    <?php endif ?>

                    <?php if($passwordMatchError): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>Passwords don't match, please try again.
                    </div>
                    <?php endif ?>

                    <form method="post">
                        <input type="hidden" id="id" name="id" value="<?= $userData['user_id'] ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="fname" name="fname"
                                    value="<?= $userData['name'] ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="lname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lname" name="lname"
                                    value="<?= $userData['lastname'] ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= $userData['email'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= $userData['username'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Role</label>
                            <select class="form-select" id="category" name="role" required>
                                <option value="" disabled>- Choose a Role -</option>
                                <option value="<?= $userData['role'] ?>"><?= $userData['role'] ?></option>
                                <option value="<?= $secondOption ?>"><?= $secondOption ?></option>
                            </select>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="changePassword" id="changePassword">
                            <label class="form-check-label" for="checkDefault">
                                Change Password
                            </label>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>

                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                        </div>
                        <div class="container d-flex flex-column align-items-center w-100">
                        <div class="d-flex align-items-center justify-content-center py-0 mb-3 btn btn-primary w-100 mt-4">
                            <i class="fas fa-edit"></i>
                            <input type="submit" id="edituser" name="edituser" class="btn btn-primary"
                                value="Update User">
                        </div>
                        <button type="button" id="delete" data-bs-toggle="modal" data-bs-target="#deleteModal"
                            class="btn btn-danger w-100">
                            <i class="fas fa-trash-alt me-2"></i>Delete Item
                        </button>
                        </div>
                        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog"
                            aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this item? This action cannot be
                                            undone.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <input type="submit" class="btn btn-danger" name="confirm" value="Delete">
                                    </div>
                                </div>
                            </div>
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