<?php

namespace classes\app\donation_comments;

use \classes\models\tables\Donation_Comments as Donation_Comments_Table;

class Donation_Comments
{
    
    // Table object
    private $table;
    
    /**
     * Class constructor.
     */
    public function __construct() {
        $this->table = new Donation_Comments_Table;
    }

	/**
	 * Process the comment field in the form.
	 *
	 * @param string $d_id The donation record id
	 * @param string $f_id The fundraiser id
	 * @param string $comment
	 * @param string $avatar
	 *
	 * @return void
	 */
	public function process( $d_id, $f_id, $comment, $avatar ) {

        if ( !empty($comment) ) {

            $comment = $this->sanitize($comment);
            $avatar_url = $this->sanitize($avatar);

            $data = (object) array(
                'd_id' => $d_id, 
                'f_id' => $f_id, 
                'comment' => $comment, 
                'avatar_url' => $avatar_url
            );
            
            $this->add($data);
        }
    }
    
    /**
     * Check to see if there was a comment.
     * @return bool
     */
    private function check_post() {
        if ( !empty($_POST['comment']) ) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get comment.
     * @return The comment|empty
     */
    private function get_comment() {
        if ( !empty($_POST['comment']) ) {
            return $this->sanitize($_POST['comment']);
        }
        return '';
    }

    
    /**
     * Get the avatar url.
     * @return The avatar url|default
     */
    private function get_avatar_url() {
        if ( !empty($_POST['avatar_url']) ) {
            return $this->sanitize($_POST['avatar_url']);
        }
        return 'default';
    }

    /**
     * Add the comment to the database.
     * @param obj $data
     *  - d_id
     *  - f_id
     *  - comment
     *  - avatar_url
     */
    private function add($data) {
        if ( $this->validate($data) ) {
            $this->table->insert($data);
        }
    }
    
    /**
     * Validate the data.
     * @return bool
     */
    private function validate($data) {
        foreach ( (array) $data as $_d ) {
            if ( empty($_d) ) return false;
        }
        
        if ( strlen($data->comment) > 250 ) {
            return false;
        }
        
        return true;
    
    }
    
    /**
     * Sanitize the text input.
     */
    private function sanitize($string) {
        return sanitize_text_field($string);
    }

}