<?php

// Require database data
require('connect.php');
// Require library to resize images
require '\xampp\htdocs\webdev2\project\utils\lib\ImageResize.php';
require '\xampp\htdocs\webdev2\project\utils\lib\ImageResizeException.php';

use \Gumlet\ImageResize;
// Variable to add a name to the title in the html head tag
$title = "Item Processing";
// Filter empty inputs form the form.
function filterInput() {
    if (
        $_POST && 
        !empty($_POST['name']) && 
        !empty($_POST['author']) &&
        !empty($_POST['content']) &&
        (isset($_POST['category']) || !empty($_POST['newCategory'])) &&
        !empty($_POST['link']) &&
        !(trim($_POST['name']) == '') &&
        !(trim($_POST['author']) == '') &&
        !(trim($_POST['link']) == '') && 
        !(trim($_POST['content']) == '')
        ){
        return true;
    }else{
        return false;
    }
}

function getInputs(){
if(filterInput()){
    $itemContent = $_POST['content'];

    $content = strip_tags($itemContent);

    $nameInput = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_URL);
    $slug = str_replace(" ", "-", $nameInput);

    
    $inputs = [
            'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'author' => filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'content' => filter_var($content, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'category' => filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT),
            'link' => filter_input(INPUT_POST, 'link', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'slug' => filter_var($slug, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
    ];

    return $inputs;
}
}

getInputs();

// file_upload_path() - Safely build a path String that uses slashes appropriate for our OS.
// Default upload path is an 'uploads' sub-folder in the current folder.
function file_upload_path($original_filename, $upload_subfolder_name = 'images') {
    $current_folder = dirname(__FILE__);
    
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
    
    return join(DIRECTORY_SEPARATOR, $path_segments);
 }
    
 // file_is_an_image() - Checks the mime-type & extension of the uploaded file for "image-ness".
 function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
    
    $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type        = mime_content_type($temporary_path);
    
    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
    
    return $file_extension_is_valid && $mime_type_is_valid;
}

// Variables to verify if a file has been uploaded, and if there has been errors during the upload process.
$file_upload_detected = isset($_FILES['file']) && ($_FILES['file']['error'] === 0);
$upload_error_detected = isset($_FILES['file']) && ($_FILES['file']['error'] > 0);

