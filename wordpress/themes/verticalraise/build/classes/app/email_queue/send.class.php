<?php

namespace classes\app\email_queue;

use \classes\models\tables\Email_Queue;
use \classes\app\initial_send\Content;
use \classes\app\initial_send\Send as Send_Invite;

class Send
{
    public function __construct() {
        $this->email_queue = new Email_Queue();
        $this->send        = new Send_Invite();
    }

    /**
     * Send an email invite to every unsent record in the email_queue table.
     */
    public function send() {
        // Every Minute: Grab the last 500 records and process
        $records = $this->email_queue->get_ready_to_send();

        if ( !empty( $records ) ) {
            $records = $this->reshuffle( $records );

            // Process records
            $this->process_records( $records );
        }
    }

    /**
     * Reshuffle the array heirarchy to f_id, u_id to make it easier for processing.
     * @param array $records
     * @return array
     */
    private function reshuffle( $records ) {
        $array = array();
        foreach ( $records as $r ) {
            $array[$r['f_id']][$r['u_id']][] = array( 
                'u_id'      => $r['u_id'], 
                'id'        => $r['id'], 
                'email'     => $r['email'], 
                'type'      => $r['type'], 
                'from_name' => $r['from_name'],
                'parent'    => $r['parent'],
                'share_type'=> $r['share_type'],
                'attempts'  => $r['attempts']
            );
        }

        return $array;
    }

    private function process_records( $records ) {
        $this->process_fundraisers( $records );
    }

    private function process_fundraisers( $records ) {
        foreach ( $records as $fid => $_records ) {
            $fundraiser = Content::set_content_fundraiser( $fid );
            $this->process_users( $_records, $fundraiser );
        }
    }

    private function process_users( $records, $fundraiser ) {
        foreach ( $records as $uid => $_records ) {
            $user = Content::set_content_user( $fundraiser->id, $uid );
            $this->process_sends( $_records, $fundraiser, $user );
        }
    }

    private function process_sends( $records, $fundraiser, $user ) {
        foreach ( $records as $record ) {
            $email_user = $user;
            // if share user
            if ( $record['type'] == 1 ) {
                $user_aux = (array)$user;
                $email_user =  array(
                    'user_name'  => $record['from_name'],
                    'from'       => _ADMIN_TO_EMAIL,
                    'from_name'  => $record['from_name'],
                    'click_url'  => get_permalink( $fundraiser-> id ) . 'email/' . $record['u_id'],
                    'avatar_url' => 'default',
                    'title'      => get_the_title( $fundraiser-> id ),
                    'subject'    => get_the_title( $fundraiser-> id )
                );
                $email_user = (object) array_merge($user_aux, $email_user);
            }
            $email_user->from_name =  $record['from_name'];
            $template = Content::set_content_template( $fundraiser, $user, $record['type'] );
            
            // Send the email
            
            $result = $this->send->send( $record['email'], $fundraiser, $email_user, $template, $record['type'], $record['parent'], $record['share_type'] );
            
            // Result status code 202 Ture or False
            if( $result["results"] === true ){
                // Update the record
                $this->email_queue->update_sent( $record['id'], 1 );
            } else {
                $this->email_queue->increment_attempts( $record['id'] , $record['attempts']);
            }
        }
    }

}