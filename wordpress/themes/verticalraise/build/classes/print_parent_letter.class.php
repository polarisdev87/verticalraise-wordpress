<?php

use \classes\app\PDF\PDF;
use \classes\models\mixed\Fundraiser;

/**
 * The main class for the Print Instructions PDF link that extends the PDF class
 */
class Print_Parent_Letter extends PDF
{

    /**
     * Protected class variables.
     */
    protected $fundraiser;          // The fundraiser object
    protected $template_page1;      // The pdf template file name for page 1
    protected $template_page2;      // The pdf template file name for page 2
    protected $file_name;           // The filename of the PDF that gets outputted for download
    
    /**
     * Class constructor.
     * @param int    $f_id Fundraiser ID
     * @param string $uid  The user_id url param we tack onto links
     */
    public function __construct($f_id, $uid) {
        
        $this->fundraiser          = new Fundraiser($f_id);

        $this->template_page1      = 'parent_letter_page1';
        $this->template_page2      = 'parent_letter_page2';
        
        $this->fundraiser_name     = $this->fundraiser->get_name();
        $this->contact_name        = $this->fundraiser->get_primary_contact_name();
        $this->coach_email         = $this->fundraiser->get_coach_email();
        $this->picture_url           = $this->fundraiser->get_flyer_picture_url();
        $this->primary_contact_name  = $this->fundraiser->get_primary_contact_name();

        $this->date = current_time('Ymd');
        $this->time = current_time('timestamp');
        
        $this->file_name   = "{$this->date}_{$this->time}_Parent_Letter.pdf";
        $this->font_family = 'Calibri';
        
        parent::__construct();
    }
    
    /**
     * Initialize the PDF for download.
     */
    public function init() {        
    
        // Set template args
        $template_args = [           
            "FUNDRAISER_NAME" => $this->fundraiser_name,
            "CONTACT_NAME"    => $this->contact_name,
            "COACH_EMAIL"     => $this->coach_email,
            "PICTURE_URL" => $this->picture_url,
            "PRIMARY_CONTACT_NAME"  => $this->primary_contact_name,
        ];
        
        // Load Templates (Only 1 page for now)
        $templates['page1'] = $this->load_template($this->template_page1);
        $templates['page2'] = $this->load_template($this->template_page2);
        
        $content = '';
        
        // Load Content
        foreach ( $templates as $template ) {
            $_content = $this->load_content($template, $template_args);
            $content .= $this->prep_content($_content);
        }
        
        // Output the PDF
        $this->output($content, $this->file_name, true, $this->font_family);
        
        exit();
        
    }

}