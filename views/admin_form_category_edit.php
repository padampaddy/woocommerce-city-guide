<?php

/**
 * The form to be loaded on the plugin's admin page
 */
if (is_admin() && isset($_GET["category"]) && $_GET["category"] !== "1") {
?>
    <h4>Edit Category</h4>
    <input required id="cg_edit_category_name" type="text" name="cg_category_name" value="<?= Category::withId($_GET['category'])->name ?>" placeholder="Category Name" />
    <button type="button" id="edit_category_button" class="button button-primary">Save</button>

    <?php
    add_action('admin_footer', 'edit_category_javascript'); // Write our JS below here

    function edit_category_javascript()
    { ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $("#edit_category_button").click(function() {
                    var data = {
                        'action': 'edit_category',
                        'id': <?= $_GET['category'] ?>,
                        'name': $('#cg_edit_category_name').val()
                    };

                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    jQuery.post(ajaxurl, data, function(response) {
                        window.location.href = "<?= get_admin_url(null, "admin.php?page=cg_categories") ?>";
                    });

                });
            });
        </script> <?php
                }
            }
