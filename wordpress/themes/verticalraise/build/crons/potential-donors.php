<?php

use \classes\models\tables\Potential_Donors_Email;
use \classes\models\tables\Donations;
use \classes\app\initial_send\Content;
use \classes\app\emails\Custom_Mail;
use \classes\app\initial_send\Opening_Line;
use \classes\app\debug\Debug;
use \classes\app\initial_send\Build_Email as Build_Email;
use \classes\app\fundraiser\Fundraiser_Media;

use \classes\app\utm\UTM;

class Process_Expiry
{

    public function __construct() {
        $this->potential_donors_email = new Potential_Donors_Email();
        $this->donators               = new Donations();
        $this->fundraiser_media       = new Fundraiser_Media();
    }

    private function send_mail( $to, $from, $cc, $subject, $type, $template_args, $from_name, $plain ) {
        $custom_mail = new Custom_Mail();
        return $custom_mail->send_api( $to, $from, $cc, $subject, $type, $template_args, $from_name, $plain );
    }

    /**
     * Process emails to potential donators letting them know how much time is left for the fundraiser.
     * @param int $period The number of days left
     */
    function process_expiry( $period ) {

        ini_set( 'memory_limit', '-1' );
        ini_set( 'max_execution_time', 0);

        try {

            global $wpdb;

            /**
             * Validation and error checking.
             */
            if ( empty( $period ) )
                return "Missing number of days";
            if ( !is_int( $period ) )
                return "Param is not an interger";
            $allowed = [ '1', '2', '3', '7', '14' ];
            if ( !in_array( $period, $allowed ) )
                return "Param is not in allowed array";

            /**
             * Create file to log job output.
             */
            $file_name      = current_time( "m-d-Y_" ) . $period . '_days.json';
            $upload_dir     = wp_upload_dir();
            $upload_path    = $upload_dir['basedir'];
            $full_file_path = $upload_path . '/cron_log/' . $file_name;

            if ( !file_exists( $full_file_path ) ) {
                $cron_log = fopen( $full_file_path, "w" );
                $cron_log = fclose( $cron_log );
            }

            $output = array();

            /**
             * Create database entry to log cron execution.
             */
            $table_name = "cron_log";
            $today      = current_time( "Y-m-d 00:00:00" );

//            echo "today: " . $today . "<br>";
            $insert_time = current_time( 'mysql' );

            // Check for existing record
            if ( $wpdb->get_row( "SELECT * FROM `{$table_name}` WHERE `type` = '{$period}_days' AND `started` >= '{$today}' LIMIT 1", ARRAY_N ) == null ) {
                // If none, insert the record
                $wpdb->insert(
                        $table_name, array(
                    'type'    => $period . '_days',
                    'started' => $insert_time,
                        )
                );
                $cron_record_id = $wpdb->insert_id;
            } else {
                update_option( 'cron_' . $period, 'idle' );
                return 'Cron already ran today';
            }

            if ( isset( $period ) ) {

                $args             = array(
                    'post_type'      => 'fundraiser',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                );
                $fundraiser_query = new WP_Query( $args );

                $f_count = 0;

                if ( $fundraiser_query->have_posts() ) :

                    echo "has posts<br>";

                    $from = _TRANSACTIONAL_FROM_EMAIL;

                    while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

                        $current_time = current_time( 'timestamp', 0 );

                        echo " fid: " . get_the_ID();

                        $start_date = get_post_meta( get_the_ID(), 'start_date', true );
                        echo " start date: " . $start_date;

                        $start_date = strtotime( $start_date, $current_time );

                        $end_date = get_post_meta( get_the_ID(), 'end_date', true );
                        echo " end date: " . $end_date;
                        $end_date = strtotime( $end_date, $current_time );

                        $current_date_start = '';
                        $current_date_end   = '';

                        if ( $period == 1 )
                            $current_date_start = strtotime( current_time( "Ymd" ), $current_time );
                        if ( $period == 3 )
                            $current_date_start = strtotime( date( "Ymd", strtotime( "-3 days", $current_time ) ), $current_time );
                        if ( $period == 14 )
                            $current_date_end   = strtotime( date( "Ymd", strtotime( "+2 week", $current_time ) ), $current_time );
                        if ( $period == 7 )
                            $current_date_end   = strtotime( date( "Ymd", strtotime( "+1 week", $current_time ) ), $current_time );
                        if ( $period == 2 )
                            $current_date_end   = strtotime( date( "Ymd", strtotime( "+2 days", $current_time ) ), $current_time );

                        if ( ($end_date == $current_date_end) || ($start_date == $current_date_start) ) {

                            echo "has start or end dates<br>";
                            echo "current_date_end {$current_date_end}<br>";

                            // Subjects
                            if ( $period == 1 )
                                $subject = "Only ONE day left for my " . get_the_title();
                            if ( $period == 3 )
                                $subject = "Please support the " . get_the_title();
                            if ( $period == 14 )
                                $subject = "Two weeks left for my " . get_the_title();
                            if ( $period == 7 )
                                $subject = "One week left for my " . get_the_title();
                            if ( $period == 2 )
                                $subject = "Only one day left for my " . get_the_title();

                            // Get the Potential Donors by FID
                            $potential_donors = $this->potential_donors_email->get_row_by_fid( get_the_ID() );

                            echo "potential donors: ";
                            print_r( $potential_donors );
                            echo "<br>";
                            echo "fid " . get_the_ID();

                            $fundraising_goal = get_post_meta( get_the_ID(), 'fundraising_goal', true );
                            $fundraiser_id    = get_the_ID();
                            echo "FID=" . $fundraiser_id;
                            $support_array    = false;

                            // Get people who already donated

                            $donations      = new Donations();
                            $donator_emails = $donations->get_donator_emails_by_fundraiser_id( $fundraiser_id );

                            echo "<br>";
                            echo "donator emails: ";
                            print_r( $donator_emails );


                            if ( !empty( $potential_donors ) ) {

                                echo "has potential donors";

                                $f_count++;

                                foreach ( $potential_donors as $pd ) {

                                    if ( in_array( $pd['p_donator'], $donator_emails ) ) {
                                        continue;
                                    }

                                    if ( empty( $support_array ) || !in_array( strtolower( $pd['p_donator'] ), $support_array ) ) {

                                        // Get the user id for pd record

                                        $fundraiser = Content::set_content_fundraiser( $fundraiser_id );
                                        $user       = Content::set_content_user( $fundraiser_id, $pd['u_id'] );

                                        $to = $pd['p_donator'];
                                        $cc = null;

                                        /*
                                         * define the classes
                                         */
                                        $opening_line = new Opening_Line();
                                        $image_url    = new Build_Email();

                                        $matches = array();
                                        preg_match_all( '/(alt|title|src)=("[^"]*")/i', $user->avatar_url, $matches );

                                        $default_logo = "user-avatar-96x96.png";
                                        $img          = trim( $matches[2][0], '"' );
                                        $img1         = basename( $img );

                                        if ( $img1 == $default_logo ) {
                                            //if admin and participant not includ picture, get Team logo
                                            $img = $this->fundraiser_media->get_fundraiser_logo( $fundraiser->id );
                                        } else {
                                            $img = $matches[2][0];
                                        }

                                        $sadmin    = json_decode( get_user_meta( $pd['u_id'], 'campaign_sadmin', true ) );
                                        $author_id = get_post_field( 'post_author', $fundraiser_id );
                                        if ( $author_id == $pd['u_id'] || in_array_my( $fundraiser_id, $sadmin ) ) {
                                            $img = $this->fundraiser_media->get_fundraiser_logo( $fundraiser->id );
                                        }

                                        /*
                                         * image url i,e
                                         * $macthes[0][0] = src="http://local.wordpress.test/wp-content/uploads/profile_img_thumb/thumb_1edd2f5a8e5b1383befea408a0fe8f09-96x96.jpg"
                                         */
                                        $url = $user->click_url . "/" . $to;
                                        $utm = new UTM;
                                        if ( $period == 2 ){
                                            $utm_link = $utm->createUTMLink($url, 'Cron_Email_2_Day');
                                        }
                                        if ( $period == 7 ){
                                            $utm_link = $utm->createUTMLink($url, 'Cron_Email_7_Day');
                                        }
                                        if ( $period == 14 ){
                                            $utm_link = $utm->createUTMLink($url, 'Cron_Email_14_Day');
                                        }
                                        
                                        
                                        $template_args = [
                                            'AVATAR'         => $img,
                                            'FROM'           => $user->from_name,
                                            'URL'            => $utm_link,
                                            'FUNDRAISER'     => $fundraiser->title,
                                            'FROM_NAME'      => $user->from_name,
                                            'YOUR_NAME'      => $user->from_name,
                                            'BASE_URI'       => get_template_directory_uri(),
                                            'BACK_IMG_TOP'   => get_template_directory_uri() . '/assets/images/background-copy1.png',
                                            'BACK_IMG_LOGO'  => get_template_directory_uri() . '/assets/images/logo-background.png',
                                            'FUNDRAISER_MSG' => $fundraiser->message,
                                            'CYEAR' => date('Y'),
                                        ];
                                        //

                                        $output['fundraisers'][$fundraiser_id][] = array( $pd['p_donator'] );

                                        if ( $period == 1 ) {
                                            if ( $author_id == $pd['u_id'] || in_array_my( $fundraiser_id, $sadmin ) ) {
                                                $template_name = 'potential_donors_1_day_admin';
                                            } else {
                                                $template_name = 'potential_donors_1_day';
                                            }
                                        }
                                        if ( $period == 3 ) {
                                            if ( $author_id == $pd['u_id'] || in_array_my( $fundraiser_id, $sadmin ) ) {
                                                $template_name = 'potential_donors_3_day_admin';
                                            } else {
                                                $template_name = 'potential_donors_3_day';
                                            }
                                        }
                                        if ( $period == 14 ) {
                                            if ( $author_id == $pd['u_id'] || in_array_my( $fundraiser_id, $sadmin ) ) {
                                                $template_name = 'potential_donors_14_day_admin';
                                            } else {
                                                $template_name = 'potential_donors_14_day';
                                            }
                                        }
                                        if ( $period == 7 ) {
                                            if ( $author_id == $pd['u_id'] || in_array_my( $fundraiser_id, $sadmin ) ) {
                                                $template_name = 'potential_donors_7_day_admin';
                                            } else {
                                                $template_name = 'potential_donors_7_day';
                                            }
                                        }
                                        if ( $period == 2 ) {
                                            if ( $author_id == $pd['u_id'] || in_array_my( $fundraiser_id, $sadmin ) ) {
                                                $template_name = 'potential_donors_2_day_admin';
                                            } else {
                                                $template_name = 'potential_donors_2_day';
                                            }
                                        }

                                        $template_args['OPENING_LINE'] = $opening_line->get_donors_opening_line( $fundraiser, $user, $template_name );
                                        $sent                          = $this->send_mail( $to, $user->from, $cc, $subject, 'potential_donors', $template_args, $user->from_name, 'potential_donors' );
                                    }
                                }
                            }
                            if ( $period == 2 ) {
                                
                                if ( _SERVER_TYPE != 'dev' ) {
                                    /*
                                     * Get Short URL
                                     */
                                    load_class( 'invite_sms/build_sms.class.php' );
                                    $buildSMS = new Build_SMS();

                                    /**
                                     * Send Text Messages via Twilio
                                     */
                                    if ( !class_exists( 'Services_Twilio' ) ) {
                                        require TEMPLATEPATH . "/twilio-php-master/Services/Twilio.php";
                                    }

                                    // Account details
                                    $AccountSid = _TWILIO_ACCOUNT_ID;
                                    $AuthToken  = _TWILIO_AUTH_TOKEN;
                                    $client     = new Services_Twilio( $AccountSid, $AuthToken );

                                    $potential_donors_sms = json_decode( get_post_meta( $fundraiser_id, 'potential_donors_sms_array', true ) );
                                    
                                    if ( !empty( $potential_donors_sms ) ) {

                                        foreach ( $potential_donors_sms as $pds ) {

                                            $phone                           = trim( $pds[1] );
                                            $user_info                       = get_userdata( $pds[0] );
                                            $output['sms'][$fundraiser_id][] = array( $pds[0], $phone );
                                            $utm = new UTM;
                                            $utm_code = $utm->getUTMCode('Cron_SMS');
                                            $url                             = $buildSMS->set_click_url( $fundraiser_id, $pds[0], 0, $utm_code );
                                            
                                            $sadmin             = json_decode( get_user_meta( $pds[0], 'campaign_sadmin', true ) );
                                            $author_id          = get_post_field( 'post_author', $fundraiser_id );
                                            $campaign_permalink = get_the_permalink( $fundraiser_id );

                                            if ( $author_id == $pds[0] || in_array_my( $fundraiser_id, $sadmin ) ) {
                                                $msg1 = '-From ' . $user_info->display_name . ' – There is only ONE DAY LEFT for the ' . get_the_title( $fundraiser_id ) . '. If you have already donated than thank you. If not, please help by donating or sharing. ' . $url;
                                                //  $campaign_permalink . 'sms/' . $pds[0]
                                            } else {
                                                $msg1 = '-From ' . $user_info->display_name . ' – There is only ONE DAY LEFT for the ' . get_the_title( $fundraiser_id ) . '. If you have already donated than thank you. If not, please help me reach my individual goal of raising $' . _PARTICIPATION_GOAL . ' for our cause. ' . $url;
                                            }
                                            try {
                                                $message = $client->account->messages->sendMessage(
                                                        _TWILIO_FROM_NUMBER, // From a valid Twilio number
                                                        $phone, // Text this number
                                                        $msg1
                                                );
                                            } catch ( Exception $e ) {
                                                echo $phone . 'is not a valid phone Number';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    endwhile;
                endif;
            }

            /**
             * Set the output.
             */
            $output['fundraiser_count'] = $f_count;
        } catch ( Exception $e ) {
            if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                newrelic_notice_error( $e->getMessage(), $e );
            }
        }

        /**
         * Log the cron output.
         */
        file_put_contents( $full_file_path, json_encode( $output ) );

        /**
         * Record the cron execution.
         */
        $wpdb->update( $table_name, array( 'started' => $insert_time, 'ended' => current_time( 'mysql' ) ), array( 'id' => $cron_record_id ), array( '%s' ) );

        return 'Cron ran successfully';
    }

}
