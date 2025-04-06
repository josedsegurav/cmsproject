<?php
session_start();
$loginSuccess = false;

if(isset($_SESSION['loggedMessage'])){
    $loggedIn = $_SESSION['loggedMessage'];
    $loginSuccess = true;
    unset($_SESSION['loggedMessage']);
}

$title = "Dashboard";

require('connect.php');

$resultsPerPage = 3;
$currentPage = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
$startQueryAt = $resultsPerPage * ($currentPage - 1);

if($_SESSION['user']['role'] === "admin"){

    function getCountData($table, $db){

        $query = "SELECT COUNT(*) FROM $table";
        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);
        // Execution on the DB server.
        $statement->execute();
        $output = $statement->fetch();
        
        return $output;
    }
    
        function getGeneralData($table, $db){
    
            $query = "";
    
            if($table === "items"){
            $query = "SELECT i.item_id, i.item_name, i.user_id, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name, u.name, u.lastname
                    FROM items i
                    JOIN categories c ON c.category_id = i.category_id
                    JOIN users u ON i.user_id = u.user_id
                    ORDER BY i.date_created";
            }elseif($table === "comments"){
                $query = "SELECT c.comment_id, c.comment_content, c.author_name, c.comment_date_created, c.status, i.item_name, i.slug
                FROM comments c
                JOIN items i ON i.item_id = c.item_id
                ORDER BY c.comment_date_created";
            }elseif($table === "categories"){
                $query = "SELECT c.category_id, c.category_name, c.category_slug, COUNT(i.item_name) AS 'item_count'  
                                FROM categories c
                                LEFT JOIN items i ON c.category_id = i.category_id
                                GROUP BY c.category_id";
            }else{
                $query = "SELECT * FROM $table";
            }
    
            // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);
            // Execution on the DB server.
            $statement->execute();
            $output = $statement->fetchAll();
    
            return $output;
        }
    
        if(isset($_GET['query'])) {
            if($_GET['query'] === "users"){
                $usersData = getGeneralData("users", $db);
            }elseif($_GET['query'] === "items"){
                $itemsData = getGeneralData("items", $db);
            }elseif($_GET['query'] === "categories"){
                $itemsData = getGeneralData("categories", $db);
            }elseif($_GET['query'] === "comments"){
                $itemsData = getGeneralData("comments", $db);
            }
        }
        
        
        $totalUsers = getCountData("users", $db);
        $userPages = ceil($totalUsers[0] / $resultsPerPage);
        
        $totalItems = getCountData("items", $db);
        $itemsPages = ceil($totalItems[0] / $resultsPerPage);
        
        $totalCategories = getCountData("categories", $db);
        $categoriesPages = ceil($totalCategories[0] / $resultsPerPage);
    
        $totalComments = getCountData("comments", $db);
        $commentsPages = ceil($totalComments[0] / $resultsPerPage);
    
        $tab = "";
        $tabData = "";
    
        if(isset($_GET['manage'])){
            $tab = filter_input(INPUT_GET, 'manage', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
            $tabData = getGeneralData($tab, $db);
    
        }
}elseif($_SESSION['user']['role'] === "user"){
    function getCountData($table, $db){

        $query = "SELECT COUNT(*) FROM $table";
        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);
        // Execution on the DB server.
        $statement->execute();
        $output = $statement->fetch();
        
        return $output;
    }
    
        function getGeneralData($table, $db){
    
            $query = "";
    
            if($table === "items"){
            $query = "SELECT i.item_id, i.item_name, i.author, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name
                    FROM items i
                    JOIN categories c ON c.category_id = i.category_id
                    ORDER BY i.date_created";
            }elseif($table === "comments"){
                $query = "SELECT c.comment_id, c.comment_content, c.author_name, c.comment_date_created, c.status, i.item_name, i.slug
                FROM comments c
                JOIN items i ON i.item_id = c.item_id
                ORDER BY c.comment_date_created";
            }elseif($table === "categories"){
                $query = "SELECT c.category_id, c.category_name, c.category_slug, COUNT(i.item_name) AS 'item_count'  
                                FROM categories c
                                LEFT JOIN items i ON c.category_id = i.category_id
                                GROUP BY c.category_id";
            }else{
                $query = "SELECT * FROM $table";
            }
    
            // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);
            // Execution on the DB server.
            $statement->execute();
            $output = $statement->fetchAll();
    
            return $output;
        }
    
        if(isset($_GET['query'])) {
            if($_GET['query'] === "users"){
                $usersData = getGeneralData("users", $db);
            }elseif($_GET['query'] === "items"){
                $itemsData = getGeneralData("items", $db);
            }elseif($_GET['query'] === "categories"){
                $itemsData = getGeneralData("categories", $db);
            }elseif($_GET['query'] === "comments"){
                $itemsData = getGeneralData("comments", $db);
            }
        }
        
        
        $totalUsers = getCountData("users", $db);
        $userPages = ceil($totalUsers[0] / $resultsPerPage);
        
        $totalItems = getCountData("items", $db);
        $itemsPages = ceil($totalItems[0] / $resultsPerPage);
        
        $totalCategories = getCountData("categories", $db);
        $categoriesPages = ceil($totalCategories[0] / $resultsPerPage);
    
        $totalComments = getCountData("comments", $db);
        $commentsPages = ceil($totalComments[0] / $resultsPerPage);
    
        $tab = "";
        $tabData = "";
    
        if(isset($_GET['manage'])){
            $tab = filter_input(INPUT_GET, 'manage', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
            $tabData = getGeneralData($tab, $db);
    
        }
}


