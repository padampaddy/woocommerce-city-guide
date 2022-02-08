<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class PlaceListTable extends WP_List_Table
{
    /** Class constructor */
    public function __construct()
    {
        parent::__construct([
            'singular' => 'Place', //singular name of the listed records
            'plural' => 'Places', //plural name of the listed records
            'ajax' => false //should this table support ajax?
        ]);
    }
    function get_columns()
    {
        $columns = array(
            'cb'       => '<input type="checkbox" />',
            'name'    => 'Name',
            'status'    => 'Status',
            'category' => 'Category',
            'user_id' => 'User',
            'created_on'      => 'Created On'
        );
        return $columns;
    }

    function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];
        $this->process_bulk_action();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $totalItems = Place::getTotalCount();
        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page'    => 10
        ));
        $this->items = Place::getAllAsArray($this->get_pagenum());
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'lat':
            case 'long':
            case 'created_on':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }
    protected function column_status($item)
    {
        return sprintf('<span style="color:white; background:%s; padding:2px 4px; border-radius:4px;text-transform:uppercase;display: inline-flex; align-items: center; justify-content: center;    font-weight: bold; font-size: smaller;">%s</span>', $item["status"] === 'pending' ? "#EDE04D" : ($item["status"] === 'active' ? "#15CD72" : "#ED4F32"), $item["status"]);
    }
    protected function column_user_id($item)
    {
        return get_user_by("ID", $item['user_id'])->display_name;
    }
    protected function column_category($item)
    {
        $category = Category::withId($item['category']);
        return $category->name;
    }
    protected function column_name($item)
    {
        // Build edit row action.
        $edit_query_args = array(
            'page' => 'cg_places_edit',
            'action' => 'edit',
            'cg_place_id'  => $item['id'],
        );

        $actions['edit'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(wp_nonce_url(add_query_arg($edit_query_args, 'admin.php'), 'editcgplace_' . $item['id'])),
            'Edit'
        );

        // Build delete row action.
        $delete_query_args = array(
            'page' => 'cg_places',
            'action' => 'delete',
            'place'  => $item['id'],
        );

        $actions['delete'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(wp_nonce_url(add_query_arg($delete_query_args, 'admin.php'), 'deletecgplace_' . $item['id'])),
            'Delete'
        );

        // Return the title contents.
        return sprintf(
            '%1$s <span style="color:silver;">(ID: %2$s)</span>%3$s',
            $item['name'],
            $item['id'],
            $this->row_actions($actions)
        );
    }
    protected function get_bulk_actions()
    {
        $actions = array(
            'delete' => "Delete",
        );

        return $actions;
    }
    protected function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            "places",  // Let's simply repurpose the table's singular label ("movie").
            $item['id']                // The value of the checkbox should be the record's ID.
        );
    }
    protected function process_bulk_action()
    {
        // Detect when a bulk action is being triggered.
        if ('delete' === $this->current_action()) {
            if (isset($_POST['places']))
                $places = $_POST['places'];
            else
                $places = $_GET['place'];
            Place::deleteBulk($places);
            echo '<div class = "wrap"><h4 style="display:inline-block;margin-right:4px;">Alert: </h4>Places Deleted!</div>';
        }
    }
}
