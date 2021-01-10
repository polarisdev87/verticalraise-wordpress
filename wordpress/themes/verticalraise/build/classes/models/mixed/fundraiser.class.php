<?php

namespace classes\models\mixed;

class Fundraiser
{
    private $fundraiser_id;
    
    public function __construct($fundraiser_id) {
        $this->fundraiser_id = $fundraiser_id;
    }
    
    /**
     * Get the fundraiser name.
     */
    public function get_name() {
        return get_the_title($this->fundraiser_id);
    }
    
    /**
     * Get the permalink.
     */
    public function get_permalink() {
        return get_permalink($this->fundraiser_id);
    }
    
    /**
     * Get the primary contact name.
     */
    public function get_primary_contact_name() {
        return get_post_meta($this->fundraiser_id, 'con_name', true);
    }
    
    /**
     * Get the primary contact email.
     */
    public function get_primary_contact_email() {
        return get_post_meta($this->fundraiser_id, 'email', true);
    }
    
    /**
     * Get the primary contact email.
     */
    public function get_primary_contact_phone() {
        return get_post_meta($this->fundraiser_id, 'phone', true);
    }
    
    /**
     * Get the coach's name.
     */
    public function get_coach_name() {
        $coach_name = get_post_meta($this->fundraiser_id, 'coach_name', true);
        
        if ( empty($coach_name) ) {
            $coach_name = _SITE_NAME;
        }
        
        return $coach_name;
    }
    
    /**
     * Get the coach's email.
     */
    public function get_coach_email() {
        $coach_email = get_post_meta($this->fundraiser_id, 'coach_email', true);
        
        if ( empty($coach_email) ) {
            $coach_email = _SUPPORT_TO_EMAIL;
        }
        
        return $coach_email;
    }
    
    /**
     * Get the join code.
     */
    public function get_join_code() {
        return get_post_meta($this->fundraiser_id, 'join_code', true);
    }
    
    /** 
     * Get the post thumbnail.
     */
    public function get_post_thumbnail() {
        return get_post_thumbnail_id($this->fundraiser_id);
    }
    
    /**
     * Get the picture to use in the flyer.
     */
    public function get_flyer_picture_url() {
        $logo = get_template_directory_uri() . '/assets/images/verticalraise-share-logo.png';
        
        // Get the fundraiser thumbnail
        $thumb_id              = $this->get_post_thumbnail();
        $thumb_url_array       = wp_get_attachment_image_src($thumb_id, 'medium', true);
        $thumb_url             = $thumb_url_array[0];
        
        if ( strpos($thumb_url, '/images/media/default.png') !== false ) {
            $thumb_url = false;
        } 
        
        $picture_url = ( !empty($thumb_url) ) ? $thumb_url : $logo;

        return $picture_url;
    }
    
    
}
