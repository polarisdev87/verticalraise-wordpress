<?php /* Template Name: Participants Invite - Page */ ?>
<?php if(is_user_logged_in()) { ?>
<?php get_header(); ?>
<?php
global $user_ID;
$campaign_participations = get_user_meta($user_ID, 'campaign_participations', true);
$participations_array = json_decode($campaign_participations);
if (!empty($participations_array)) {
    if (!in_array($user_ID, $participations_array)) {
        $uid = '/' . $user_ID;
    }
}
?>
    <div id="content">
        <div class="maincontent">
            <div class="section group">
                <div class="col span_12_of_12">
                    <div class="container">
                        <?php while (have_posts()) : the_post(); ?>
                            <h1><?php the_title(); ?></h1>
                            <div><?php the_content(); ?></div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $post_id = $_GET['fundraiser_id']; ?>
<?php
if ($_POST['invite_submit']) {
    $user_info = get_userdata($user_ID);
    $i = 0;
    require TEMPLATEPATH . "/twilio-php-master/Services/Twilio.php";
    $AccountSid = "AC037719c74be43e6c9d6fda5cd21deaf3";
    $AuthToken = "49373418695f4059a546c5aab15a8cfc";
    $client = new Services_Twilio($AccountSid, $AuthToken);


    foreach ($_POST['invite_name'] as $name) {
        if (!empty($name) && !empty($_POST['invite_email'][$i])) {
            $to = $_POST['invite_email'][$i];
            $from = _TRANSACTIONAL_FROM_EMAIL;
            $from1 = $user_info->user_email;
            $headers = 'From: ' . $from . "\r\n";
            $headers .= 'Reply-to: ' . $from1 . "\r\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            $subject = get_the_title($post_id);
            if (get_field('enable_custom_email', $post_id) == 1) {
                $msg = get_field('email_invitation_template', $post_id);
                $msg = str_replace("{name}", $name, $msg);
                $msg = str_replace("{User Name}", $user_info->display_name, $msg);
                $msg = str_replace("{Fundraiser Name}", get_the_title($post_id), $msg);
                $invite_link = '<a href="' . get_the_permalink($post_id) . 'email/' . $user_ID . '">' . get_the_permalink($post_id) . 'email/' . $user_ID . '</a>';
                $msg = str_replace("{Invite Link}", $invite_link, $msg);
            } else {
                echo $msg = '<p>From ' . $user_info->display_name . ' – please support ' . get_the_title($post_id) . '. Even the smallest donation will make a difference. <a href="' . get_the_permalink($post_id) . 'email/' . $user_ID . '">' . get_the_permalink($post_id) . 'email/' . $user_ID . '</a></p>';
            }
            wp_mail($to, $subject, $msg, $headers);
            $msg = '';
            $email_share = json_decode(get_post_meta($post_id, 'email_share', true), true);
            if (empty($email_share)) {
                $email_share = array();
                $email_share['total'] = 1;
                $email_share['user_array'] = array();
                $user_array = array();
                $user_array['uid'] = $user_ID;
                $user_array['total'] = 1;
                array_push($email_share['user_array'], $user_array);
                update_post_meta($post_id, 'email_share', json_encode($email_share));
            } else {
                $flag = 0;
                $email_share['total'] = $email_share['total'] + 1;
                foreach ($email_share['user_array'] as $key => $user_array) {
                    if ($user_array['uid'] == $user_ID) {
                        $email_share['user_array'][$key]['total'] = $user_array['total'] + 1;
                        $flag = 1;
                    }
                }
                if ($flag == 0) {
                    $user_array = array();
                    $user_array['uid'] = $user_ID;
                    $user_array['total'] = 1;
                    array_push($email_share['user_array'], $user_array);
                }
                update_post_meta($post_id, 'email_share', json_encode($email_share));
            }

            $potential_donors = json_decode(get_post_meta($post_id, 'potential_donors', true));
            array_push($potential_donors, $to);
            update_post_meta($post_id, 'potential_donors', json_encode($potential_donors));
        }
        if (!empty($name) && !empty($_POST['invite_phone'][$i])) {
            $phone = $_POST['invite_phone'][$i];
            $msg1 .= "Hi, " . $name . "\n";
            $msg = '<p>From ' . $user_info->display_name . ' – please support ' . get_the_title($post_id) . '. Even the smallest donation will make a difference. <a href="' . get_the_permalink($post_id) . 'email/' . $user_ID . '">' . get_the_permalink($post_id) . 'email/' . $user_ID . '</a></p>';

            $message = $client->account->messages->sendMessage(
                _TWILIO_FROM_NUMBER, // From a valid Twilio number
                $phone, // Text this number
                $msg1
            );

            //print $message->sid;
            $msg1 = '';
            $sms_share = json_decode(get_post_meta($post_id, 'sms_share', true), true);
            if ( empty($sms_share) ) {
                $sms_share = array();
                $sms_share['total'] = 1;
                $sms_share['user_array'] = array();
                $user_array = array();
                $user_array['uid'] = $user_ID;
                $user_array['total'] = 1;
                array_push($sms_share['user_array'], $user_array);
                update_post_meta($post_id, 'sms_share', json_encode($sms_share));
            } else {
                $flag = 0;
                $sms_share['total'] = $sms_share['total'] + 1;
                foreach ($sms_share['user_array'] as $key => $user_array) {
                    if ($user_array['uid'] == $user_ID) {
                        $sms_share['user_array'][$key]['total'] = $user_array['total'] + 1;
                        $flag = 1;
                    }
                }
                if ( $flag == 0 ) {
                    $user_array = array();
                    $user_array['uid'] = $user_ID;
                    $user_array['total'] = 1;
                    array_push($sms_share['user_array'], $user_array);
                }
                update_post_meta($post_id, 'sms_share', json_encode($sms_share));
            }
        }
        $i++;
    }
    header("Location: " . get_permalink(221) . "?fundraiser_id=" . $_GET['fundraiser_id']);
}
?>
    <div id="content">
        <div class="maincontent">
            <div class="section group">
                <div class="col span_12_of_12">
                    <div class="invite">
                        <form method="POST" action="">
                            <table>
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Name:</th>
                                    <th>Email Address:</th>
                                    <th>Phone:</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php for ($i = 1; $i <= 40; $i++) { ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><input type="text" placeholder="Name" name="invite_name[]"/></td>
                                        <td><input type="email" placeholder="Email" name="invite_email[]"/></td>
                                        <td><input type="tel" placeholder="Please provide the country code"
                                                   name="invite_phone[]"/></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <p></p>
                            <hr/>
                            <p></p>

                            <p class="successMsg" style="margin: 15px; padding: 15px 10px 15px 50px;">Tip: Use your
                                personal email account to invite unlimited contacts. Copy and paste the campaign link
                                below and visit your personal email account
                                now...<br/><br/><?php echo get_permalink($_GET['fundraiser_id']); ?>
                                email/<?php echo $user_ID; ?></p>

                            <p style="text-align: center;"><input style="font-size: 26px; padding: 15px 25px;"
                                                                  type="submit" name="invite_submit"
                                                                  value="Invite Contacts"/></p>
                        </form>
                        <div id="loading" style="text-align: center; display: none;">
                            <img src="<?php bloginfo('template_directory'); ?>/assets/images/loading_spinner.gif"/>
                        </div>
                        <p style="text-align: center;">After clicking this button please be patient as the system is
                            sending out your requests</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            jQuery(".invite form").submit(function (event) {
                jQuery('.invite form').hide();
                jQuery('#loading').show();
            });
        });
    </script>
<?php get_footer(); ?>
<?php } else {
    header('Location: '.get_bloginfo('url').'/login');
} ?>