<?php

namespace classes\app\previously_sent;

use \classes\models\mixed\Previously_Sent as P_Sent;

class Previously_Sent
{

    /**
     * Class Variables.
     */
    private $user_id;
    private $fundraiser_id;

    /**
     * Class Constructor.
     */
    public function __construct( $user_ID, $fundraiser_ID ) {
        $this->user_id       = $user_ID;
        $this->fundraiser_id = $fundraiser_ID;
        $this->template_url  = get_template_directory_uri();
    }

    /**
     * Main function to process and output results.
     * @param array $results
     */
    public function init( $results ) {

        // Determine type
        $type = (empty( $_GET['display_type'] )) ? 'invite_wizard' : 'spread_the_word';

        // Output
        $this->display( $type, $results );
    }

    /**
     * Previously sent.
     * @param string $type Users or visitors
     * @param array $emails Array of emails
     */
    private function type( $type = null, $emails = null ) {

        switch ( $type ) {
            case 'invite_wizard':
                // User logged in
                return $this->invite_wizard();
                break;
            case 'spread_the_word':
                // Spread the Word (Public user)
                return $this->spread_the_word( $emails );
                break;
        }
    }

    /**
     * Previously sent for users.
     * @param array $potential_donors
     */
    private function invite_wizard() {
        $user_ID = $this->user_id;

        $p_sent           = new P_Sent( $this->fundraiser_id, $this->user_id );
        $p_sent_records   = $p_sent->get_all();
        $potential_donors = (!empty( $p_sent_records[$this->fundraiser_id] )) ? $p_sent_records[$this->fundraiser_id] : null;

        ob_start();
        echo "<p>Previously Sent Emails:</p>";

        if ( !empty( $potential_donors ) ) {

            $i = 0;
            echo "<b>" . count( $potential_donors ) . "</b>";
            echo "<img class=\"double_arrow\" src=\"{$this->template_url}/assets/images/double-arrow.png\" width=\"32\" height=\"32\">";
            echo "<div class='inviteEmailList'>";
            echo "<ul>";
            foreach ( $potential_donors as $pd ) {
                echo "<li><div class='inviteEmailListBlock'>{$pd}</div></li>";
                $i++;
            }
            echo "</ul></div>";

            // No records for our user were found
            if ( $i == 0 ) {
                echo "<b>";
                echo "You have not sent any emails yet.";
                echo "</b>";
            }
        } else {
            echo "<b>";
            echo "You have not sent any emails yet.";
            echo "</b>";
        }

        $contents = ob_get_clean();
        return $contents;
    }

    /**
     * Previously sent for visitors.
     * @param array $invalid_emails
     */
    private function spread_the_word( $results ) {

        session_start();

        $_emails        = $results['emails']; // Get the emails
        $invalid_emails = $results['invalid_emails']; // Invalid emails

        $emails = $this->remove_invalid_emails( $_emails, $invalid_emails ); // Remove invalid emails
        // the email array might be reduced to nothing if all emails were invalid
        if ( !empty( $emails ) ) {
            $_SESSION['emails_array'][] = $emails;
        }

        $output_emails = (empty( $_SESSION['emails_array'] )) ? '' : $_SESSION['emails_array'];

        return $this->output( $output_emails ); // Output the html
    }

    /**
     * Output html for previously sent emails.
     * @param  array $emails The previously sent emails.
     * @return string Ob cleaned html.
     */
    private function output( $emails = null ) {
        ob_start();
        echo "<p>Previously Sent Emails:</p>";
        if ( !empty( $emails ) ) {
            echo "<b>" . count( $emails ) . "</b>";
            echo "<img class=\"double_arrow\" src=\"{$this->template_url}/assets/images/double-arrow.png\" width=\"32\" height=\"32\">";
            echo "<div class='inviteEmailList'>";
            echo "<ul>";
            foreach ( $emails as $email ) {
                foreach ( $email as $em ) {
                    echo "<li><div class='inviteEmailListBlock'>" . strtolower( $em ) . "</div></li>";
                }
            }
            echo "</ul></div>";
        } else {
            echo "<b>";
            echo "You have not sent any emails yet.";
            echo "</b>";
        }

        $contents = ob_get_clean();
        return $contents;
    }

    /**
     * Remove invalids emails from the active email array.
     *
     * @param array $emails_array An array of our submitted email addresses.
     * @param array $invalid_emails An array of our invalid submitted email addresses.
     *
     * @return array An array of our active emails with invalid emails removed.
     */
    private function remove_invalid_emails( $emails_array, $invalid_emails ) {

        // Remove invalid emails from $emails_array
        if ( !empty( $invalid_emails ) ) {
            foreach ( $invalid_emails as $invalid_email ) {
                $key = array_search( strtolower( $invalid_email ), $emails_array );
                unset( $emails_array[$key] );
            }
        }

        return $emails_array;
    }

    /**
     * Display the output.
     * @param string $type
     * @param array $results
     */
    private function display( $type, $results ) {
        echo $this->type( $type, $results );
    }

}
