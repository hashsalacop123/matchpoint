<? get_header(); ?>
<div class="coach-wrapper " data-coach="<?php echo get_the_author_meta('ID'); ?>">
        <div class = "container">
            <div class = "row">
            <div class = "col-xl-5 col-lg-5 col-md-5 col-sm-12">
                <div class = "profile-pic">

                <?php 

// Declare image size (thumbnail, medium, large, full, or custom size)
$image_size = 'large';

// Fallback image URL
$fallback_image = get_template_directory_uri() . '/assets/images/fallback.jpg';

// Get ACF image field
$featured_image = get_field('featured_image');

if ( ! empty( $featured_image ) ) {
    // Image exists
    $image_url = $featured_image['sizes'][ $image_size ] ?? $featured_image['url'];
    $image_alt = esc_attr( $featured_image['alt'] );
} else {
    // Fallback image
    $image_url = $fallback_image;
    $image_alt = 'Default image';
}
?>

<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo $image_alt; ?>">

              
                </div>
            </div>
            <div class = "col-xl-7 col-lg-7 col-md-7 col-sm-7">
                <div class = "wrapper-coach-info">
                    <?php $nickname = get_field('nick_name'); 
                          $sports = get_field('sports');

                          echo '<h1>'.$nickname.'</h1>';
                            echo '<ul>';
                                    foreach($sports as $sport) {
                                        echo '<li>'.$sport->name.'</li>';
                                      
                                    }
                            echo '</ul>';
                     
                        echo '<div class = "additional-information-coach">';
                         echo '<div>';     
                               if ( $address = get_field('address') ) {
                                     echo '<b><i class="fa fa-home" aria-hidden="true"></i>
</b> ' . $address;
                                  }
                                echo '</div>';
                                 echo '<div>';     
                               if ( $phone = get_field('phone') ) {
                                     echo '<b><i class="fa fa-phone" aria-hidden="true"></i> 
</b> ' . $phone;
                                  }
                                echo '</div>';
                               echo '<div>';     
                               if ( $gender = get_field('gender') ) {
                                     echo '<b><i class="fa fa-id-card" aria-hidden="true"></i> 
</b>  ' . $gender;
                                  }
                                echo '</div>';
                                echo '<div>';
                                if ( $hourly_rate = get_field('hourly_rate') ) {
                                     echo '<b><i class="fa fa-hourglass-start" aria-hidden="true"></i> 


</b> ' .  $hourly_rate.' / hr';
                                  }
                                  echo '</div>';
                                  echo '<div>';
                                    echo '<a href = "#booknow"><i class="fa fa-bookmark" aria-hidden="true"></i>
 Book Now</a>';
                                  echo '</div>';
                        echo '</div>';
                    ?>
                    
                </div>
            </div>
            <div class = "col-lg-12 col-xl-12 col-md-12 col-xs-12">
                <div class = "wrappper-main-formation">
                    <h3>About Me</h3>
                    <?php $about = get_field('about_me'); ?>
                        <?php if($about) :
                            echo $about;
                        endif;  ?>
                    <div class = "gallery-wrapper">
                        <h3>Beyond the court<h3>
                        <?php
                        $gallery = get_field('mygallery');

                        if ( $gallery ) : ?>
                            <div class="coach-gallery">
                                <?php foreach ( $gallery as $image ) : ?>
                        <a 
                                        href="<?php echo esc_url( $image['url'] ); ?>"
                                        data-fancybox="coach-gallery"
                                        data-caption="<?php echo esc_attr( $image['caption'] ); ?>"
                                        class="coach-gallery-item"
                                    >                <img 
                                            src="<?php echo esc_url( $image['sizes']['medium'] ); ?>" 
                                            alt="<?php echo esc_attr( $image['alt'] ); ?>"
                                        >
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                        <div class = "availability-of-coach">
                            <h3>Availability Calendar</h3>

<?php 
$datacoach = get_field('avalability');
$dates = json_decode($datacoach, true);
$rate_value = get_field('hourly_rate'); 

// 2. Convert to a clean number (remove commas or currency signs if any)
$clean_rate = filter_var($rate_value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
if(empty($clean_rate)) { $clean_rate = 0; }

if($dates) {

    // Get coach ID from post author
    $coach_id = get_post_field('post_author', get_the_ID());

    // Get bookings for this coach (pending + approved)
    $args = [
        'post_type' => 'booking',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => 'coach__services',
                'value' => $coach_id,
                'compare' => '='
            ],
            [
                'key' => 'booking_status',
                'value' => ['pending', 'approved'],
                'compare' => 'IN'
            ]
        ]
    ];

    $booking_posts = get_posts($args);

    $blocked_slots = [];

 foreach ($booking_posts as $booking) {
    $date_raw  = get_field('date_booked', $booking->ID);
    $start_raw = get_field('time_start', $booking->ID);
    $end_raw   = get_field('time_end', $booking->ID);
    $status    = get_field('booking_status', $booking->ID);

    if ($date_raw && $start_raw && $end_raw) {

        $date = date('Y-m-d', strtotime($date_raw));

        $start = new DateTime($start_raw);
        $end   = new DateTime($end_raw);

        // Loop through each hour in the range
        $current = clone $start;

        while ($current <= $end) {
            $slot_time = $current->format('g:00 A');
            $blocked_slots[$date][$slot_time] = $status;
            $current->modify('+1 hour');
        }
    }
}



