<!-- Item template to render on list.php -->
<div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <?php if(!empty($item['image'])): ?>
                                <div class="image">
                                    <a href="images/<?= $item['image'] ?>">
                                        <img src="images/<?= $item['image'] ?>" class="card-img-top" alt="<?= $item['item_name'] ?>">
                                    </a>
                                </div>
                                <?php else: ?>
                                <div class="card-img-top bg-light text-center py-5">
                                    <i class="fas fa-image text-muted fs-1"></i>
                                </div>
                                <?php endif ?>
                                
                                <div class="card-body">
                                    <span class="category-pill"><?= $item['category_name'] ?></span>
                                    <h5 class="card-title"><?= $item['item_name'] ?></h5>
                                    <p class="card-text"><?= substr($item['content'], 0, 100) ?>...</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="items/<?= $item['item_id'] ?>/<?= $item['slug'] ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                        <small class="text-muted"><?= $item['comments_count'] ?? 0 ?> comments</small>
                                    </div>
                                </div>
                            </div>
                        </div>