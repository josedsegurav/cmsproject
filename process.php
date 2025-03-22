<?php

// Require database data
require('connect.php');
require '\xampp\htdocs\webdev2\project\utils\lib\ImageResize.php';
require '\xampp\htdocs\webdev2\project\utils\lib\ImageResizeException.php';

use \Gumlet\ImageResize;

function file_upload_path($original_filename, $upload_subfolder_name = 'images') {
    $current_folder = dirname(__FILE__);
    
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
    
    return join(DIRECTORY_SEPARATOR, $path_segments);
 }

 function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
    
    $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type        = mime_content_type($temporary_path);
    
    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
    
    return $file_extension_is_valid && $mime_type_is_valid;
}

$file_upload_detected = isset($_FILES['file']) && ($_FILES['file']['error'] === 0);
$upload_error_detected = isset($_FILES['file']) && ($_FILES['file']['error'] > 0);

if ($file_upload_detected) {
    $original_file        =  $_FILES['file'];
    $file_filename        = $original_file['name'];
    $temporary_file_path  = $original_file['tmp_name'];
    $new_file_path        = file_upload_path($file_filename);

    $inputError = false;

// Function to verify there is data coming from the forms, and not blank or just whitespaces on the inputs.
function filterInput($temporary_file_path, $new_file_path) {
    if (
        $_POST && 
        !empty($_POST['name']) && 
        !empty($_POST['author']) &&
        file_is_an_image($temporary_file_path, $new_file_path) &&
        !empty($_POST['content']) &&
        isset($_POST['category']) &&
        !empty($_POST['link']) &&
        !(trim($_POST['name']) == '') &&
        !(trim($_POST['link']) == '') && 
        !(trim($_POST['content']) == '')
        ){
        return true;
    }else{
        return false;
    }
}



if(filterInput($temporary_file_path, $new_file_path)){
    // If statement that checks if the data is coming from the create button from the create.php file.
    if(isset($_POST['create'])){
        // Sanitize special characters from the data. 
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $img = $file_filename;

        move_uploaded_file($temporary_file_path, $new_file_path);

        $path_info = pathinfo($new_file_path);

        $medium_image = new ImageResize($new_file_path);
        $medium_image->resizeToWidth(300);
        $medium_image->save($path_info['dirname'] . DIRECTORY_SEPARATOR . "medium_" . $path_info['filename'] . "." . $path_info['extension']);

        // SQL query
        $query = "INSERT INTO items (item_name, author, content, category_id, store_url, image) 
                VALUES (:name, :author, :content, :category, :link, :img)";

        echo($query);
        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':author', $author, PDO::PARAM_STR);
        $statement->bindValue(':content', $content, PDO::PARAM_STR);
        $statement->bindValue(':category', $category, PDO::PARAM_INT);
        $statement->bindValue(':link', $link, PDO::PARAM_STR);
        $statement->bindValue(':img', $img, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();
        // Get the data from the DB after the query was executed.

        $row = $statement->fetch();

        // Then it is redirected to index.php.
        session_start();
        $_SESSION['message'] = "Item Created.";
        header("Location: ../");
    }

    if(isset($_POST['id']) && isset($_POST['update'])){
        // Sanitizing id data into a number.
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        // Sanitize special characters from the data.
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $img = $file_filename;
    
        // SQL query
        $query = "UPDATE items 
                SET item_name = :name, author = :author, content = :content, category_id = :category, store_url = :link, image = :img 
                WHERE item_id = :id";
    
        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is an int
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':author', $author, PDO::PARAM_STR);
        $statement->bindValue(':content', $content, PDO::PARAM_STR);
        $statement->bindValue(':category', $category, PDO::PARAM_INT);
        $statement->bindValue(':link', $link, PDO::PARAM_STR);
        $statement->bindValue(':img', $img, PDO::PARAM_STR);
    
        // Execution on the DB server.
        $statement->execute();
        session_start();
        $_SESSION['message'] = "Item Updated.";
    
        // Then it is redirected to edit.php according to it's id data.
        header("Location: edit/{$id}");
    }
}else{
    $inputError = true;
}
}else{
    function filterInput() {
        if (
            $_POST && 
            !empty($_POST['name']) && 
            !empty($_POST['author']) &&
            !empty($_POST['content']) &&
            isset($_POST['category']) &&
            !empty($_POST['link']) &&
            !(trim($_POST['name']) == '') &&
            !(trim($_POST['link']) == '') && 
            !(trim($_POST['content']) == '')
            ){
            return true;
        }else{
            return false;
        }
    }
    if(filterInput()){
        if($_FILES['file']['error'] === 4 && isset($_POST['id']) && isset($_POST['update'])){
            // Sanitizing id data into a number.
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            // Sanitize special characters from the data.
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
            $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
            // SQL query
            $query = "UPDATE items 
                    SET item_name = :name, author = :author, content = :content, category_id = :category, store_url = :link 
                    WHERE item_id = :id";
        
            // A PDO::Statement is prepared from the query. 
            $statement = $db->prepare($query);
            // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is an int
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':name', $name, PDO::PARAM_STR);
            $statement->bindValue(':author', $author, PDO::PARAM_STR);
            $statement->bindValue(':content', $content, PDO::PARAM_STR);
            $statement->bindValue(':category', $category, PDO::PARAM_INT);
            $statement->bindValue(':link', $link, PDO::PARAM_STR);
        
            // Execution on the DB server.
            $statement->execute();
            session_start();
            $_SESSION['message'] = "Item Updated.";
        
            // Then it is redirected to edit.php according to it's id data.
            header("Location: edit/{$id}");
        }
    }else{
        $inputError = true;
    }
}

