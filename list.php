<?php
    // Require authentication script to protect data manipulation from unauthorized users
    require 'authenticate.php';
    // Require database data
    require('connect.php');
    // Variable to add a name to the title in the html head tag
    $title = "Items List";
    // SQL query to fill the category select tag
    $category_query = "SELECT * FROM categories";
    // A PDO::Statement is prepared from the query.
    $categoryStatement = $db->prepare($category_query);
    // Execution on the DB server.
    $categoryStatement->execute();
    $categories = $categoryStatement->fetchAll();

    // SQL query to display first set of information being order by date created Asc
    $general_query = "SELECT i.item_id, i.item_name, i.author, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name
        FROM items i
        JOIN categories c ON c.category_id = i.category_id
        ORDER BY i.date_created";
    // A PDO::Statement is prepared from the query.
    $general_statement = $db->prepare($general_query);
    // Execution on the DB server.
    $general_statement->execute();
    $items = $general_statement->fetchAll();

    // Function to display the first set of data before any sorting.
    function generalListDisplay(){
        if(!isset($_POST['search']) && 
        !isset($_POST['sortByNameAsc']) && 
        !isset($_POST['sortByAuthorAsc']) && 
        !isset($_POST['sortByDateAsc']) && 
        !isset($_POST['sortByNameDesc']) && 
        !isset($_POST['sortByAuthorDesc']) && 
        !isset($_POST['sortByDateDesc']) && 
        !isset($_POST['sortByCategoryDesc']) && 
        !isset($_POST['sortByCategoryAsc'])){
            return false;
        }else{
            return true;
        }
    }

    // Verify if there is a sort request from the sort form. 
    function sortDisplay() {
        if(isset($_POST['sortByNameDesc']) || 
        isset($_POST['sortByNameAsc']) || 
        isset($_POST['sortByAuthorDesc']) || 
        isset($_POST['sortByAuthorAsc']) || 
        isset($_POST['sortByDateDesc']) || 
        isset($_POST['sortByDateAsc']) || 
        isset($_POST['sortByCategoryDesc']) || 
        isset($_POST['sortByCategoryAsc'])) {
            return true;
        }else{
            return false;
        }
    }

    // Variable as a main query template
    $mainQuery = "SELECT i.item_id, i.item_name, i.author, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name
    FROM items i
    JOIN categories c ON c.category_id = i.category_id";

    // Sorting by Item Name in Descending order
    if(isset($_POST['sortByNameDesc'])) {

        $sortHeader = "Item Name in Descending Order";
        
        $sortQuery = $mainQuery . " ORDER BY i.item_name DESC";
        // A PDO::Statement is prepared from the query.
        $sortStatement = $db->prepare($sortQuery);

    // Execution on the DB server.
        $sortStatement->execute();
        $sortedList = $sortStatement->fetchAll();
    }

    // Sorting by Item Name in ascending order
    if(isset($_POST['sortByNameAsc'])) {

        $sortHeader = "Item Name in Ascending Order";
        
        $sortQuery = $mainQuery . " ORDER BY i.item_name";
        // A PDO::Statement is prepared from the query.
        $sortStatement = $db->prepare($sortQuery);

    // Execution on the DB server.
        $sortStatement->execute();
        $sortedList = $sortStatement->fetchAll();
    }

    // Sorting by Author Name in Descending order
    if(isset($_POST['sortByAuthorDesc'])) {

        $sortHeader = "Author Name in Descending Order";
        
        $sortQuery = $mainQuery . " ORDER BY i.author DESC";
        // A PDO::Statement is prepared from the query.
        $sortStatement = $db->prepare($sortQuery);

    // Execution on the DB server.
        $sortStatement->execute();
        $sortedList = $sortStatement->fetchAll();
    }

    // Sorting by Author Name in Ascending order
    if(isset($_POST['sortByAuthorAsc'])) {

        $sortHeader = "Item Name in Ascending Order";
        
        $sortQuery = $mainQuery . " ORDER BY i.author";
        // A PDO::Statement is prepared from the query.
        $sortStatement = $db->prepare($sortQuery);

    // Execution on the DB server.
        $sortStatement->execute();
        $sortedList = $sortStatement->fetchAll();
    }

    // Sorting by Date Created in Descending order
    if(isset($_POST['sortByDateDesc'])) {

        $sortHeader = "Date in Descending Order";
        
        $sortQuery = $mainQuery . " ORDER BY i.date_created DESC";
        // A PDO::Statement is prepared from the query.
        $sortStatement = $db->prepare($sortQuery);

    // Execution on the DB server.
        $sortStatement->execute();
        $sortedList = $sortStatement->fetchAll();
    }

    // Sorting by Date Created in Ascending order
    if(isset($_POST['sortByDateAsc'])) {

        $sortHeader = "Date in Ascending Order";
        
        $sortQuery = $mainQuery . " ORDER BY i.date_created";
        // A PDO::Statement is prepared from the query.
        $sortStatement = $db->prepare($sortQuery);

    // Execution on the DB server.
        $sortStatement->execute();
        $sortedList = $sortStatement->fetchAll();
    }

    // Sorting by Category in Descending order
    if(isset($_POST['sortByCategoryDesc'])) {

        $sortHeader = "Category in Descending Order";
        
        $sortQuery = $mainQuery . " ORDER BY c.category_name DESC";
        // A PDO::Statement is prepared from the query.
        $sortStatement = $db->prepare($sortQuery);

    // Execution on the DB server.
        $sortStatement->execute();
        $sortedList = $sortStatement->fetchAll();
    }

    // Sorting by Category in Ascending order
    if(isset($_POST['sortByCategoryAsc'])) {

        $sortHeader = "Category in Ascending Order";
        
        $sortQuery = $mainQuery . " ORDER BY c.category_name";
        // A PDO::Statement is prepared from the query.
        $sortStatement = $db->prepare($sortQuery);

    // Execution on the DB server.
        $sortStatement->execute();
        $sortedList = $sortStatement->fetchAll();
    }
    
    // Search functionality
    if (isset($_POST['search'])) {

        // Verify if the input variable passed is set and not empty.
        function filterInput($input){
            if(isset($input) && !empty($input) && !(trim($input) == '')){
                return true;
            }else{
                return false;
            }
        }

        // Search by name
        if (filterInput($_POST['name'])) {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $query = "SELECT i.item_id, i.item_name, i.author, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name
        FROM items i
        JOIN categories c ON c.category_id = i.category_id
        WHERE i.item_name LIKE :name
        ORDER BY i.date_created DESC";
    // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);

            $statement->bindValue(':name', "%$name%", PDO::PARAM_STR);

    // Execution on the DB server.
            $statement->execute();
            $searchByItemName = $statement->fetchAll();
        }else{
            $searchByItemName = false;
        }

        // Search by author name
        if (filterInput($_POST['author'])) {
            $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $query = "SELECT i.item_id, i.item_name, i.author, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name
        FROM items i
        JOIN categories c ON c.category_id = i.category_id
        WHERE i.author LIKE :author
        ORDER BY i.date_created DESC";
    // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);

            $statement->bindValue(':author', "%$author%", PDO::PARAM_STR);

    // Execution on the DB server.
            $statement->execute();
            $searchByAuthor = $statement->fetchAll();
        }else{
            $searchByAuthor = false;
        }

        // Search by category
        if (isset($_POST['category'])) {
            $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

            $query = "SELECT i.item_id, i.item_name, i.author, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name
        FROM items i
        JOIN categories c ON c.category_id = i.category_id
        WHERE i.category_id = :category
        ORDER BY i.date_created DESC";
    // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);

            $statement->bindValue(':category', $category, PDO::PARAM_INT);

    // Execution on the DB server.
            $statement->execute();
            $searchByCategory = $statement->fetchAll();
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<!-- Include head tag from template -->
<?php include('htmlHead.php'); ?>

<body>
    <!-- Include nav tag from template -->
    <?php include 'nav.php'; ?>
    <!-- Form for sort functioanility -->
    <form method="post">
        <p>Sort List By:</p>
        <label>Item Name</label>
        <input type="submit" id="nameAsc" name="sortByNameAsc" value="⬆ a-z">
        <input type="submit" id="nameDesc" name="sortByNameDesc" value="⬇ z-a">
        <label>Author Name</label>
        <input type="submit" id="authorAsc" name="sortByAuthorAsc" value="⬆ a-z">
        <input type="submit" id="authorDesc" name="sortByAuthorDesc" value="⬇ z-a">

        <label>Category</label>
        <input type="submit" id="categoryAsc" name="sortByCategoryAsc" value="⬆ a-z">
        <input type="submit" id="categoryDesc" name="sortByCategoryDesc" value="⬇ z-a">

        <label>Date Created</label>
        <input type="submit" id="dateAsc" name="sortByDateAsc" value="⬆">
        <input type="submit" id="dateDesc" name="sortByDateDesc" value="⬇">

    </form>

    <!-- Form for Search functionality -->
    <form method="post">
        <label for="name">Item Name</label>
        <input id="name" type="text" name="name">

        <label for="author">Author Name</label>
        <input id="author" type="text" name="author">

        <label for="category">Category</label>
        <select id="category" name="category">
            <option value="" disabled selected>- Choose a Category -</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?= $category['category_id']?>"><?= $category['category_name']?></option>
            <?php endforeach?>
        </select>

        <input type="submit" id="search" name="search" value="Search">
    </form>
    <!-- Display first set of data before any sort or search -->
    <?php if (!generalListDisplay()): ?>
    <?php foreach ($items as $item): ?>
    <div>
        <!-- Include a template for each item display -->
        <?php include 'listItemTemplate.php'; ?>
    </div>
    <?php endforeach?>
    <?php endif?>
    <!-- Display items by sorting -->
    <?php if (sortDisplay()): ?>
    <h2>Sorted by <?= $sortHeader ?></h2>
    <?php foreach ($sortedList as $item): ?>
    <div>
        <!-- Include a template for each item display -->
        <?php include 'listItemTemplate.php'; ?>
    </div>
    <?php endforeach?>
    <?php endif?>

    <!-- Error displayed when no search inputs are filled when submitting the search form -->
    <?php if (isset($_POST['search']) && !filterInput($_POST['name']) && !filterInput($_POST['author']) && !isset($_POST['category'])): ?>
    <p>You have to fill one field to search.</p>
    <?php endif?>

    <!-- Display items based category on search or error if there are no results -->
    <?php if (isset($_POST['search']) && isset($_POST['category'])): ?>
    <?php if ($searchByCategory): ?>
    <h2>Results for "<?= $searchByCategory[0]['category_name'] ?>" in Categories</h2>
    <?php foreach ($searchByCategory as $item): ?>
    <div>
        <!-- Include a template for each item display -->
        <?php include 'listItemTemplate.php'; ?>
    </div>
    <?php endforeach?>
    <?php else: ?>
    <p>There are no items in this category.</p>
    <?php endif?>
    <?php endif?>

    <!-- Display items based on author search or error if there are no results -->
    <?php if (isset($_POST['search']) && filterInput($_POST['author'])): ?>
    <?php if ($searchByAuthor): ?>
    <h2>Results for "<?= $_POST['author'] ?>" in Author</h2>
    <?php foreach ($searchByAuthor as $item): ?>
    <div>
        <!-- Include a template for each item display -->
        <?php include 'listItemTemplate.php'; ?>
    </div>
    <?php endforeach?>
    <?php else: ?>
    <p>There are no items with this Author.</p>
    <?php endif?>
    <?php endif?>

    <!-- Display items based on item name search or error if there are no results -->
    <?php if (isset($_POST['search']) && filterInput($_POST['name'])): ?>
    <?php if ($searchByItemName): ?>
    <h2>Results for "<?= $_POST['name'] ?>" in Item Name</h2>
    <?php foreach ($searchByItemName as $item): ?>
    <div>
        <!-- Include a template for each item display -->
        <?php include 'listItemTemplate.php'; ?>
    </div>
    <?php endforeach?>
    <?php else: ?>
    <p>There are no items with this name.</p>
    <?php endif?>
    <?php endif?>
</body>

</html>