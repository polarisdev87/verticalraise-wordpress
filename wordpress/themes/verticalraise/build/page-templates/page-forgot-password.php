<?php

/* Template Name: Forgot Password Template */

use classes\app\emails\Custom_Mail;

global $wpdb, $user_ID;

if ( empty( $user_ID ) ) {

    if ( isset( $_POST['forgot'] ) ) {
        $user_login = $_POST['emailid'];
        $user_data  = $wpdb->get_row( $wpdb->prepare( "SELECT ID, user_login, user_email, user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login ) );
        if ( !empty( $user_data ) ) {

            $custom_mail = new Custom_Mail();
            $sent        = false;

            // Mail to User 
            $useremail           = $user_data->user_email;
            $useremail_encripted = encripted( $useremail );
            $to                  = $useremail;
            $from                = _TRANSACTIONAL_FROM_EMAIL;
            $subject             = "Reset Password";
            $cc                  = null;
            $reply               = null;

            $template_args = array(
                'RESET_URL'              => get_bloginfo( 'url' ) . '/reset-password/?action=' . $useremail_encripted,
                'SIGNATURE_EMAIL'        => _SIGNATURE_EMAIL,
                'SIGNATURE_FAX_NUMBERS'  => _SIGNATURE_FAX_NUMBER,
                'SIGNATURE_PHONE_NUMBER' => _SIGNATURE_OFFICE_PHONE_NUMBER,
                'TEMPLATE_DIRECTORY'     => get_template_directory_uri()
            );

            try {
                $sent = $custom_mail->send_api( $to, $from, $cc, $subject, 'password_reset', $template_args );
            } catch ( \Exception $e ) {
                if ( extension_loaded( 'newrelic' ) ) {
                    newrelic_notice_error( $e->getMessage(), $e );
                }
            }

            if ( $sent ) {
                $result['status'] = true;
                $result['data']   = '<div class="successMsg">Please check your registered email and click on the reset password link.</div>';
            } else {
                $result['status'] = false;
                $result['data']   = '<div class="errorMsg">Internal Server Error. Please try again later</div>';
            }
            die( json_encode( $result ) );
        } else {
            $result['status'] = false;
            $result['data']   = '<div class="errorMsg">Email/Username not found.</div>';

            die( json_encode( $result ) );
        }
    }
} else {
    header( 'Location:' . get_bloginfo( 'url' ) );
}
?>
