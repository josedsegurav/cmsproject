<?php
session_start();
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
        !empty($_POST['content']) &&
        (isset($_POST['category'])) &&
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

function getInputs(){
if(filterInput()){
    // Filter html tags from the WYSIWYG editor. 
    $itemContent = $_POST['content'];
    $content = strip_tags($itemContent);

    // Replacing spaces for dashes from name input to use it as a slug.
    $nameSlug = str_replace(" ", "-", $_POST['name']);
    $cleanSlug = preg_replace('/[^A-Za-z0-9\-]/', '', $nameSlug);
    $singlehyphenSlug = preg_replace('/-+/', '-', $cleanSlug);
    $finalSlug = preg_replace('/-$/', '', $singlehyphenSlug);
    
    $userSlug = "";
    
    // Sanitize special characters from the data and storing it into an array.
    $inputs = [
    'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
    'content' => filter_var($content, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
    'category' => filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT),
    'link' => filter_input(INPUT_POST, 'link', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
    ];

    if(isset($_POST['slugCheck']) && !empty($_POST['slug']) && !(trim($_POST['slug']) == '')){
        $nameSlug = str_replace(" ", "-", $_POST['slug']);
        $cleanSlug = preg_replace('/[^A-Za-z0-9\-]/', '', $nameSlug);
        $singlehyphenSlug = preg_replace('/-+/', '-', $cleanSlug);
        $finalSlug = preg_replace('/-$/', '', $singlehyphenSlug);
        $inputs['slug'] = $finalSlug;
    }else{
        $inputs['slug'] = $finalSlug;
    }

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
    $allowed_mime_types      = ['image/gif', 'image/jpg', 'image/jpeg', 'image/png'];
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

        $user_id = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT);

        $img = $file_filename;

        move_uploaded_file($temporary_file_path, $new_file_path);

        $path_info = pathinfo($new_file_path);

        $medium_image = new ImageResize($new_file_path);
        $medium_image->resizeToWidth(300);
        $medium_image->save($path_info['dirname'] . DIRECTORY_SEPARATOR . "medium_" . $path_info['filename'] . "." . $path_info['extension']);

        // SQL query
        $query = "INSERT INTO serverside.items (item_name, user_id, content, category_id, store_url, image, slug) 
                VALUES (:name, :user_id, :content, :category, :link, :img, :slug)";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
        $statement->bindValue(':name', getInputs()['name'], PDO::PARAM_STR);
        $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $statement->bindValue(':content', strip_tags(getInputs()['content']), PDO::PARAM_STR);
        $statement->bindValue(':category', getInputs()['category'], PDO::PARAM_INT);
        $statement->bindValue(':link', getInputs()['link'], PDO::PARAM_STR);
        $statement->bindValue(':img', $img, PDO::PARAM_STR);
        $statement->bindValue(':slug', getInputs()['slug'], PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        // Variable session message added with create message.
        
        $_SESSION['message'] = "Item Created.";
        // Then it is redirected to index.php.
       header("Location: ../dashboard/items");
    }

    if(isset($_POST['id']) && isset($_POST['update'])){
        // Sanitizing id data into a number.
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $user_id = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT);

        move_uploaded_file($temporary_file_path, $new_file_path);
    
        $path_info = pathinfo($new_file_path);

        $medium_image = new ImageResize($new_file_path);
        $medium_image->resizeToWidth(300);
        $medium_image->save($path_info['dirname'] . DIRECTORY_SEPARATOR . "medium_" . $path_info['filename'] . "." . $path_info['extension']);

        // SQL query
        $query = "UPDATE serverside.items 
                SET item_name = :name, user_id = :user_id, content = :content, category_id = :category, store_url = :link, image = :img, slug = :slug 
                WHERE item_id = :id";
    
        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is an int
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':name', getInputs()['name'], PDO::PARAM_STR);
        $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $statement->bindValue(':content', strip_tags(getInputs()['content']), PDO::PARAM_STR);
        $statement->bindValue(':category', getInputs()['category'], PDO::PARAM_INT);
        $statement->bindValue(':link', getInputs()['link'], PDO::PARAM_STR);
        $statement->bindValue(':img', $img, PDO::PARAM_STR);
        $statement->bindValue(':slug', getInputs()['slug'], PDO::PARAM_STR);
    
        // Execution on the DB server.
        $statement->execute();
        $item = $statement->fetch();
        // Variable session message added with update message.

        $_SESSION['message'] = "Item Updated.";
    
        // Then it is redirected to edit.php according to it's id data.
       header("Location: ../dashboard/items");
    }
}
}else{

    if(filterInput()){
        // If statement that checks if the data is coming from the create button from the create.php file.
    if($_FILES['file']['error'] === 4 && isset($_POST['create'])){
        $user_id = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT);
        // SQL query
        $query = "INSERT INTO serverside.items (item_name, user_id, content, category_id, store_url, slug) 
                VALUES (:name, :user_id, :content, :category, :link, :slug)";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
        $statement->bindValue(':name', getInputs()['name'], PDO::PARAM_STR);
        $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $statement->bindValue(':content', strip_tags(getInputs()['content']), PDO::PARAM_STR);
        $statement->bindValue(':category', getInputs()['category'], PDO::PARAM_INT);
        $statement->bindValue(':link', getInputs()['link'], PDO::PARAM_STR);
        $statement->bindValue(':slug', getInputs()['slug'], PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();
        // Variable session message added with create message.
        
        $_SESSION['message'] = "Item Created.";
        // Then it is redirected to index.php.
        header("Location: ../dashboard/items");
    }

        // Check if the error is an empty file input, if the id is set and the request comes from edit.php update button.
        if($_FILES['file']['error'] === 4 && isset($_POST['id']) && isset($_POST['update'])){
            // Sanitizing id data into a number.
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $user_id = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT);
            
            // SQL query
            $query = "UPDATE serverside.items 
                    SET item_name = :name, content = :content, category_id = :category, store_url = :link, slug = :slug 
                    WHERE item_id = :id AND user_id = :user_id";
        
            // A PDO::Statement is prepared from the query. 
            $statement = $db->prepare($query);
            // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is an int
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':name', getInputs()['name'], PDO::PARAM_STR);
            $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $statement->bindValue(':content', strip_tags(getInputs()['content']), PDO::PARAM_STR);
            $statement->bindValue(':category', getInputs()['category'], PDO::PARAM_INT);
            $statement->bindValue(':link', getInputs()['link'], PDO::PARAM_STR);
            $statement->bindValue(':slug', getInputs()['slug'], PDO::PARAM_STR);
        
            // Execution on the DB server.
            $statement->execute();
    
            // Variable session message added with update message.
            $_SESSION['message'] = "Item Updated.";
        
            // Then it is redirected to dashboard items tab
            header("Location: ../dashboard/items");
        }
    }
}

// If statement checks if data is coming from the confirm button from the delete confirmation form. 
if(isset($_POST['confirm'])){
    // Sanitizing id data into a number.
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$user_id = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT);

// SQL query
$query = "DELETE FROM serverside.items WHERE item_id = :id AND user_id = :user_id";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);
// Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is an int
$statement->bindValue(':id', $id, PDO::PARAM_INT);
$statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);


// Execution on the DB server.
$statement->execute();

// Variable session message added with delete message.

$_SESSION['message'] = "Item Deleted.";

// Then it is redirected to index.php.
header("Location: ../dashboard/items");

// // If statement checks if data is coming from the cancel button from the delete confirmation form.
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
    <?php if((!filterInput() && $upload_error_detected && !isset($_POST['delete'])) || ($file_upload_detected && !file_is_an_image($temporary_file_path, $new_file_path) && !isset($_POST['delete']))): ?>
    <div>
        <h2>There is an error code: <?= $_FILES['file']['error'] ?> on the entry.</h2>
        <p>Verify that you are uploading an image file.</p>
    </div>
    <?php endif ?>
</body>

</html>