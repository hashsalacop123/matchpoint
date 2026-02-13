<?php
// Template Name: Dashboard
acf_form_head(); // Must be first
get_header();
?>

<div class="dasboard-wrapper-page">
    <div class="container">
<div class = "row">
    <?php 
       // $user = wp_get_current_user();

$current_user_id = get_current_user_id();

if ( $current_user_id ) {

    // Get registration_status or fallback to 'pending'
    $registration_status = get_field('registration_status', 'user_' . $current_user_id) ?: 'pending';
    // Only show message if status is pending
    if ( $registration_status === 'pending' ) : ?>
        <div class="col-xl-12 col-lg-12 col-md-12 col-xs-12">
            <div class="notification">
                Your registration has been received. Good news—you can start adding your listings! 
                They won't be published until your account is approved. Approval usually takes 4–5 hours.
            </div>
        </div>
    <?php endif; 
}
?>
    <!-- SIDEBAR START HERE -->
    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-12">
        <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
    </div>
    <!-- CONTTENT START HERE -->
    <div class = "col-xl-9 col-lg-9 col-md-9 col-sm-12">

<div id="geocoder"></div>
<div id="map" style="height:400px;"></div>
    <?php echo 'test';?>
    </div>
</div>

    </div>
</div>

<?php get_footer(); ?>