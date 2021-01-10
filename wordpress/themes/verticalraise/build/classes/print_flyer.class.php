<?php

/**
 * The main class for the Print Flyer PDF link that extends the PDF class
 */
class Print_Flyer extends PDF
{
    
    /**
     * Protected class variables.
     */
    protected $fundraiser_id;       // Fundraiser ID
    protected $uid;                 // User id param that gets attached to links
    protected $template;            // The pdf template file name
    protected $wefund4u_logo;       // The url of the Wefund4u logo
    protected $file_name;           // The filename of the PDF that gets outputted for download
    
    /**
     * Class constructor.
     * @param int    $f_id Fundraiser ID
     * @param string $uid  The user_id url param we tack onto links
     */
    public function __construct($f_id, $uid) {
        
        $_date               = current_time('Ymd');
        $_time               = current_time('timestamp');
        
        $this->fundraiser_id = $f_id;
        $this->uid           = $uid;
        $this->template      = 'flyer';
        $this->wefund4u_logo = get_site_url() . '/wp-content/uploads/2016/03/logo.png';
        $this->file_name     = "{$_date}_{$_time}_Flyer.pdf";
        
        parent::__construct();
    }
    
    /**
     * Initialize the PDF for download.
     */
    public function init() {
        
        // Get the fundraiser thumbnail
        $thumb_id         = get_post_thumbnail_id($this->fundraiser_id);
        $thumb_url_array  = wp_get_attachment_image_src($thumb_id, 'medium', true);
        $thumb_url        = $thumb_url_array[0];
        
        if ( strpos($thumb_url, '/images/media/default.png') !== false ) {
            $thumb_url = false;
        } 
        
        $picture_url      = ( !empty($thumb_url) ) ? $thumb_url : $this->wefund4u_logo;
        $wefund4u_logo    = $this->wefund4u_logo;
        
        // Get fundraiser data
        $fundraiser_name  = get_the_title($this->fundraiser_id);
        $message          = nl2br(get_post_meta($this->fundraiser_id, 'campaign_msg', true));
        $permalink        = get_permalink($this->fundraiser_id);
        $fundraiser_link  = "{$permalink}flyer{$this->uid}";
        $qr_code          = "<qrcode value='{$fundraiser_link}flyer{$this->uid}' ec='H' style='width: 30mm;'></qrcode>";

        // Set the template args
        $template_args = [
            "WEFUND4U_LOGO"              => $wefund4u_logo,
            "FUNDRAISER_IMAGE"           => $picture_url,
            "FUNDRAISER_NAME"            => $fundraiser_name,
            "CAMPAIGN_MESSAGE_TO_DONORS" => $message,
            "FUNDRAISER_LINK"            => $fundraiser_link,
            "QR_CODE"                    => $qr_code
        ];

        // Load template
        $template = $this->load_template($this->template);

        if ( $template != false) {

            // Load content
            $content = $this->load_content($template, $template_args);

            // Prep content
            $content = $this->prep_content($content);

            // Output the PDF
            $this->output($content, $this->file_name, false, 'Calibri');
        }
    }
    
}