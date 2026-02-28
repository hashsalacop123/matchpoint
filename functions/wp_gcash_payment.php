<?php 
add_action('template_redirect', function () {

    if (get_query_var('paymongo_webhook') != 1) {
        return;
    }

    $payload = file_get_contents('php://input');
    $data = json_decode($payload, true);

    if (!$data) {
        status_header(400);
        exit('Invalid payload');
    }

    $event_type = $data['data']['attributes']['type'] ?? '';

if ($event_type === 'link.payment.paid') {

    $payment_data = $data['data']['attributes']['data']['attributes'];
    $remarks = $payment_data['remarks'] ?? '';
    $amount_paid = $payment_data['amount'] ?? 0;

    preg_match('/Booking ID:\s*(\d+)/', $remarks, $matches);

    if (!empty($matches[1])) {

        $booking_id = intval($matches[1]);

        // Make sure booking exists
        if (get_post_type($booking_id) !== 'booking') {
            status_header(400);
            exit('Invalid booking');
        }

        // Prevent double approval
        $current_status = get_field('booking_status', $booking_id);
        if ($current_status === 'approved') {
            status_header(200);
            exit('Already approved');
        }

        // OPTIONAL: Validate amount matches
        $expected_amount = get_field('amount', $booking_id) * 100;

        if ($expected_amount != $amount_paid) {
            status_header(400);
            exit('Amount mismatch');
        }

        // Approve booking
        update_field('booking_status', 'approved', $booking_id);
    }
}

    status_header(200);
    echo 'Webhook received';
    exit;
});


/**
 * Create PayMongo GCash payment link
 */
function create_paymongo_gcash_payment($amount, $description, $booking_id) {

    $secret_key = PAYMONGO_SECRET_KEY;

    $data = [
        'data' => [
            'attributes' => [
                'amount' => intval($amount * 100),
                'description' => $description,
                'remarks' => 'Booking ID: ' . $booking_id,
                'payment_method_types' => ['gcash'],
                'redirect' => [
                    'success' => home_url('/payment-success/?booking_id=' . $booking_id),
                    'failed'  => home_url('/payment-failed/')
                ]
            ]
        ]
    ];

    $args = [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode($secret_key . ':'),
            'Content-Type'  => 'application/json'
        ],
        'body' => json_encode($data),
        'method' => 'POST'
    ];

    $response = wp_remote_post(
        'https://api.paymongo.com/v1/links',
        $args
    );

    // Debug response
    if (is_wp_error($response)) {
        error_log('PayMongo error: ' . $response->get_error_message());
        return false;
    }

    $body_raw = wp_remote_retrieve_body($response);
    error_log('PayMongo response: ' . $body_raw);

    $body = json_decode($body_raw, true);

    return $body['data']['attributes']['checkout_url'] ?? false;
}



add_action('rest_api_init', function () {
    register_rest_route('paymongo/v1', '/webhook', [
        'methods'  => 'POST',
        'callback' => 'handle_paymongo_webhook',
        'permission_callback' => '__return_true',
    ]);
});

// function handle_paymongo_webhook($request) {

//     $webhook_secret = PAYMONGO_WEBHOOK_SECRET;
//         error_log('Webhook secret: ' . $webhook_secret); // 👈 Add it here temporarily

//     $payload = $request->get_body();
//     $signature_header = $_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '';

//     if (!$signature_header) {
//         return new WP_REST_Response(['error' => 'Missing signature'], 400);
//     }

//     // Extract signature (PayMongo format: t=timestamp,v1=signature)
//     preg_match('/v1=([^,]+)/', $signature_header, $matches);

//     if (empty($matches[1])) {
//         return new WP_REST_Response(['error' => 'Invalid signature format'], 400);
//     }

//     $signature = $matches[1];

//     // Compute expected signature
//     $expected_signature = hash_hmac('sha256', $payload, $webhook_secret);

//     if (!hash_equals($expected_signature, $signature)) {
//         return new WP_REST_Response(['error' => 'Invalid signature'], 400);
//     }

//     // ✅ Signature verified — safe to process
//     $data = json_decode($payload, true);

//     $event_type = $data['data']['attributes']['type'] ?? '';

//     if ($event_type === 'link.payment.paid') {

//         $payment_data = $data['data']['attributes']['data']['attributes'];
//         $remarks = $payment_data['remarks'] ?? '';

//         preg_match('/Booking ID:\s*(\d+)/', $remarks, $matches);

//         if (!empty($matches[1])) {

//             $booking_id = intval($matches[1]);

//             if (get_post_type($booking_id) === 'booking') {

//                 $current_status = get_field('booking_status', $booking_id);

//                 if ($current_status !== 'approved') {
//                     update_field('booking_status', 'approved', $booking_id);
//                 }
//             }
//         }
//     }

//     return new WP_REST_Response(['received' => true], 200);
// }

// function handle_paymongo_webhook($request) {

//     file_put_contents(
//         WP_CONTENT_DIR . '/webhook-test.txt',
//         'Webhook hit at: ' . current_time('mysql') . "\n",
//         FILE_APPEND
//     );

//     return new WP_REST_Response(['ok' => true], 200);
// }

function handle_paymongo_webhook($request) {

    file_put_contents(
        WP_CONTENT_DIR . '/webhook-test.txt',
        'Webhook hit at: ' . current_time('mysql') . "\n",
        FILE_APPEND
    );

    return new WP_REST_Response(['ok' => true], 200);
}
?>