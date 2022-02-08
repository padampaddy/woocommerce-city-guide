<?php
class AdminPlacePage
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $myListTable = new PlaceListTable();
        echo '<div class="wrap"><h1 class="wp-heading-inline">Places</h1><a href="' . admin_url('admin.php?page=cg_places_add') . '" class="page-title-action">Add New</a>';
        echo '<form id="cg_place_page_form" action="" method="post">';
        $myListTable->prepare_items();
        $myListTable->display();
        $myListTable->pagination("top");
        echo '</form></div>';
    }
}
