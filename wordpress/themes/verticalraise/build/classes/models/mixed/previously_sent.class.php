<?php

/**
 * Get and set previously sent records for a specific user.
 */
namespace classes\models\mixed;

class Previously_Sent
{
    
    /**
     * Class Variables.
     */
    private $fundraiser_id;
    private $user_id;
    
    /**
     * Class Constructor.
     */
    public function __construct($fundraiser_id, $user_id) {
        $this->fundraiser_id = $fundraiser_id;
        $this->user_id = $user_id;
    }

    /**
     * Update previously sent records.
     * @param array $emails Emails
     */
    public function update($emails) {  
        if ( empty($emails) ) {
            return;
        }
        
        $previous = $this->get_all();
        
        if ( !empty($previous) ) {
            if ( !empty($previous[$this->fundraiser_id]) ) {
                $previous[$this->fundraiser_id] = array_unique(array_merge($previous[$this->fundraiser_id], $emails));
            } else {
                $previous[$this->fundraiser_id] = $emails;
            }
        } else {
            $previous[$this->fundraiser_id] = $emails;
        }
        
        update_user_meta($this->user_id, 'previously_sent', json_encode($previous));
    }
    
    /**
     * Get previously sent records.
     * @return mixed Array if data, false if empty
     */
    public function get_all() {
        $meta = get_user_meta($this->user_id, 'previously_sent'); // returns an array
        if ( !empty($meta) ) {
            return json_decode($meta[0], true);
        }
        
        return false;
    }
    
    /**
     * Get previously sent records by fundraiser id.
     * @return mixed Array if data, false if empty
     */
    public function get_by_fid() {
        $records = $this->get_all();
        if ( $records ) {
            if ( !empty($records[$this->fundraiser_id]) ) {
                return $records[$this->fundraiser_id];
            } else {
                return false;
            }
        }
        
        return false;
    }

}