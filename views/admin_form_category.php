<?php

/**
 * The form to be loaded on the plugin's admin page
 */
if (is_admin()) {
    wp_enqueue_script('jquery');
    // This will enqueue the Media Uploader script
    wp_enqueue_media();

?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="cg_category_add_form">
        <h4>Add a Category</h4>
        <input type="hidden" name="action" value="add_cg_category">
        <input required id="cg_category_name" type="text" name="cg_category_name" value="" placeholder="Category Name" />
        <img style="max-height:300px" id="image_url" src="" /><br>
        <input type="hidden" id="cg_category_image" name="cg_category_image" />
        <select name="cg_category_parent" id="cg_category_parent">
            <?php
            $categories = Category::getAllAsArray(null);
            foreach ($categories as $category) {
            ?>
                <option value="<?= $category["id"] ?>"><?= $category["name"] ?></option>
            <?php
            }
            ?>
        </select><br>

        <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image"><br />

        <input type="submit" name="submit" id="submit" class="button button-primary" value="Add">
    </form>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#upload-btn').click(function(e) {
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
                        $('#image_url').attr("src", image_url);
                        $('#cg_category_image').val(uploaded_image.toJSON().id)
                    });
            });
        });
    </script>

<?php
} else {
?>
    <p> You are not authorized to perform this operation. </p>
<?php
}
