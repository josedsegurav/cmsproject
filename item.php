<?php
session_start();

if(isset($_SESSION['user'])){
    $logged = true;
    $user = $_SESSION['user'];
    if($_SESSION['user']['role'] === "admin"){
        $adminUser = true;
    }elseif($_SESSION['user']['role'] === "user"){
        $userLogged = true;
    }
}

$loginSuccess = false;

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

$currentPage = $_SERVER['PHP_SELF'];
$previous_page = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
    $previous_page .= '?' . $_SERVER['QUERY_STRING'];
}

$_SESSION['current_page'] = $currentPage;
$_SESSION['previous_page'] = $previous_page;


require('connect.php');

$title = "Item - ";

if(isset($_GET['id'])){
    // If statemet to verify the input form the GET is an int.
    if(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)){
        // Sanitizing id data into a number.
       $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
       $slug = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
       // SQL query
       $query =   "SELECT i.item_id, i.item_name, i.user_id, i.content, i.category_id, i.store_url, i.image, i.date_created, i.slug, 
                   c.category_name, c.category_slug, 
                   u.name, u.lastname
                   FROM items i 
                   JOIN categories c ON c.category_id = i.category_id
                   JOIN users u ON i.user_id = u.user_id 
                   WHERE i.item_id = :id
                   AND i.slug = :slug";
       // A PDO::Statement is prepared from the query. 
       $statement = $db->prepare($query);
       // Bind the value of the id coming from the GET and sanitized into the query.
       $statement->bindValue(':id', $id, PDO::PARAM_INT);
       $statement->bindValue(':slug', $slug, PDO::PARAM_STR);
       // Execution on the DB server.
       $statement->execute();
       // Get the data from the DB after the query was executed.
       $item = $statement->fetch();
       $commentQuery = "SELECT u.name, u.lastname, 
                       c.comment_content, c.comment_date_created 
                       FROM comments c 
                       JOIN users u ON c.user_id = u.user_id 
                       WHERE c.item_id = :item_id
                       ORDER BY c.comment_date_created DESC";

       // A PDO::Statement is prepared from the query. 
       $commentStatement = $db->prepare($commentQuery);
       // Bind the value of the id coming from the GET and sanitized into the query.
       $commentStatement->bindValue(':item_id', $item['item_id'], PDO::PARAM_INT);
       // Execution on the DB server.
       $commentStatement->execute();
       // Get the data from the DB after the query was executed.
       $comments = $commentStatement->fetchAll();
       if (!$item) {
           header("HTTP/1.0 404 Not Found");
           echo "Page not found or URL has been modified";
           exit;
       }else{
           $title .= " {$item['item_name']}";
       }
}}

?>

<!DOCTYPE html>
<html lang="en">

<?php include('htmlHead.php'); ?>

<body>
    <?php include('nav.php'); ?>
    
    <!-- Item Header Section -->
    <section class="bg-light py-4">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="/webdev2/project/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/webdev2/project/browse">Items</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $item['item_name'] ?></li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0"><?= $item['item_name'] ?></h1>
                <span class="badge bckg-secondary"><?= $item['category_name'] ?></span>
            </div>
            <p class="text-muted mb-0">
                <small>Created by <?= $item['name'] ?> <?= $item['lastname'] ?> on
                    <?= date("F d, Y, g:i a", strtotime($item['date_created'])) ?></small>
            </p>
        </div>
    </section>

    <!-- Item Detail Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                        <?php if(!empty($item['image'])): ?>
                            <div class="image">
                                <a href="/webdev2/project/images/<?= $item['image'] ?>" class="d-block">
                                    <img src="/webdev2/project/images/medium_<?= $item['image'] ?>" 
                                         class="img-fluid rounded w-100" 
                                         alt="<?= $item['image'] ?>">
                                </a>
                            </div>
                            <?php else: ?>
                                <div class="card-img-top bg-light text-center py-5">
                                    <i class="fas fa-image text-muted fs-1"></i>
                                </div>
                                <?php endif ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Description</h5>
                            <div class="item-content mb-4">
                                <?= $item['content'] ?>
                            </div>
                            
                            <div class="item-meta mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted">Category</p>
                                        <p class="mb-3 fw-bold">
                                            <i class="fas fa-tag me-2 text-info"></i>
                                            <a class="fw-bold" href="/webdev2/project/browse/<?= $item['category_slug'] ?>"><?= $item['category_name'] ?></a>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted">Author</p>
                                        <p class="mb-3 fw-bold">
                                            <i class="fas fa-user me-2 text-primary"></i>
                                            <?= $item['name'] ?> <?= $item['lastname'] ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <a id="lbox" href="<?= $item['store_url'] ?>" target="_blank" 
                                   class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Purchase from Store
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comments Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h3 class="mb-0">Comments</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($comments)): ?>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="comment-item mb-4">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                                                    <?= substr($comment['name'], 0, 1) ?><?= substr($comment['lastname'], 0, 1) ?>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0 fw-bold"><?= $comment['name'] ?> <?= $comment['lastname'] ?></h6>
                                                    <small class="text-muted"><?= date("M d, Y", strtotime($comment['comment_date_created'])) ?></small>
                                                </div>
                                                <p class="mb-0"><?= $comment['comment_content'] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <p class="mb-0">No comments yet. Be the first to comment!</p>
                                </div>
                            <?php endif ?>

                            <!-- Comment Form -->
                            <?php if ($logged): ?>
                                <hr class="my-4">
                                <h5 class="mb-3">Leave a Comment</h5>
                                <form action="/webdev2/project/comments/add" method="post">
                                    <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                    <div class="mb-3">
                                        <textarea class="form-control" name="comment_text" rows="3" placeholder="Share your thoughts about this item..."></textarea>
                                    </div>
                                    <div class="d-grid d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Submit Comment
                                        </button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <hr class="my-4">
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-info-circle fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-0">Please <a href="/webdev2/project/login">log in</a> to leave a comment.</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Items Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title mb-4">Similar Items</h2>
            <div class="row g-4">
                <?php if (!empty($relatedItems)) : ?>
                    <?php foreach ($relatedItems as $relatedItem): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="/webdev2/project/images/medium_<?= $relatedItem['image'] ?>" class="card-img-top" alt="<?= $relatedItem['item_name'] ?>">
                                <div class="card-body">
                                    <span class="category-pill"><?= $relatedItem['category_name'] ?></span>
                                    <h5 class="card-title"><?= $relatedItem['item_name'] ?></h5>
                                    <p class="card-text text-truncate"><?= strip_tags($relatedItem['content']) ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="/webdev2/project/items/view/<?= $relatedItem['item_id'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                        <small class="text-muted"><?= $relatedItem['comment_count'] ?> comments</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-light text-center">
                            <p class="mb-0">No similar items found.</p>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </section>
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
    
    <script>
    var options = {
        closeOnScroll: true,
    };

    new LuminousGallery(document.querySelectorAll(".image a"), options);
    </script>
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