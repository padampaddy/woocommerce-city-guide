<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/screen.php' );
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class CategoryListTable extends WP_List_Table
{
    /** Class constructor */
    public function __construct()
    {
        parent::__construct([
            'singular' => 'Category', //singular name of the listed records
            'plural' => 'Categories', //plural name of the listed records
            'ajax' => false //should this table support ajax?
        ]);
    }
    function get_columns()
    {
        $columns = array(
            'cb'       => '<input type="checkbox" />',
            'name'    => 'Name',
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
        $this->items = Category::getAllAsArray(null);
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'created_on':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }
    protected function column_name($item)
    {
        // Build edit row action.
        $edit_query_args = array(
            'page' => 'cg_categories',
            'action' => 'edit',
            'category'  => $item['id'],
        );

        $actions['edit'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(wp_nonce_url(add_query_arg($edit_query_args, 'admin.php'), 'editcgcategory_' . $item['id'])),
            'Edit'
        );

        // Build delete row action.
        $delete_query_args = array(
            'page' => 'cg_categories',
            'action' => 'delete',
            'category'  => $item['id'],
        );

        $actions['delete'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(wp_nonce_url(add_query_arg($delete_query_args, 'admin.php'), 'deletecgcategory_' . $item['id'])),
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
            "categories",  // Let's simply repurpose the table's singular label ("movie").
            $item['id']                // The value of the checkbox should be the record's ID.
        );
    }
    protected function process_bulk_action()
    {
        // Detect when a bulk action is being triggered.
        if ('delete' === $this->current_action()) {
            if (isset($_POST['categories']))
                $categories = $_POST['categories'];
            else
                $categories = $_GET['category'];
            Category::deleteBulk($categories);
            echo '<div class = "wrap"><h4 style="display:inline-block;margin-right:4px;">Alert: </h4>Categories Deleted!</div>';
        }
        if ('edit' === $this->current_action()) {
            include(PLUGIN_CITY_GUIDE__DIR__ . "/views/admin_form_category_edit.php");
        }
    }
}
