<div class="wrapper-category">
    <div class="cat">
        <h4>Categories</h4>
    </div>
    <ul>
        <?php foreach (Category::getAllAsArray(null) as $category) { 
        
        if(!isset($category["parent"]) || $category["parent"]==0) continue;
            $parent = Category::withId($category['parent']);
        ?>
            <li class="<?= isset($_GET["category"]) && $_GET["category"] == $categoryRow["id"] ? 'active' : '' ?>"><a href="<?= add_query_arg("category", $category["id"]) ?>"><span><i class="fal fa-utensil-fork"></i></span><?=  $category["name"] ."(".$parent->name.")" ?></a></li>
        <?php } ?>
    </ul>

</div>