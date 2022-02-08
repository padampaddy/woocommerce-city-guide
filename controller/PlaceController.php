<?php
class PlaceController
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        add_shortcode("cg_places_list", [$this, "cgPlacesShortcode"]);
    }

    public function cgPlacesShortcode($atts)
    {
        global $the_item;
        global $detail_page_slug;
        $detail_page_slug = $atts['detail-page-slug'];
        $ret = '<div class="wrapper-list"><ul>';
        $page = isset($_GET['cg_page']) ? $_GET['cg_page'] : '1';
        $query = isset($_GET['cg_query']) ? $_GET['cg_query'] : '%';
        $category = isset($_GET['category']) ? $_GET['category'] : 'all';
        foreach (Place::getAllWithParams(intval($page), $category, $query) as $place) {
            $the_item = $place;
            ob_start();
            include(PLUGIN_CITY_GUIDE__DIR__ . "/views/partials/_place_list_item.php");
            $ret .= ob_get_contents();
            ob_end_clean();
        }
        $ret .= "</ul></div>";
        return $ret;
    }
}
new PlaceController();
