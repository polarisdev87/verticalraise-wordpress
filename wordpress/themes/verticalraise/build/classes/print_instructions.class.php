<?php

use \classes\app\PDF\PDF;
use \classes\models\mixed\Fundraiser;

/**
 * The main class for the Print Instructions PDF link that extends the PDF class
 */
class Print_Instructions extends PDF
{

    /**
     * Protected class variables.
     */
    protected $fundraiser;          // The fundraiser object
    protected $fundraiser_id;       // Fundraiser ID
    protected $uid;                 // User id param that gets attached to links
    protected $template_page1;      // The pdf template file name for page 1
    protected $template_page2;      // The pdf template file name for page 2
    protected $file_name;           // The filename of the PDF that gets outputted for download
    protected $verticalraise_name;  // VerticalRaise's display name
    protected $verticalraise_email; // VerticalRaise contact email address
    protected $verticalraise_logo;  // The url of the VerticalRaise logo
    
    /**
     * Class constructor.
     * @param int    $f_id Fundraiser ID
     * @param string $uid  The user_id url param we tack onto links
     */
    public function __construct($f_id, $uid) {
        
        $this->fundraiser          = new Fundraiser($f_id);
        $this->fundraiser_id       = $f_id;
        $this->uid                 = $uid;
        $this->template_page1      = 'instructions_page1';
        $this->template_page2      = 'instructions_page2';
        
        $date = current_time('Ymd');
        $time = current_time('timestamp');
        
        $this->file_name = "{$date}_{$time}_Instructions.pdf";
        
        parent::__construct();
    }
    
    /**
     * Initialize the PDF for download.
     */
    public function init() {
        
        // Get the contacts
        $primary_contact_name  = $this->fundraiser->get_primary_contact_name();
        $primary_contact_email = $this->fundraiser->get_primary_contact_email();
        $primary_contact_phone = $this->fundraiser->get_primary_contact_phone();
        $coach_name            = $this->fundraiser->get_coach_name();
        $coach_email           = $this->fundraiser->get_coach_email();
        $picture_url           = $this->fundraiser->get_flyer_picture_url();
        
        // Get the fundraiser data
        $fundraiser_name  = $this->fundraiser->get_name();
        $join_code        = $this->fundraiser->get_join_code();
        $permalink        = $this->fundraiser->get_permalink();
        $fundraiser_link  = "{$permalink}flyer{$this->uid}";
        
        // Set template args
        $template_args = [
            "PICTURE_URL"           => $picture_url,
            "FUNDRAISER_NAME"       => $fundraiser_name,
            "FUNDRAISER_LINK"       => $fundraiser_link,
            "PRIMARY_CONTACT_NAME"  => $primary_contact_name,
            "PRIMARY_CONTACT_EMAIL" => $primary_contact_email,
            "PRIMARY_CONTACT_PHONE" => $primary_contact_phone,
            "COACH_NAME"            => $coach_name,
            "COACH_EMAIL"           => $coach_email,
            "PARTICIPANT_JOIN_CODE" => $join_code
        ];
        
        // Load Templates (Only 1 page for now)
        $templates['page1'] = $this->load_template($this->template_page1);
        //$templates['page2'] = $this->load_template($this->template_page2);
        
        $_content = $this->load_content($templates['page1'], $template_args);
        $content  = $this->prep_content($_content);
        
        /*
        // Multiple pages
        $content = '';
        
        // Load Content
        foreach ( $templates as $template ) {
            $_content = $this->load_content($template, $template_args);
            $content .= $this->prep_content($_content);
        }
        */

        // Output the PDF
        $this->output($content, $this->file_name, true, 'Calibri');
        
        exit();
        
    }

}