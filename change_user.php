<?php
/*
Plugin Name: Change User
Version: 1.1
Author: Morten Proschowsky
Author URI: http://proschowsky.dk/morten/
Description: Allows administrators to log in as other blog users.
*/

add_action('plugins_loaded', array('ChangeUser', 'init'));

class ChangeUser {
    static function init() {
        if (current_user_can('edit_users')) {
            if (isset($_GET['action']) && $_GET['action'] == 'login_as') ChangeUser::change(intval($_GET['user']));
            add_filter('user_row_actions', array('ChangeUser', 'addToUserRow'), 10, 2 );
        }
    }

    public static function change($uid) {
        if ($uid > 0) {
            $nonce=$_REQUEST['_wpnonce'];
            if (! wp_verify_nonce($nonce, 'login_as_user') ) wp_die( __( 'Cheatin&#8217; uh?' ));

            wp_set_auth_cookie($uid, false, is_ssl());
            $url = get_option('siteurl') . '/?_login=' . dechex(mt_rand()) . '/';
            wp_redirect(apply_filters('login_redirect', $url ));
            exit(0);
        }
    }

    public static function addToUserRow($actions, $user) {
        $actions['login_as'] = '<a class="login_as" href="' . wp_nonce_url('users.php?action=login_as&amp;user=' . $user->ID, 'login_as_user') . '">' . __('Log In') . '</a>';
        return $actions;
    }
}
