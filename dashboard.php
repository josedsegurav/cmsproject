<?php
session_start();

require('utils/functions.php');

    unsetRedirectSessions();

if(!empty($_SESSION['user'])){
    $user = $_SESSION['user'];
}else{
    header("Location: login");
}

$currentPage = $_SERVER['PHP_SELF'];
$_SESSION['current_page'] = $currentPage;

$loginSuccess = false;
$loggedIn = '';

if(isset($_SESSION['loggedMessage'])){
    $loggedIn = $_SESSION['loggedMessage'];
    $loginSuccess = true;
    unset($_SESSION['loggedMessage']);
}

if(!empty($_SESSION['message'])){
    $message = $_SESSION['message'];
    echo "<script>alert('{$message}')</script>";
    unset($_SESSION['message']);
}

$categoryFormError = false;

if(!empty($_SESSION['categoryFormError'])){
    $categoryFormError = true;
    $categoryErrorMessage = $_SESSION['categoryFormError'];
}

unset($_SESSION['categoryFormError']);

$title = "Dashboard";

require('connect.php');

$resultsPerPage = 50;
$currentPage = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
$startQueryAt = $resultsPerPage * ($currentPage - 1);

if($user['role'] === "admin"){

    function getCountData($table, $db){

        $query = "SELECT COUNT(*) FROM serverside.$table";
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
                    FROM serverside.items i
                    JOIN serverside.categories c ON c.category_id = i.category_id
                    JOIN serverside.users u ON i.user_id = u.user_id
                    ORDER BY i.date_created";
            }elseif($table === "comments"){
                $query =    "SELECT c.comment_id, c.user_id, c.comment_content, c.disvowel_comment, c.comment_date_created, c.status, c.review_reason, 
                            i.item_id, i.item_name, i.slug, 
                            u.name, u.lastname
                            FROM serverside.comments c
                            JOIN serverside.items i ON i.item_id = c.item_id
                            JOIN serverside.users u ON c.user_id = u.user_id
                            ORDER BY c.comment_date_created DESC";
            }elseif($table === "categories"){
                $query =    "SELECT c.category_id, c.category_name, c.category_slug, COUNT(i.item_name) AS item_count
                            FROM serverside.categories c                                 
                            LEFT JOIN serverside.items i ON c.category_id = i.category_id                                 
                            GROUP BY c.category_id, c.category_name, c.category_slug";
            }else{
                $query = "SELECT * FROM serverside.$table";
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

            $_SESSION['dashboardTab'] = true;
    
        }
}elseif($user['role'] === "user"){
    function getCountData($table, $db, $user){

        $query = "";

        if($table === "categories"){
            $query = "SELECT COUNT(*) FROM serverside.$table";
        }elseif($table === "comments"){
            $query = "SELECT COUNT(*) FROM serverside.comments c JOIN serverside.items i ON c.item_id = i.item_id WHERE i.user_id = :user";
        }else{
            $query = "SELECT COUNT(*) FROM serverside.items WHERE user_id = :user";
        }
        
        // A PDO::Statement is prepared from the query.
        $statement = $db->prepare($query);
        if($table !== "categories"){
            $statement->bindValue(':user', $user, PDO::PARAM_INT);
        }
        // Execution on the DB server.
        $statement->execute();
        $output = $statement->fetch();
        
        return $output;
    }
    
        function getGeneralData($table, $db, $user){
    
            $query = "";
    
            if($table === "items"){
                $query = "SELECT i.item_id, i.item_name, i.user_id, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name, u.name, u.lastname
                FROM serverside.items i
                JOIN serverside.categories c ON c.category_id = i.category_id
                JOIN serverside.users u ON i.user_id = u.user_id
                WHERE i.user_id = :user_id
                ORDER BY i.date_created";
            }elseif($table === "comments"){
                $query =    "SELECT c.comment_id, c.comment_content, c.disvowel_comment, c.comment_date_created, c.status, 
                            i.item_id, i.item_name, i.slug, 
                            u.name, u.lastname
                            FROM serverside.comments c
                            JOIN serverside.items i ON i.item_id = c.item_id
                            JOIN serverside.users u ON c.user_id = u.user_id
                            WHERE i.user_id = :user_id 
                            ORDER BY c.comment_date_created";
            }elseif($table === "categories"){
                $query = "SELECT c.category_id, c.category_name, c.category_slug, COUNT(i.item_name) AS 'item_count'  
                                FROM serverside.categories c
                                LEFT JOIN serverside.items i ON c.category_id = i.category_id
                                GROUP BY c.category_id, c.category_name, c.category_slug";
            }
    
            // A PDO::Statement is prepared from the query.
            $statement = $db->prepare($query);
            if($table !== "categories"){
                $statement->bindValue(':user_id', $user, PDO::PARAM_INT);
            }
            
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
        
        
        $totalUsers = getCountData("users", $db, $user['user_id']);
        $userPages = ceil($totalUsers[0] / $resultsPerPage);
        
        $totalItems = getCountData("items", $db, $user['user_id']);
        $itemsPages = ceil($totalItems[0] / $resultsPerPage);
        
        $totalCategories = getCountData("categories", $db, $user['user_id']);
        $categoriesPages = ceil($totalCategories[0] / $resultsPerPage);
    
        $totalComments = getCountData("comments", $db, $user['user_id']);
        $commentsPages = ceil($totalComments[0] / $resultsPerPage);
    
        $tab = "";
        $tabData = "";
    
        if(isset($_GET['manage'])){
            
            $tab = filter_input(INPUT_GET, 'manage', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
            $tabData = getGeneralData($tab, $db, $user['user_id']);

            $_SESSION['dashboardTab'] = true;
    
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
    <section id="dashboardHeader" class="text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">Admin <span class="text-warning">Dashboard</span></h1>
                </div>
                <div class="col-md-6 text-md-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 justify-content-md-end">
                            <li class="breadcrumb-item"><a href="" class="text-white-50">Home</a></li>
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
                <?php if($user['role'] === "admin"): ?>
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
                                <a class="page-link" href="dashboard/users">Manage Users</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif ?>
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
                                <a class="page-link" href="dashboard/items">Manage Items</a>
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
                                <a class="page-link" href="dashboard/comments">Manage Comments</a>
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
                                <a class="page-link" href="dashboard/categories">Manage Categories</a>
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

            <form class="d-flex">
                <div class="input-group me-2">
                    <input type="text" class="form-control" placeholder="Search users...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <a href="user-manage/add" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>Add User
                </a>
            </form>

        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Date Created</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tabData as $user): ?>
                        <?php if($user['role'] === 'user'): ?>
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
                            <span class="badge bckg-secondary">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="user-manage/edit/<?= $user['user_id'] ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif ?>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <!-- <?php if($totalUsers[0] > $resultsPerPage): ?>
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

        <?php endif ?> -->

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
                <a href="add" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i>Add Item
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
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
                            <?php if(!empty($item['image'])): ?>
                            <img src="images/medium_<?= $item['image'] ?>"
                                alt="<?= $item['item_name'] ?>" class="thumbnail" width="50">
                            <?php else: ?>
                            <i class="fas fa-image text-muted fs-4"></i>
                            <?php endif ?>
                        </td>
                        <td class="text-truncate" style="max-width: 150px;"><?= $item['item_name'] ?></td>
                        <td><?= $item['name'] ?> <?= $item['lastname'] ?></td>
                        <td>
                            <span class="badge bckg-secondary">
                                <?= $item['category_name'] ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($item['date_created'])) ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="items/<?= $item['item_id'] ?>/<?= $item['slug'] ?>"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="items/edit/<?= $item['item_id'] ?>/<?= $item['slug'] ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <!-- <?php if($totalItems[0] > $resultsPerPage): ?>
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

        <?php endif ?> -->

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
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Author</th>
                        <th scope="col">Item</th>
                        <th scope="col">Comment</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tabData as $comment): ?>
                    <tr>
                        <th scope="row"><?= $comment['comment_id'] ?></th>
                        <td class="text-nowrap"><?= $comment['name'] ?> <?= $comment['lastname'] ?></td>
                        <td>
                            <a href="items/<?= $comment['item_id'] ?>/<?= $comment['slug'] ?>"
                                class="text-truncate d-inline-block"
                                style="max-width: 120px;"><?= $comment['item_name'] ?></a>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 150px;">
                                <?= $comment['comment_content'] ?>
                            </div>
                        </td>
                        <td>
                            <span
                                class="badge text-bg-<?= $comment['status'] === 'accepted' ? 'success' : ($comment['status'] === 'hidden' ? 'info' : 'warning') ?>">
                                <?= $comment['status'] ?>
                            </span>
                            <?php if($comment['disvowel_comment']): ?>
                            <span class="badge bg-secondary ms-1">Disvoweled</span>
                            <?php endif ?>
                        </td>
                        <td class="text-nowrap"><?= date('M d, Y', strtotime($comment['comment_date_created'])) ?></td>
                        <td>
                            <?php if($user['role'] === "admin"): ?>
                            <div class="btn-group" role="group">
                                <button type="button" data-bs-toggle="modal"
                                    data-bs-target="#viewModal_<?= $comment['comment_id'] ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" data-bs-toggle="modal"
                                    data-bs-target="#disvowelModal_<?= $comment['comment_id'] ?>"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal_<?= $comment['comment_id'] ?>"
                                    class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>

                            <!-- View Modal -->
                            <div class="modal fade" id="viewModal_<?= $comment['comment_id'] ?>" tabindex="-1"
                                role="dialog" aria-labelledby="viewModalLabel_<?= $comment['comment_id'] ?>"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel_<?= $comment['comment_id'] ?>">
                                                Confirm Change</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Change the visibility of this item.</p>
                                            <div class="mt-3">
                                                <strong>Comment:</strong>
                                                <p class="mb-0"><?= $comment['comment_content'] ?></p>
                                            </div>
                                            <?php if($comment['review_reason']): ?>
                                            <div class="mt-3">
                                                <strong>Reason for review:</strong>
                                                <p class="mb-0"><?= $comment['review_reason'] ?></p>
                                            </div>
                                            <?php endif ?>
                                        </div>
                                        <form action="comments/add" method="post">
                                            <div class="modal-footer">
                                                <input type="hidden" name="comment_id"
                                                    value="<?= $comment['comment_id'] ?>">
                                                <input type="hidden" name="status" value="<?= $comment['status'] ?>">
                                                <input type="hidden" name="censored"
                                                    value="<?= $comment['disvowel_comment'] ? 'censored' : 'not censored' ?>">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <input type="submit" class="btn btn-primary" name="updateView"
                                                    value="<?= ($comment['status'] === 'accepted' || $comment['status'] === 'censored' || $comment['status'] === 'review') ? 'Hide' : 'Show' ?>">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Disvowel Modal -->
                            <div class="modal fade" id="disvowelModal_<?= $comment['comment_id'] ?>" tabindex="-1"
                                role="dialog" aria-labelledby="disvowelModalLabel_<?= $comment['comment_id'] ?>"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"
                                                id="disvowelModalLabel_<?= $comment['comment_id'] ?>">Confirm Edit</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Disvowel the comment.</p>
                                            <div class="mt-3">
                                                <strong>Original comment:</strong>
                                                <p class="mb-0"><?= $comment['comment_content'] ?></p>
                                            </div>
                                            <?php if($comment['review_reason']): ?>
                                            <div class="mt-3">
                                                <strong>Reason for review:</strong>
                                                <p class="mb-0"><?= $comment['review_reason'] ?></p>
                                            </div>
                                            <?php endif ?>
                                        </div>
                                        <form action="comments/add" method="post">
                                            <div class="modal-footer">
                                                <input type="hidden" name="comment_id"
                                                    value="<?= $comment['comment_id'] ?>">
                                                <input type="hidden" name="content"
                                                    value="<?= $comment['comment_content'] ?>">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <input type="submit" class="btn btn-primary" name="disvowel"
                                                    value="Confirm">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal_<?= $comment['comment_id'] ?>" tabindex="-1"
                                role="dialog" aria-labelledby="deleteModalLabel_<?= $comment['comment_id'] ?>"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel_<?= $comment['comment_id'] ?>">
                                                Confirm Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete this item? This action cannot be undone.
                                            </p>
                                            <?php if($comment['review_reason']): ?>
                                            <div class="mt-3">
                                                <strong>Reason for review:</strong>
                                                <p class="mb-0"><?= $comment['review_reason'] ?></p>
                                            </div>
                                            <?php endif ?>
                                        </div>
                                        <form action="comments/add" method="post">
                                            <div class="modal-footer">
                                                <input type="hidden" name="comment_id"
                                                    value="<?= $comment['comment_id'] ?>">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <input type="submit" class="btn btn-danger" name="confirm"
                                                    value="Delete">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php elseif($user['role'] === "user"): ?>
                            <button type="button" data-bs-toggle="modal"
                                data-bs-target="#reviewModal_<?= $comment['comment_id'] ?>"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Review Modal -->
                            <div class="modal fade" id="reviewModal_<?= $comment['comment_id'] ?>" tabindex="-1"
                                role="dialog" aria-labelledby="reviewModalLabel_<?= $comment['comment_id'] ?>"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="reviewModalLabel_<?= $comment['comment_id'] ?>">
                                                Send comment for review</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Confirm if you want to send this comment for review.</p>
                                        </div>
                                        <form action="comments/add" method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="comment_id"
                                                    value="<?= $comment['comment_id'] ?>">
                                                <div class="form-floating mb-3">
                                                    <textarea class="form-control" name="review_reason"
                                                        placeholder="Leave a comment here" id="floatingTextarea"
                                                        style="height: 100px"></textarea>
                                                    <label for="floatingTextarea">Comments (Optional)</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <input type="submit" class="btn btn-danger" name="review"
                                                    value="Confirm">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <!-- <nav aria-label="Page navigation">
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
        </nav> -->

        <?php endif ?>
        <!-- Categories Tab -->
        <?php if($tab === "categories"): ?>

        <div class="row">
            <div class="col-md-6">
                <div class="d-flex flex-column">
                    <?php if($categoryFormError): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $categoryErrorMessage ?>
                    </div>
                    <?php endif ?>
                    <div class="bg-white">
                        <h5 class="mb-0">Add Category</h5>
                    </div>
                    <form action="categoryprocess" method="post">
                        <div class="mb-3">
                            <label for="newCategory" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="newCategory" name="newCategory">
                        </div>
                        <div class="d-grid gap-2 mb-4">
                            <button type="submit"
                                class="btn btn-primary d-flex align-items-center justify-content-center"
                                name="createCategory">
                                <i class="fas fa-plus-circle me-2"></i>Add Category
                            </button>
                        </div>

                        <hr class="my-4">

                        <h5 class="card-title mb-3">Update Category</h5>
                        <div class="mb-3">
                            <label for="category" class="form-label">Select Category</label>
                            <select class="form-select mb-3" id="categorytab" name="category">
                                <option value="" disabled selected>- Choose a Category -</option>
                                <?php foreach ($tabData as $row): ?>
                                <option value="<?= $row['category_id'] ?>"><?= $row['category_name'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="updateCategoryName" class="form-label">New Category Name</label>
                            <input type="text" class="form-control" id="updateCategoryName" name="updateCategoryName">
                        </div>
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit"
                                class="btn btn-primary d-flex align-items-center justify-content-center"
                                name="updateCategory">
                                <i class="fas fa-edit me-2"></i>Update Category
                            </button>
                        </div>
                        <div class="d-grid">
                            <button type="button" id="delete" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                class="btn btn-danger">
                                <i class="fas fa-trash-alt me-2"></i>Delete Category
                            </button>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog"
                            aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this category? This action cannot be undone.
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <input type="submit" class="btn btn-danger" name="confirm" value="Delete">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border-0 shadow-sm">
                    <div class="bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Current Categories</h5>
                            <!-- <div class="input-group w-50">
                                <input type="text" class="form-control" placeholder="Search categories...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div> -->
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Items</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tabData as $category): ?>
                                <tr>
                                    <th scope="row"><?= $category['category_id'] ?></th>
                                    <td><?= $category['category_name'] ?></td>
                                    <td>
                                        <span class="badge bckg-secondary"><?= $category['item_count'] ?></span>
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



    <!-- Success Toast -->

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="successToast" class="toast border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-white text-dark border-bottom border-warning">
                <strong class="me-auto">
                    <i class="fas fa-check-circle me-2 text-warning"></i>
                    <span>Interior<span class="text-warning">Items</span></span>
                </strong>
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
    <!-- Footer -->
    <?php include('footer.php'); ?>
    <?php if($loginSuccess): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var successToast = new bootstrap.Toast(document.getElementById('successToast'), {
            delay: 5000
        });
        successToast.show();
    });
    </script>
    <?php endif ?>

</body>

</html>