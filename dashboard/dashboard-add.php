
<?php 
// TEMPLATE NAME: Add
acf_form_head(); // Must be first
get_header(); ?>


<div class="dasboard-wrapper-page">
    <div class="container">
<div class = "row">
    <!-- SIDEBAR START HERE -->
    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-12">
        <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
    </div>
    <!-- CONTTENT START HERE -->
    <div class = "col-xl-9 col-lg-9 col-md-9 col-sm-12">
        <!-- Map container on top -->
         <div id="geocoder" style="margin-bottom:10px;"></div>

<div id="map" style="height:300px; margin-bottom:10px;"></div>

<?php
$current_user = wp_get_current_user();
$full_name = trim($current_user->first_name . ' ' . $current_user->last_name);
if(empty($full_name)) $full_name = $current_user->display_name;

acf_form(array(
    'post_id'  => 'new_post',
    'new_post' => array(
        'post_type'   => 'service',
        'post_status' => 'publish',
        'post_author' => $current_user->ID,
        'uploader' => 'wp', // use WP media uploader instead of ACF JS
        'post_title'  => $full_name,
    ),
    'fields' => array(
        'field_695342a608250', // address
         'field_694f8fb13c33e', // are_you
        'field_695372c113ba9', //address_lat
        'field_695372d913baa', // address_lang
        'field_694f91a9337a4', // featured_image
        'field_695a06f665038', // HOURLAY RATE
        'field_694f8e7f3c336', // age
        'field_694f90df260bf', // gender
        'field_694f91c4337a5', // phone
        'field_6959ed3caa0db', //email
        'field_6959ee0892a89', //website
        'field_694f8e8c3c337', // images
        'field_694f8ea43c338', // social_media
        'field_694f8f523c33a', // availability
        'field_694f8f923c33d', // about_me
        'field_694f90873c33f', // additional_information
    ),
    'submit_value' => 'Submit',
    'return' => add_query_arg('submitted', 'true', get_permalink()),
    'html_form' => true,
));
?>


<!-- Hidden lat/lng fields inside the form -->
<input type="hidden" id="acf-field_lat" name="acf[field_lat]" />
<input type="hidden" id="acf-field_lng" name="acf[field_lng]" />

    </div>
</div>

    </div>
</div>



<?php get_footer(); ?>