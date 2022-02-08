<div class="wrapper-category">
    <div class="cat">
        <h4>Categories</h4>
    </div>
    <ul>
        <?php foreach (Category::getAllAsArray() as $categoryRow) { ?>
            <li class="<?= isset($_GET["category"]) && $_GET["category"] == $categoryRow["id"] ? 'active' : '' ?>"><a href="<?= add_query_arg("category", $categoryRow["id"]) ?>"><span><i class="fal fa-utensil-fork"></i></span><?= $categoryRow["name"] ?></a></li>
        <?php } ?>
    </ul>

</div>