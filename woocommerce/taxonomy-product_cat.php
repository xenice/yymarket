<?php

if(!defined('ABSPATH')) exit;


$id = get_queried_object_id();

$term = get_term($id, 'product_cat');




add_action('yymarket_archive_brand', function()use($term){

?>
<div class="container brand">
    <h2><?php echo $term->name ?></h2>
    <div class="desc"><?php echo $term->description ?></div>
</div>
<?php
});


include __DIR__ . '/archive-product.php';