foreach ($booking_posts as $booking) {
    $date_raw  = get_field('date_booked', $booking->ID);
    $start_raw = get_field('time_start', $booking->ID);
    $status    = get_field('booking_status', $booking->ID);

    if ($date_raw && $start_raw) {

        $date  = date('Y-m-d', strtotime($date_raw));
        $start = date('g:00 A', strtotime($start_raw));

        $blocked_slots[$date][$start] = $status;
    }
}


    usort($dates, function($a, $b) {
        return strtotime($a['start']) - strtotime($b['start']);
    });

    $timezone = new DateTimeZone('Asia/Manila'); 
    echo '<ul class="step-1-wrapper slider-calendar" data-rate="'.$clean_rate.'">';
    
    foreach ($dates as $date) {
        $start = new DateTime($date['start']);
        $start->setTimezone($timezone);
        $end = new DateTime($date['end']);
        $end->setTimezone($timezone);

        // Add 'day-parent' class here so JS can find this container easily
        echo '<li class="day-parent">'; 
        
        echo '<div class="day-booked">
                <div class="month">'.$start->format('F') .'</div>
                <strong>' . $start->format('l, m/d/Y') . '</strong>';
        
        // --- MOVED INPUT HERE ---
        // Placing it inside an existing div prevents it from breaking the UL layout
        $slots = [];
        $temp_curr = clone $start;
        while ($temp_curr < $end) {
            $slots[] = $temp_curr->format('g:00 A');
            $temp_curr->modify('+1 hour');
        }
        echo '<input type="hidden" class="day-slots" value="'.htmlspecialchars(json_encode($slots)).'">';
        // ------------------------

        echo '</div>'; // close day-booked


        echo '<ul class="step-2-wrapper">';
foreach ($slots as $time_val) {

    $slot_date = $start->format('Y-m-d');
    $status = $blocked_slots[$slot_date][$time_val] ?? '';

    $class = 'availability';
    $style = 'cursor:pointer;';
    $label = $time_val;

    if ($status === 'pending') {
        $class .= ' pending';
        $label .= ' (Pending)';
    }

    if ($status === 'approved') {
        $class .= ' booked';
        $label .= ' (Booked)';
    }

    echo '<li class="'.$class.'" 
            data-time="'.$time_val.'" 
            data-date="'.$slot_date.'" 
            style="'.$style.'">' . $label .'</li>';
}

        echo '</ul>';
        echo '</li>';
    }
    echo '</ul>';
}
?>
                            <div class = "container">
                                <div class = "row">
                                    <div class = "col-md-6 col-xs-12">

                                    </div>
                                    <div class = "col-md-6 col-xs-12">
                                
                                    </div>
                                </div>
                            </div>

                        </div>


                </div>
            </div>
            </div> <!--row-->
        </div>  <!-- <--CONTAINER CLOSE DIV --> 
         
    </div>
<div id="bookingModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h3 class="modal-title">Book this slot</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <label>Name:</label>
                <input type="text" id="booking_name" class="form-control mb-3" placeholder="Enter your name" required>
                <input type="hidden" id="selected_date">

                <label>Email:</label>
                <input type="email" id="booking_email" class="form-control mb-3" placeholder="Enter your email" required>

                <div class="row">
                    <div class="col-md-6">
                        <label>Start Time:</label>
                        <select id="booking_start" class="form-control mb-3"></select>
                    </div>
                    <div class="col-md-6">
                        <label>End Time:</label>
                        <select id="booking_end" class="form-control mb-3"></select>
                    </div>
                </div>

                <label>Comment (optional):</label>
                <textarea id="booking_comment" class="form-control mb-3" rows="3"></textarea>
                
                <label>Total Amount:</label>
                <div class="total-amount">
                    <input id="amount" type="text" class="form-control" placeholder="0.00">
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="confirm_booking_btn" class="btn btn-primary">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>
<script>
var coachBookingData = {
    coach_id: <?php echo get_post_field('post_author', get_the_ID()); ?>
};
</script>


<?php get_footer(); ?>