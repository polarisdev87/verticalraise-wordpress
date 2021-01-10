<?php

namespace classes\app\invite_emails;

use \classes\app\debug\Debug;
use \classes\app\emails\Email_Utils;
use \classes\app\encryption\Encryption;
use \classes\app\emails\Check_Duplicates;
use \classes\app\invite_emails\Email_Template;
use \classes\app\invite_emails\Form_Handler;
use \classes\app\invite_emails\Type;
use \classes\models\mixed\Admins;
use \classes\models\tables\Email_Queue;
use \classes\models\tables\Participant_Fundraiser_Details as Participant_Sharing_Totals;
use \classes\models\tables\Potential_Donors_Email;
use \classes\models\mixed\Previously_Sent;
use \classes\app\initial_send\Send;
use \classes\app\initial_send\Content;
use \classes\app\fundraiser\Fundraiser_emails;

class Invite_Emails
{

    /**
     * Class objects
     */
    protected $debug;                       // Debug object
    protected $email_utils;                 // Email_Utils object
    protected $encryption;                  // Encryption object
    protected $check_duplicates;            // Check_Duplicates object
    protected $email_template;              // Email_Template object
    protected $form_handler;                // Form_Handler object
    protected $type;                        // Type object
    protected $admins;                      // Admins object
    protected $email_queue;                 // Email_Queue object
    protected $participant_records;         // Participant_Sharing_totals object
    protected $potential_donors;            // Potential_Donors object
    protected $previously_sent;             // Previously_Sent object
    protected $email_send;                  // Email send object
    protected $email_content;               // Content_Email object

    /**
     * Class variables
     */
    protected $fundraiser_id;
    protected $user_id;
    protected $limit      = _EMAIL_INVITE_LIMIT;  // The limit of emails one can send to at a time
    protected $debug_init = false;           // Turn off debugger

    //protected $debug_init = true;          // Turn on debugger
    protected  $stored_emails;

    /**
     * Class constructor
     */
    public function __construct( $user_ID, $fundraiser_ID ) {

        $this->user_id       = (int) $user_ID;
        $this->fundraiser_id = (int) $fundraiser_ID;

        $this->debug               = new Debug( $this->debug_init );
        $this->email_utils         = new Email_Utils();
        $this->encryption          = new Encryption();
        $this->check_duplicates    = new Check_Duplicates();
        $this->email_template      = new Email_Template();
        $this->form_handler        = new Form_Handler();
        $this->type                = new Type();
        $this->admins              = new Admins();
        $this->email_queue         = new Email_Queue();
        $this->participant_records = new Participant_Sharing_Totals();
        $this->potential_donors    = new Potential_Donors_Email();
        $this->previously_sent     = new Previously_Sent( $this->fundraiser_id, $this->user_id );

        $this->email_send    = new Send();
        $this->email_content = new Content();
    }

    /**
     * Queue
     */
    public function process_input() {

        // Check if form submit
        if ( $this->form_handler->is_invite_submit() == true ) {

            // We need to store the type of template to use for the future
            $type          = $this->type->get_type();
            $is_admin      = $this->admins->is_fundraiser_admin_or_site_admin( $this->user_id, $this->fundraiser_id );
            $template_type = $this->email_template->get_template( $is_admin, $type );

            // Placeholders
            $invalid_emails   = array();
            $valid_emails     = array();
            $duplicate_emails = array();

            // Get the emails
            $emails = $this->form_handler->get_the_emails();

            $this->debug->debug( $emails, 'emails' );

            // Get lists to check against for duplicates
            //$potential_donors = $this->potential_donors->get_by_fid( $this->fundraiser_id, $this->user_id );
            //$queued_emails    = $this->email_queue->get_by_fid( $this->fundraiser_id, $this->user_id, $template_type );
            $this->stored_emails = Fundraiser_emails::get_emails_for($this->fundraiser_id);
            $participant_share_details = $this->participant_records->get_single_row($this->fundraiser_id, $this->user_id);
            @$available_email_invites = _EMAIL_PARTICIPANT_INVITE_LIMIT - intval($participant_share_details->email);
            $invalid = 0;
            $i       = 0;

            foreach ( $emails as $email ) {
                try {
                    // Check for empty email
                    if ( $this->is_empty( $email ) ) {
                        break;
                    }

                    // Format email
                    $email = $this->email_utils->format_email( $email );

                    $this->debug->debug( $email, 'email' );

                    if ( $this->type->is_spread_the_word() != true ) {
                        
                        // Check for duplicates
                        $check_duplicate = in_array( $email, $this->stored_emails);
//                        var_dump($stored_emails);
                        // If it's a duplicate, skip
                        if ( $check_duplicate ) {
                            $this->debug->debug( $check_duplicate, 'checkDuplicate' );
                            $duplicate_emails[] = $email;
                            continue;
                        }

                        $this->debug->debug( $check_duplicate, 'checkDuplicate' );
                    }

                    // Check to see if we are over our max limit
                    if ( $this->check_limit( $i ) == true ) {
                        break;
                    }

                    // Check for a valid email address
                    $isValid = $this->email_utils->is_email_valid( $email );

                    // Build list of invalid email addresses
                    if ( $isValid != true ) {

                        $this->debug->debug( $isValid, 'isValid' );

                        $invalid++;
                        $invalid_emails[] = $email;
                        continue;
                    }

                    if($available_email_invites > 0 ) {
                        // If not generic
                        if ($this->is_generic() != true && $is_admin == false) {
                            if ($this->type->is_spread_the_word() != true) {
                                // Increment the user's email sharing record by one
                                $this->participant_records->adjust($this->fundraiser_id, $this->user_id, 'email', 1);
                            }
                        }
                        $valid_emails[] = $email;
                        // The email is valid
                        if ($this->type->is_spread_the_word() != true) {
                            $this->previously_sent->update(array($email));
                            $stored_emails = Fundraiser_emails::store_emails_for( $this->fundraiser_id, array($email) );
                        }
                        $available_email_invites--;
                    }
                } // catch
                catch ( Exception $e ) {
                    if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                        newrelic_notice_error( $e->getMessage(), $e );
                    }
                }
                $i++;
            }

            // Update previously sent
            // Return the results
            $data['emails']           = $emails;           // All submitted emails
            $data['valid_emails']     = $valid_emails;     // All valid emails
            $data['invalid_emails']   = $invalid_emails;   // All invalid emails
            $data['duplicate_emails'] = $duplicate_emails; // All invalid emails
            $data['template_type']    = $template_type;    // Template Type

            return $data;
        } else {
            throw new Exception( "Missing form POST" );
        }
    }

    private function check_limit( $i ) {
        if ( $i >= $this->limit ) {
            $this->debug->debug( $i, 'i' );
            $this->debug->debug( $this->limit, 'limit' );
            return true;
        }

        return false;
    }

    private function is_empty( $email ) {
        if ( empty( $email ) ) {
            return true;
        }

        return false;
    }

    private function is_generic() {
        if ( $this->user_id == 0 || $this->user_id == '' ) {
            return true;
        }

        return false;
    }

}
