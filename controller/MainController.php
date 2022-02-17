<?php
class MainController
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        add_action("wp_enqueue_scripts", [$this, 'enqueueStyles']);
        add_shortcode("cg_listing_page", [$this, "cgListingPageShortcode"]);
        add_shortcode("cg_listing_detail_page", [$this, "cgListingDetailPageShortcode"]);
        add_shortcode("cg_listing_user_form", [$this, "cgListingUserFormShortcode"]);
    }

    public function enqueueStyles()
    {
        wp_register_style(
            'cg_listing_style',
            PLUGIN_CITY_GUIDE__URL__ . '/css/main.css'
        );
    }

    public function cgListingPageShortcode($atts)
    {
        $page = isset($_GET['cg_page']) ? intval($_GET['cg_page']) : 1;
        $isFirstPage = $page === 1;
        wp_enqueue_style('cg_listing_style');
        $query = isset($_GET['cg_query']) ? $_GET['cg_query'] : '%';
        $category = isset($_GET['category']) ? $_GET['category'] : 'all';
        $totalPages = Place::getTotalPagesWithParams($category, $query);
        $isLastPage = $page === $totalPages;
        return '<div class="wrapper-listing">
        <div class="listing-searchbar">
            <form method="get">
                <input value="' . (isset($_GET['cg_query']) ? $_GET['cg_query'] : '') . '" type="text" name="cg_query" class="" placeholder="Search">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>
        <div class="wrapper-category-list">
            ' . do_shortcode("[cg_category_filter]") . do_shortcode("[cg_places_list detail-page-slug='" . $atts['detail-page-slug'] . "']") . '
        </div>

        <div class="pagination-wrapper">
            <ul class="pagination">
                <li class="page-item">
                    <a class="page-link" href="' . add_query_arg("cg_page", $isFirstPage ? 1 : $page - 1) . '" tabindex="-1">Previous</a>
                </li>
                <li class="page-item active">
                    <a class="page-link" href="#">' . $page . '/' . $totalPages . '</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="' . add_query_arg("cg_page", $isLastPage ? $page : $page + 1) . '">Next</a>
                </li>
            </ul>
    
        </div>
    </div>';
    }
    public function cgListingUserFormShortcode($atts)
    {
        if (get_current_user_id()) {
            ob_start();
            include_once(PLUGIN_CITY_GUIDE__DIR__ . "/views/user_form_place.php");
            $ret = ob_get_contents();
            ob_end_clean();
        } else {
            $ret = "<p>You need to login to create a listing.</p>";
        }
        return $ret;
    }
    public function cgListingDetailPageShortcode()
    {
        wp_enqueue_style('cg_listing_style');
        ob_start();
        include_once(PLUGIN_CITY_GUIDE__DIR__ . "/views/detail-page.php");
        $ret .= ob_get_contents();
        ob_end_clean();
        return $ret;
    }

}
new MainController();
