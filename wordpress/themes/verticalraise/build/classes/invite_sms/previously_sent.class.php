<?php

class Previously_Sent
{

    public function __construct( $user_ID, $fundraiser_ID )
    {
        $this->user_id = $user_ID;
        $this->fundraiser_id = $fundraiser_ID;
        $this->template_url = get_template_directory_uri();
    }

    public function init( $results )
    {

        $type = (empty($_GET['display_type'])) ? 'invite_wizard' : 'spread_the_word';
        echo $this -> type($type, $results);
    }

    /**
     * Previously sent.
     * @param string $type Users or visitors
     * @param array $emails Array of emails
     */
    private function type( $type = null, $emails = null )
    {

        switch ( $type ) {
            case 'invite_wizard':
                // User logged in
                return $this -> invite_wizard();
                break;
            case 'spread_the_word':
                // Spread the Word (Public user)
                return $this -> spread_the_word($emails);
                break;
        }

    }

    /**
     * Previously sent for users.
     * @param array $potential_donors
     */
    private function invite_wizard()
    {
        $user_ID = $this -> user_id;
        $potential_donors = json_decode(get_post_meta($this -> fundraiser_id, 'potential_donors_sms_array', true));

        ob_start();
        if ( !is_mobile_new() ) {
            $lists='';
            echo "<p>Previously Sent Texts:</p>";

            if ( !empty($potential_donors) ) {

                $i = 0;
                $lists .= "<img class=\"double_arrow\" src=\"{$this->template_url}/assets/images/double-arrow.png\" width=\"32\" height=\"32\">";
                $lists .= "<div class='inviteEmailList'>";
                $lists .= "<ul>";
                foreach ( $potential_donors as $pd ) {
                    if ( $pd[0] == $user_ID ) {
                        $lists .= "<li><div class='inviteEmailListBlock'>{$pd[1]}</div></li>";
                        $i++;
                    }
                }
                $lists .= "</ul></div>";

                // No records for our user were found

                if ( $i == 0 ) {
                    echo "<b>";
                    echo "You have not sent any texts yet.";
                    echo "</b>";
                } else {
                    echo "<b>" . $i . "</b>";
                    echo $lists;
                }

            } else {

                echo "<b>";
                echo "You have not sent any texts yet.";
                echo "</b>";

            }
        } else {
            echo "";
        }

        $contents = ob_get_clean();
        return $contents;
    }

    /**
     * Previously sent for visitors.
     * @param array $invalid_emails
     */
    private function spread_the_word( $results )
    {

        session_start();

        $_numbers = $results['numbers_array']; // Get the emails
        $invalid_numbers = $results['invalid_numbers']; // Invalid emails

        $numbers = $this -> remove_invalid_numbers($_numbers, $invalid_numbers); // Remove invalid emails

        // the email array might be reduced to nothing if all emails were invalid
        if ( !empty($numbers) ) {
            $_SESSION['numbers_array'][] = $numbers;
        }

        $output_numbers = (empty($_SESSION['numbers_array'])) ? '' : $_SESSION['numbers_array'];

        echo $this -> output($output_numbers); // Output the html
    }

    /**
     * Output html for previously sent emails.
     * @param  array $emails The previously sent emails.
     * @return string Ob cleaned html.
     */
    private function output( $emails = null )
    {
        ob_start();

        if ( !is_mobile_new() ) {
            echo "<p>Previously Sent Texts:</p>";

            if ( !empty($emails) ) {

                echo "<b>" . count($emails) . "</b>";
                echo "<img class=\"double_arrow\" src=\"{$this->template_url}/assets/images/double-arrow.png\" width=\"32\" height=\"32\">";
                echo "<div class='inviteEmailList'>";
                echo "<ul>";
                foreach ( $emails as $email ) {
                    foreach ( $email as $em ) {
                        echo "<li><div class='inviteEmailListBlock'>" . strtolower($em) . "</div></li>";
                    }
                }
                echo "</ul></div>";

            } else {

                echo "<b>";
                echo "You have not sent any texts yet.";
                echo "</b>";

            }
        } else {
            echo "";
        }

        $contents = ob_get_clean();
        return $contents;
    }

    /**
     * Remove invalids emails from the active email array.
     * @param array $emails_array An array of our submitted email addresses.
     * @param array $invalid_emails An array of our invalid submitted email addresses.
     * @return array An array of our active emails with invalid emails removed.
     */
    private function remove_invalid_numbers( $numbers_array, $invalid_numbers )
    {

        // Remove invalid emails from $emails_array
        if ( !empty($invalid_numbers) ) {
            foreach ( $invalid_numbers as $invalid_number ) {
                $key = array_search(strtolower($invalid_number), $numbers_array);
                unset($numbers_array[$key]);
            }
        }

        return $numbers_array;
    }

}