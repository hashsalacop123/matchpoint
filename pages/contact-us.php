<?php 
//Template Name: Contact Us

get_header(); ?>
<div class = "contact-us-main-wrapper">
    <div class="banner-pages" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/img/contact-us-2.jpg' ); ?>'); background-position: center top;">
        <div class = "container">
            <div class = "row">
                <div class = "col-lg-6 col-md-6 col-sm-12">
                    <h1>Contact Us</h1>
                    <p>Have questions or want to get in touch?</p>
                    <p>We'd love to hear from you.</p>
                </div>
                <div class = "col-lg-6 col-md-6 col-sm-12">
                </div>

            </div>
        </div>
    </div>

</div>

<section class = "contact-from">
    <div class = "container">
        <div class = "row">
            <div class = "col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class = "form-container">
                        <h2>Get in Touch</h2>
                        <div class = "contact-form-wrapper">
                        <?php echo do_shortcode('[contact-form-7 id="455a464" title="Contact us"]'); ?>
                        </div>

                    </div>
            </div>
            <div class = "col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class = "form-image">
                        <img src = "<?php echo get_template_directory_uri().'/img/pexels-lebih-dari-ini-3915826-5908430.jpg' ?>" class = "img-fluid">

                    </div>
            </div>
        </div>
    </div>

</section>

<?php get_footer(); ?>