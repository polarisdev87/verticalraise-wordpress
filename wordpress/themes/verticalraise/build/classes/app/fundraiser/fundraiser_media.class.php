<?php

namespace classes\app\fundraiser;

class Fundraiser_Media
{

    public function get_fundraiser_logo( $f_id ) {

        $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $f_id ), "fundraiser-logo-thumb" );
        if ( is_mobile_new() ) {
            $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $f_id ), "fundraiser-logo-small" );
        }

        $image_url = $image_url[0];
        if ( $image_url == null ) {
            return $this->get_fundraiser_default_image();
        }

        if ( $image_url != '' ) {
            return $image_url;
        } else {
            return $this->get_fundraiser_default_image();
        }
    }

    public function get_fundraiser_logo_stripe( $f_id ) {
        $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $f_id ), array( 150, 150 ) );
        $image_url = $image_url[0];

        if ( $image_url == null ) {
            return $this->get_fundraiser_default_image();
        }

        return $image_url;
    }

    /**
     * 
     * @return string
     */
    private function get_fundraiser_default_image() {
        if ( is_mobile_new() ) {
            $image_url = get_template_directory_uri() . '/assets/images/mobile-default-logo.png';
        } else {
            $image_url = get_template_directory_uri() . '/assets/images/default-logo.png';
        }
        return $image_url;
    }

    public function get_fundraiser_youtube_image( $youtubeURL ) {

    	$re = '/(?:v=(?\'id\'\w+)$|\/(?\'id\'\w+)$)/mJ';

	    if ( preg_match($re, $youtubeURL, $matches) ){

		    $youtubeImg = 'https://img.youtube.com/vi/' . $matches['id'] . '/0.jpg';

		    $ch = curl_init();
		    curl_setopt_array($ch, array(
			    CURLOPT_URL            => $youtubeImg,
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_NOBODY         => true
		    ));

		    curl_exec( $ch );
		    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE );
		    curl_close( $ch);

		    if ( $status === 200 ){
		    	return $youtubeImg;
		    }
		    return false;

	    }
    }

}
