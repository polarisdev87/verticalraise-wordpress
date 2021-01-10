<?php

/**
 * Edit Fundraiser
 */

namespace classes\app\fundraiser;

use classes\models\tables\Reports_Fundraisers_Reference;
use classes\app\stripe\Stripe_Form;
use classes\models\tables\Subgroups;

class Edit_Fundraiser
{

    /**
     * @param int $user_ID User ID.
     * @param int $f_id Fundraiser ID.
     */
    public function __construct( $user_id, $f_id ) {

        /**
         * Setup log edits class.
         */
        load_class( 'log_edits.class.php' );
        $this->log = new \Log_Edits();

        $this->f_id    = $f_id;
        $this->user_ID = $user_id;

        $this->format_in  = 'm/d/Y';
        $this->format_out = 'Ymd';
        
        $this->reference = new Reports_Fundraisers_Reference();
    }

    public function pending_update() {
        $this->update_meta( $this->f_id );
        $this->update_media( $this->f_id );
        $this->redirect( $this->f_id );
    }

    /**
     * Process the user's changes to the fundraiser.
     */
    public function edit() {
        if ( $this->form_submit() == true ) {

            // Log the content
            $this->log_content( $this->f_id, $this->user_ID );

            // Update the content
            $this->update_content( $this->f_id, $this->user_ID );

            // Redirect the user
            return $this->redirect( $this->f_id );

        }
    }
    