?>

<!DOCTYPE html>
<html lang="en">
<?php include('htmlHead.php'); ?>

<body>
    <!-- Navigation -->
    <?php include('nav.php'); ?>

    <!-- Dashboard Header -->
    <section class="bg-primary text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">Admin <span class="text-warning">Dashboard</span></h1>
                </div>
                <div class="col-md-6 text-md-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 justify-content-md-end">
                            <li class="breadcrumb-item"><a href="/webdev2/project/" class="text-white-50">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Stats -->
    <section class="py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Total Users</h6>
                                    <h3 class="mb-0"><?= $totalUsers[0] ?></h3>
                                </div>
                            </div>
                            <div>
                                <a class="page-link" href="?manage=users">Manage Users</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-couch fa-2x text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Total Items</h6>
                                    <h3 class="mb-0"><?= $totalItems[0] ?></h3>
                                </div>
                            </div>
                            <div>
                                <a class="page-link" href="?manage=items">Manage Items</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-comments fa-2x text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Total Comments</h6>
                                    <h3 class="mb-0"><?= $totalComments[0] ?></h3>
                                </div>
                            </div>
                            <div>
                                <a class="page-link" href="?manage=comments">Manage Comments</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded">
                                    <i class="fas fa-tags fa-2x text-info"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="text-muted mb-1">Categories</h6>
                                    <h3 class="mb-0"><?= $totalCategories[0] ?></h3>
                                </div>
                            </div>
                            <div>
                                <a class="page-link" href="?manage=categories">Manage Categories</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php if(isset($_GET['manage'])): ?>
    <div class="container py-5 bg-white mb-5 shadow-sm">

        <!-- Users Tab -->
        <?php if($tab === "users"): ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Manage Users</h3>
                <div class="d-flex">
                    <div class="input-group me-2">
                        <input type="text" class="form-control" placeholder="Search users...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Add User
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tabData as $user): ?>
                        <tr>
                            <th scope="row"><?= $user['user_id'] ?></th>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?= $user['name'] . ' ' . $user['lastname'] ?>
                                </div>
                            </td>
                            <td><?= $user['username'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td>
                                <span class="badge bg-<?= $user['role'] == 'admin' ? 'primary' : 'secondary' ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <?php if($totalUsers[0] > $resultsPerPage): ?>
            <nav aria-label="Search results pages" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>&query=users" aria-label="Previous"
                            style="color: #2c3e50;">
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

                    <?php for($i = 1; $i <= $userPages; $i++): ?>
                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&query=users"
                            style="<?= $i == $currentPage ? 'background-color: #2c3e50; border-color: #2c3e50;' : 'color: #2c3e50;' ?>"><?= $i ?></a>
                    </li>
                    <?php endfor ?>

                    <?php if($currentPage < $userPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>&query=users" aria-label="Next"
                            style="color: #2c3e50;">
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
            <?php else: ?>

            <?php endif ?>
        
        <?php endif ?>
        <!-- Items Tab -->
        <?php if($tab === "items"): ?>
        
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Manage Items</h3>
                <div class="d-flex">
                    <div class="input-group me-2">
                        <input type="text" class="form-control" placeholder="Search items...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <a href="/webdev2/project/add" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Add Item
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Image</th>
                            <th scope="col">Name</th>
                            <th scope="col">Author</th>
                            <th scope="col">Category</th>
                            <th scope="col">Added</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tabData as $item): ?>
                        <tr>
                            <th scope="row"><?= $item['item_id'] ?></th>
                            <td>
                                <img src="/webdev2/project/images/medium_<?= $item['image'] ?>"
                                    alt="<?= $item['item_name'] ?>" class="thumbnail" width="50">
                            </td>
                            <td><?= $item['item_name'] ?></td>
                            <td><?= $item['name'] ?>  <?= $item['lastname'] ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?= $item['category_name'] ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($item['date_created'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="/webdev2/project/items/<?= $item['slug'] ?>"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/webdev2/project/items/edit/<?= $item['item_id'] ?>/<?= $item['slug'] ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <?php if($totalItems[0] > $resultsPerPage): ?>
            <nav aria-label="Search results pages" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if($currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>&query=items" aria-label="Previous"
                            style="color: #2c3e50;">
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

                    <?php for($i = 1; $i <= $itemsPages; $i++): ?>
                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&query=items"
                            style="<?= $i == $currentPage ? 'background-color: #2c3e50; border-color: #2c3e50;' : 'color: #2c3e50;' ?>"><?= $i ?></a>
                    </li>
                    <?php endfor ?>

                    <?php if($currentPage < $itemsPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>&query=items" aria-label="Next"
                            style="color: #2c3e50;">
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
            <?php else: ?>

            <?php endif ?>
        
        <?php endif ?>
        <!-- Comments Tab -->
        <?php if($tab === "comments"): ?>
        
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Manage Comments</h3>
                <div class="input-group w-50">
                    <input type="text" class="form-control" placeholder="Search comments...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Author</th>
                            <th scope="col">Item</th>
                            <th scope="col">Comment</th>
                            <th scope="col">Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tabData as $comment): ?>
                        <tr>
                            <th scope="row"><?= $comment['comment_id'] ?></th>
                            <td><?= $comment['author_name'] ?></td>
                            <td>
                                <a
                                    href="/webdev2/project/items/<?= $comment['slug'] ?>"><?= $comment['item_name'] ?></a>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;">
                                    <?= $comment['comment_text'] ?>
                                </div>
                            </td>
                            <td><?= date('M d, Y', strtotime($comment['comment_date_created'])) ?></td>
                            <td>
                                <span class="badge bg-success">
                                    <?= $comment['status'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        
        <?php endif ?>
        <!-- Categories Tab -->
        <?php if($tab === "categories"): ?>
        
            <div class="row">
                <div class="col-md-4">
                    <div class="border-0 shadow-sm">
                        <div class="bg-white">
                            <h5 class="mb-0">Add Category</h5>
                        </div>
                        <form action="/webdev2/project/admin/categories/process" method="post">
                            <div class="mb-3">
                                <label for="categoryName" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="categoryName" name="category_name" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-2"></i>Add Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="border-0 shadow-sm">
                        <div class="bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Current Categories</h5>
                                <div class="input-group w-50">
                                    <input type="text" class="form-control" placeholder="Search categories...">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Items</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tabData as $category): ?>
                                    <tr>
                                        <th scope="row"><?= $category['category_id'] ?></th>
                                        <td><?= $category['category_name'] ?></td>
                                        <td>
                                            <span class="badge bg-info"><?= $category['item_count'] ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary edit-category"
                                                    data-id="<?= $category['category_id'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger delete-category"
                                                    data-id="<?= $category['category_id'] ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        
        <?php endif ?>
    </div>
    <?php endif ?>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this item? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <?php if($loginSuccess): ?>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-white text-dark border-bottom border-warning">
                <strong class="me-auto">
                    <i class="fas fa-check-circle me-2 text-warning"></i>
                    <span>Interior<span class="text-warning">Items</span></span>
                </strong>
                <small class="text-muted">just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check text-success me-2"></i>
                    <?= $loggedIn ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var successToast = new bootstrap.Toast(document.getElementById('successToast'), {
            delay: 5000
        });
        successToast.show();
    });
    </script>
    <?php endif ?>

    <!-- Footer -->
    <?php include('footer.php'); ?>
</body>

</html>