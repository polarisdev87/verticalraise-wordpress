<?php

/**
 * Template Name: Participants Invite - SMS Share
 * @name SMS Invite page
 * @description The participant can enter potential donors' phone numbers and send them an sms invitiation to donate to the fundraiser.
 */
use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object

/**
 * Load Classes.
 */
load_class( 'invite_wizard.class.php' );
load_class( 'participant_records.class.php' );
load_class( 'sharing.class.php' );
load_class( 'page.invite_sms.class.php' );
load_class( 'invite_sms/previously_sent.class.php' );

/**
 * Instantiate Classes.
 */
get_header( 'invite' );
$p_invite_wizard     = new Invite_Wizard();                    // Parent Invite Wizard class object
$participant_records = new Participant_Sharing_Totals();              // Participant Sharing Totals class object
$sharing             = new Sharing();                                 // Sharing class object
// Define user ID
$user_ID             = $sharing->user_ID;
$fundraiser_ID       = $sharing->fundraiser_ID;                    // Define fundraiser ID
$previously_sent     = new Previously_Sent( $user_ID, $fundraiser_ID ); // Previously Sent class object

$single = false;
if ( isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single' ) {
    $user_ID = $_GET['uid'];
    $single  = true;
}

/**
 * Process the Form.
 */
$sharing_type = ($single) ? 'spread_word' : null;

if ( isset( $_GET['page'] ) && $_GET['page'] == 'thankyou' ) {
    $share_type = 1;
}else{
    $share_type = 0;
}
$invite_sms      = new Page_Invite_SMS( $user_ID, $fundraiser_ID, $sharing_type, $share_type );
$results         = $invite_sms->process_form();
/**
 * Output results
 */
$nonvalid        = 0;
$invalid_numbers = array();
$valid_numbers   = array();

if ( !empty( $results['invalid_numbers'] ) ) {
    $nonvalid        = 1;
    $invalid_numbers = $results['invalid_numbers'];
}

if ( !empty( $results['valid_numbers'] ) ) {
    $nonvalid      = 0;
    $valid_numbers = $results['valid_numbers'];
}

$template_directory = get_template_directory_uri();

$fundraiser_name = get_the_title( $fundraiser_ID );

if ( is_mobile_new() ) {
    $default_size = 150;
} else {
    $default_size = 282;
}

?>
<script>
    jQuery(document).ready(function () {
        jQuery('textarea[name="numbers"]').focus();
    });
</script>
<script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/clipboard.min.js"></script>
<script src="<?php echo $template_directory; ?>/assets/js/invite_sms.footer.js"></script>

