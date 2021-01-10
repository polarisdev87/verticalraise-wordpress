<?php
/* Template Name: Activation Template */

use classes\app\emails\Custom_Mail;

get_header();
?>
<div id="title">
    <div class="maincontent">
        <div class="section group">
            <div class="col span_12_of_12"> 
                <h1><?php the_title(); ?></h1>                
            </div>
        </div>
    </div>
</div>
<div id="content">
    <div class="maincontent noPadding">
        <div class="section group">
            <div class="col span_12_of_12">
                <?php
                if ( !is_user_logged_in() ) {
                    global $wpdb;
                    $user_status = 2;
                    $key         = $_GET['key'];
                    $wpdb->update( $wpdb->users, array( 'user_status' => $user_status ), array( 'user_activation_key' => $key ) );
                    // mail to user
                    $user_data   = $wpdb->get_row( $wpdb->prepare( "SELECT ID  FROM $wpdb->users WHERE user_activation_key = %s", $key ) );
                    $user_info   = get_userdata( $user_data->ID );
                    
                    $mail    = new Custom_Mail();
                    // Setup Vars for the email
                    $to      = $user_info->user_email;
                    $from    = _CRON_FROM_EMAIL;
                    $cc      = null;
                    $subject = "Your Account with VerticalRaise.com";

                    $template_args = [
                        'EMAIL'              => $to,
                        'DISPLAY_NAME'       => $user_info->display_name,
                        'LOGIN_URL'          => get_bloginfo( 'url' ) . "/?login",
                        'TEMPLATE_DIRECTORY' => get_template_directory_uri()
                    ];
                    // Send the emaail
                    $mail->send_api( $to, $from, $cc, $subject, 'activated', $template_args );
                 
                    echo '<p class="successMsg"><strong>Congratulations</strong><br /><br />Your account has been activated. Click here to <strong><a href="' . get_bloginfo( 'url' ) . '/?login">Sign In</a></strong></p>';
                }
                ?>   
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
