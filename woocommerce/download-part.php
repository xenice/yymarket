<?php 
if(!defined('ABSPATH')) exit;


?>

<div class="flex">
  <div class="top">
    <h2 class="field post-title"><a href="<?php the_permalink()?>"
        title="<?php the_title()?>"><?php the_title()?></a>
        <?php if(yy_get_field(get_the_ID(),'version')):?>
        <span class="badge badge-secondary"><?php echo yy_get_field(get_the_ID(),'version');?></span>
        <?php endif;?>
    </h2>
    <div class="field excerpt"><?php the_excerpt()?></div>
    
    <div class="field price">
        <?php echo yy_get_price($post->ID)?>
        <?php
        /*
        $discount_text = get_vip_discount_text($post->ID);
        if($discount_text){?>
        <span class="badge badge-custom"><?php echo $discount_text ?></span>
        <?php } */ ?>
    </div>
    <div class="field buttons">
        <?php if(yy_get_price($post->ID)):?>
            <a class="btn btn-custom" href="<?php echo yy_get_checkout_url(get_the_ID())?>">
                <?php echo esc_html__('Buy Now', 'onenice')?>
            </a>
        <?php endif;?>
        <?php do_action('yymarket_after_buy_now_button', $post->ID); ?>
        
        <?php if(yy_get_field(get_the_ID(),'free_download_url')):?>
            <a class="btn btn-info" target="_blank" href="<?php echo yy_get_field(get_the_ID(),'free_download_url')?>">
                <?php echo esc_html__('Free download', 'onenice')?>
            </a>
        <?php endif;?>
        <?php if(yy_get_field(get_the_ID(),'demo_url')):?>
            <a class="btn btn-success" href="<?php echo yy_get_field(get_the_ID(),'demo_url')?>" target="_blank">
                <?php echo esc_html__('Demo url', 'onenice')?>
            </a>
        <?php endif;?>
        
        <?php 
            if(yy_get('enable_customer_service')){
                include __DIR__ . '/customer-service.php';
            }
        ?>
            
    </div>
    <?php 
    $info = get_post_meta($post->ID, 'yy_service_info', true);
    if($info){
        $str = '<div class="service"><ul>';
        $arr =  explode(',', $info);
        foreach($arr as $val){
            $str .= '<li><i class="fa fa-check"></i> '.$val.'</li>';
        }
        $str .= '</ul></div>';
        echo $str;
    }
    ?>
  </div> <!-- top -->
  <div class="bottom">
      <div class="cover">
          <?php 
            the_post_thumbnail();
          ?>
          <?php //do_action('yymarket_add_discount_text', $post->ID); ?>
      </div>
  </div>
</div> <!-- flex -->