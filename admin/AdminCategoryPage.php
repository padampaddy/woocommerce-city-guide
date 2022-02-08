<?php
class AdminCategoryPage
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $myListTable = new CategoryListTable();
        echo '<div class="wrap"><h2>Categories</h2>';
        include_once(PLUGIN_CITY_GUIDE__DIR__ . "/views/admin_form_category.php");
        echo '<form id="cg_category_page_form" action="" method="post">';
        $myListTable->prepare_items();
        $myListTable->display();
        echo '</form></div>';
    }
}
