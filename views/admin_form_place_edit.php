<?php

/**
 * The form to be loaded on the plugin's admin page
 */
if (is_admin() && $_GET["cg_place_id"]) {
    // jQuery
    wp_enqueue_script('jquery');
    // This will enqueue the Media Uploader script
    wp_enqueue_media();
    $place = Place::withId($_GET["cg_place_id"]);
    if (!$place) wp_die("No Place found with this ID", "Error finding the Place");
?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="cg_place_edit_form">
        <input type="hidden" name="action" value="edit_cg_place">
        <input type="hidden" name="cg_place_id" value="<?= $place->id ?>">
        <label for="cg_place_name">Name</label><br>
        <input required id="cg_place_name" type="text" name="cg_place_name" value="<?= $place->name ?>" placeholder="Place Name" /><br><br>
        <label for="cg_place_description">Description</label><br>
        <?php wp_editor(html_entity_decode($place->description), 'cg_place_description', $settings = array('textarea_name' => 'cg_place_description', 'editor_css' => '<style>#wp-cg_place_description-wrap{max-width:900px}</style>')); ?>
        <br>
        <label for="cg_place_embed_code">Embed Code</label><br>
        <input required id="cg_place_embed_code" type="text" name="cg_place_embed_code" value="<?= $place->embedCode ?>" placeholder="Embed Code" /><br><br>
        <br><label for="cg_place_status">Status</label><br>
        <select name="cg_place_status" id="cg_place_status">
            <option <?= $place->status === "pending" ? "selected" : "" ?> value="pending">Pending</option>
            <option <?= $place->status === "active" ? "selected" : "" ?> value="active">Active</option>
            <option <?= $place->status === "inactive" ? "selected" : "" ?> value="inactive">Inactive</option>
        </select><br>
        <br><label for="cg_place_category">Category</label><br>
        <select name="cg_place_category" id="cg_place_category">
            <?php
            $categories = Category::getAllAsArray();
            foreach ($categories as $category) {
            ?>
                <option <?= strval($place->category->id) === $category["id"] ? "selected" : "" ?> value="<?= $category["id"] ?>"><?= $category["name"] ?></option>
            <?php
            }
            ?>
        </select><br>
        <br><label for="cg_place_user_id">User</label><br>
        <select name="cg_place_user_id" id="cg_place_user_id">
            <?php
            $args = array('orderby' => 'display_name');
            $wp_user_query = new WP_User_Query($args);
            $authors = $wp_user_query->get_results();
            foreach ($authors as $author) {
                $author_info = get_userdata($author->ID);
            ?>
                <option <?= strval($place->user_id) === $author_info->ID ? "selected" : "" ?> value="<?= $author->ID ?>"><?= $author_info->display_name ?></option>
            <?php
            }
            ?>
        </select><br>
        <br><label for="image_url">Image</label><br>
        <img style="max-height:300px" id="image_url" src="<?= wp_get_attachment_url($place->image) ?>" /><br>
        <input type="hidden" id="cg_place_image" name="cg_place_image" value="<?= $place->image ?>" />
        <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image"><br />
        <br><input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
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
                            $('#cg_place_image').val(uploaded_image.toJSON().id)
                        });
                });
            });
        </script>
    </form>
<?php
} else {
?>
    <p> You are not authorized to perform this operation. </p>
<?php
}
