<?php

// Require database data
require('connect.php');

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

}

// Function to verify there is data coming from the forms, and not blank or just whitespaces on the inputs.
function filterInput() {
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
        !(trim($_POST['content']) == ''
    )){
        return true;
    }else{
        return false;
    }
}



if(filterInput()){
    // If statement that checks if the data is coming from the create button from the create.php file.
    if(isset($_POST['create'])){
        // Sanitize special characters from the data. 
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $link = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $img = $file_filename;

        move_uploaded_file($temporary_file_path, $new_file_path);

        // SQL query
        $query = "INSERT INTO items (item_name, author, content, category_id, store_url, image) VALUES (:name, :author, :content, :category, :link, :img)";

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
        header("Location: index.php");
    }
}
?>