<?php
class CategoryController
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        add_shortcode("cg_category_filter", [$this, "categoryFilterShortcode"]);
    }

    public function categoryFilterShortcode()
    {
        ob_start();
        include_once(PLUGIN_CITY_GUIDE__DIR__ . "/views/partials/_category_list.php");
        $stringa = ob_get_contents();
        ob_end_clean();
        return $stringa;
    }
}
new CategoryController();