    public function payment_method( $f_id ) {
        // check if back acount is updated        
        if ( $_POST['payment_option'] == "1" ) {
            //get stripe account id for this fundraiser
            $stripe = new Stripe_Form();
            if ( isset( $_POST['edit_flag'] ) && $_POST['edit_flag'] == 1 ) {
                $stripe_account = $stripe->get_account_id( $f_id );

                $stripe_params = array(
                    'id'             => $stripe_account->id,
                    'account_id'     => $stripe_account->stripe_account_id,
                    'account_name'   => $_POST['bank_account_name'],
                    'routing_number' => $_POST['routing'],
                    'account_number' => $_POST['direct_account'],
                    'b_token'        => $_POST['b_token']
                );

                if ( $stripe->update_account_id( $f_id, $stripe_params, $_POST ) ) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if ( isset( $_POST['routing'] ) && !empty( $_POST['routing'] ) ) {

                    $create_connect_account = $stripe->create_connect_account( $_POST );
                    if ( $create_connect_account ) {
                        if ( $create_connect_account ) {
                            $stripe->insert_account_id( $f_id, $create_connect_account );
                        }
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return true;
                }
            }
        } else {
            return true;
        }
    }

    /**
     * Perform the logging.
     * @param int $f_id Fundraiser ID.
     * @param int $user_ID User ID.
     */
    private function log_content( $f_id, $user_ID ) {
        // Log the edits
        $this->log_edits( $f_id, $user_ID );

        // Logs the title change
        $this->log_title_change( $f_id, $user_ID );

        // Log the logo change
        $this->log_logo_change( $f_id, $user_ID );

        // Log the media changes
        //$this->log_media_changes($f_id, $user_ID);
        ### Handle this where the updates take place ###
    }

    /**
     * Log the edits to the fundraiser.
     * @param int $f_id Fundraiser ID.
     * @param int $user_ID User ID.
     */
    private function log_edits( $f_id, $user_ID ) {

        // A list of possible edits
        $edits = [ 'con_name', 'phone', 'email', 'tax_id', 'our_fee', 'check_pay', 'mailing_address', 'org_type', 'coach_name', 'coach_email', 'eft', 'stripe_connect', 'ac_num', 'routing_num', 'hear_about_us', 'street', 'city', 'state', 'zipcode', 'team_name', 'start_date', 'end_date', 'org_name', 'est_team_size', 'fundraising_goal', 'campaign_msg', 'showPc_table', 'participants_goal', 'fund_amount', 'currency_selection' ];

        // Look for form data, check against post_meta
        foreach ( $edits as $edit ) {
            try {

                $old = $this->get_old( $f_id, $edit );

                if ( isset( $_POST[$edit] ) ) {
                    $new = sanitize_text_field( trim( $_POST[$edit] ) );

                    // Filters
                    if ( $edit == 'fundraising_goal' ) {
                        $new = str_replace( array( "$", "," ), '', $new );
                    }
                    if ( $edit == 'start_date' ) {
                        $new = $this->format_date( "start", sanitize_text_field( trim( $_POST['start_date'] ) ) );
                    }
                    if ( $edit == 'end_date' ) {
                        $new = $this->format_date( "end", sanitize_text_field( trim( $_POST['end_date'] ) ) );
                    }

                    if ( $edit == 'check_pay' ) {
                        if ( !isset( $_POST['check_pay'] ) || $_POST['check_pay'] == '' ) {
                            if ( isset( $_POST['bank_account_name'] ) ) {
                                $new = sanitize_text_field( trim( $_POST['bank_account_name'] ) );
                            }
                        }
                    }
                    if ( $edit == 'mailing_address' ) {
                        if ( !isset( $_POST['mailing_address'] ) || $_POST['mailing_address'] == '' ) {
                            if ( isset( $_POST['con_name'] ) ) {
                                $new = sanitize_text_field( trim( $_POST['con_name'] ) );
                            }
                        }
                    }

                    // Log
                    if ( $new != $old ) {
                        $this->log->log( $f_id, $user_ID, $edit, $new, $old );
                    }
                }
            } catch ( Exception $e ) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Get the old value of the field being edited.
     * @param int $f_id Fundraiser ID.
     * @param string $edit The type of edit.
     * @return string The old value.
     */
    private function get_old( $f_id, $edit ) {
        $get_old = get_post_meta( $f_id, $edit );
        if ( !empty( $get_old ) ) {
            if ( is_array( $get_old ) == true ) {
                $old = $get_old[0];
            } else {
                $old = $get_old;
            }
        } else {
            $old = '';
        }

        return $old;
    }

    /**
     * Format the date.
     * @param string $type "Start" or "End".
     * @param date $date The date to format.
     * @return date Formatted date.
     */
    private function format_date( $type, $date ) {
        switch ( $type ) {
            case "start":
                $_date = \DateTime ::createFromFormat( $this->format_in, $date );
                $_date = $_date->format( $this->format_out );
                break;
            case "end":
                $_date = \DateTime ::createFromFormat( $this->format_in, $date );
                $_date = $_date->format( $this->format_out );
                break;
	        case "secondary_end_date":
		        $_date = \DateTime ::createFromFormat( $this->format_in, $date );
		        $_date = $_date->format( $this->format_out );
		        break;
        }
        return $_date;
    }

    /**
     * Log the title change.
     * @param int $f_id Fundraiser ID.
     * @param int $user_ID User ID.
     */
    private function log_title_change( $f_id, $user_ID ) {

        if ( isset( $_POST['fundraiser_name'] ) && !empty( $_POST['fundraiser_name'] ) ) {

            $new = sanitize_text_field( $_POST['fundraiser_name'] );
            $old = get_the_title( $f_id );

            // Log the results
            if ( $old != $new ) {
                $this->log->log( $f_id, $user_ID, 'title', $new, $old );
            }
        }
    }

    /**
     * Log the logo change.
     * @param int $f_id Fundraiser ID.
     * @param int $user_ID User ID.
     */
    private function log_logo_change( $f_id, $user_ID ) {

        if ( isset( $_POST['logoImage'] ) && !empty( $_POST['logoImage'] ) ) {
            $new = $_POST['logoImageName'];
            $old = get_post( get_post_thumbnail_id( $f_id ) )->post_title;
            if ( $new != $old ) {
                $this->log->log( $f_id, $user_ID, 'logo', $new, $old );
            }
        }
    }

    /**
     * Perform the updates based on the $_POST data.
     * @param int $f_id Fundraiser ID.
     * @param int $user_ID User ID.
     */
    private function update_content( $f_id, $user_ID ) {
        $this->update_title( $f_id );
        $this->update_meta( $f_id );
        $this->update_media( $f_id );
	    $this->update_subgroups( $f_id );
//        $this->update_videos($f_id, $user_ID);
    }

    /**
     * Update the title for the fundraiser ID.
     * @param int $f_id Fundraiser ID.
     */
    private function update_title( $f_id ) {
        if ( isset( $_POST['fundraiser_name'] ) ) {
            $fundraiser_name = trim( $_POST['fundraiser_name'] );
            $post = array(
                'ID'         => $f_id,
                'post_title' => sanitize_text_field( $fundraiser_name ),
                'post_type'  => 'fundraiser',
            );

            wp_update_post( $post );
            
            $this->reference->update($f_id, 'name', $fundraiser_name);
        }
    }

    /**
     * Update post meta for the fundraiser ID.
     * @param int $f_id Fundraiser ID.
     */
    private function update_meta( $f_id ) {
        if ( !empty( $_POST['con_name'] ) ) {
            update_post_meta( $f_id, 'con_name', sanitize_text_field( trim( $_POST['con_name'] ) ) );
        }
        if ( !empty( $_POST['phone'] ) ) {
            update_post_meta( $f_id, 'phone', sanitize_text_field( trim( $_POST['phone'] ) ) );
        }
        if ( !empty( $_POST['email'] ) ) {
            update_post_meta( $f_id, 'email', sanitize_text_field( trim( $_POST['email'] ) ) );
        }


        /*if ( isset( $_POST['payment_option'] ) && !empty( $_POST['payment_option'] ) ) {
            update_post_meta( $f_id, 'stripe_connect', sanitize_text_field( trim( $_POST['payment_option'] ) ) );
        } else {
            update_post_meta( $f_id, 'stripe_connect', '0' );
        }*/

        /* if (!empty($_POST['bank_account_name'])) {
          update_post_meta($f_id, 'bank_account_name', sanitize_text_field(trim($_POST['bank_account_name'])));
          }
          if (!empty($_POST['routing'])) {
          update_post_meta($f_id, 'routing', sanitize_text_field(trim($_POST['routing'])));
          }
          if (!empty($_POST['direct_account'])) {
          update_post_meta($f_id, 'direct_account', sanitize_text_field(trim($_POST['direct_account'])));
          }
          if (!empty($_POST['confirm_account'])) {
          update_post_meta($f_id, 'confirm_account', sanitize_text_field(trim($_POST['confirm_account'])));
          } */

        if ( isset( $_POST['check_pay'] ) && !empty( $_POST['check_pay'] ) ) {
            update_post_meta( $f_id, 'check_pay', sanitize_text_field( trim( $_POST['check_pay'] ) ) );
        } /*else {
            if ( isset( $_POST['bank_account_name'] ) && !empty( $_POST['bank_account_name'] ) ) {
                update_post_meta( $f_id, 'check_pay', sanitize_text_field( trim( $_POST['bank_account_name'] ) ) );
            }
        }*/

        if ( isset( $_POST['mailing_address'] ) && !empty( $_POST['mailing_address'] ) ) {
            update_post_meta( $f_id, 'mailing_address', sanitize_text_field( trim( $_POST['mailing_address'] ) ) );
        }

        if ( isset( $_POST['con_name'] ) && !empty( $_POST['con_name'] ) ) {
            update_post_meta( $f_id, 'con_name', sanitize_text_field( trim( $_POST['con_name'] ) ) );
        }


        if ( !empty( $_POST['attention_to'] ) ) {
            update_post_meta( $f_id, 'attention_to', sanitize_text_field( trim( $_POST['attention_to'] ) ) );
        }
        if ( !empty( $_POST['street'] ) ) {
            update_post_meta( $f_id, 'street', sanitize_text_field( trim( $_POST['street'] ) ) );
        }
        if ( !empty( $_POST['city'] ) ) {
            update_post_meta( $f_id, 'city', sanitize_text_field( trim( $_POST['city'] ) ) );
        }
        if ( !empty( $_POST['state'] ) ) {
            update_post_meta( $f_id, 'state', sanitize_text_field( trim( $_POST['state'] ) ) );
        }
        if ( !empty( $_POST['zipcode'] ) ) {
            update_post_meta( $f_id, 'zipcode', sanitize_text_field( trim( $_POST['zipcode'] ) ) );
        }


        if ( !empty( $_POST['org_type'] ) ) {
            update_post_meta( $f_id, 'org_type', sanitize_text_field( trim( $_POST['org_type'] ) ) );
        }
        if ( !empty( $_POST['tax_id'] ) ) {
            update_post_meta( $f_id, 'tax_id', sanitize_text_field( trim( $_POST['tax_id'] ) ) );
        }
        if ( !empty( $_POST['our_fee'] ) ) {
            update_post_meta( $f_id, 'our_fee', sanitize_text_field( trim( $_POST['our_fee'] ) ) );
        }
        if ( !empty( $_POST['coach_name'] ) ) {
            update_post_meta( $f_id, 'coach_name', sanitize_text_field( trim( $_POST['coach_name'] ) ) );
        }
        if ( !empty( $_POST['coach_email'] ) ) {
            update_post_meta( $f_id, 'coach_email', sanitize_text_field( trim( $_POST['coach_email'] ) ) );
        }
        if ( !empty( $_POST['coach_code'] ) ) {
            update_post_meta( $f_id, 'coach_code', sanitize_text_field( trim( $_POST['coach_code'] ) ) );
        }
        if ( !empty( $_POST['eft'] ) ) {
            update_post_meta( $f_id, 'eft', sanitize_text_field( trim( $_POST['eft'] ) ) );
        }
        if ( isset( $_POST['eft'] ) && $_POST['eft'] == 1 ) {
            update_post_meta( $f_id, 'ac_num', sanitize_text_field( trim( $_POST['ac_num'] ) ) );
            update_post_meta( $f_id, 'routing_num', sanitize_text_field( trim( $_POST['routing_num'] ) ) );
        }
        if ( !empty( $_POST['hear_about_us'] ) ) {
            update_post_meta( $f_id, 'hear_about_us', sanitize_text_field( trim( $_POST['hear_about_us'] ) ) );
        }

        if ( !empty( $_POST['team_name'] ) ) {
            update_post_meta( $f_id, 'team_name', sanitize_text_field( trim( $_POST['team_name'] ) ) );
        }
        if ( !empty( $_POST['start_date'] ) ) {
            $start_date = $this->format_date( "start", sanitize_text_field( trim( $_POST['start_date'] ) ) );
            update_post_meta( $f_id, 'start_date', $start_date );
            $this->reference->update($f_id, 'start_date', $start_date);
        }
        if ( !empty( $_POST['end_date'] ) ) {
            $end_date = $this->format_date( "end", sanitize_text_field( trim( $_POST['end_date'] ) ) );
            update_post_meta( $f_id, 'end_date', $end_date );
            $this->reference->update($f_id, 'end_date', $end_date);
        }
        if ( !empty( $_POST['org_name'] ) ) {
            update_post_meta( $f_id, 'org_name', sanitize_text_field( trim( $_POST['org_name'] ) ) );
        }
        if ( !empty( $_POST['est_team_size'] ) ) {
            update_post_meta( $f_id, 'est_team_size', sanitize_text_field( trim( $_POST['est_team_size'] ) ) );
        }
        if ( !empty( $_POST['fundraising_goal'] ) ) {
            update_post_meta( $f_id, 'fundraising_goal', str_replace( array( "$", "," ), '', sanitize_text_field( trim( $_POST['fundraising_goal'] ) ) ) );
        }
        if ( !empty( $_POST['campaign_msg'] ) ) {
            update_post_meta( $f_id, 'campaign_msg', sanitize_text_field( trim( $_POST['campaign_msg'] ) ) );
        }
        if ( isset( $_POST['showPc_table'] ) ) {
            update_post_meta( $f_id, 'showPc_table', sanitize_text_field( trim( $_POST['showPc_table'] ) ) );
        }
        if ( !empty( $_POST['participants_goal'] ) ) {
            update_post_meta( $f_id, 'participants_goal', sanitize_text_field( trim( $_POST['participants_goal'] ) ) );
        }
        if ( !empty( $_POST['currency_selection'] ) ) {
            update_post_meta( $f_id, 'currency_selection', sanitize_text_field( trim( $_POST['currency_selection'] ) ) );
        }
        if ( isset( $_POST['youtube_url'] ) ) {
            update_field( 'youtube_url', sanitize_text_field( trim( $_POST['youtube_url'] ) ), $f_id );
        }

	    if ( isset( $_POST['edit_sport_scope_integration_value'] ) ) {
	    	$current_sport_scope_integrated_val = get_post_meta( $f_id, 'sport_scope_integrated', true );
	    	$new_sport_scope_integrated_val = sanitize_text_field( $_POST['edit_sport_scope_integration_value'] );
		    update_post_meta( $f_id, 'sport_scope_integrated', $new_sport_scope_integrated_val );
		    if( $current_sport_scope_integrated_val == "0" && $new_sport_scope_integrated_val == "1" ){
			    do_action( 'dbt_fundraiser_updated_to_ssi' , $f_id );
		    }

	    }

	    if ( ! empty( $_POST['edit_secondary_end_date'] ) ) {
		    $secondary_end_date = $this->format_date( 'secondary_end_date', sanitize_text_field( trim( $_POST['edit_secondary_end_date'] ) ) );
		    update_post_meta( $f_id, 'secondary_end_date', $secondary_end_date );
	    }

        ### TODO: Take this out? ###
        update_post_meta( $f_id, 'fund_amount', 0 );
    }

    /**
     * Update the media for the fundraiser ID.
     * @param int $f_id Fundraiser ID.
     */
    private function update_media( $f_id ) {
        require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
        require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
        require_once( ABSPATH . "wp-admin" . '/includes/media.php' );

        if ( isset( $_POST['logoImage'] ) && !empty( $_POST['logoImage'] ) ) {
            $upload_dir = wp_upload_dir();

            //@new
            $upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
            $img         = $_POST['logoImage'];

            list( $type, $img ) = explode( ";", $img );
            list(, $img ) = explode( ",", $img );
            list(, $type ) = explode( "/", $type );

            $decode = base64_decode( $img );

            $filename          = $_POST['logoImageName'];
            ///$hashed_filename =  md5($filename.microtime ()). '_' . $filename;
            //@new
            $image_upload      = file_put_contents( $upload_path . $filename, $decode );
            $image             = array();
            $image['error']    = 0;
            $image['tmp_name'] = $upload_path . $filename;
            $image['name']     = $filename;
            $image['type']     = 'image/' . $type;
            $image['size']     = filesize( $upload_path . $filename );

            if ( $image['size'] ) {
                if ( preg_match( '/(jpg|jpeg|png|gif)$/', $image['type'] ) ) {

                    $override   = array( 'test_form' => false );
                    $file       = wp_handle_sideload( $image, $override );
                    $attachment = array(
                        'post_title'     => $image['name'],
                        'post_content'   => '',
                        'post_type'      => 'attachment',
                        'post_mime_type' => $image['type'],
                        'guid'           => $file['url']
                    );
                    $attach_id  = wp_insert_attachment( $attachment, $file['file'] );
                    wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $file['file'] ) );
                    set_post_thumbnail( $f_id, $attach_id );
                } else {
                    wp_die( 'No image was uploaded.' );
                }
            }
        }
    }

    /**
     * Update videos for the fundraiser ID.
     * @param int $f_id Fundraiser ID.
     * @param int $user_ID User ID.
     */
    private function update_videos( $f_id, $user_ID ) {
        $img_vid_array = array();

        if ( isset( $_FILES['img_vid'] ) ) {
            $file_ary = $this->reArrayFiles( $_FILES['img_vid'] );

            foreach ( $file_ary as $image ) {
                if ( $image['size'] ) {
                    if ( preg_match( '/(jpg|jpeg|png|gif|mp4)$/', $image['type'] ) ) {

                        $override = array( 'test_form' => false );

                        // Upload the file
                        $file = wp_handle_upload( $image, $override );

                        $attachment = array(
                            'post_title'     => $image['name'],
                            'post_content'   => '',
                            'post_type'      => 'attachment',
                            'post_mime_type' => $image['type'],
                            'guid'           => $file['url']
                        );

                        // Attach the file to the post
                        $attach_id = wp_insert_attachment( $attachment, $file['file'] );

                        // Attach the file to metadeta
                        wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $file['file'] ) );

                        array_push( $img_vid_array, $attach_id );

                        // If mp4
                        if ( preg_match( '/(mp4)$/', $image['type'] ) ) {
                            $file_name  = $attach_id;
                            $date       = current_time( 'Ymd' );
                            $time       = current_time( 'timestamp' );
                            $filename   = $date . '_' . $time . '_' . ( $file_name ) . '.jpg';
                            $upload_dir = wp_upload_dir();
                            $uploaddir  = $upload_dir['basedir'] . '/vid_thumb/';
                            $file       = $uploaddir . $date . '_' . $time . '_' . ( $file_name ) . '.jpg';
                            $video_path = '/var/www/html/verticalraise/' . wp_make_link_relative( wp_get_attachment_url( $attach_id ) );
                            shell_exec( "ffmpeg -i $video_path -deinterlace -an -ss 1 -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $file 2>&1" );

                            $thumb_array             = array();
                            $thumb_array[$file_name] = $filename;
                            update_post_meta( $f_id, 'div_thumb', json_encode( $thumb_array ) );
                        }
                    } else {
                        wp_die( 'No image was uploaded.' );
                    }
                }
            }
            if ( !empty( $img_vid_array ) ) {
                if ( empty( get_field( 'image_gallery', $f_id ) ) ) {
                    $old_field = 'none';
                } else {
                    $_old_field = get_field( 'image_gallery', $f_id );
                    if ( is_array( $_old_field ) ) {
                        if ( !empty( $_old_field['ID'] ) ) {
                            $old_field = $_old_field['ID'];
                            json_encode( $old_field );
                        } else {
                            foreach ( $_old_field as $o ) {
                                $old_field[] = $o["ID"];
                            }
                            $old_field = json_encode( $old_field );
                        }
                    }
                }
                update_field( 'image_gallery', $img_vid_array, $f_id );

                $new_field = $img_vid_array;

                if ( is_array( $new_field ) ) {
                    $new_field = json_encode( $new_field );
                }
                $this->log->log( $f_id, $user_ID, 'image_gallery', $new_field, $old_field );
            }
        }
    }

    /**
     * Re arrange the array?
     */
    private function reArrayFiles( &$file_post ) {
        $file_ary   = array();
        $file_count = count( $file_post['name'] );

        $file_keys = array_keys( $file_post );

        for ( $i = 0; $i < $file_count; $i++ ) {
            foreach ( $file_keys as $key ) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
        return $file_ary;
    }

    /**
     * Redirect the user.
     * @param int $f_id Fundraiser ID
     */
    private function redirect( $f_id ) {
        if ( isset( $_POST['update_fundraiser'] ) ) {
            if ( current_user_can( 'administrator' ) ) {
                $redirectUrl = get_permalink( $f_id );
            } else {
                $redirectUrl = get_site_url() . '/single-fundraiser/?fundraiser_id=' . $f_id;
            }
            $result['status'] = true;
            $result['data']   = $redirectUrl;
            return $result;
        } else {
            header( 'Location: ' . get_site_url() . '/single-fundraiser/?fundraiser_id=' . $f_id );
            exit();
        }
    }

    /**
     * Look for the form submit.
     * @return bool
     */
    private function form_submit() {
        if ( isset( $_POST['submit_for_approval'] ) || isset( $_POST['update_media'] ) || isset( $_POST['update_fundraiser'] ) ) {
            return true;
        }
    }

    private function update_subgroups($f_id){
    	
	    if ( isset( $_POST['participants_subgroups'] ) ) {

		    $subgroups = $_POST['participants_subgroups'];

		    $subgroups_table = new Subgroups();
		    foreach ( $subgroups as $id => $subgroup ) {
			    if ( ! empty( trim( $subgroup ) ) ) {
				    $found = $subgroups_table->isFundraiserSubgroup( $id, $f_id );
				    if ( $found ) {
					    $subgroups_table->update($subgroup, $id, $f_id);
				    } else {
					    $subgroups_table->insert($subgroup, $f_id);
				    }
			    }

		    }

	    }
    }
    
}
