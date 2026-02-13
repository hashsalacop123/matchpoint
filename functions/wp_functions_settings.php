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

// Save ACF JSON in the theme folder
add_filter('acf/settings/save_json', function($path) {
    // Path to your theme's acf-json folder
    $path = get_stylesheet_directory() . '/acf-json';
    return $path;
});


?>