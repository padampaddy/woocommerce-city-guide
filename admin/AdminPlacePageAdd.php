<?php
class AdminPlacePageAdd
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        echo '<div class="wrap"><h1 class="wp-heading-inline">Add Place</h1>';
        include_once(PLUGIN_CITY_GUIDE__DIR__ . "/views/admin_form_place.php");
        echo '</div>';
    }
}
