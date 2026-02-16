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

// THIS FUNCTIONS IS TO UPDATE THE STATUS IN THE DASBOARD

// Handle AJAX booking status update
function update_booking_status() {

    // Verify nonce for security
    check_ajax_referer('booking_nonce', 'nonce');

    // Get current user
    $current_user_id = get_current_user_id();

    // Get posted values
    $booking_id = intval($_POST['booking_id']);
    $status     = sanitize_text_field($_POST['status']);

    // Validate booking ID
    if (!$booking_id) {
        wp_send_json_error('Invalid booking.');
    }

    // Allowed statuses only
    $allowed_statuses = ['pending', 'approved', 'rejected'];
    if (!in_array($status, $allowed_statuses)) {
        wp_send_json_error('Invalid status.');
    }

    // SECURITY: Make sure this booking belongs to the logged-in coach
  $coach_field = get_field('coach__services', $booking_id);

        // Normalize coach ID
        $coach_id = 0;

        if (is_array($coach_field) && isset($coach_field['ID'])) {
            $coach_id = $coach_field['ID'];
        } elseif (is_object($coach_field) && isset($coach_field->ID)) {
            $coach_id = $coach_field->ID;
        } else {
            $coach_id = intval($coach_field);
        }

        // Security check
        if ($coach_id !== $current_user_id) {
            wp_send_json_error('Unauthorized.');
        }

    // Update ACF field
    update_field('booking_status', $status, $booking_id);

    // -----------------------------
    // Send email to the guest
    // -----------------------------
    $guest_email = get_field('guest_email', $booking_id);
    $guest_name  = get_field('guest_name', $booking_id);
    $date        = get_field('date_booked', $booking_id);
    $start       = get_field('time_start', $booking_id);
    $end         = get_field('time_end', $booking_id);

    if ($guest_email) {

        $subject = 'Your Booking Status Has Been Updated';

        $message = "
        Hi {$guest_name},

        Your booking status has been updated.

        Booking Details:
        Date: {$date}
        Time: {$start} - {$end}
        Status: " . ucfirst($status) . "

        Thank you.
        ";

        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        wp_mail($guest_email, $subject, $message, $headers);
    }

    wp_send_json_success('Status updated.');
}
add_action('wp_ajax_update_booking_status', 'update_booking_status');
