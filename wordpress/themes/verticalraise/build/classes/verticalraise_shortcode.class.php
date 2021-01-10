<?php

namespace classes;

/**
 * This class can be used to create a Wefund4u branded Shorturl for a specific fundraiser donation page
 */
class VerticalRaise_Shorturl
{

    /**
     * @var object $wpdb The WP Database object.
     */
    private $wpdb;

    /**
     * @var string $table The name of the database table.
     */
    private $table = 'shorturls';

    /**
     * @var int $length The length of the Shorturl code.
     */
    private $length = 6;

    /**
     * @var string $base_url The base url to use.
     */
    private $base_url;

    /**
     * @var string Short urls
     */
    private $shorturl;
    private $shorturl_local;
    private $shorturl_dev;

    /**
     * Class Constructor.
     */
    public function __construct()
    {

        // Wordpress Database Object
        global $wpdb;
        $this->wpdb = $wpdb;

        $this->base_url = $this->get_base_url();

        // Short URLs
        $this -> shorturl       = _SHORTURL;
        $this -> shorturl_local = _SHORTURL_LOCAL;
        $this -> shorturl_dev   = _SHORTURL_DEV;
    }

    /**
     * Get a short url for the given params.
     *
     * @param int $fid The fundraiser ID
     * @param int $uid The user ID
     * @param string $channel The invite channel
     *
     * @return string $url
     */
    public function get( $fid, $uid, $channel, $parent = 0, $utm_code )
    {      
        // Need $fid at minimum
        if ( empty($fid) ) {         
            throw new \Exception("Missing fundraiser ID, Coming URL is " . $actual_link);
        }

        // If $uid is not a user, set a default
        if ( empty($uid) ) {
            $uid = 0;
        }

        // If channel is missing, set a default
        if ( empty($channel) ) {
            $channel = '';
        }

        // Check for an existing code
        $code_exists = $this->exists_by_params($fid, $uid, $channel, $parent, $utm_code);

        if ( !empty($code_exists) ) {
            // Use the existing code
            $url = $this->get_base_url() . $code_exists;

            return $url;
        } else {
            // Create a new code
            $code = $this->create($fid, $uid, $channel, $parent, $utm_code);
            $url = $this->get_base_url() . $code;
            $insert = $this->insert($code, $fid, $uid, $channel, $parent, $utm_code);
            if ( $insert ) {
                return $url;
            } else {
                throw new \Exception('Failed to insert code');
            }
        }
    }

    /**
     * Create a Wefund4u Short URL.
     * @return string $code
     */
    private function create()
    {
        $unused_code = false;
        $found_code = false;
        $max_tries = 50000; // Max tries

        $x = 0;
        while ( $unused_code == false ) {

            if ( $x == $max_tries ) {
                return false;
            }

            // Generate a code
            $code = $this->generate_code();

            if ( $this->exists_by_code($code) == false ) {
                $unused_code = true; // We found a code
            }

            $x++;
        }
        return $code;

    }

    /**
     * Check if the generated shortcode exists.
     *
     * @param int $fid The fundraiser ID
     * @param int $uid The user ID
     * @param string $channel The invite channel
     *
     * @return array $results
     */
    private function exists_by_params( $fid, $uid, $channel = '', $parent = 0, $utm_code )
    {
        // See if the short code exists
        $results = $this->wpdb->get_results("SELECT * FROM `{$this->table}` WHERE `fid` = '{$fid}' AND `uid` = '{$uid}' AND `channel` = '{$channel}' AND `parent` = '{$parent}' AND `utm_source` = '{$utm_code['source']}' AND `utm_medium` = '{$utm_code['medium']}' AND `utm_campaign` = '{$utm_code['campaign']}' AND `utm_content` = '{$utm_code['content']}' AND `utm_term` = '{$utm_code['term']}' LIMIT 1", ARRAY_A);


        if ( $results != false ) {
            return $results[0]['code'];
        }

        return false;
    }

    /**
     * Check if the generated shortcode exists.
     * @param string $code
     * @return array $results
     */
    private function exists_by_code( $code )
    {
        // See if the short code exists
        $results = $this->wpdb->get_results("SELECT * FROM `{$this->table}` WHERE `code` = '{$code}' LIMIT 1", ARRAY_A);

        if ( $results != false ) {
            return true;
        }

        return false;
    }

    /**
     * Check if the generated shortcode exists.
     * @param string $code
     * @return array $results
     */
    public function lookup_code( $code )
    {
        // See if the short code exists
        $results = $this->wpdb->get_results("SELECT * FROM `{$this->table}` WHERE `code` = '{$code}' LIMIT 1", ARRAY_A);

        if ( $results != false ) {
            return $results;
        }

        return false;
    }

    /**
     * Insert the record.
     *
     * @param string $code The 6 character code shorturl code
     * @param int $fid The fundraiser ID
     * @param int $uid The user ID
     * @param string $channel The invite channel
     * @param int $parent Is this a parent invite?
     *
     * @return bool
     */
    private function insert( $code, $fid, $uid, $channel, $parent, $utm_code )
    {
        // Insert the record
        $insert = $this->wpdb->insert($this -> table,
            array(
                'code' => $code,
                'fid' => $fid,
                'uid' => $uid,
                'channel' => $channel,
                'parent' => $parent,
                'utm_source' => $utm_code['source'],
                'utm_medium' => $utm_code['medium'],
                'utm_campaign' => $utm_code['campaign'],
                'utm_content' => $utm_code['content'],
                'utm_term' => $utm_code['term']
                //created_at
            ),
            array('%s', '%d', '%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
        );

        // Return the results
        if ( empty($insert) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Generate a 6 character alphanumeric code including uppercase and lowecase characters.
     *
     * Number of possible codes:
     * 62^6= 56800235584
     * 62^5= 916132832
     * 62^4= 14776336
     * 62^3= 238328
     * 62^2= 3844
     * 62^1= 62
     * Total = 57,731,386,986
     *
     * @return string
     */
    private function generate_code()
    {
        $length = $this->length;

        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        for ( $i = 0; $i < $length; $i++ ) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;

    }

    /**
     * Define the shorturl domain based on the server environment.
     * @return string The correct shorturl domain
     */
    private function get_base_url()
    {

        if ( _SERVER_TYPE == 'dev' ) {
            if ( _IS_LOCAL_DEV == true ) {
                return $this->shorturl_local;
            }
            return $this->shorturl_dev;
        }
        return $this->shorturl;
    }

}