<?php

/* Template Name: Invite Admin */

use classes\app\emails\Custom_Mail;

if ( isset( $_POST['inviteadmin'] ) ) {
    try {

        $custom_mail = new Custom_Mail();

        $ID = $_POST['fundraiser_id'];
        $sadmin_email = explode( ',', $_POST['sadmin_email'] );
        $msg = '';
        $res = array();
        foreach ( $sadmin_email as $email ) {
            $sent = false;

            // Mail to Sadmin
            $admin_name = get_post_meta( $ID, 'con_name', true );
            $title      = get_the_title( $ID );
            $to         = trim( $email );
            $from       = _TRANSACTIONAL_FROM_EMAIL;
            $subject    = "Invitation to Join " . $title;
            $cc         = null;
            $reply      = null;

            $template_args = array(
                'ADMIN_NAME'             => $admin_name,
                'FUNDRAISER_NAME'        => $title,
                'ADMIN_CODE'             => get_post_meta( $ID, 'join_code_sadmin', true ),
                'SIGNUP_URL'             => get_bloginfo( 'url' ) . '/?signup',
                'SIGNATURE_EMAIL'        => _SIGNATURE_EMAIL,
                'SIGNATURE_FAX_NUMBERS'  => _SIGNATURE_FAX_NUMBER,
                'SIGNATURE_PHONE_NUMBER' => _SIGNATURE_OFFICE_PHONE_NUMBER,
                'TEMPLATE_DIRECTORY'     => get_template_directory_uri()
            );

            $sent = $custom_mail->send_api( $to, $from, $cc, $subject, 'invite_sadmin', $template_args );

            if ( $sent ) {
                array_push( $res, "<p class='successMsg'>Your message was sent successfully to " . $to . "</p>" );
            } else {
                array_push( $res, "<p class='warningMsg'>There is a problem sending the message to " . $to . "</p>" );
            }
        }
        die( json_encode( $res ) );
    } catch ( \Exception $e ) {
        if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
            newrelic_notice_error( $e->getMessage(), $e );
        }
        die( json_encode( ["<p class='warningMsg'>Internal Server Error. Please try again later.</p>"] ) );

    }
}
?>
