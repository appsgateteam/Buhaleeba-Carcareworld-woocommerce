<section class="ysg-product-brands-section">
    <div class="all-brands">
        <?php foreach ($product_brands as $item) : ?>
            <div class="one_brand">
                <a href="/shop/?filter_product-brand=<?php echo strtolower($item->name)?>">
                <img src="<?php echo $item->image ?>" alt=""/>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>