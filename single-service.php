<?php get_header(); ?>
<div class = "service-wrapper">
    <div class = "container">
        <div class = "row">
        <div class = "col-xl-9 col-lg-9 col-md-9 col-sm-12">
            <div class = "wrapper-data">
                <h1><?php echo get_the_title(); ?></h1>
                <div class="service-meta">
    <!-- Author -->
   

    <!-- Date -->
    <span class="service-date">
      Posted  on <?php echo get_the_date(); ?>
    </span>

    <!-- Genre (custom taxonomy) -->
    <?php 
    $terms = get_the_terms( get_the_ID(), 'genre' );
    if ( $terms && ! is_wp_error( $terms ) ) : 
        $genre_names = wp_list_pluck( $terms, 'name' ); // get names only
    ?>
        <span class="service-genre">
            | Genre: <?php echo esc_html( implode( ', ', $genre_names ) ); ?>
        </span>
    <?php endif; ?>
</div>
                <div class = "gallery-image">
                   <?php
$images = get_field('images');

if ( $images ) : ?>
    
    <!-- Main Slider -->
    <div class="service-gallery-main">
        <?php foreach ( $images as $image ) : ?>
            <div class="slide">
                <img 
                    src="<?php echo esc_url( $image['sizes']['large'] ); ?>" 
                    alt="<?php echo esc_attr( $image['alt'] ); ?>">
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Thumbnail Navigation -->
    <div class="service-gallery-thumbs">
        <?php foreach ( $images as $image ) : ?>
            <div class="thumb">
                <img 
                                src="<?php echo esc_url( $image['sizes']['thumbnail'] ); ?>" 
                                alt="<?php echo esc_attr( $image['alt'] ); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php endif; ?>
                </div>
                <!-- address data -->
                <div class = "divider-sp">

                    <hr>
                </div>
                <div class = "address-data">
                    <div class = "address-data-information">
                        <?php $address = get_field('address');?>
                        <h3><i class="fa fa-map-pin" aria-hidden="true"></i> <?php echo $address; ?></h3>

                    </div>
                    <div class = "share-and-heart">
                        <ul>
                            <li><a href = "#"><i class="fa fa-share-alt" aria-hidden="true"></i></a></li>
                            <li><a href = "#"><i class="fa fa-heart" aria-hidden="true"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class = "service-maps">
                    <?php
                        $lat = get_field('address_lat');
                        $lng = get_field('address_lang');

                        if ( $lat && $lng ) : ?>
                            <div 
                                id="service-map"
                                data-lat="<?php echo esc_attr($lat); ?>"
                                data-lng="<?php echo esc_attr($lng); ?>"
                                style="width:100%; height:200px;">
                            </div>
                        <?php endif; ?>
                </div>
                    <?php if ( have_rows('availability') ) : ?>
                            <ul class="service-availability">
                                <?php while ( have_rows('availability') ) : the_row(); ?>
                                    <li><div class = "day-availability">
                                       <?php $field = get_sub_field_object('day');
                                    if ( $field ) {
                                        $value = $field['value'];
                                        $label = $field['choices'][ $value ];
                                        echo esc_html( mb_substr( $label, 0, 3 ) ); } ?></div>
                                        <?php echo  get_sub_field('time'). ' - '.get_sub_field('end_time'); ?>

                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php endif; ?>

                 <div class = "divider-sp">
                    <hr>
                </div>  
                <div class = "about-us-service">
                    <?php $about = get_field('about_me'); 
                        echo $about;
                    ?>

                </div>



            </div>
        </div>
                <!-- SIDEBAR START HERE -->
        <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-12">
            <?php get_template_part( 'single-service/sidebar' ); ?>      
        </div>
          
        <!-- SIDEBAR CLOSED HERE -->
        </div>
    </div>
</div>
<div class = "container">
             <?php 

require_once get_template_directory() . '/inc/customer-booking.php';
?>
                    </div>
   
<?php get_footer(); ?>