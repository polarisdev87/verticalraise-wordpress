<?php

namespace classes\app\utm;

class UTM
{
    
    /**
    * Create UTM link function
    * @param string $url
    * @param string $profile
    * @return utmlink
    */
   
    public function createUTMLink( $url, $profile ) {
        // take url
        $utm_url    = ( $this->checkurl( $url ) ) ? $url."&" : $url."/?" ;
        // call profile function
        $utm_code   = $this->getUTMCode( $profile );
        // append the data to the url
        return $utm_url."utm_source=".$utm_code['source']."&utm_medium=".$utm_code['medium']."&utm_campaign=".$utm_code['campaign']."&utm_content=".$utm_code['content']."&utm_term=".$utm_code['term'];
    }
    //check if url has ? or not
    public function checkurl($url){
        if (strpos($url, '?') !== false) {
            return true;
        }else{
            return false;
        }
    }
    public function getUTMCode( $profile ){
        switch( $profile ){
            case "Cron_SMS":
                $utm['source']      = 'cron_sms_2_day';
                $utm['medium']      = 'sms';
                $utm['campaign']    = '2_day';
                $utm['content']     = 'message';
                $utm['term']        = 'main_link';
                break;
            case "Cron_Email_2_Day":
                $utm['source']      = 'cron_email_2_day';
                $utm['medium']      = 'email';
                $utm['campaign']    = '2_day';
                $utm['content']     = 'body';
                $utm['term']        = 'main_link';
                break;
            case "Cron_Email_7_Day":
                $utm['source']      = 'cron_email_7_day';
                $utm['medium']      = 'email';
                $utm['campaign']    = '7_day';
                $utm['content']     = 'body';
                $utm['term']        = 'main_link';
                break;
            case "Cron_Email_14_Day":
                $utm['source']      = 'cron_email_14_day';
                $utm['medium']      = 'email';
                $utm['campaign']    = '14_day';
                $utm['content']     = 'body';
                $utm['term']        = 'main_link';
                break;
            case "Email_Share":
                $utm['source']      = 'email_share';
                $utm['medium']      = 'email';
                $utm['campaign']    = 'sharing';
                $utm['content']     = 'body';
                $utm['term']        = 'main_link';
                break;
            case "SMS_Share_Desktop":
                $utm['source']      = 'sms_share_desktop';
                $utm['medium']      = 'sms';
                $utm['campaign']    = 'sharing';
                $utm['content']     = 'message';
                $utm['term']        = 'main_link';
                break;
            case "SMS_Share_Mobile":
                $utm['source']      = 'sms_share_mobile';
                $utm['medium']      = 'sms';
                $utm['campaign']    = 'sharing';
                $utm['content']     = 'message';
                $utm['term']        = 'main_link';
                break;
            case "Facebook_Share":
                $utm['source']      = 'facebook_share';
                $utm['medium']      = 'social_media';
                $utm['campaign']    = 'sharing';
                $utm['content']     = 'post';
                $utm['term']        = 'main_link';
                break;
            case "Twitter_Share":
                $utm['source']      = 'twitter_share';
                $utm['medium']      = 'social_media';
                $utm['campaign']    = 'sharing';
                $utm['content']     = 'post';
                $utm['term']        = 'main_link';
                break;
            case "URL_Share":
                $utm['source']      = 'url_share';
                $utm['medium']      = 'copy_paste';
                $utm['campaign']    = 'sharing';
                $utm['content']     = 'paste';
                $utm['term']        = 'main_link';
                break;
            case "Email_Invite":
                $utm['source']      = 'email_invite';
                $utm['medium']      = 'email';
                $utm['campaign']    = 'invite';
                $utm['content']     = 'body';
                $utm['term']        = 'main_link';
                break;
            case "SMS_Invite_Desktop":
                $utm['source']      = 'sms_invite_desktop';
                $utm['medium']      = 'sms';
                $utm['campaign']    = 'invite';
                $utm['content']     = 'message';
                $utm['term']        = 'main_link';
                break;
            case "SMS_Invite_Mobile":
                $utm['source']      = 'sms_invite_mobile';
                $utm['medium']      = 'sms';
                $utm['campaign']    = 'invite';
                $utm['content']     = 'message';
                $utm['term']        = 'main_link';
                break;
            case "Facebook_Invite_Post":
                $utm['source']      = 'facebook_invite_post';
                $utm['medium']      = 'social_media';
                $utm['campaign']    = 'invite';
                $utm['content']     = 'post';
                $utm['term']        = 'main_link';
                break;
            case "Facebook_Invite_Message":
                $utm['source']      = 'facebook_invite_message';
                $utm['medium']      = 'social_media';
                $utm['campaign']    = 'invite';
                $utm['content']     = 'message';
                $utm['term']        = 'main_link';
                break;
            case "Facebook_Message":
                $utm['source']      = 'facebook_message';
                $utm['medium']      = 'social_media';
                $utm['campaign']    = 'message';
                $utm['content']     = 'message';
                $utm['term']        = 'main_link';
                break;
            case "Twitter_Invite":
                $utm['source']      = 'twitter_invite';
                $utm['medium']      = 'social_media';
                $utm['campaign']    = 'invite';
                $utm['content']     = 'post';
                $utm['term']        = 'main_link';
                break;
            case "URL_Invite":
                $utm['source']      = 'url_invite';
                $utm['medium']      = 'copy_paste';
                $utm['campaign']    = 'invite';
                $utm['content']     = 'paste';
                $utm['term']        = 'main_link';
                break;
            case "Parent_Email_Invite":
                $utm['source']      = 'parent_email_invite';
                $utm['medium']      = 'email';
                $utm['campaign']    = 'parent_invite';
                $utm['content']     = 'body';
                $utm['term']        = 'main_link';
                break;
            case "Parent_SMS_Invite_Desktop":
                $utm['source']      = 'parent_sms_invite_desktop';
                $utm['medium']      = 'sms';
                $utm['campaign']    = 'parent_invite';
                $utm['content']     = 'message';
                $utm['term']        = 'main_link';
                break;
            case "Parent_SMS_Invite_Mobile":
                $utm['source']      = 'parent_sms_invite_mobile';
                $utm['medium']      = 'sms';
                $utm['campaign']    = 'parent_invite';
                $utm['content']     = 'message';
                $utm['term']        = 'main_link';
                break;
            case "Parent_Facebook_Invite":
                $utm['source']      = 'parent_facebook_invite';
                $utm['medium']      = 'social_media';
                $utm['campaign']    = 'parent_invite';
                $utm['content']     = 'post';
                $utm['term']        = 'main_link';
                break;
            case "Parent_Twitter_Invite":
                $utm['source']      = 'parent_twitter_invite';
                $utm['medium']      = 'social_media';
                $utm['campaign']    = 'parent_invite';
                $utm['content']     = 'post';
                $utm['term']        = 'main_link';
                break;
            case "Parent_URL_Invite":
                $utm['source']      = 'parent_url_invite';
                $utm['medium']      = 'copy_paste';
                $utm['campaign']    = 'parent_invite';
                $utm['content']     = 'paste';
                $utm['term']        = 'main_link';
                break;
            case "Parent_Donate":
                $utm['source']      = 'parent_donate';
                $utm['medium']      = 'website';
                $utm['campaign']    = 'parent_invite';
                $utm['content']     = 'page';
                $utm['term']        = 'donate_link';
                break;
            case "Thank_You_Email_Share":
                $utm['source']      = 'thank_you_email_share';
                $utm['medium']      = 'email';
                $utm['campaign']    = 'thank_you';
                $utm['content']     = 'body';
                $utm['term']        = 'main_link';
                break;
            case "Thank_You_SMS_Share":
                $utm['source']      = 'thank_you_sms_share';
                $utm['medium']      = 'email';
                $utm['campaign']    = 'thank_you';
                $utm['content']     = 'message';
                $utm['term']        = 'main_link';
                break;
            case "Thank_You_Facebook_Share":
                $utm['source']      = 'thank_you_facebook_share';
                $utm['medium']      = 'social_media';
                $utm['campaign']    = 'thank_you';
                $utm['content']     = 'post';
                $utm['term']        = 'main_link';
                break;
            case "Thank_You_Twitter_Share":
                $utm['source']      = 'thank_you_twitter_share';
                $utm['medium']      = 'social_media';
                $utm['campaign']    = 'thank_you';
                $utm['content']     = 'post';
                $utm['term']        = 'main_link';
                break;
            default:
                $utm['source']      = '';
                $utm['medium']      = '';
                $utm['campaign']    = '';
                $utm['content']     = '';
                $utm['term']        = '';
                break;
        }
        return $utm;
    }

}