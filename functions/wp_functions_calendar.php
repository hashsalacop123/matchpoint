<?php 
add_action('wp_ajax_handle_coach_booking', 'handle_coach_booking');
add_action('wp_ajax_nopriv_handle_coach_booking', 'handle_coach_booking');

function handle_coach_booking() {
$name    = sanitize_text_field($_POST['name']);
$email   = sanitize_email($_POST['email']);
$comment = sanitize_textarea_field($_POST['comment']);
$amount  = floatval($_POST['amount']);

$start_raw = sanitize_text_field($_POST['start']);
$end_raw   = sanitize_text_field($_POST['end']);
$date_raw  = sanitize_text_field($_POST['date']);

$start = date('g:00 A', strtotime($start_raw));
$end   = date('g:00 A', strtotime($end_raw));
$date  = date('Y-m-d', strtotime($date_raw));


    $date_raw = sanitize_text_field($_POST['date']);
    $date = date('Y-m-d', strtotime($date_raw));
    $comment = sanitize_textarea_field($_POST['comment']);
    $amount  = floatval($_POST['amount']);

    if (!$name || !$email || !$start || !$end || !$date) {
        wp_send_json_error('Missing required fields');
    }

    // Get coach (current post author)
$coach_id = intval($_POST['coach_id']);

    // Hold time in minutes
    $hold_minutes = 15;
    $expires = date('Y-m-d H:i:s', strtotime("+{$hold_minutes} minutes"));

    $booking_id = wp_insert_post([
        'post_type'   => 'booking',
        'post_status' => 'publish',
        'post_title'  => 'Booking - ' . $name,
    ]);

    if (!$booking_id) {
        wp_send_json_error('Booking failed');
    }

    // Save ACF fields
    update_field('guest_name', $name, $booking_id);
    update_field('guest_email', $email, $booking_id);
    update_field('guest_comment', $comment, $booking_id);
    update_field('date_booked', $date, $booking_id);
    update_field('time_start', $start, $booking_id);
    update_field('time_end', $end, $booking_id);
    update_field('amount', $amount, $booking_id);
    update_field('coach__services', $coach_id, $booking_id);

    update_field('booking_status', 'pending', $booking_id);
    update_field('hold_expires', $expires, $booking_id);

    // Email guest
    wp_mail(
        $email,
        'Booking Pending',
        'Your booking is pending approval.'
    );

    wp_send_json_success();
}
add_action('acf/save_post', function ($post_id) {

    if (get_post_type($post_id) !== 'booking') return;

    $status = get_field('booking_status', $post_id);
    $email  = get_field('guest_email', $post_id);

    if ($status === 'approved') {
        wp_mail($email, 'Booking Approved', 'Your booking was approved.');
    }

    if ($status === 'rejected') {
        wp_mail($email, 'Booking Rejected', 'Your booking was rejected.');
    }

});
