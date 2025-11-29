<?php

if(!defined('ABSPATH')) exit;

if(is_shop()){
    add_filter( 'document_title_parts', function($title){
        $title['tagline'] = $title['title'];
        $title['title'] = __('Shop', 'onenice');
        return $title;
    });
    
    
    add_action('yymarket_archive_brand', function(){
    ?>
    <div class="breadcrumb">
    	<div class="container">
    	    <a class="breadcrumb-item" href="<?php echo home_url()?>"><?php echo __('Home', 'onenice') ?></a>
    	    <span class="breadcrumb-item active"><?php echo __('Shop', 'onenice'); ?></span>
    	</div>
    </div>
    <?php
    });

}

add_filter( 'body_class', function( $classes ) {
	return array_merge( $classes, array( 'archive-product' ) );
});
add_filter( 'the_excerpt', 'vessel\ext\products_excerpt_lenght',99);

get_header();



add_action('wp_footer', function(){
    ?>
    <script>
        
    </script>
    <?php
})
?>
<style>

</style>
<div class="yy-main">
    <div class="yy-group main-show">
        <div class="show-brand">
            <?php do_action('yymarket_archive_brand') ?>
        </div>
    </div><!-- yy-group -->
    <div class="yy-group">
        <div class="container">
            <div class="flex product-list">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="card">
                  <div class="card-body">
                    <a class="thumbnail" href="<?php the_permalink()?>" title="<?php the_title() ?>">
                        <img class="lazyload" src="<?php echo yy_get('site_loading_image')?>" data-src="<?php echo get_the_post_thumbnail_url()?:yy_get('site_thumbnail')?>" alt="<?php the_title() ?>" />
                    </a>
                    <div class="data">
                    	<h4 class="card-title">
                    	    <a href="<?php the_permalink()?>" title="<?php the_title() ?>"><?php the_title() ?></a>
                    	</h4>
                    	<div class="bottom-data">
                    	    <div class="time"><?php echo get_the_modified_date('Y-m-d', get_the_ID()); ?></div>
                    	    <div class="price"><?php echo yy_get_price( get_the_ID())?:'';?></div>
                    	</div>
                        
                	</div>
                  </div>
                </div>
                <?php endwhile;?>
                
                <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <div class="data">
                        <p class="card-text"><?php echo __('No products.', 'onenice')?></p>
                        </div">
                    </div>
                </div>
                <?php endif; ?>
                    
                
            </div> <!-- flex -->
            <ul class="pagination">
                <?php echo paginate_links(); ?>
            </ul>
        </div>
    </div><!-- yy-group -->
</div><!-- yy-main -->

<?php


get_footer();