<?php
session_start();

require('connect.php');

$title = "Category Process";

function filterInput() {
    if (
        $_POST && 
        (!empty($_POST['newCategory']) && empty($_POST['updateCategoryName']) && !(trim($_POST['newCategory']) == '')) ||  
        (!empty($_POST['updateCategoryName']) && empty($_POST['newCategory'])) && !(trim($_POST['updateCategoryName']) == '')){
        return true;
    }else{
        return false;
    }
}

if(filterInput()){
    if(isset($_POST['createCategory'])){

        // Replacing spaces for dashes from name input to use it as a slug.
        $filterSlug = str_replace(" ", "-", $_POST['newCategory']);

        $newCategory = filter_input(INPUT_POST, 'newCategory', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $slug = filter_var($filterSlug, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "INSERT INTO serverside.categories (category_name, category_slug) 
        VALUES (:newCategory, :slug)";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
        $statement->bindValue(':newCategory', $newCategory, PDO::PARAM_STR);
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        unset($_SESSION['categoryFormError']);

        header("Location: dashboard/categories");

    }elseif(isset($_POST['updateCategory'])) {

        // Replacing spaces for dashes from name input to use it as a slug.
        $filterSlug = str_replace(" ", "-", $_POST['updateCategoryName']);
        $id = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        $updateCategory = filter_input(INPUT_POST, 'updateCategoryName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $slug = filter_var($filterSlug, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "UPDATE serverside.categories 
                SET category_name = :updateCategory, category_slug = :slug 
                WHERE category_id = :id";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':updateCategory', $updateCategory, PDO::PARAM_STR);
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        unset($_SESSION['categoryFormError']);

        header("Location: dashboard/categories");
    }
}else{
    $_SESSION['categoryFormError'] = "You need to fill the correct information.";
    header("Location: dashboard/categories");
}

if(isset($_POST['confirm'])){
// Sanitizing id data into a number.
$id = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

// SQL query
$query = "DELETE FROM serverside.categories WHERE category_id = :id";

// A PDO::Statement is prepared from the query.
$statement = $db->prepare($query);
// Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is an int
$statement->bindValue(':id', $id, PDO::PARAM_INT);

// Execution on the DB server.
$statement->execute();

// Variable session message added with delete message.

$_SESSION['message'] = "Item Deleted.";

unset($_SESSION['categoryFormError']);

// Then it is redirected to index.php.
header("Location: dashboard/categories");

}

?>