<?php while ( have_posts() ) : the_post(); ?>
    <main>
        <?php if ( $single ) { ?>

            <div class="modal invite_step tnx_modal tnx_e" id="invite_step" data-backdrop="static" tabindex="-1" role=""
                 aria-labelledby="" aria-hidden=""
                 style="display: block;">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header model_title">
                            <h3>share by text message</h3>
                            <p>Help spread the word by texting your friends and family. <br>
                                For best results send at least <?php echo _SUGGESTED_EMAIL_AMOUNT; ?></p>
                            <br>
                            <br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="javascript:parent.$.fancybox.close();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png" alt="">
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php
                            if ( is_mobile_new() ) {
                                // Output the contact import button
                                $invite_sms->contact_import_button();
                                echo "<hr>";
                                echo "<div style='color: #ffffff;'><center>OR<br>Copy link and paste into text message</center></div>";
                                // Output the copy url button
                                $invite_sms->copy_message_button();
                                echo "<br>";
                            } else {
                                ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="_form_nonce" value="<?php echo $invite_sms->generate_nonce(); ?>">
                                    <?php
                                    $user_nonce = $invite_sms->generate_user_nonce( $user_ID );
                                    if ( !empty( $user_nonce ) ) {
                                        ?>
                                        <input type="hidden" name="e_key" value="<?php echo $user_nonce; ?>">
                                    <?php } ?>

                                    <input type="text" name="your_name" id="your_name" placeholder="Your Name*" class="form-control ip" required>
                                    <textarea name="numbers" placeholder="Enter or paste phone numbers here" class="form-control" required></textarea>
                                    <?php
                                    if ( !empty( $invalid_numbers ) ) {
                                        echo "<p style='color: red;font-weight:400'>WARNING!<br/>The number in red were not delivered because they were invalid. You may correct them and re-enter above. </p>";
                                        echo "<p style='color: #f91717;height:100px;overflow:auto'>";
                                        foreach ( $invalid_numbers as $i_number ) {
                                            echo "<strong class='invalidEmail'> {$i_number} </strong>";
                                        }
                                        echo "</p>";
                                    }

                                    if ( !empty( $valid_numbers ) ) {
                                        echo "<p style='color: #7de078;font-weight:400'>The invite was successfully sent.</p>";
                                        echo "<p>";
                                        foreach ( $valid_numbers as $i_number ) {
                                            echo "<strong class='invalidEmail'> {$i_number} </strong>";
                                        }
                                        echo "</p>";
                                    }
                                    ?>
                                    <input type="hidden" name="invite_submit" value="Send"/>
                                    <button type="submit" id="inviteSentBtn" class="submit_btn btn has-spinner" data-loading="Sending...">Send
                                    </button>
                                </form>
                            <?php } ?>
                        </div>
                        <div class="modal-footer">
                            <?php
                            if ( isset( $_GET['action'] ) && $_GET['action'] == 'spread' ) {
                                
                            } else {
                                ?>
                                <?php include_once( get_template_directory() . '/prev_next_buttons.php' ); ?>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="modal invite_step " id="invite_step" data-backdrop="static" tabindex="-1" role=""
                 aria-labelledby="" aria-hidden=""
                 style="display: block;">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header model_title">
                            <h4>Send text messages</h4>
                           
                            <br>
                            <br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:parent.$.fancybox.close();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                                     alt="">
                            </button>

                        </div>
                        <div class="modal-body">
                            <?php
                            if ( is_mobile_new() ) {
                                // Output the contact import button
                                $invite_sms->contact_import_button();
                                echo "<hr>";
                                echo "<div style='color: #ffffff;'><center>OR<br>Copy link and paste into text message</center></div>";
                                // Output the copy message button
                                $invite_sms->copy_message_button();
                                echo "<br>";
                            } else {
                                ?>
                                <form method="POST" action="">

                                    <input type="hidden" name="_form_nonce"
                                           value="<?php echo $invite_sms->generate_nonce(); ?>">

                                    <?php
                                    $user_nonce = $invite_sms->generate_user_nonce( $user_ID );
                                    if ( !empty( $user_nonce ) ) {
                                        ?>
                                        <input type="hidden" name="e_key" value="<?php echo $user_nonce; ?>">
                                    <?php } ?>

                                    <?php if ( isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single' ) { ?>

                                        <input type="text" name="your_name" id="your_name" placeholder="Your Name*" class="form-control ip" required>
                                    <?php } ?>

                                    <textarea name="numbers" class="form-control" required
                                              placeholder="Enter or paste phone numbers here"></textarea>

                                    <?php
                                    if ( !empty( $invalid_numbers ) ) {
                                        echo "<p style='color: red;font-weight:400'>WARNING!<br/>The number in red were not delivered because they were invalid. You may correct them and re-enter above. </p>";
                                        echo "<p style='color: #f91717;height:100px;overflow:auto'>";
                                        foreach ( $invalid_numbers as $i_number ) {
                                            echo "<strong class='invalidEmail'> {$i_number} </strong>";
                                        }
                                        echo "</p>";
                                    }

                                    if ( !empty( $valid_numbers ) ) {
                                        echo "<p style='color: #7de078;'>The invite was successfully sent.</p>";
                                        echo "<p>";
                                        foreach ( $valid_numbers as $i_number ) {
                                            echo "<strong class='invalidEmail'> {$i_number} </strong>";
                                        }
                                        echo "</p>";
                                    }
                                    ?>
                                    <input type="hidden" name="invite_submit" value="Send"/>
                                    <button type="submit" id="inviteSentBtn" class="submit_btn btn has-spinner"
                                            data-loading="Sending...">Send
                                    </button>
                                </form>
                            <?php } ?>
                            <?php
                            if ( isset( $_GET['action'] ) && $_GET['action'] == 'spread' ) {
                                
                            } else {
                            ?>
                                <?php include_once( get_template_directory() . '/prev_next_buttons.php' ); ?>
                            <?php } ?>
                        </div>
                        <div class="modal-footer">
                            <div class="total_sent">
                                <?php $previously_sent->init( $results ); ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <script>
            jQuery('.next_prev_buttons a').click(function (e) {
                var i = 0;
                jQuery('textarea[name^="numbers"]').each(function () {
                    var valid;
                    valid = jQuery(this).val();
                    if ( valid != '' ) {
                        i++;
                    }
                });
                if ( i > 0 ) {
                    e.preventDefault();
                    alert('You must click the "SEND" button to send the invites out before you can go to the next page.');
                }
            });
            var clipboard = new ClipboardJS('.copy-button');
            clipboard.on('success', function(e) {
                $('.copy-button span').text('Copied!');
                console.log(e);
            });

            clipboard.on('error', function(e) {
                console.log(e);
            });
        </script>
    </main>
<?php endwhile;