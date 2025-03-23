    <div>
        <h2><a href="./"><?= $item['item_name']?></a></h2>
        <span>Created by <?= $item['author']?> on
            <?= date("F d, Y, g:i a", strtotime($item['date_created']))?></span>
        <p>Category: <span><?= $item['category_name']?></span></p>
    </div>
