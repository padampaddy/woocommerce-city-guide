<?php global $the_item;
global $detail_page_slug; ?><li>
    <a href="<?= get_home_url(null, $detail_page_slug . "?listing=" . $the_item["id"]) ?>">
        <div class="wraper-img">
            <img src="<?= wp_get_attachment_url($the_item['image']) ?>" alt="" class="">
        </div>
        <div class="desp">
            <a href="<?= get_home_url(null, $detail_page_slug . "?listing=" . $the_item["id"]) ?>">
                <h3><?= $the_item['name'] ?></h3>
            </a>
            <h4><?= Category::withId($the_item['category'])->name ?></h4>
            <p><?= sanitize_text_field(html_entity_decode(substr($the_item["description"], 0, 400))) ?>...
            </p>
        </div>
    </a>
</li>