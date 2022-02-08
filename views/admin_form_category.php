<?php

/**
 * The form to be loaded on the plugin's admin page
 */
if (is_admin()) {
?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="cg_category_add_form">
        <h4>Add a Category</h4>
        <input type="hidden" name="action" value="add_cg_category">
        <input required id="cg_category_name" type="text" name="cg_category_name" value="" placeholder="Category Name" />
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Add">
    </form>
<?php
} else {
?>
    <p> You are not authorized to perform this operation. </p>
<?php
}
