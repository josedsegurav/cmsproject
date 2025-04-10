<?php
    session_start();

    if(empty($_SESSION['user'])){
        header("Location: /webdev2/project/login");
    }

    require('connect.php');

    $title = "Comment Process";

    function filterInput() {
        if (
            $_POST && 
            !empty($_POST['item_id']) &&
            !empty($_POST['user_id']) && 
            !empty($_POST['comment_text']) &&
            !(trim($_POST['comment_text']) == '')){
            return true;
        }else{
            return false;
        }
    }

    if(filterInput()){

        
            $item_id = filter_input(INPUT_POST, 'item_id', FILTER_SANITIZE_NUMBER_INT);
            $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
            $content = filter_input(INPUT_POST, 'comment_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $query = "INSERT INTO comments (user_id, item_id, comment_content) 
                        VALUES (:user_id, :item_id, :content)";
            
            // A PDO::Statement is prepared from the query. 
            $statement = $db->prepare($query);
            // Bind the value of the id coming from the GET and sanitized into the query.
            $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $statement->bindValue(':item_id', $item_id, PDO::PARAM_INT);
            $statement->bindValue(':content', $content, PDO::PARAM_STR);

            // Execution on the DB server.
            $statement->execute();
            
            if (isset($_SESSION['previous_page']) && isset($_SESSION['current_page'])) {
                if($_SESSION['current_page'] === "/webdev2/project/item.php"){
                header("Location: " . $_SESSION['previous_page']);
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
                <div class="card shadow-sm">
                    <div class="card-body p-4">
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
                            <?php if(isset($_GET['id'])): ?>
                            <input type="hidden" id="id" name="id" value="<?= $userData['user_id'] ?>">
                            <?php endif ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="fname" name="fname"
                                        value="<?= isset($_GET['p']) ? $userData['name'] : '' ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="lname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lname" name="lname" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
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
                                <input type="submit" id="adduser" name="adduser" class="btn btn-primary">Create
                                User</input>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <?php include('footer.php'); ?>
</body>

</html>