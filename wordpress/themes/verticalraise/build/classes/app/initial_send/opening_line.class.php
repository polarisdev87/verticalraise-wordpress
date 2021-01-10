<?php

namespace classes\app\initial_send;

class Opening_Line
{

    /**
     * Construct an opening line for the email being sent based on the fundraiser and user objects.
     *
     * @param object $fundraiser
     * @param object $user
     *
     * @return string The opening line
     */
    public function get( $fundraiser, $user, $template_type ) {
        $params = $this->set_params( $fundraiser, $user );

        $type = $this->get_type( $params );
        $line = $this->get_line( $params, $type, $template_type );

        return $line;
    }

    /*
     * additional get opening line for the cron invite email and donors  email
     */

    public function get_opening_line1( $fundraiser, $user, $type ) {
        $params = $this->set_params( $fundraiser, $user );
        $line   = $this->get_line( $params, $type );

        return $line;
    }

    public function get_donors_opening_line( $fundraiser, $user, $temp_name ) {
        $params = $this->set_params( $fundraiser, $user );
        $line   = $this->get_line_potential( $params, $temp_name );

        return $line;
    }

    /**
     * Set params.
     *
     * @param object $fundraiser
     * @param object $user
     *
     * @return object
     */
    private function set_params( $fundraiser, $user ) {
        $params = [
            'user_name' => $user->user_name,
            'title'     => $fundraiser->title,
            'user_id'   => $user->id,
            'author_id' => $fundraiser->author_id,
            's_admins'  => $fundraiser->s_admins,
            'f_id'      => $fundraiser->id
        ];

        return (object) $params;
    }

    /**
     * Get the opening line for the email.
     * @param object $data
     * @retun int    $type
     */
    private function get_type( $data ) {
        /**
         * Opening Lines: Set the opening line for the spread the word based on user id and their user type
         * 1. User ID is 0, blank or an author
         * 2. User ID is a secondary admin
         * 3. User ID exists
         */
        // User ID is 0, blank or an author
        if ( empty( $data->user_id ) || $data->author_id == $data->user_id ) {

            return 1; // Type 1
        }

        // User ID is a secondary admin
        else if (
                (!empty( $data->s_admins ) && in_array( $data->user_id, $data->s_admins ) ) ) {

            return 3; // Type 3
        }

        // User ID exists - regular user
        else {

            return 2; // Type 2
        }
    }

    /**
     * Return the opening line.
     */
    private function get_line( $data, $type, $template_type ) {
        $line = '';
        switch ( $type ) {

            // Type 1
            case 1:
                $line = "Please help support our {$data->title} by donating or sharing. Thank you so much for your support.";
                break;

            // Type 2
            case 2:
                if ( $template_type == "2" ) {
                    $line = "Help me reach my goal of raising &#36;" . _PARTICIPATION_GOAL . " for the {$data->title}. Please help by donating or sharing. Thank you for your support!";
                } else {
                    $line = "Help {$data->user_name} reach their goal of raising &#36;" . _PARTICIPATION_GOAL . " for the {$data->title}. Please help by donating or sharing. Thank you for your support!";
                }

                break;

            // Type 3
            case 3:
                $line = "Please help support our {$data->title} by donating or sharing. Thank you for your support.";
                break;
        }

        return $line;
    }

    /**
     * Additional get_donor_opening_line
     */

    private function get_line_potential( $data, $temp_name ) {
        switch ( $temp_name ) {

            // template name
            case 'potential_donors_1_day_admin':
                return "There is only one day left in our {$data->title}. Please help by donating or sharing. Thank you for your support!";
                break;

            case 'potential_donors_1_day':
                return "There is only one day left in our {$data->title}. Help me reach my goal of raising <b>&#36;" . _PARTICIPATION_GOAL . "</b> by donating and sharing. Thank you for your support!";
                break;

            case 'potential_donors_14_day_admin':
                return "There are two weeks left in our {$data->title}. Please help by donating or sharing. Thank you for your support!";
                break;

            case 'potential_donors_14_day':
                return "There are two weeks left in our {$data->title}. Help me reach my goal of raising <b>&#36;" . _PARTICIPATION_GOAL . "</b> by donating and sharing. Thank you for your support!";
                break;
            case 'potential_donors_7_day_admin':
                return "There is only one week left in our {$data->title}. Please help by donating or sharing. Thank you for your support!";
                break;

            case 'potential_donors_7_day':
                return "There is only one week left in our {$data->title}. Help me reach my goal of raising <b>&#36;" . _PARTICIPATION_GOAL . "</b> by donating and sharing. Thank you for your support!";
                break;
            case 'potential_donors_3_day_admin':
                return "3 days into our {$data->title}.  Please help by donating or sharing. Thank you so much for your support!";
                break;

            case 'potential_donors_3_day':
                return "3 days into our {$data->title}. Help me reach my goal of raising <b>&#36;" . _PARTICIPATION_GOAL . "</b>. Thank you so much for your support!";
                break;
            case 'potential_donors_2_day_admin':
                return "Only <u><span style='color: #ff0000;'>ONE DAY</span></u> left in our {$data->title}. Please help by donating or sharing. Thank you so much for your support!";
                break;

            case 'potential_donors_2_day':
                return " Only <u><span style='color: #ff0000;'>ONE DAY</span></u> left in our {$data->title}. Help me reach my goal of raising <b>&#36;" . _PARTICIPATION_GOAL . "</b>. Thank you so much for your support!";
                break;
        }
    }

}
