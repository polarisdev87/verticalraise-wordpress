<?php

namespace classes\app\donation;

use classes\app\emails\Custom_Mail;

class Donations_Notification
{
    public static function notify_participant( $data ) {

        try {
            /**
             * Instantiate the CustomMail class.
             */
            $mail = new Custom_Mail();

            if ( ! empty( $data['fundraiser_title'] ) ) {
                $fundraiser_title = $data['fundraiser_title'];
            } else {
                $fundraiser_title = "Fundraiser";
            }

            if ( ! empty( $data['uid'] ) ){
                $uid = $data['uid'];
                $user_info = get_userdata( $uid );
                $participant_name = $user_info->display_name;
                $participant_email = $user_info->user_email;

                $to        = $participant_email;
                $from      = _ADMIN_TO_EMAIL;
                $from_name = "Vertical Raise Customer Support";
                $subject   = "You received a new donation";
                $cc        = null;
                $reply     = null;

                $template_args = array(
                    'PARTICIPANT_NAME'   => $participant_name,
                    'FUNDRAISER_TITLE'   => $fundraiser_title,
                    'CYEAR'              => date('Y'),
                );

                /**
                 * Send the email.
                 */
                $sent = $mail->send_api( $to, $from, $cc, $subject, 'donation_received', $template_args , $from_name );

            } else {
                if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                    newrelic_notice_error( "The uid parameter was empty in `charge`.`params` " .
                        "variable when sending donation received email in donation page" );
                }
            }

        } catch ( \Exception $e ) {
            if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                newrelic_notice_error( $e->getMessage(), $e );
            }
        }

    }
}
