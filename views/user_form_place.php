<?php
wp_enqueue_script('jquery');
?>
<form id="user_form_place" enctype="multipart/form-data">
    <label for="cg_place_name">Name</label><br>
    <input required id="cg_place_name" type="text" name="cg_place_name" value="" placeholder="Place Name" /><br><br>
    <label for="cg_place_description">Description</label><br>
    <?php wp_editor('Enter Description here...', 'cg_place_description', $settings = array('textarea_name' => 'cg_place_description', 'editor_css' => '<style>#wp-cg_place_description-wrap{max-width:900px}</style>')); ?>
    <br>
    <label for="cg_place_embed_code">Embed Code</label><br>
    <input required id="cg_place_embed_code" type="text" name="cg_place_embed_code" value="" placeholder="Embed Code" /><br><br>
    <br><label for="cg_place_category">Category</label><br>
    <select name="cg_place_category" id="cg_place_category">
        <?php
        $categories = Category::getAllAsArray();
        foreach ($categories as $category) {
        ?>
            <option value="<?= $category["id"] ?>"><?= $category["name"] ?></option>
        <?php
        }
        ?>
    </select><br>
    <input type="file" id="cg_place_image" name="upload"><br><br>
    <button id="save-support" name="add_listing" type="submit">Add Listing</button>
</form>
<div id="cg-add-to-cart-button" style="display:none;">
    <h1>You already have a pending listing.</h1>
    <h4>Please complete the order to continue.</h4><?= do_shortcode('[add_to_cart id="' . get_option('cg_product_id') . '"]') ?>
</div>
<script type="text/javascript">
    var place = <?= json_encode(Place::checkPending(get_current_user_id())); ?>;
    if (place) {
        jQuery("#cg-add-to-cart-button").show();
        jQuery("#user_form_place").hide();
    } else {
        jQuery("#user_form_place").show();
        jQuery("#cg-add-to-cart-button").hide();
    }
    jQuery(document).on('submit', '#user_form_place', function(e) {
        e.preventDefault();
        var name = jQuery('#cg_place_name').val();
        var description = jQuery('#cg_place_description').val();
        var embed_code = jQuery('#cg_place_embed_code').val();
        var category = jQuery('#cg_place_category').val();
        var file_data = jQuery('#cg_place_image').prop('files')[0];

        var form_data = new FormData();
        form_data.append('image', file_data);
        form_data.append('action', 'cg_place_user_form');
        form_data.append('name', name);
        form_data.append('description', description);
        form_data.append('embed_code', embed_code);
        form_data.append('category', category);
        jQuery.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'post',
            contentType: false,
            processData: false,
            data: form_data,
            success: function(response) {
                // TODO: show pay pal button
                alert("Place submitted successfully");
                jQuery("#cg-add-to-cart-button").show();
                jQuery("#user_form_place").hide();
            },
            error: function(response) {
                console.log('error');
            }

        });
    });
</script>