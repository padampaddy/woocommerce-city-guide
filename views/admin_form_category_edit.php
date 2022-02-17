<?php

/**
 * The form to be loaded on the plugin's admin page
 */
if (is_admin() && isset($_GET["category"]) && $_GET["category"] !== "1") {
    wp_enqueue_script('jquery');
    // This will enqueue the Media Uploader script
    wp_enqueue_media();
    $c = Category::withId($_GET['category'])
?>
    <h4>Edit Category</h4>
    <input required id="cg_edit_category_name" type="text" name="cg_category_name" value="<?= $c->name ?>" placeholder="Category Name" />
    <select name="cg_edit_category_parent" id="cg_edit_category_parent">
        <?php
        $categories = Category::getAllAsArray(null);
        foreach ($categories as $category) {
        ?>
            <option <?= strval($c->parent) === $category["id"] ? "selected" : "" ?> value="<?= $category["id"] ?>"><?= $category["name"] ?></option>
        <?php
        }
        ?>
    </select><br>
    <br><label for="image_url">Image</label><br>
    <img style="max-height:300px" id="image_url_edit" src="<?= wp_get_attachment_url($c->image) ?>" /><br>
    <input type="hidden" id="cg_edit_category_image" name="cg_category_image" value="<?= $c->image ?>" />
    <input type="button" name="upload-btn" id="upload-btn-edit" class="button-secondary" value="Upload Image"><br />

    <button type="button" id="edit_category_button" class="button button-primary">Save</button>

    <?php
    add_action('admin_footer', 'edit_category_javascript'); // Write our JS below here

    function edit_category_javascript()
    { ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#upload-btn-edit').click(function(e) {
                    e.preventDefault();
                    var image = wp.media({
                            title: 'Upload Image',
                            // mutiple: true if you want to upload multiple files at once
                            multiple: false
                        }).open()
                        .on('select', function(e) {
                            // This will return the selected image from the Media Uploader, the result is an object
                            var uploaded_image = image.state().get('selection').first();
                            // We convert uploaded_image to a JSON object to make accessing it easier
                            // Output to the console uploaded_image
                            console.log(uploaded_image);
                            var image_url = uploaded_image.toJSON().url;
                            // Let's assign the url value to the input field
                            $('#image_url_edit').attr("src", image_url);
                            $('#cg_edit_category_image').val(uploaded_image.toJSON().id)
                        });
                });

                $("#edit_category_button").click(function() {
                    var data = {
                        'action': 'edit_category',
                        'id': <?= $_GET['category'] ?>,
                        'name': $('#cg_edit_category_name').val(),
                        'image': $('#cg_edit_category_image').val(),
                        'parent': $('#cg_edit_category_parent').val()
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
