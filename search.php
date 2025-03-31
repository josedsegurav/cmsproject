<?php
session_start();

require('connect.php');

$title = "Results";

function filterInput() {
    if (
        $_POST && 
        !empty($_POST['searchInput']) && 
        !(trim($_POST['searchInput']) == '')
        ){
        return true;
    }else{
        return false;
    }
}

$resultsPerPage = 3;
$currentPage = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
$startQueryAt = $resultsPerPage * ($currentPage - 1);

// Handle GET request for pagination
if(isset($_GET['query'])) {
    // Sanitizing search data
    $search = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    // Check if category is specified
    if(isset($_GET['category'])) {
        // Sanitizing category data
        $categoryQuery = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);
        
        // Count query for category-specific search
        $countResultsQuery = "SELECT COUNT(*) FROM items i 
                             JOIN categories c ON c.category_id = i.category_id 
                             WHERE i.category_id = :category
                             AND (i.item_name LIKE :search OR i.author LIKE :search)";
        
        $countResultsStatement = $db->prepare($countResultsQuery);
        $countResultsStatement->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $countResultsStatement->bindValue(':category', $categoryQuery, PDO::PARAM_INT);
        $countResultsStatement->execute();
        $countSearchResults = $countResultsStatement->fetch();
        
        // Query for items with category filter
        $query = "SELECT i.item_id, i.item_name, i.author, i.content, i.category_id, i.store_url, 
                 i.image, i.date_created, i.slug, c.category_name, c.category_slug  
                 FROM items i 
                 JOIN categories c ON c.category_id = i.category_id 
                 WHERE i.category_id = :category
                 AND (i.item_name LIKE :search OR i.author LIKE :search)
                 LIMIT $startQueryAt, $resultsPerPage";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $statement->bindValue(':category', $categoryQuery, PDO::PARAM_INT);
        $statement->execute();
        $searchResults = $statement->fetchAll();
    } else {
        // Count query for general search
        $countResultsQuery = "SELECT COUNT(*) FROM items i 
                             JOIN categories c ON c.category_id = i.category_id 
                             WHERE i.item_name LIKE :search 
                             OR i.author LIKE :search";
        
        $countResultsStatement = $db->prepare($countResultsQuery);
        $countResultsStatement->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $countResultsStatement->execute();
        $countSearchResults = $countResultsStatement->fetch();
        
        // Query for all items matching search
        $query = "SELECT i.item_id, i.item_name, i.author, i.content, i.category_id, i.store_url, 
                 i.image, i.date_created, i.slug, c.category_name, c.category_slug  
                 FROM items i 
                 JOIN categories c ON c.category_id = i.category_id 
                 WHERE i.item_name LIKE :search
                 OR i.author LIKE :search
                 LIMIT $startQueryAt, $resultsPerPage";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $statement->execute();
        $searchResults = $statement->fetchAll();
    }
    
    // Calculate total pages
    $pages = ceil($countSearchResults[0] / $resultsPerPage);
}

