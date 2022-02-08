<?php
class CityGuide
{
    public function __construct()
    {
        register_activation_hook(PLUGIN_CITY_GUIDE__FILE__, [$this, 'activationHook']);
        register_deactivation_hook(PLUGIN_CITY_GUIDE__FILE__, [$this, 'deactivationHook']);
        add_action('admin_menu', [$this, 'adminPages']);
        add_action('admin_post_add_cg_category', [$this, 'addCGCategory']);
        add_action('wp_ajax_edit_category', [$this, 'editCGCategory']);
        add_action('admin_post_add_cg_place', [$this, 'addCGPlace']);
        add_action('admin_post_edit_cg_place', [$this, 'editCGPlace']);
        add_action('wp_ajax_cg_place_user_form', [$this, 'cgPlaceUserForm']);
        add_action('wp_ajax_nopriv_cg_place_user_form', [$this, 'cgPlaceUserForm']);
        add_filter('woocommerce_add_to_cart_redirect', [$this, 'cgRedirectAddToCart']);
        add_action('woocommerce_order_status_completed', [$this, 'cgCompleteOrder'], 10, 1);
    }
    public function cgCompleteOrder($order_id)
    {
        $order = wc_get_order($order_id); //<--check this line
        Place::makeActive($order->get_user_id());
    }
    public function cgRedirectAddToCart()
    {
        $cw_redirect_url_checkout = wc_get_checkout_url();
        return $cw_redirect_url_checkout;
    }
    public function cgPlaceUserForm()
    {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $name = sanitize_text_field($_POST['name']);
        $status = "pending";
        $description = htmlentities2($_POST['description']);
        $embedCode = htmlentities2($_POST['embed_code']);
        $category = sanitize_text_field($_POST['category']);
        $userId = get_current_user_id();
        $uploadedfile = $_FILES['image'];
        $attachmentId = media_handle_sideload($uploadedfile);
        $place = new Place($name, $embedCode, $description, null, $status, $category, null, $attachmentId, $userId);
        Place::save($place);
        echo json_encode(($place));
    }

    public function createProduct()
    {
        include(PLUGIN_CITY_GUIDE__DIR__ . '/includes/create-product.php');
        $product_id = create_product([
            'type'               => '', // Simple product by default
            'name'               => __("Listing", "woocommerce"),
            'description'        => __("Listing in the city guide", "woocommerce"),
            'short_description'  => __("Listing in the city guide", "woocommerce"),
            // 'sku'                => '',
            'regular_price'      => '5.00', // product price
            // 'sale_price'         => '',
            'reviews_allowed'    => false,
            'attributes'         => [],
        ]);
        add_option("cg_product_id", $product_id);
    }
    public function addCGCategory()
    {
        $name = sanitize_text_field($_POST['cg_category_name']);
        $category = new Category($name, null, null);
        Category::save($category);
        wp_redirect(admin_url('admin.php?page=cg_categories'));
        exit();
    }
    public function editCGCategory()
    {
        $name = sanitize_text_field($_POST['name']);
        $id = sanitize_text_field($_POST['id']);
        $category = Category::withId(intval($id));
        $category->name = $name;
        Category::save($category);
        wp_die();
    }
    public function addCGPlace()
    {
        $name = sanitize_text_field($_POST['cg_place_name']);
        $status = sanitize_text_field($_POST['cg_place_status']);
        $description = htmlentities2($_POST['cg_place_description']);
        $embedCode = htmlentities2($_POST['cg_place_embed_code']);
        $category = sanitize_text_field($_POST['cg_place_category']);
        $userId = sanitize_text_field($_POST['cg_place_user_id']);
        if (isset($_POST['cg_place_image'])) $image = intval(sanitize_text_field($_POST['cg_place_image']));
        else $image = null;
        $place = new Place($name, $embedCode, $description, null, $status, $category, null, $image, $userId);
        Place::save($place);
        wp_redirect(admin_url('admin.php?page=cg_places'));
        exit();
    }
    public function editCGPlace()
    {
        $id = sanitize_text_field($_POST['cg_place_id']);
        $name = sanitize_text_field($_POST['cg_place_name']);
        $status = sanitize_text_field($_POST['cg_place_status']);
        $description = htmlentities2($_POST['cg_place_description']);
        $userId = sanitize_text_field($_POST['cg_place_user_id']);
        $embedCode = htmlentities2($_POST['cg_place_embed_code']);
        $category = sanitize_text_field($_POST['cg_place_category']);
        if (isset($_POST['cg_place_image'])) $image = intval(sanitize_text_field($_POST['cg_place_image']));
        else $image = null;
        $place = new Place($name, $embedCode, $description, $id, $status, $category, null, $image, $userId);
        Place::save($place);
        wp_redirect(admin_url('admin.php?page=cg_places'));
        exit();
    }
    public function adminPages()
    {
        add_menu_page("City Guide", "City Guide", "administrator",  "cg_categories", null, "dashicons-admin-site");
        add_submenu_page("cg_categories", "CG Categories", "Categories", "administrator", "cg_categories", [$this, 'categoryPage']);
        add_submenu_page("cg_categories", "CG Places", "Places", "administrator", "cg_places",  [$this, 'placePage']);
        add_submenu_page("cg_categories", "CG Add Place", "Add Place", "administrator", "cg_places_add",  [$this, 'placePageAdd']);
        add_submenu_page(null, "CG Edit Place", "Edit Place", "administrator", "cg_places_edit",  [$this, 'placePageEdit']);
    }
    public function categoryPage()
    {
        new AdminCategoryPage();
    }
    public function placePage()
    {
        new AdminPlacePage();
    }
    public function placePageAdd()
    {
        new AdminPlacePageAdd();
    }
    public function placePageEdit()
    {
        include(PLUGIN_CITY_GUIDE__DIR__ . "/views/admin_form_place_edit.php");
    }
    public function activationHook()
    {
        $this->createPlacesTable();
        $this->createCategoriesTable();
        $this->createProduct();
    }

    public function deactivationHook()
    {
        $this->dropPlacesTable();
        $this->dropCategoriesTable();
    }

    private function createPlacesTable()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . "places";
        $sql = "CREATE TABLE  if not exists $table_name (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `created_on` datetime DEFAULT NOW() NOT NULL,
                `name` tinytext NOT NULL,
                `description` text NOT NULL,
                `status` varchar(10) DEFAULT 'pending' NOT NULL,
                `embed_code` text NOT NULL,
                `category` mediumint(9) NOT NULL,
                `image` mediumint(9),
                `user_id` bigint(20),
                PRIMARY KEY  (id)
                ) $charset_collate;";
        $wpdb->query($sql);
    }
    private function dropPlacesTable()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "places";
        $sql = "DROP TABLE if exists $table_name;";
        $wpdb->query($sql);
    }
    private function createCategoriesTable()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . "categories";
        $sql = "CREATE TABLE if not exists $table_name (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `created_on` datetime DEFAULT NOW() NOT NULL,
                `name` tinytext NOT NULL,
                PRIMARY KEY  (id)
                ) $charset_collate;";
        $wpdb->query($sql);
        if (!Category::withId(1)) {
            $category = new Category("Others", null, null);
            Category::save($category);
        }
    }
    private function dropCategoriesTable()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "categories";
        $sql = "DROP TABLE if exists $table_name;";
        $wpdb->query($sql);
    }
}

new CityGuide();
