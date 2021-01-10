<?php

namespace classes\app\utm;

class UTMJavascript
{
    
    /**
     * Google UA Code
     */
    private $google_ua_code = _GOOGLE_UA_CODE;

    /**
     * Google Adwords conversion Code
     */
    private $google_awc_code = _GOOGLE_AWC_CODE;
    
    private $profile_data;
    
    public function __construct( $data ) {
        $this->profile_data = $data;      
    }
    
   /**
    * Get the profile
    * @return $profile
    */
    public function get_profile() {
        $getdata = $this->profile_data;
        if ( !isset( $getdata['utm_source'] ) ) {
            $utm_code = $this->fundraiserPageUTM();
        } else {
            $utm_code['source']   = isset( $getdata['utm_source'] )   ? $getdata['utm_source']   : '';
            $utm_code['medium']   = isset( $getdata['utm_medium'] )   ? $getdata['utm_medium']   : '';
            $utm_code['campaign'] = isset( $getdata['utm_campaign'] ) ? $getdata['utm_campaign'] : '';
            $utm_code['content']  = isset( $getdata['utm_content'] )  ? $getdata['utm_content']  : '';
            $utm_code['term']     = isset( $getdata['utm_term'] )     ? $getdata['utm_term']     : ''; 
        }

        return $utm_code;
    }
    
    /**
     * Render google analtics javascript
     * 
     */
    public function display() {
        
        $utm_code = $this->get_profile();
        
        echo "
        <!-- Google Analytics code11 -->
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', '{$this->google_ua_code}', 'auto');
            
            ga('set', 'campaignSource', '{$utm_code['source']}');
            ga('set', 'campaignMedium', '{$utm_code['medium']}');    
            ga('set', 'campaignName', '{$utm_code['campaign']}');
            ga('set', 'campaignContent', '{$utm_code['content']}');
            ga('set', 'campaignTerm', '{$utm_code['term']}');
            
            ga('send', 'pageview');
        </script>
        
        <!-- Global site tag (gtag.js) - Google Ads: {$this->google_awc_code}' --> 
        <script async src=\"https://www.googletagmanager.com/gtag/js?id={$this->google_awc_code}\">
        </script> 
        <script> 
            window.dataLayer = window.dataLayer || []; 
            function gtag(){
                dataLayer.push(arguments);
            } 
            gtag('js', new Date()); 
            gtag('config', '{$this->google_awc_code}'); 
        </script>
        ";
    }

    public function fundraiserPageUTM() {

        $utm = array();
        
        if ( is_user_logged_in() ) {
            $utm['source']      = 'participant_logged_in';
            $utm['medium']      = 'website';
            $utm['campaign']    = 'participant_logged_in';
            $utm['content']     = 'body';
            $utm['term']        = 'main_link';
        } else {
            $utm['source']      = '';
            $utm['medium']      = 'website';
            $utm['campaign']    = '';
            $utm['content']     = 'body';
            $utm['term']        = 'main_link';
        }

        return $utm;
    }
    
}
