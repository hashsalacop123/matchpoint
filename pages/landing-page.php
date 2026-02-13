<?php 
// Template Name: Landing Page

get_header(); ?>


<div class="landing-page-wrapper">
  <!-- Video Background -->
  <video autoplay muted loop playsinline class="landing-video">
    <source src="<?php echo get_stylesheet_directory_uri(); ?>/img/video-landing-home-compress.mp4" type="video/mp4">
    <!-- Fallback image if video can't play -->
    Your browser does not support the video tag.
  </video>

  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <div class="content-landing-page">
          <h1>Welcome to Match Point</h1>
          <ul>
            <li class = "neon-hover"><a href="/court/"><img src = "<?php echo get_stylesheet_directory_uri(); ?>/img/tennis-ball.png"/><span>Court</span>
</a></li>                      <li class = "neon-hover"><a href="/coaches/"><img src = "<?php echo get_stylesheet_directory_uri(); ?>/img/tennis-ball.png"/><span>Coaches</span></a></li>

          </ul>
        </div>
      </div>
    </div>
  </div>
</div>


<?php get_footer(); ?>