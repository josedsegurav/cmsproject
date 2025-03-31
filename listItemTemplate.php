
<!-- Item template to render on list.php -->
<div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100 shadow-sm">
        <div class="card-img-top position-relative overflow-hidden" style="height: 220px;">
            <?php if(!empty($item['image'])): ?>
            <div class="image">
                <a href="/webdev2/project/images/<?= $item['image'] ?>" class="d-block h-100">
                    <img src="/webdev2/project/images/<?= $item['image'] ?>" alt="<?= $item['item_name'] ?>"
                        class="img-fluid w-100 h-100 object-fit-cover">
                </a>
            </div>
            <?php else: ?>
            <img src="/webdev2/project/images/No_Image_Available.jpg" alt="No image available"
                class="img-fluid w-100 h-100 object-fit-cover">
            <?php endif ?>
            <span class="category-pill position-absolute"
                style="top: 10px; left: 10px; background-color: #e67e22; color: white; font-size: 0.8rem; padding: 0.25rem 0.75rem; border-radius: 50px;">
                <?= $item['category_name'] ?>
            </span>
        </div>
        <div class="card-body">
            <h5 class="card-title">
                <a href="/webdev2/project/items/<?= $item['slug'] ?>" class="text-decoration-none"
                    style="color: #2c3e50;"><?= $item['item_name'] ?></a>
            </h5>
            <p class="card-text text-muted small">
                Created by <?= $item['author'] ?> on
                <?= date("F d, Y, g:i a", strtotime($item['date_created'])) ?>
            </p>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="/webdev2/project/items/<?= $item['slug'] ?>" class="btn btn-sm btn-outline-primary"
                    style="border-color: #2c3e50; color: #2c3e50;">View Details</a>
                    
                <?php if(!empty($item['comment_count'])): ?>
                <small class="text-muted"><?= $item['comment_count'] ?> comments</small>
                <?php else: ?>
                <small class="text-muted">0 comments</small>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>