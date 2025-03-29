<!-- Item template to render on list.php -->
<div>
    <h2><a href="./"><?= $item['item_name']?></a></h2>
    <span><a href="/webdev2/project/items/edit/<?= $item['item_id'] ?>/<?= $item['slug'] ?>">edit item</a></span>
    <p>Created by <?= $item['author']?> on
        <?= date("F d, Y, g:i a", strtotime($item['date_created']))?></p>
    <p>Category: <span><?= $item['category_name']?></span></p>
</div>