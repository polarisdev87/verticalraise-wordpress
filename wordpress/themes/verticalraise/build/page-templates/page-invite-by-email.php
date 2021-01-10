<?php

/**
 * Template Name: Participants Invite - Email Share
 * @name Email Invite Page
 * @description The participant can enter potential donors' emails and send them an invitiation to donate to the fundraiser.
 */
use \classes\app\previously_sent\Previously_Sent;
use classes\app\fundraiser\Fundraiser_Media;       //Fundraiser Media Class Object
use \classes\models\tables\Participant_Fundraiser_Details;
// Load classes
load_class( 'invite_wizard.class.php' );
load_class( 'sharing.class.php' );
load_class( 'page.invite_email.class.php' );

$p_invite_wizard = new Invite_Wizard();                           // Parent Invite Wizard class object
$sharing         = new Sharing();                                 // Sharing class object
$user_ID         = $sharing->user_ID;                             // Define user ID
$fundraiser_ID   = $sharing->fundraiser_ID;                       // Define fundraiser ID

$parent = 0;
if (isset( $_GET['parent']) && $_GET['parent'] == 1 ) {
    $parent = 1;
}


if ( (isset( $_GET['parent'] ) && $_GET['parent'] == '1') || (isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single') ) {
    $user_ID = $_GET['uid'];
}

$previously_sent = new Previously_Sent( $user_ID, $fundraiser_ID ); // Previously sent class object

/**
 * Process the Form.
 */
$invite_emails = new Page_Invite_Emails( $user_ID, $fundraiser_ID );
$results = $invite_emails->process_form();

/**
 * Output results
 */
$nonvalid          = 0;
$invalid_emails    = array();
$duplicated_emails = array();
$valid_emails      = array();

if ( !empty( $results['invalid_emails'] ) ) {
    $nonvalid       = 1;
    $invalid_emails = $results['invalid_emails'];
}

if ( !empty( $results['duplicate_emails'] ) ) {
    $nonvalid          = 1;
    $duplicated_emails = $results['duplicate_emails'];
}

if ( !empty( $results['valid_emails'] ) ) {
    $nonvalid     = 0;
    $valid_emails = $results['valid_emails'];
}
/**
 * Get participant email share stats
 */
$participant_fundraiser_detailsObj = new Participant_Fundraiser_Details();
$share_detailsObj  = $participant_fundraiser_detailsObj->get_single_row($fundraiser_ID, $user_ID);
@$amount_of_email_sent = $share_detailsObj->email;

$single = false;
if ( isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single' ) {
    $single = true;
}

/**
 * Get Header.
 */
get_header( 'invite' );

$fundraiser_name = get_the_title( $fundraiser_ID );

if ( is_mobile_new() ) {
    $default_size = 150;
} else {
    $default_size = 282;
}

/**
 * If the device is mobile, we display tools to import contacts from their phone.
 */
?>
<!-- Cloud Sponge Import Widget -->
<!--<script src="//api.cloudsponge.com/widget/<?php echo _CLOUDSPONGE_API_KEY; ?>.js"></script>-->
<script src="//api.cloudsponge.com/widget/<?php echo _CLOUDSPONGE_API_KEY; ?>.js"></script>

<script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/invite_emails.cloudsponge.js?v1.1"></script>
<!-- /Cloud Sponge Import Widget -->


<script src="<?php bloginfo( 'template_directory' ); ?>/assets/js/invite_emails.footer.js"></script>

<?php
//$args = array(
//    'post_type' => 'fundraiser',
//    'post_status' => array('pending', 'publish', 'rejected'),
//    'p' => $_GET['fundraiser_id']
//);
//$fundraiser_query = new WP_Query($args);
//
//while ( $fundraiser_query -> have_posts() ) : $fundraiser_query -> the_post();

while ( have_posts() ) : the_post();
    ?>
    <main>


        <?php if ( $single ) { ?>

            <div class="modal invite_step tnx_modal tnx_f" id="invite_step" data-backdrop="static" tabindex="-1" role=""
                 aria-labelledby=""
                 aria-hidden=""
                 style="display: block;z-index: 0">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header model_title">
                            <h3>share by email</h3>
                            <p>Help spread the word by sending quality emails. <br>
                                For best results send at least 20</p>
                            <?php if ( is_mobile_new() ) { ?>
                                <p class="email_address_btn">
                                    <a href="javascript:void(0);" onclick="return cloudsponge.launch();">
                                        Click to access your address book </a>
                                </p>
                                <div style='text-align: center;margin-bottom: 0px'>
                                    <a href="javascript:void(0);" onclick="return cloudsponge.launch();">
                                        <img class="cloud" style=""
                                             src="<?php bloginfo( 'template_directory' ); ?>/assets/images/share2.png"
                                             alt="">
                                    </a>
                                </div>
                            <?php } else { ?>
                                <p class="email_address_btn">
                                    <a href="javascript:void(0);" onclick="return cloudsponge.launch();">
                                        Click to access your address book </a>
                                </p>
                                <div style='text-align: center;margin-bottom: 0px'>
                                    <a href="javascript:void(0);" onclick="return cloudsponge.launch();">
                                        <img class="cloud" style=""
                                             src="<?php bloginfo( 'template_directory' ); ?>/assets/images/share2.png"
                                             alt="">
                                    </a>
                                </div>

                            <?php } ?>
                            <br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:parent.$.fancybox.close();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                                     alt="">
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="" id="inviteEmailForm">
                                <input type="hidden" name="_form_nonce"
                                       value="<?php echo $invite_emails->generate_nonce(); ?>">

                                <?php
                                $user_nonce = $invite_emails->generate_user_nonce( $user_ID );
                                if ( !empty( $user_nonce ) ) {
                                    ?>
                                    <input type="hidden" name="e_key" value="<?php echo $user_nonce; ?>">
                                <?php }
                                ?>
                                
                                
                                <input type="text" name="your_name" id="your_name" placeholder="Your Name*"
                                       class="form-control ip" required>

                                <textarea name="emails" class="form-control" required
                                          placeholder="Enter or paste email addresses here"></textarea>

                                <div id="emailfields" style="display: none"> </div>

                                <!-- Invalid E-mail Addresses -->
                                <?php
                                if ( !empty( $invalid_emails ) ) {
                                    $invite_emails->invalid_emails( $nonvalid, $invalid_emails );
                                }
                                ?>
                                <!-- /Invalid E-amil Addresses -->
                                <!-- Duplicated E-mail Addresses -->
                                <?php
                                if ( !empty( $duplicated_emails ) ) {
                                    $invite_emails->duplicated_emails( $nonvalid, $duplicated_emails );
                                }
                                ?>
                                <!-- /Duplicated E-amil Addresses -->
                                <!-- Valid E-mail Addresses -->
                                <?php
                                if ( !empty( $valid_emails ) ) {
                                    $invite_emails->success_emails( $nonvalid, $valid_emails );
                                }
                                ?>
                                <!-- /Valid E-amil Addresses -->
                                <div class="suggestions" ><label>Did you mean? (Click to update)</label><ul></ul></div>
                                <div class="invalidemails" ><label>Invalid Emails</label><ul></ul></div>
                                <input type="text" id="email_check_status" value="" required="" style="display:none"/>
                                <input type="hidden" name="input_submit" value="Send" />
                                <input type="hidden" name="parent" value="<?php echo $parent;?>">
                                <div class="progress" style="width:200px; margin:0 auto 20px;height:inherit; display:none">
                                    <div class="bar"
                                         style="width: 200px; background: white;">
                                        <div id="progressBar"
                                             style="background: #52B6D5; width: 0%; color: #58ff50; border: none; 
                                             height: 30px;line-height: 30px;text-align: center;"
                                             class="percent"><span>0%</span></div>
                                    </div>
                                </div>
                                <button type="submit" id="inviteSentBtn" class="submit_btn btn has-spinner"
                                        data-loading="Sending...">Send
                                </button>

                            </form>
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
                 aria-labelledby=""
                 aria-hidden=""
                 style="display: block;z-index: 0">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header model_title">
                            <h4>Send <?php echo _SUGGESTED_EMAIL_AMOUNT; ?> quality emails!</h4>

                            <?php if ( $amount_of_email_sent < _EMAIL_PARTICIPANT_INVITE_LIMIT ) { ?>
                                <?php if ( is_mobile_new() ) { ?>
                                    <p class="email_address_btn">
                                        <a href="javascript:void(0);" onclick="return cloudsponge.launch();">
                                            Click to access your address book </a>
                                    </p>
                                    <div style='text-align: center;margin-bottom: 0px'>
                                        <a href="javascript:void(0);" onclick="return cloudsponge.launch();">
                                            <img class="cloud" style=""
                                                 src="<?php bloginfo( 'template_directory' ); ?>/assets/images/share2.png"
                                                 alt="">
                                        </a>
                                    </div>
                                <?php } else { ?>
                                    <p class="email_address_btn">
                                        <a href="javascript:void(0);" onclick="return cloudsponge.launch();">
                                            Click to access your address book </a>
                                    </p>
                                    <div style='text-align: center;margin-bottom: 0px'>
                                        <a href="javascript:void(0);" onclick="return cloudsponge.launch();">
                                            <img class="cloud" style=""
                                                 src="<?php bloginfo( 'template_directory' ); ?>/assets/images/share2.png"
                                                 alt="">
                                        </a>
                                    </div>

                                <?php } ?>
                            <?php } else { ?>
                                <p id="email-limit-reached">
                                    You have reached the maximum number of email invites sent.
                                </p>
                            <?php } ?>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:parent.$.fancybox.close();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                                     alt="">
                            </button>

                        </div>

                        <?php if ( $amount_of_email_sent < _EMAIL_PARTICIPANT_INVITE_LIMIT ) { ?>
                        <div class="modal-body">
                            <form method="POST" action="" id="inviteEmailForm">

                                <input type="hidden" name="_form_nonce"
                                       value="<?php echo $invite_emails->generate_nonce(); ?>">

                                <?php
                                $user_nonce = $invite_emails->generate_user_nonce( $user_ID );
                                if ( !empty( $user_nonce ) ) {
                                    ?>
                                    <input type="hidden" name="e_key" value="<?php echo $user_nonce; ?>">
                                    <?php
                                }
                                if ( isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single' ) {
                                    ?>
                                    <!-- From Field -->

                                    <input type="text" name="your_name" id="your_name" placeholder="Your Name*"
                                           class="form-control ip" required>
                                    <!-- /From Field -->
                                <?php } ?>

                                <textarea name="emails" class="form-control" required
                                          placeholder="Enter or paste email addresses here"></textarea>                                

                                <div id="emailfields" style="display: none"></div>

                                <!-- Invalid E-mail Addresses -->
                                <?php
                                if ( !empty( $invalid_emails ) ) {
                                    $invite_emails->invalid_emails( $nonvalid, $invalid_emails );
                                }
                                ?>
                                <!-- /Invalid E-amil Addresses -->

                                <!-- Duplicated E-mail Addresses -->
                                <?php
                                if ( !empty( $duplicated_emails ) ) {
                                    $invite_emails->duplicated_emails( $nonvalid, $duplicated_emails );
                                }
                                ?>
                                <!-- /Duplicated E-amil Addresses -->
                                <!-- Valid E-mail Addresses -->
                                <?php
                                if ( !empty( $valid_emails ) ) {
                                    $invite_emails->success_emails( $nonvalid, $valid_emails );
                                }
                                ?>
                                <!-- /Valid E-amil Addresses -->

                                <div class="suggestions" ><label>Did you mean? (Click to update)</label><ul></ul></div>
                                <div class="invalidemails" ><label>Invalid Emails</label><ul></ul></div>
                                <input type="text" id="email_check_status" value="" class="form-control" style="display:none"/>
                                <input type="hidden" name="input_submit" value="Send" />
                                <input type="hidden" name="parent" value="<?php echo $parent;?>">
                                <div class="progress" style="width:200px; margin:0 auto 20px;height:inherit; display:none">
                                    <div class="bar"
                                         style="width: 200px; background: white;">
                                        <div id="progressBar"
                                             style="background: #52B6D5; width: 0%; color: #58ff50; border: none; 
                                             height: 30px;line-height: 30px;text-align: center;"
                                             class="percent"><span>0%</span></div>
                                    </div>
                                </div>
                                <button type="submit" id="inviteSentBtn" class="submit_btn btn has-spinner"
                                        data-loading="Sending..." >Send
                                </button>

                            </form>
                            
                            <?php
                            if ( isset( $_GET['action'] ) && $_GET['action'] == 'spread' ) {
                                
                            } else {
                                ?>
                                <?php include_once( get_template_directory() . '/prev_next_buttons.php' ); ?>
                            <?php } ?>

                        </div>
                        <?php } ?>
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


            $('#invite_step .next_prev_buttons a').click(function (e) {
                var i = 0;
                $('textarea[name^="emails"]').each(function () {
                    var valid;
                    valid = $(this).val();
                    if ( valid != '' ) {
                        i++;
                    }
                });
                if ( i > 0 ) {
                    e.preventDefault();
                    alert('You must click the "SEND" button to send the invites out before you can go to the next page.');
                }
            });

            $('#inviteEmailForm textarea[name=emails]').focus(function () {
                $('#email_check_status').val('');
                $('#inviteEmailForm .invalidemails').hide();
                $("#inviteSentBtn").buttonLoader('stop');
            });

            var EMAILS_ARRAY;
            var INVALID_EMAILS;

            $(document).ready(function () {


                $('#inviteEmailForm textarea[name=emails]').on('blur', function (event) {
                    //email_verification($(this));
                    $('#inviteEmailForm .suggestions').fadeOut();
                    $('#emailfields input').remove();
                    $('#inviteEmailForm .suggestions ul').children().remove();
                    $('#inviteEmailForm .invalidemails ul').children().remove();
                    $('#inviteEmailForm .invalidemails').hide();

                    var textVal = $(this).val();
                    EMAILS_ARRAY = textVal.split(/[;, \r\n]+/);
                    INVALID_EMAILS = [];
                    EMAILS_ARRAY = EMAILS_ARRAY.filter(function (entry) {
                        return /\S/.test(entry);
                    });

                    for ( var i = 0; i < EMAILS_ARRAY.length; i++ ) {
                        if ( EMAILS_ARRAY[i] != '' ) {
                            $("#emailfields").append('<input type="text" name="input_email[]" value="' + EMAILS_ARRAY[i] + '" class="form-control ip" />');
                        }
                    }

                    $("#emailfields input").each(function (index) {
                        email_checker($(this), index);
                    });

                });

                $('#inviteEmailForm .suggestions').on('click', 'span', function () {
                    var index = $(this).data("arrayindex");
                    EMAILS_ARRAY[index] = $(this).text();
                    $(this).parent().remove();

                    $("#emailfields input:eq(" + index + ")").val($(this).text());
                    $('#inviteEmailForm textarea[name=emails]').val(EMAILS_ARRAY.join());
                    if ( $('#inviteEmailForm .suggestions ul').children("li").length < 1 ) {
                        $('#inviteEmailForm .suggestions').fadeOut();
                    }

                });

                setTimeout(function(){
                    if( $("#your_name").length ){
                        $("#your_name")[0].focus();
                    }
                }, 500);

            });

            // 
            function show_validation_progress(total, i) {

                var percent = (i / total) * 100;

                $("#progressBar").css("width", percent.toFixed(2) + '%');
                $("#progressBar span").text(percent.toFixed(2) + '%');

                if ( percent == 100 ) {
                    $("#inviteSentBtn").buttonLoader('stop');

                    if ( INVALID_EMAILS.length > 0 ) {
                        setTimeout(function () {
                            $('#inviteEmailForm').submit();
                        }, 1000)
                    } else {
                        setTimeout(function () {
                            $('#inviteEmailForm').submit();
                        }, 1000)
                    }
                }

            }

            function show_invalid_lists(email) {
                $('#inviteEmailForm .invalidemails').fadeIn(200);
                INVALID_EMAILS.push(email);
                EMAILS_ARRAY = remove(EMAILS_ARRAY, email);
                $('#inviteEmailForm textarea[name=emails]').val(EMAILS_ARRAY.join());
                $('#inviteEmailForm .invalidemails ul').append("<li><span >" + email + "</span></li>");
            }


            function email_checker(emailObj, index) {

                var FirstDomains = ["gmail.com", "yahoo.com", "verizon.net"];
                var SecondDomains = ["com", "net", "org", "co.nz", "co.uk", "co.il", "com.au", "com.tw", "net.au"];
                emailObj.mailcheck({
                    //                    topLevelDomains: topLevelDomains,
                    suggested: function (element, suggestion) {
                        var splitDomain = suggestion.domain.split(".");
                        switch ( splitDomain.length ) {
                            case 2 :
                                splitDomain = splitDomain[1];
                                break;
                            case 3:
                                splitDomain = splitDomain[1] + "." + splitDomain[2];
                                break;
                            default:
                                splitDomain = splitDomain;
                                break;
                        }

                        if ( FirstDomains.indexOf(suggestion.domain) > -1 ) {
                            EMAILS_ARRAY[index] = suggestion.full;
                            $("#emailfields input:eq(" + index + ")").val(suggestion.full);
                        } else if ( splitDomain != emailObj.val().split(".")[1] && SecondDomains.indexOf(splitDomain) > -1 ) {
                            EMAILS_ARRAY[index] = suggestion.full;
                            $("#emailfields input:eq(" + index + ")").val(suggestion.full);
                        } else {
                            $('#inviteEmailForm .suggestions').fadeIn(200);
                            $('#inviteEmailForm .suggestions ul').append("<li><span data-arrayindex='" + index + "'>" + suggestion.full + "</span></li>");
                        }
                    },
                    empty: function (element) {
                        // $(".suggestions").empty();
                    }
                });
                if ( EMAILS_ARRAY.length == index + 1 ) {
                    $('#inviteEmailForm textarea[name=emails]').val(EMAILS_ARRAY.join());
                    $('#email_check_status').val(1);
                }
            }

            function remove(array, element) {
                const index = array.indexOf(element);

                if ( index !== -1 ) {
                    array.splice(index, 1);
                }
                return array;
            }

            $("#inviteEmailForm").validate({
                rules: {
                    your_name: {
                        required: true,
                        minlength: 2
                    },
                    emails: {
                        required: true,
                    }
                },
                messages: {
                    your_name: {
                        required: "Please add your name",
                        minlength: "Your name must be at least 2 characters long",
                    },
                    emails: {
                        required: "Please enter at least 1 email",
                    }
                }
            });

            $('#inviteEmailForm').submit(function () {

                if ( $('#inviteEmailForm').valid() === false ) {
                    setTimeout(function () {
                        $("#inviteSentBtn").buttonLoader('stop')
                    }, 250);

                }
            });

        </script>
    </main>
<?php endwhile; ?>
