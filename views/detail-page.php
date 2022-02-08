    <?php $placeId = $_GET["listing"];
    $place = Place::withId(intval($placeId));
    if ($place) {
    ?>
        <div class="container">
            <section class="item-detail">
                <h4><?= $place->name ?></h4>
            </section>
            <section class="product-detail">
                <div class="cat-img">
                    <img src="<?= wp_get_attachment_url($place->image) ?>" alt="" class="">
                </div>
                <div class="cat-detail">
                    <h2>Description</h2>
                    <h4>Category: <?= $place->category->name ?></h4>
                    <div class="item-info">
                        <h4></h4>
                    </div>
                </div>
            </section>
            <section class="desp-wrap">
                <div class="">
                    <h4>Place Information</h4>
                    <?= html_entity_decode($place->description) ?>
                </div>
            </section>
        </div>
        <div class="map">
            <?= html_entity_decode($place->embedCode) ?>
        </div>
        <!-- <section class="feature-listing">
            <div class="container">
                <h1>PEOPLE WHO LOVED THIS PROPERTY ALSO VIEWED:</h1>
                <div class="feature-wrap">
                    <div class="feature-list">
                        <a href="#">
                            <div class="feature-img">
                                <img src="images/feature.jpg" class="" alt="">
                            </div>
                            <div class="feature-info">
                                <h4>Lorem Ipsum is simply dummy text of the printing</h4>
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting.
                                </p>
                            </div>
                        </a>

                    </div>
                    <div class="feature-list">
                        <a href="#">
                            <div class="feature-img">
                                <img src="images/feature.jpg" class="" alt="">
                            </div>
                            <div class="feature-info">
                                <h4>Lorem Ipsum is simply dummy text of the printing</h4>
                                <p>Lorem Ipsum is simply dummy.
                                </p>
                            </div>
                        </a>

                    </div>
                    <div class="feature-list">
                        <a href="#">
                            <div class="feature-img">
                                <img src="images/feature.jpg" class="" alt="">
                            </div>
                            <div class="feature-info">
                                <h4>Lorem Ipsum is simply dummy text of the printing</h4>
                                <p>Lorem Ipsum is simply dummy text of the printing.
                                </p>
                        </a>
                    </div>

                </div>
                <div class="feature-list">
                    <a href="#">
                        <div class="feature-img">
                            <img src="images/feature.jpg" class="" alt="">
                        </div>
                        <div class="feature-info">
                            <h4>Lorem Ipsum is simply dummy text of the printing</h4>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting.
                            </p>
                        </div>
                    </a>
                </div>

            </div>
            </div>

            </div>

        </section> -->
    <?php
    } ?>