// Check if the file is uploaded, get the data form the file and prepare it to move it to the storage.
if ($file_upload_detected) {
    $original_file        =  $_FILES['file'];
    $file_filename        = $original_file['name'];
    $temporary_file_path  = $original_file['tmp_name'];
    $new_file_path        = file_upload_path($file_filename);

if(filterInput() && file_is_an_image($temporary_file_path, $new_file_path)){
    // If statement that checks if the data is coming from the create button from the create.php file.
    if(isset($_POST['create'])){
        // Sanitize special characters from the data. 
        
        // $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // $content = filter_var($content, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        // $newCategory = filter_input(INPUT_POST, 'newCategory', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $img = $file_filename;

        move_uploaded_file($temporary_file_path, $new_file_path);

        $path_info = pathinfo($new_file_path);

        $medium_image = new ImageResize($new_file_path);
        $medium_image->resizeToWidth(300);
        $medium_image->save($path_info['dirname'] . DIRECTORY_SEPARATOR . "medium_" . $path_info['filename'] . "." . $path_info['extension']);

        // SQL query
        $query = "INSERT INTO items (item_name, author, content, category_id, store_url, image, slug) 
                VALUES (:name, :author, :content, :category, :link, :img, :slug)";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
        $statement->bindValue(':name', getInputs()['name'], PDO::PARAM_STR);
        $statement->bindValue(':author', getInputs()['author'], PDO::PARAM_STR);
        $statement->bindValue(':content', strip_tags(getInputs()['content']), PDO::PARAM_STR);
        $statement->bindValue(':category', getInputs()['category'], PDO::PARAM_INT);
        $statement->bindValue(':link', getInputs()['link'], PDO::PARAM_STR);
        $statement->bindValue(':img', $img, PDO::PARAM_STR);
        $statement->bindValue(':slug', getInputs()['slug'], PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();
        // Get the data from the DB after the query was executed.

        $row = $statement->fetch();

        // Variable session message added with create message.
        session_start();
        $_SESSION['message'] = "Item Created.";
        // Then it is redirected to index.php.
        header("Location: /webdev2/project/");
    }

    if(isset($_POST['id']) && isset($_POST['update'])){
        // Sanitizing id data into a number.

        $itemContent = $_POST['content'];

        $content = strip_tags($itemContent);
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        // Sanitize special characters from the data.
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_var($content, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $img = $file_filename;

        move_uploaded_file($temporary_file_path, $new_file_path);
    
        $path_info = pathinfo($new_file_path);

        $medium_image = new ImageResize($new_file_path);
        $medium_image->resizeToWidth(300);
        $medium_image->save($path_info['dirname'] . DIRECTORY_SEPARATOR . "medium_" . $path_info['filename'] . "." . $path_info['extension']);

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
        // Variable session message added with update message.
        session_start();
        $_SESSION['message'] = "Item Updated.";
    
        // Then it is redirected to edit.php according to it's id data.
        header("Location: edit/{$id}");
    }
}
}else{

    if(filterInput()){
        // If statement that checks if the data is coming from the create button from the create.php file.
    if(isset($_POST['create'])){
        // Sanitize special characters from the data. 
        $itemContent = $_POST['content'];

        $content = strip_tags($itemContent);
        
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_var($content, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // SQL query
        $query = "INSERT INTO items (item_name, author, content, category_id, store_url) 
                VALUES (:name, :author, :content, :category, :link)";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
        $statement->bindValue(':name', $name, PDO::PARAM_STR);
        $statement->bindValue(':author', $author, PDO::PARAM_STR);
        $statement->bindValue(':content', strip_tags($content), PDO::PARAM_STR);
        $statement->bindValue(':category', $category, PDO::PARAM_INT);
        $statement->bindValue(':link', $link, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();
        // Get the data from the DB after the query was executed.
        $row = $statement->fetch();

        // Variable session message added with create message.
        session_start();
        $_SESSION['message'] = "Item Created.";
        // Then it is redirected to index.php.
        header("Location: /webdev2/project/");
    }

        // Check if the error is an empty file input, if the id is set and the request comes from edit.php update button.
        if($_FILES['file']['error'] === 4 && isset($_POST['id']) && isset($_POST['update'])){
            // Sanitizing id data into a number.
            $itemContent = $_POST['content'];

            $content = strip_tags($itemContent);
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            // Sanitize special characters from the data.
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $content = filter_var($content, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
            // Variable session message added with update message.
            session_start();
            $_SESSION['message'] = "Item Updated.";
        
            // Then it is redirected to edit.php according to it's id data.
            header("Location: edit/{$id}");
        }
    }
}

// Verify if the request comes from edit.php delete button and if the id is set.
if(isset($_POST['delete']) && isset($_POST['id'])){

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

// Variable session message added with delete message.
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
<!-- Include head tag from template -->
<?php include('htmlHead.php'); ?>

<body>
    <!-- Include nav tag from template -->
    <?php include('nav.php'); ?>
    <!-- Error message if there are empty inputs -->
    <?php if(!filterInput()): ?>
    <div>
        <h2>There is an error on the entry.</h2>
        <p>Verify that all fields are properly filled.</p>
    </div>
    <?php endif ?>
    <!-- Error message if there no file is uploaded and the request is not from the delete button -->
    <?php if(($upload_error_detected != 0 && !isset($_POST['delete'])) || (!file_is_an_image($temporary_file_path, $new_file_path) && !isset($_POST['delete']))): ?>
    <div>
        <h2>There is an error code: <?= $_FILES['file']['error'] ?> on the entry.</h2>
        <p>Verify that you are uploading an image file.</p>
    </div>
    <!-- Display a confirm form if there is no file bieng uploaded and the data is coming from the delete button on edit.php -->
    <?php elseif($_FILES['file']['error'] === 4 && isset($_POST['delete'])): ?>
    <div>
        <!-- Delete confirmation form -->
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