if(isset($_POST['delete']) && isset($_POST['id'])){

    $upload_error_detected = false;
    // Sanitizing id data into a number.
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
}

// If statement checks if data is coming from the confirm button from the delete confirmation form. 
if(isset($_POST['confirm'])){
    // Sanitizing id data into a number.
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

// SQL query
$query = "DELETE FROM items WHERE item_id = :id";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);
// Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is an int
$statement->bindValue(':id', $id, PDO::PARAM_INT);

// Execution on the DB server.
$statement->execute();

session_start();
$_SESSION['message'] = "Item Deleted.";

// Then it is redirected to index.php.
header("Location: ../");

// // If statement checks if data is coming from the cancel button from the delete confirmation form.
}elseif (isset($_POST['cancel'])) {
    // Sanitizing id data into a number.
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    // It is redirected to edit.php according to it's id data.
    header("Location: edit/{$id}");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Post Process</title>
</head>

<body>
    <?php if ($upload_error_detected): ?>
    <p>There is a problem with the image. Error Number: <?= $_FILES['file']['error'] ?></p>
    <?php elseif(!$file_upload_detected && isset($_POST['file']) && !isset($_POST['delete'])): ?>
    <!-- If statement to display an error if data does not meet function filterInput requirements and it is not coming from the delete button -->
    <div id="wrapper">
        <!-- Nav imported from php file  -->
        <h2>There is an error on the entry.</h2>
        <p>Verify that all fields are properly filled.</p>
        <a href="index.php">Return Home</a>
    </div>
    <!-- If statement to display a confirm form if the data is coming from the delete button on edit.php file -->
    <?php elseif($_FILES['file']['error'] === 4 && isset($_POST['delete'])): ?>
    <div id="wrapper">
        <form method="post">
            <fieldset>
                <p>Confirm to delete post.
                    <input type="hidden" id="id" name="id" value="<?= $id ?>">
                    <input type="submit" id="confirm" name="confirm" value="Ok">
                    <input type="submit" id="cancel" name="cancel" value="Cancel">
                </p>
            </fieldset>
        </form>
    </div>
    <?php endif ?>
</body>

</html>