<?php
if(isset($_POST['createCategory'])){

    // Replacing spaces for dashes from name input to use it as a slug.
    $filterSlug = str_replace(" ", "-", $_POST['newCategory']);

    $newCategory = filter_input(INPUT_POST, 'newCategory', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $slug = filter_var($filterSlug, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "INSERT INTO categories (category_name, category_slug) 
    VALUES (:newCategory, :slug)";

    // A PDO::Statement is prepared from the query. 
    $statement = $db->prepare($query);
    // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
    $statement->bindValue(':newCategory', $newCategory, PDO::PARAM_STR);
    $statement->bindValue(':slug', $slug, PDO::PARAM_STR);

    // Execution on the DB server.
    $statement->execute();

    header("Location: /webdev2/project/add");

}elseif (isset($_POST['cancelCreateCategory'])) {
    // It is redirected to edit.php according to it's id data.
    header("Location: /webdev2/project/add");
}

?>
            <form method="post">
                <input type="submit" id="addCategory" name="addCategory" value="Add Category">
            </form>
<div>
        <!-- Delete confirmation form -->
        <form method="post">
            <fieldset>
                <label for="newCategory">New Category</label>
                <input id="newCategory" type="text" name="newCategory">
                <input type="submit" id="createCategory" name="createCategory" value="Add Category">
                <input type="submit" id="cancelCreateCategory" name="cancelCreateCategory" value="Cancel">
            </fieldset>
        </form>
    </div>