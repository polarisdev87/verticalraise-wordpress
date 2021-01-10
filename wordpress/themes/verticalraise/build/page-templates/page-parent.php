<?php

/**
 * Template Name: Participants Invite - Parent Share
 * @name Parent Invite Page
 * @description The participant can enter their parents' cell phone number(s) and invite them to the "Parent Invite Wizard".
 */
use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object

/**
 * Load classes.
 */
load_class( 'invite_wizard.class.php' );
load_class( 'participants.class.php' );
load_class( 'sharing.class.php' );
load_class( 'page.invite_parent.class.php' );

/**
 * Include Theme Header.
 */
get_header( 'invite' );

/**
 * Instantiate classes.
 */
$p_invite_wizard = new Invite_Wizard();                              // Parent Invite Wizard class object

$sharing       = new Sharing();                                      // Sharing class object
$user_ID       = $sharing->user_ID;                                  // Define user ID
$fundraiser_ID = $sharing->fundraiser_ID;                            // Define fundraiser ID

$participants  = new Participants();                                 // Participant records class object
$invite_parent = new Page_Invite_Parent( $user_ID, $fundraiser_ID ); // Invite Parent class object

$single = false;

if ( isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single' ) {
    $single = true;
}

?>
<script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.maskedinput.min.js"></script>
<script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/clipboard.min.js"></script>
<script>
    jQuery(document).ready(function () {
        jQuery('input[name="invitesms"]').focus();
    });
</script>
<script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/invite_parent.footer.js?ts=<?php echo time() ?>"></script>

<?php while ( have_posts() ) : the_post(); ?>
    <main>
        <div class="modal invite_step " id="invite_step" data-backdrop="static" tabindex="-1" role="" aria-labelledby=""
             aria-hidden="" style="display: block;">
            <div class="" role="document">
                <div class="modal-content">
                    <div class="modal-header model_title">
                        <h4>Invite parent/Guardian</h4>
                        <p>Input your parent/guardian’s cell phone number so they can help input quality emails of potential supporters</p>
                        <br>
                        <br>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                onclick="javascript:parent.$.fancybox.close();">
                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png" alt="">
                        </button>
                        <?php if ( !is_mobile_new() ) { ?>
                            <p>Add cell phone numbers. One per box.</p>
                    <?php } ?>

                    </div>
                    <div class="modal-body">
                        <?php
                        if ( is_mobile_new() ) {

                            $message = $invite_parent->get_mobile_message();
                            $copylink_message = $invite_parent->get_mobile_copylink_message();
                            ?>
                            <!-- Directly send a text message on your cell phone -->
                            <div class="inviteEmailImport">
                                    <?php if ( isIphone() ) { ?>
                                    <p style="text-align: center;margin-bottom: 20px; text-decoration: underline;">
                                        <a href="sms:&body=<?php echo $message; ?>">Click here to input your
                                            parent/guardian's cell phone number</a>
                                    </p>
                                    <div style='text-align: center;margin-bottom: 0px'>
                                        <a href="sms:&body=<?php echo $message; ?>">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/share3.png">
                                        </a>
                                    </div>
                                    <?php } else { ?>
                                    <p style="text-align: center;margin-bottom: 20px; text-decoration: underline;">
                                        <a href="sms:?body=<?php echo $message; ?>">Click here to input your
                                            parent/guardian's cell phone number</a>
                                    </p>
                                    <div style='text-align: center;margin-bottom: 0px'>
                                        <a href="sms:?body=<?php echo $message; ?>">
                                            <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/share3.png">
                                        </a>
                                    </div>
                            <?php } ?>
                            <hr>
                            <div style='color: #ffffff;'><center>OR<br>Copy link and paste into text message</center></div>
                            <button class="copy-button" data-clipboard-text="<?php echo $copylink_message;?>">
                                <i class="fa fa-link"></i> <span>Copy Link</span>
                            </button>
                            <br>
                            </div>
                            <!-- /Directly send a text message on your cell phone -->
                            <?php
                        } else {
                            $results = $invite_parent->process();
                            ?>
                            <form id="invite_parent_form" method="POST" action="">
                                <div class="parent_nums">
                                    <span class="addicon"><i class="fa fa-plus"></i></span>
                                    <span class="crossicon" style="display: none;"><i
                                            class="fa fa-remove"></i></span>
                                    <input type="tel" name="invitesms[]" placeholder="(###) ###-####"
                                           class="phone form-control ip" required>
                                </div>

                                <?php
                                // Failure
                                if ( !empty( $results['invalid'] ) ) {
                                    $invalid = $results['invalid'];
                                    // Phone number was not valid
                                    echo "<p style='color: red;font-weight:400'>WARNING!<br/>The number in red were not delivered because they were invalid. You may correct them and re-enter above. </p>";
                                    foreach ( $invalid as $in ) {
                                        if ( strlen( $in ) == 10 ) {
                                            $a  = str_split( $in );
                                            $in = "({$a[0]}{$a[1]}{$a[2]}) {$a[3]}{$a[4]}{$a[5]}-{$a[6]}{$a[7]}{$a[8]}{$a[9]}";
                                        }
                                        echo "<p style='color: #f91717;height:100px;overflow:auto'>";
                                        echo "<strong class='invalidEmail'>{$in}</strong>";
                                        echo "</p>";
                                    }
                                }
                                //Success
                                if ( !empty( $results['valid'] ) ) {
                                    $valid = $results['valid'];
                                    // Phone number was not valid
                                    echo "<p style='color: #7de078;font-weight:400'>The invite was successfully sent.</p>";
                                    foreach ( $valid as $in ) {
                                        if ( strlen( $in ) == 10 ) {
                                            $a  = str_split( $in );
                                            $in = "({$a[0]}{$a[1]}{$a[2]}) {$a[3]}{$a[4]}{$a[5]}-{$a[6]}{$a[7]}{$a[8]}{$a[9]}";
                                        }
                                        /* echo "<p>";
                                          echo "<strong class='invalidEmail'>{$in}</strong>";
                                          echo "</p>"; */
                                    }
                                }
                                ?>

                                <input type="hidden" name="invite_submit" value="Send" />
                                <button type="submit" id="inviteSentBtn" class="submit_btn btn has-spinner" data-loading="Sending..." >Send</button>
                            </form>
    <?php } ?>
                    <?php include_once(get_template_directory() . '/prev_next_buttons.php'); ?>
                    </div>
                    <div class="modal-footer">

                        <div class="total_sent">

                            <?php
                            
                            $total_label    = "<p>Previously Sent Texts</p>";
                            $total_list     = '';
                            $stored_parents = get_user_meta( $user_ID, 'stored_parents', true );
                            
                            if ( isset( $stored_parents[$fundraiser_ID] ) && !empty( $stored_parents[$fundraiser_ID] ) ) {
                                $total_count = "<b>" . count( $stored_parents[$fundraiser_ID] ) . " </b>";

                                $total_list .= "<div class='inviteEmailList'><ul>";
                                foreach ( $stored_parents[$fundraiser_ID] as $parent ) {
                                    $total_list .= "<li><div class='inviteEmailListBlock'>{$parent}</div></li>";
                                }
                                $total_list .= "</ul></div>";
                            } else {

                                $total_count = "<b>You have not sent any texts yet.</b>";
                            }
                            ?>

                            <?php
                            if ( !is_mobile_new() ) {
                                echo $total_label . $total_count . $total_list;
                            }
                            ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery('.next_prev_buttons a').click(function (e) {
                var i = 0;
                jQuery('input[name^="invitesms"]').each(function () {
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