// Handle POST requests (initial search)
if(filterInput()) {
    // Sanitizing search data
    $search = filter_input(INPUT_POST, 'searchInput', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    if($_POST['category'] === "default") {
        $categoryQuery = "";

        
        // Count query for general search
        $countResultsQuery = "SELECT COUNT(*) FROM items i 
                             JOIN categories c ON c.category_id = i.category_id 
                             WHERE i.item_name LIKE :search 
                             OR i.author LIKE :search";
        
        $countResultsStatement = $db->prepare($countResultsQuery);
        $countResultsStatement->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $countResultsStatement->execute();
        $countSearchResults = $countResultsStatement->fetch();
        
        // Query for all items matching search
        $query = "SELECT i.item_id, i.item_name, i.author, i.content, i.category_id, i.store_url, 
                 i.image, i.date_created, i.slug, c.category_name, c.category_slug  
                 FROM items i 
                 JOIN categories c ON c.category_id = i.category_id 
                 WHERE i.item_name LIKE :search
                 OR i.author LIKE :search
                 LIMIT $startQueryAt, $resultsPerPage";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $statement->execute();
        $searchResults = $statement->fetchAll();
    } else {
        // Sanitizing category data
        $categoryQuery = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        echo($categoryQuery);
        
        // Count query for category-specific search
        $countResultsQuery = "SELECT COUNT(*) FROM items i 
                             JOIN categories c ON c.category_id = i.category_id 
                             WHERE i.category_id = :category
                             AND (i.item_name LIKE :search OR i.author LIKE :search)";
        
        $countResultsStatement = $db->prepare($countResultsQuery);
        $countResultsStatement->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $countResultsStatement->bindValue(':category', $categoryQuery, PDO::PARAM_INT);
        $countResultsStatement->execute();
        $countSearchResults = $countResultsStatement->fetch();
        
        // Query for items with category filter
        $query = "SELECT i.item_id, i.item_name, i.author, i.content, i.category_id, i.store_url, 
                 i.image, i.date_created, i.slug, c.category_name, c.category_slug  
                 FROM items i 
                 JOIN categories c ON c.category_id = i.category_id 
                 WHERE i.category_id = :category
                 AND (i.item_name LIKE :search OR i.author LIKE :search)
                 LIMIT $startQueryAt, $resultsPerPage";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $statement->bindValue(':category', $categoryQuery, PDO::PARAM_INT);
        $statement->execute();
        $searchResults = $statement->fetchAll();
    }
    
    // Calculate total pages
    $pages = ceil($countSearchResults[0] / $resultsPerPage);
}

?>

<!DOCTYPE html>
<html lang="en">

<?php include('htmlHead.php'); ?>

<body>
    <?php include('nav.php'); ?>
    
    <!-- Search Results Header -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="fw-bold mb-3">Search Results</h1>
                    <p class="lead text-muted">
                        <?php if(filterInput() && !empty($searchResults)): ?>
                            Showing <?= $countSearchResults[0] ?> results
                        <?php endif ?>
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Search Results Content -->
    <section class="py-5">
        <div class="container">
            <?php if(filterInput() || isset($_GET['query']) && !empty($searchResults)): ?>
                <div class="row">
                    <?php foreach ($searchResults as $item): ?>
                    <?php include('listItemTemplate.php') ?>
                    <?php endforeach ?>
                </div>
                
                <!-- Pagination -->
                <?php if(!empty($searchResults) && $countSearchResults[0] >= $resultsPerPage): ?>
                <nav aria-label="Search results pages" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php if($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>&query=<?= $search ?><?= !empty($categoryQuery) ? "&category={$categoryQuery}" : "" ?>" aria-label="Previous" style="color: #2c3e50;">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php endif ?>
                        
                        <?php for($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&query=<?= $search ?><?= !empty($categoryQuery) ? "&category={$categoryQuery}" : "" ?>" style="<?= $i == $currentPage ? 'background-color: #2c3e50; border-color: #2c3e50;' : 'color: #2c3e50;' ?>"><?= $i ?></a>
                        </li>
                        <?php endfor ?>
                        
                        <?php if($currentPage < $pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>&query=<?= $search ?><?= !empty($categoryQuery) ? "&category={$categoryQuery}" : "" ?>" aria-label="Next" style="color: #2c3e50;">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif ?>
                    </ul>
                </nav>                    
                <?php endif ?>
                
            <?php else: ?>
                <div class="row">
                    <div class="col-md-8 mx-auto text-center">
                        <div class="card shadow-sm border-0 p-5">
                            <div class="card-body">
                                <i class="fas fa-search fa-3x mb-3" style="color: #e67e22;"></i>
                                <h3 class="mb-3">We need some input to begin the search</h3>
                                <p class="text-muted">Please enter keywords to find interior design items.</p>
                                <form action="" method="GET" class="mt-4">
                                    <div class="input-group">
                                        <input type="text" name="query" class="form-control" placeholder="Search for items..." style="border-radius: 50px 0 0 50px; padding: 0.75rem 1.5rem; border: 1px solid #ced4da;">
                                        <button class="btn btn-primary" type="submit" style="border-radius: 0 50px 50px 0; background-color: #2c3e50; border-color: #2c3e50;">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
            
            <?php if(filterInput() && empty($searchResults)): ?>
                <div class="row">
                    <div class="col-md-8 mx-auto text-center">
                        <div class="card shadow-sm border-0 p-5">
                            <div class="card-body">
                                <i class="fas fa-exclamation-circle fa-3x mb-3" style="color: #e67e22;"></i>
                                <h3 class="mb-3">No results found</h3>
                                <p class="text-muted">There are no items matching your search criteria.</p>
                                <a href="index.php" class="btn btn-outline-primary mt-3" style="border-color: #2c3e50; color: #2c3e50;">Back to Home</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </section>
   
    <script>
    var options = {
        closeOnScroll: true,
    };

    new LuminousGallery(document.querySelectorAll(".image a"), options);
    </script>
</body>

</html>