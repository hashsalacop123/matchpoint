<?php 
function allow_custom_roles_media_upload() {
    $roles = ['coach', 'player', 'court']; // add your custom roles here

    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role && ! $role->has_cap('upload_files')) {
            $role->add_cap('upload_files');
        }
    }
}
add_action('init', 'allow_custom_roles_media_upload');


function restrict_media_library_to_own_uploads( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Allow admins to see everything
    if ( current_user_can('manage_options') ) {
        return;
    }

    // Only affect media library
    if ( $query->get('post_type') === 'attachment' ) {
        $query->set('author', get_current_user_id());
    }
}
add_action('pre_get_posts', 'restrict_media_library_to_own_uploads');

function restrict_media_library_to_current_user( $query ) {

    // Only affect AJAX requests for media browsing
    if ( defined('DOING_AJAX') && DOING_AJAX ) {

        // Only target the media library browsing request
        if ( isset($_REQUEST['action']) && $_REQUEST['action'] === 'query-attachments' ) {

            $user = wp_get_current_user();
            $restricted_roles = ['coach', 'player', 'court']; // your custom roles

            // Restrict only these roles
            if ( array_intersect( $restricted_roles, (array) $user->roles ) ) {
                $query['author'] = get_current_user_id();
            }

            // Admins can still see everything
            if ( current_user_can('manage_options') ) {
                unset($query['author']);
            } 
        }
    }

    return $query;
}
add_filter('ajax_query_attachments_args', 'restrict_media_library_to_current_user');

// Ensure custom roles can edit their own posts and upload files
add_action('init', function () {

    $roles = ['coach', 'player', 'court'];

    foreach ($roles as $role_name) {
        $role = get_role($role_name);

        if ($role) {
            $role->add_cap('upload_files');
            $role->add_cap('edit_posts');
            $role->add_cap('edit_published_posts');
        }
    }
});



/**
 * Redirect logged-in users to a specific page
 *
 * @param string $redirect_url URL to redirect logged-in users to. Default is '/dashboard'.
 */
function redirect_user_login($redirect_url = '/dashboard') {
    if ( is_user_logged_in() ) {
        wp_redirect( esc_url( $redirect_url ) );
        exit;
    }
}

add_action('update_post_meta', function($meta_id, $post_id, $meta_key, $meta_value){
    // Only target bookings
    $post = get_post($post_id);
    if(!$post || $post->post_type !== 'booking') return;

    // Only target our status field
    if($meta_key !== 'status') return;

    // Only send if changed to approved
    if($meta_value === 'approved'){
        $user_email = get_post_meta($post_id, 'user_email', true);
        $user_name  = get_post_meta($post_id, 'user_name', true);
        $coach_id   = get_post_meta($post_id, 'coach_id', true);
        $start      = get_post_meta($post_id, 'start', true);
        $end        = get_post_meta($post_id, 'end', true);
        $comment    = get_post_meta($post_id, 'comment', true);

        if($user_email){
            $subject = 'Booking Approved';
            $message = "Hi $user_name,\n\nYour booking request for Coach #$coach_id has been approved.\n";
            $message .= "Start: $start\nEnd: $end\nComment: $comment\n\nSee you then!";
            wp_mail($user_email, $subject, $message);
        }
    }

}, 10, 4);

add_action('after_setup_theme', function () {

    // Save ACF JSON inside theme
    add_filter('acf/settings/save_json', function ($path) {
        return get_stylesheet_directory() . '/acf-json';
    });

    // Load ACF JSON from theme
    add_filter('acf/settings/load_json', function ($paths) {
        $paths[] = get_stylesheet_directory() . '/acf-json';
        return $paths;
    });

});

/**
 * AJAX search for coach + service (ACF + author + title)
 */
function search_coaches_ajax() {

    check_ajax_referer('booking_nonce', 'nonce'); // security

    $search = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';

    if (empty($search)) {
        wp_send_json([]);
    }

    $results = [];

    /**
     * STEP 1: SEARCH USERS (for author matching)
     */
    $user_ids = [];
    $users = get_users([
        'search'         => '*' . esc_attr($search) . '*',
        'search_columns' => ['user_login', 'display_name'],
    ]);

    if (!empty($users)) {
        foreach ($users as $user) {
            $user_ids[] = $user->ID;
        }
    }

    /**
     * STEP 2: GET POSTS (no strict filtering)
     */
    $query = new WP_Query([
        'post_type'      => ['coach', 'service'],
        'posts_per_page' => -1, // get all, filter manually
        'post_status'    => 'publish',
    ]);

    /**
     * STEP 3: LOOP + MATCH
     */
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $post_id   = get_the_ID();
            $title     = get_the_title() ?: '';
            $post_type = get_post_type();
            $author_id = get_the_author_meta('ID');

            // ACF fields (safe fallback)
            $nickname  = get_field('nick_name', $post_id) ?: '';
            $address   = get_field('address', $post_id) ?: '';
            $court     = get_field('court_name', $post_id) ?: '';

            // User fields (safe fallback)
            $first = get_user_meta($author_id, 'first_name', true) ?: '';
            $last  = get_user_meta($author_id, 'last_name', true) ?: '';

            /**
             * MULTI-WORD SEARCH SUPPORT
             */
            $search_terms = explode(' ', strtolower($search));
            $match = false;

            foreach ($search_terms as $term) {

                if (
                    stripos($title, $term) !== false ||
                    stripos($nickname, $term) !== false ||
                    stripos($address, $term) !== false ||
                    stripos($court, $term) !== false ||
                    stripos($first, $term) !== false ||
                    stripos($last, $term) !== false ||
                    in_array($author_id, $user_ids)
                ) {
                    $match = true;
                    break;
                }
            }

            /**
             * ADD RESULT
             */
            if ($match) {

                $label = $title;

                if ($post_type === 'service') {
                    $label .= ' (Court)';
                    if (!empty($address)) {
                        $label .= ' - ' . $address;
                    }
                } else {
                    $label .= ' (Coach)';
                    if (!empty($first) || !empty($last)) {
                        $label .= ' - ' . $first . ' ' . $last;
                    }
                }

                $results[] = [
                    'id'   => get_permalink(), // for redirect
                    'text' => $label,
                ];
            }

            // LIMIT results (important for performance)
            if (count($results) >= 10) {
                break;
            }
        }
    }

    wp_reset_postdata();

    wp_send_json($results);
}

add_action('wp_ajax_search_coaches', 'search_coaches_ajax');
add_action('wp_ajax_nopriv_search_coaches', 'search_coaches_ajax');
?>