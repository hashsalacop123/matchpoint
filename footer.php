
<?php if ( is_front_page() || is_page('Registration'))  {
 ?>
<footer>
    <div class = "footer-wrapper dashboard-footer">
        <div class = "container">
                <div class="row">
                    <div class = "col-sm-12 col-md-12 text-center">
                    <p class = "copy-right">&copy; <?php echo date('Y'); ?> Match Point. All rights reserved, All Right Reserved | Designed By <a href="https://hashcrafter.com/">hashcrafter</a></p>
                    </div>
                </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

<?php }else { ?>

<footer>
    <div class = "footer-wrapper inner-footer">
        <div class = "container">
                <div class="row">
                    <div class = "col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <div class = "footer-first-column footer-column">
                            <h5>About us</h5>
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever

 </p>
                        </div>
                    </div>
                    <div class = "col-xl-2 col-lg-2 col-md-2 col-sm-12">
                        <div class = "footer-second-column footer-column">
                            <h5>Quick Links</h5>
                                <ul>
                                    <li><a href = "#">About Us</a></li>
                                    <li><a href = "#">Contact Us</a></li>
                                    <li><a href = "#">FAQ</a></li>
                                    <li><a href = "#">Sitemap</a></li>
                                </ul>
                        </div>
                    </div>
                    <div class = "col-xl-2 col-lg-2 col-md-2 col-sm-12">
                        <div class = "footer-third-column footer-column">
                            <h5>Latest News</h5>
                                <ul>
                                    <li><a href = "#">Tips about Pickleball</a></li>
                                    <li><a href = "#">Cebu City Player</a></li>
                                    <li><a href = "#">You Need to know</a></li>
                                    <li><a href = "#">Player in cebu</a></li>
                                </ul>
                        </div>
                    </div>
                    <div class = "col-xl-4 col-lg-4 col-md-4 col-sm-12">
                        <div class = "footer-third-column footer-column">
                            <h5>Subscribe</h5>
                            <?php echo do_shortcode('[contact-form-7 id="a120c20"]'); ?>
                        </div>
                    </div>
                    <div class = "col-xl-12 col-lg-12 col-md-12">
                        <div class = "divider-sp">
                            <hr>
                        </div>
                    </div>
                    <div class = "col-sm-12 col-md-12 text-center">
                    <p class = "copy-right-bottom-text">&copy; <?php echo date('Y'); ?> test123 Match Point. All rights reserved, All Right Reserved | Designed By <a href="https://hashcrafter.com/">hashcrafter</a></p>
                    </div>
                </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

    <?php } ?>