<?php

use \classes\app\previously_sent\Previously_Sent;
if(isset($_GET['fundraiser_id']) && !empty($_GET['fundraiser_id'])) {
    $image_url = wp_get_attachment_url(get_post_thumbnail_id($_GET['fundraiser_id']), 'fundraiser-logo');


    $potential_donors = json_decode(get_post_meta($_GET['fundraiser_id'], 'potential_donors_sms_array', true));
    $stored_parents = get_user_meta($user_ID, 'stored_parents', true);
//
//    var_dump($potential_donors);
//    var_dump($stored_parents);
//    exit;


//
//    $previously_sent = new Previously_Sent($user_ID, $fundraiser_ID); // Previously sent class object
//    var_dump($previously_sent->init($results)); exit;

?>
<!--INVITE STEP1 start-->
<div class="modal fade invite_step" id="invite_step_start" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="invite_step"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header model_title">
                <img class="modal_logo" src="<?php echo $image_url; ?>" width="220" alt="">
                <h3><?php the_title(); ?></h3>
                <h4>Spread the word</h4>
                <p>This wizard will take you step-by-step through the invite process. Just click NEXT</p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="<?php bloginfo('template_directory'); ?>/assets/images/close-btn1.png" alt="">
                </button>
            </div>
            <div class="modal-body">
                <ul>
                    <li class="left">
                        <input type="radio" name="spread" id="spread1">
                        <label for="spread1">Add Parent(s)</label>
                    </li>
                    <li class="right">
                        <b><img src="<?php bloginfo('template_directory'); ?>/assets/images/icon16.png" alt=""></b>Parent
                    </li>
                </ul>
                <ul>
                    <li class="left">
                        <input type="radio" name="spread" id="spread2">
                        <label for="spread2">Send Valid Emails</label>
                    </li>
                    <li class="right">
                        <b><img src="<?php bloginfo('template_directory'); ?>/assets/images/icon17.png" alt=""></b>Email
                    </li>
                </ul>
                <ul>
                    <li class="left">
                        <input type="radio" name="spread" id="spread3">
                        <label for="spread3">Send Text Messages</label>
                    </li>
                    <li class="right">
                        <b><img src="<?php bloginfo('template_directory'); ?>/assets/images/icon18.png" alt=""></b>Text
                    </li>
                </ul>
                <ul>
                    <li class="left">
                        <input type="radio" name="spread" id="spread4">
                        <label for="spread4">Share on Social Media</label>
                    </li>
                    <li class="right">
                        <b><img src="<?php bloginfo('template_directory'); ?>/assets/images/icon19.png" alt=""></b>Facebook
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <input type="button" data-current="invite_step_start" data-act="invite_step_parent"  value="Next (send emails) →" class="submit_btn btn nav">
            </div>
        </div>
    </div>
</div>
<!--INVITE STEP1 end-->

<!--INVITE STEP1 start-->
<div class="modal fade invite_step step2" id="invite_step_parent" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="invite_step"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header model_title">
                <img class="modal_logo" src="<?php echo $image_url; ?>" width="220" alt="">
                <h3>Various High school <br>
                    sports Program 2018</h3>
                <h4>Invite parent</h4>
                <p>Input your parent/guardian’s cell phone number so they can help input quality emails of potential
                    supporters</p>
                <br>
                <br>
                <p>Add cell phone numbers. One per box.</p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="<?php bloginfo('template_directory'); ?>/assets/images/close-btn1.png" alt="">
                </button>
            </div>
            <div class="modal-body">
                <input type="text" placeholder="(123) 456-7899" class="form-control ip">
                <input type="text" placeholder="(###) ###-####" class="form-control ip">
                <input type="submit" value="Send" class="submit_btn btn">
            </div>
            <div class="modal-footer">
                <div class="total_sent">
                    <p>Previously Sent Texts</p>
                    <b>0</b>
                </div>
                <input type="button" data-current="invite_step_parent" data-act="invite_step_email"  value="Next (send emails) →" class="submit_btn btn nav">
            </div>
        </div>
    </div>
</div>
<!--INVITE STEP1 end-->

<!--INVITE STEP1 start-->
<div class="modal fade invite_step step2" id="invite_step_email" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="invite_step"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header model_title">
                <img class="modal_logo" src="<?php echo $image_url; ?>" alt="" width="220">
                <h3>Various High school <br>
                    sports Program 2018</h3>
                <h4>Send 20 emails!</h4>
                <p>Help spread the word by sending quality emails.
                    For best results send at least 20</p>
                <img class="cloud" src="<?php bloginfo('template_directory'); ?>/assets/images/icon21.png"  alt="">
                <p>Click to access your address book </p>
                <br>
                <br>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="<?php bloginfo('template_directory'); ?>/assets/images/close-btn1.png" alt="">
                </button>
            </div>
            <div class="modal-body">
                <textarea placeholder="Enter or paste email addresses here"></textarea>
                <input type="submit" value="Send" class="submit_btn btn">
            </div>
            <div class="modal-footer">
                <div class="total_sent">
                    <p>Previously Sent Texts</p>
                    <b>15</b>
                </div>
                <input type="button" data-current="invite_step_email" data-act="invite_step_parent" value="← Back" class="submit_btn btn back nav">
                <input type="button" data-current="invite_step_email" data-act="invite_step_text"  value="Next (text) →" class="submit_btn btn next nav">
            </div>
        </div>
    </div>
</div>
<!--INVITE STEP1 end-->

<!--INVITE STEP1 start-->
<div class="modal fade invite_step step2" id="invite_step_text" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="invite_step"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header model_title">
                <img class="modal_logo" src="<?php echo $image_url; ?>" width="220" alt="">
                <h3>Various High school <br>
                    sports Program 2018</h3>
                <h4>Send text messages</h4>
                <p>Help spread the word by sending quality emails.
                    For best results send at least 20</p>
                <br>
                <br>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="<?php bloginfo('template_directory'); ?>/assets/images/close-btn1.png" alt="">
                </button>
            </div>
            <div class="modal-body">
                <textarea placeholder="Enter or paste phone numbers here"></textarea>
                <input type="submit" value="Send" class="submit_btn btn">
            </div>
            <div class="modal-footer">
                <div class="total_sent">
                    <p>Previously Sent Texts</p>
                    <b>15</b>
                </div>
                <input type="button" data-current="invite_step_text" data-act="invite_step_email"  value="← Back" class="submit_btn btn back nav">
                <input type="button" data-current="invite_step_text" data-act="invite_step_facebook" value="Next (text) →" class="submit_btn btn next nav">
            </div>
        </div>
    </div>
</div>
<!--INVITE STEP1 end-->

<!--INVITE STEP1 start-->
<div class="modal fade invite_step step5" id="invite_step_facebook" data-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="invite_step"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header model_title">
                <img class="modal_logo" src="<?php echo $image_url; ?>" alt="" width="220">
                <h3>Various High school <br>
                    sports Program 2018</h3>
                <h4>Share on Social Media</h4>
                <p>Post a link to this fundraiser on your Facebook timeline or send a direct message to specific
                    people.</p>
                <br>
                <br>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <img src="<?php bloginfo('template_directory'); ?>/assets/images/close-btn1.png" alt="">
                </button>
            </div>
            <div class="modal-body">
                <a href="#" class="fb_link">
                    <img src="<?php bloginfo('template_directory'); ?>/assets/images/icon19.png" alt="">facebook
                    timeline
                </a>
                <b class="and">and</b>
                <a href="#" class="fb_link">
                    <img src="<?php bloginfo('template_directory'); ?>/assets/images/icon19.png" alt="">facebook Message
                </a>
            </div>
            <div class="modal-footer">
                <input type="button" data-current="invite_step_facebook" data-act="invite_step_text" value="← Back" class="submit_btn btn back nav">
                <input type="button" id="inviteDonorFinish" value="Finish (dashboard) →" class="submit_btn btn next">
            </div>
        </div>
    </div>
</div>
<?php } ?>

<!--INVITE STEP1 end-->