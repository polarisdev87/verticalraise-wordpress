<?php
/*
 * Get Fundraiser information
 * $f_id = $_GET['fundraiser_id'] : FundraiserId
 */

use classes\app\stripe\Stripe_Form;
use classes\app\fundraiser\Fundraiser_Media;
use classes\app\fundraiser\Fundraiser_Subgroups;
use classes\models\tables\Subgroups;
use classes\app\fundraiser\Fundraiser_Ended;

while ( have_posts() ) :
    the_post();
    $f_id = get_the_ID();
endwhile;
if ( isset( $_GET['fundraiser_id'] ) ) {
    $f_id = $_GET['fundraiser_id'];
} else {
    $f_id = $f_id;
}

//$payment_option = get_post_meta($f_id, 'payment_option', true);
$stripe_connect_status = get_post_meta( $f_id, 'stripe_connect', true );

if ( $stripe_connect_status == "1" ) {
    $stripe_connect = new Stripe_Form();
    $bank_account   = $stripe_connect->retrieve_account( $f_id );
    if ( $bank_account ) {
        $stripe_account_detail = $bank_account->data[0];
    }
}

$fundraiser_ended = new Fundraiser_Ended( $f_id );
$ended            = $fundraiser_ended->check_normal_end();

?>


<!--Edit Fundraiser POPUP start-->

<div class="modal fade edit_fundraiser_modal" data-backdrop="static" id="edit_fundraiser">
    <div class="modal-dialog">

        <div class="modal-content">

            <!-- body -->
            <div class="modal-body">

                <div class="modal-header model_title">
                    <button type="button" class="close show_in_mob1" data-dismiss="modal"
                            aria-label="Close"></button>
                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon1.png" alt="">
                    <h3 class="modal-title">Edit fundraiser</h3>
                </div>
                <div class="modal-body">

                    <form id="editFundraiserForm" class="edit-fund"
                          action="<?php echo bloginfo( 'url' ) ?>/edit-fundraiser" method="POST"
                          enctype="multipart/form-data" role="form">
                        <input type="hidden" name="fundraiser_id" value="<?php echo $f_id ?>" />
                        <h4>Your information</h4>

                        <div class="row level-0">
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="con_name" placeholder="Primary Contact Name*"
                                       value="<?php echo get_post_meta( $f_id, 'con_name', true ); ?>"
                                       class="form-control ip non_specific_char" required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="tel" name="phone" placeholder="Phone Number*" class="form-control ip phone"
                                       value="<?php echo get_post_meta( $f_id, 'phone', true ); ?>"
                                       required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="email" name="email" placeholder="Email*" class="form-control ip"
                                       value="<?php echo get_post_meta( $f_id, 'email', true ); ?>"
                                       required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <?php $org_type = get_post_meta( $f_id, 'org_type', true ); ?>
                                <select name="org_type" required="">
                                    <option value="">Select Organization Type*</option>
                                    <option<?php
                                    if ( $org_type == 'High School' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="High School">High School
                                    </option>
                                    </option>
                                    <option<?php
                                    if ( $org_type == 'Jr. High School' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Jr. High School">Jr. High School
                                    </option>
                                    <option<?php
                                    if ( $org_type == 'Elementary School' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Elementary School">Elementary School
                                    </option>
                                    <option<?php
                                    if ( $org_type == 'University' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="University">University
                                    </option>
                                    <option<?php
                                    if ( $org_type == 'Club Sports Team' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Club Sports Team">Club Sports Team
                                    </option>
                                    <option<?php
                                    if ( $org_type == 'Charity' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Charity">Charity
                                    </option>
                                    <option<?php
                                    if ( $org_type == 'Individual' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Individual">Individual
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <select name="hear_about_us" required="">
                                    <?php
                                    $hear_about_us = get_post_meta( $f_id, 'hear_about_us', true );
                                    echo $hear_about_us;
                                    ?>
                                    <option value="">Select Referral Type*</option>
                                    <option<?php
                                    if ( $hear_about_us == 'Facebook' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Facebook">Facebook
                                    </option>
                                    <option<?php
                                    if ( $hear_about_us == 'Google' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Google">Google
                                    </option>
                                    <option<?php
                                    if ( $hear_about_us == 'Bing' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Bing">Bing
                                    </option>
                                    <option<?php
                                    if ( $hear_about_us == 'Fundraising Rep' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Fundraising Rep">Fundraising Rep
                                    </option>
                                    <option<?php
                                    if ( $hear_about_us == 'Referral' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Referral">Referral
                                    </option>
                                    <option<?php
                                    if ( $hear_about_us == 'Athletic Director' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Athletic Director">Athletic Director
                                    </option>
                                    <option<?php
                                    if ( $hear_about_us == 'Other' ) {
                                        echo ' selected="selected"';
                                    }
                                    ?> value="Other">Other
                                    </option>
                                </select>
                            </div>

                            <div class="coach_info" <?php if ( $hear_about_us != 'Fundraising Rep' ) { ?> style="display: none;"<?php } ?>>

                                <div class="col-md-6 col-sm-6 col-xs-12 col">
                                    <input type="text" name="coach_name" placeholder="Fundraising Rep Name*"
                                           value="<?php echo get_post_meta( $f_id, 'coach_name', true ); ?>"
                                           class="form-control ip non_specific_char" required="required" />
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12 col">
                                    <input type="email" name="coach_email" placeholder="Fundraising Rep Email*"
                                           value="<?php echo get_post_meta( $f_id, 'coach_email', true ); ?>"
                                           class="form-control ip" required="required" />
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12 col">
                                    <input type="number" name="coach_code" placeholder="Fundraising Rep Code*"
                                           value="<?php echo get_post_meta( $f_id, 'coach_code', true ); ?>"
                                           class="form-control ip" required="required" />
                                </div>
                            </div>
                        </div>

                        <h4>Payment Information</h4>
                        <div class="row ">
                            <div class="col-md-12 col-sm-12 col-xs-12 col">
                                <p>
                                    You can receive your funds via check or by direct deposit (checks are mailed within
                                    2 business days after a fundraiser ends)
                                </p>
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <select name="payment_option" required="" disabled>
                                    <?php if ( $stripe_connect_status == 1 ) { ?>
                                        <option value="1" class="direct" selected>Direct Deposit</option>
                                    <?php } else { ?>
                                        <option value="0" class="checkpay" selected>Check by Mail</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <?php if ( $stripe_connect_status == '1' && isset( $stripe_account_detail ) ) { ?>

                            <div class="row direct_diposit">

                            <input type="hidden" name="stripe_connect" value="<?php echo $stripe_connect_status ?>" />


                            <div class="edit-stripe-account">
                                <input type="hidden" name="b_token" />
                                <div class="col-md-6 col-sm-6 col-xs-12 col">
                                    <input type="text" name="bank_account_name" placeholder="Payee Name (Team Name on Bank Account)*" disabled class="form-control ip" required="" value="Payee Name: <?php echo $stripe_account_detail->account_holder_name; ?>" />
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12 col">
                                    <input type="text" name="direct_account" placeholder="Account Number*" disabled class="form-control ip account" required="" value="Account Number: ********<?php echo $stripe_account_detail->last4; ?>"/>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12 col">
                                    <input type="text" name="routing" placeholder="Routing Number*" disabled class="form-control ip" required="" value="Routing Number: <?php echo $stripe_account_detail->routing_number; ?>"/>
                                </div>

                            </div>


                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <select name="our_fee" required="" disabled>
                                    <option value="">Our Fee*</option>
                                    <option value="1" <?php echo (get_post_meta( $f_id, 'our_fee', true ) == '1') ? 'selected' : '' ?>>Deposit 100% of funds to client</option>
                                    <option value="2" <?php echo (get_post_meta( $f_id, 'our_fee', true ) == '2') ? 'selected' : '' ?>>Take VR fee out before deposit </option>
                                </select>
                            </div>

                            <label class="col-md-12 col-sm-12 col-xs-12 col error" id="bank-error-message" style="display:none;"></label>
                        </div>
                        <?php } else { ?>
                        <div class="row check_by_mail">

                            <input type="hidden" name="address" value="update" />

                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="check_pay" placeholder="Make Check Payable to*"
                                       value="<?php echo get_post_meta( $f_id, 'check_pay', true ); ?>"
                                       class="form-control ip non_specific_char" required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="mailing_address" placeholder="Mailing Address Name*"
                                       value="<?php echo get_post_meta( $f_id, 'mailing_address', true ); ?>"
                                       class="form-control ip " required="" />
                            </div>


                        </div>
                        <?php } ?>
                        <div class="row address_field" >
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="street" placeholder="Street Address*"
                                       class="form-control ip "
                                       value="<?php echo get_post_meta( $f_id, 'street', true ); ?>"
                                       required="" />
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <div class="row">
                                    <div class="col-md-5 col-sm-5 col-xs-12 city">
                                        <input type="text" name="city" placeholder="City*" class="form-control ip "
                                               value="<?php echo get_post_meta( $f_id, 'city', true ); ?>"
                                               required="" />
                                    </div>
                                    <div class="col-md-3 col-sm-3 col-xs-6 state">
                                        <select name="state" required="">
                                            <option value="">State*</option>

                                            <?php
                                            foreach ( _US_STATES as $key => $state ) {
                                                $selected  = false;
                                                $sel_state = get_post_meta( $f_id, 'state', true );
                                                if ( $sel_state == $key || $sel_state == $state ) {
                                                    $selected = true;
                                                }
                                                ?>

                                                <option value="<?php echo $key ?>" <?php echo ($selected) ? 'selected' : '' ?>>
                                                    <?php echo $key ?>
                                                </option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-6 zip_code">
                                        <input type="text" name="zipcode" placeholder="Zip Code*"
                                               value="<?php echo get_post_meta( $f_id, 'zipcode', true ); ?>"
                                               class="form-control ip non_specific_char"
                                               required="" />
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row address_field">
                            <div class="col-md-4 col-sm-6 col-xs-12 col">
                            <input type="text" name="tax_id" <?php if ($stripe_connect_status == 1) { echo "disabled"; } ?> placeholder="Tax ID (optional)" class="form-control ip"
                                           value="<?php echo get_post_meta( $f_id, 'tax_id', true ); ?>" />
                            </div>
                        </div>


                        <h4 class="level-1">Fundraiser Details</h4>
                        <p>
                            Please end the fundraiser name with "Fundraiser"
                        </p>
                        <div class="row level-0">
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="fundraiser_name" placeholder="Fundraiser Name*"
                                       value="<?php echo get_the_title( $f_id ); ?>"
                                       class="form-control ip non_specific_char" required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="team_name" placeholder="Team Name*" class="form-control ip non_specific_char"
                                       value="<?php echo get_post_meta( $f_id, 'team_name', true ); ?>"
                                       required="" />
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <?php
                                $format_in          = 'Ymd';
                                $format_out         = 'm/d/Y';
                                $start_date         = get_post_meta( $f_id, 'start_date', true );
                                $start_date         = DateTime ::createFromFormat( $format_in, $start_date );
                                $start_date         = $start_date->format( $format_out );
                                $end_date           = get_post_meta( $f_id, 'end_date', true );
                                $end_date           = DateTime ::createFromFormat( $format_in, $end_date );
                                $end_date           = $end_date->format( $format_out );
                                ?>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 margin">
                                        <input type="text" name="start_date" id="start_date1"
                                               value="<?php echo $start_date; ?>" placeholder="Start Date*"
                                               class="form-control ip" required="" <?php if ( $ended ) { echo 'disabled'; } ?>/>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" name="end_date" id="end_date1" placeholder="End Date*"
                                               value="<?php echo $end_date; ?>" class="form-control ip" required="" <?php if ( $ended ) { echo 'disabled'; } ?> />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 margin">
                                        <input type="text" name="fundraising_goal" id="editFund_goal"
                                               value="<?php echo get_post_meta( $f_id, 'fundraising_goal', true ); ?>"
                                               placeholder="$ Fundraising Goal ($USD)*"
                                               class="form-control ip" required="" />
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" name="est_team_size"
                                               placeholder="Estimated Participants*"
                                               value="<?php echo get_post_meta( $f_id, 'est_team_size', true ); ?>"
                                               class="form-control ip" required="" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <blockquote>
                                    Campaign message to donors (please include what you will be using the donations
                                    for, why they are necessary, and a personal message)* <br>
                                    <a href="javascript:void(0)" class="fill_compaign_msg">Click here</a> to input a default message
                                    <textarea row="7" id="campaign_msg1" required="" name="campaign_msg">
                                        <?php echo get_post_meta( $f_id, 'campaign_msg', true ); ?>
                                    </textarea>
                                </blockquote>
                            </div>
                            <?php $show_check         = get_post_meta( $f_id, 'showPc_table', true ); ?>
	                        <?php $sport_scope_integrated        = get_post_meta( $f_id, 'sport_scope_integrated', true ); ?>
	                        <?php
	                        $secondary_end_date = '';
                            if ( $sport_scope_integrated ) {
		                        $secondary_end_date = get_post_meta( $f_id, 'secondary_end_date', true );
	                            $secondary_end_date_object = DateTime::createFromFormat('Ymd', $secondary_end_date);
	                            $secondary_end_date = $secondary_end_date_object->format('m/d/Y');
	                        } ?>

                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="checkboxDIv">
                                    <input type="checkbox" <?php echo ( $show_check == '1' ) ? 'checked' : ''; ?>
                                           class="icheckbox_flat "
                                           name="showCheck"
                                           tabindex="2" />
                                    <label for="showPc_table" class="">
                                        Show Participation Table to all registered participants.
                                    </label>
                                    <input type="hidden" name="showPc_table" id="showPc_table1" value="<?echo $show_check?>" />
                                </div>
                                <div class="checkboxDIv">
                                    <input type="checkbox"  <?php echo ( $sport_scope_integrated == '1' ) ? 'checked' : ''; ?> class="icheckbox_flat" name="edit_sport_scope_integration" id="edit_sport_scope_integration"
                                           tabindex="2"/>
                                    <label for="edit_sport_scope_integration_value" class="">
                                        Sport Scope Integration
                                    </label>
                                    <input type="hidden" name="edit_sport_scope_integration_value" id="edit_sport_scope_integration_value" value="<?php echo $sport_scope_integrated?>"  />

                                </div>

                                <div class="col-md-4 col-sm-6 col-xs-12" >
                                    <input type="text" name="edit_secondary_end_date" id="edit_secondary_end_date" style="display: none" placeholder="Secondary End Date*"
                                           class="form-control ip"  disabled/>
                                </div>

                            </div>
                            <input type="hidden" name="update_fundraiser" />

                        </div>

                        <h4 id="multimedia">Fundraiser Logo and Video</h4>

                        <div class="row ">
                            <div class="col-md-6 col-sm-6 col-xs-12 col teamlogo">
                                <?php
                                $img_exist          = false;
                                $fundraise_mediaObj = new Fundraiser_Media();
                                $image_url          = $fundraise_mediaObj->get_fundraiser_logo( $f_id );

                                if ( $image_url ) {
                                    $img_exist = true;
                                }
                                ?>
                                <a class="logo_update fancyboxLogoUpload" data-fancybox-type="iframe" href="<?php bloginfo( 'url' ); ?>/team-logo-upload/?from_file=1&f_id=<?php echo $f_id ?>&return=editfundraiser"
                                   style="cursor:pointer;border: solid 1px;
                                   background: url(<?php echo ( $img_exist ) ? $image_url : bloginfo( 'template_directory' ) . '/assets/images/Asset5.png' ?>);
                                   background-size: cover;background-repeat: no-repeat;background-position: top center;">
                                    <b>Add/Change</b>
                                </a>
<!--                                <input type="file" id="fundlogoFile" name="logo" style="display: none;" />
                                <input type="hidden" id = "logoImage" name="logoImage" />
                                <input type="hidden" id = "logoImageName" name="logoImageName" />-->
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <div style="text-align: center;">
                                    <a class="video_change"  >
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/Asset6.png"
                                             alt="">
                                        <b>Add/Change</b>
                                    </a>

                                    <div class="upload_div">
                                        <input id="title" type="text"
                                               value="<?php echo get_the_title( $f_id ) ?>"
                                               style="display: none">
                                        <textarea id="description"
                                                  style="display: none"><?php echo nl2br( get_post_meta( $f_id, 'campaign_msg', true ) ) ?></textarea>

                                        <input input type="file" id="fundvideoFile" class="button" accept="video/*"
                                               style="display: none">

                                        <label id="button" class="youtube_uploadbtn">Upload Video</label>
                                        <div class="during-upload youtube_progressDiv">
                                            <p><span id="percent-transferred"></span>% done (<span
                                                    id="bytes-transferred"></span>/<span
                                                    id="total-bytes"></span>
                                                bytes)</p>
                                            <progress id="upload-progress" max="1" value="0"></progress>
                                        </div>
                                    </div>
                                    <input type="text" name="youtube_url" placeholder="Enter YouTube video url"
                                           id="youtube_link"
                                           value="<?php echo get_post_meta( $f_id, 'youtube_url', true ); ?>"
                                           class="form-control ip"
                                           />
                                    <div>
                                        <img src="<?php bloginfo(
                                            'template_directory'
                                        ); ?>/assets/images/youtube-logo.png" alt="">
                                        <div style="display: flex;justify-content: space-evenly;">
                                            <a style="font-size: 12px" target="_blank" href="http://www.google.com/policies/privacy">Google Privacy Policy</a>
                                            <a style="font-size: 12px" target="_blank" href="https://www.youtube.com/t/terms">YouTube Terms of Service</a>
                                        </div>
                                    </div>
                                    <div id="post-upload-status"></div>

                                    <div id="loading" style="text-align: center; display: none;">
                                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/ajax-loader.gif" width="35"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4 class="level-1">Participants Subgroups</h4>
                        <div class="row level-0">
                            <div class="col-md-12 col-sm-12 col-xs-12 col">
                                <a id="edit_add_subgroup" href="#">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/plus-icon.png"
                                         alt=""
                                         style="background: white;border: 25;border-radius: 100px;max-width: 35px;">
                                    Add Subgroup</a>
                                <div id="edit_subgroup_container">
                                </div>
                            </div>
                        </div>
                        <div class="terms_cond_point">
                            <p>By clicking continue, you are agreeing to the <a
                                    href="<?php echo get_the_permalink( 157 ); ?> " target="_blank">Terms and
                                    Conditions</a> of the site.</p>
                        </div>


                        <div class="btn_row">
                            <button type="button" id="edit_fundraiser"
                                    data-loading="Updating..." class="submit_btn has-spinner">
                                Submit →
                            </button>
                        </div>
                        <input type="hidden" name="update_fundraiser" />

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--PEdit Fundraiser POPUP end-->
<?php
$subgroups_table = new Subgroups();
$subgroups = $subgroups_table->getSubgroups($f_id);

?>

<style>
    #edit_subgroup_container label.error{
        position: absolute;
        right: 50px;
        top: 15px;
    }

</style>

<script>
    $(document).ready(function () {

        var current_groups = 0;
        const used_index = [];

        var groups = <?php echo json_encode( $subgroups ); ?>;
        var secondary_end_date = '<?php echo $secondary_end_date;?>';

        if(secondary_end_date){
            $("#edit_secondary_end_date").show().datetimepicker({format: 'm/d/Y', timepicker: false, value: secondary_end_date }).removeProp('disabled').prop('required', 'required');
        }

        $("#edit_sport_scope_integration").on('ifChecked', function (event) {
            $("#edit_sport_scope_integration_value").val(1);
            $("#edit_secondary_end_date").show().datetimepicker({format: 'm/d/Y', timepicker: false}).removeProp('disabled').prop('required', 'required');
        });

        $("#edit_sport_scope_integration").on('ifUnchecked', function (event) {
            $("#edit_sport_scope_integration_value").val(0);
            $("#edit_secondary_end_date").hide().removeProp('required').prop('disabled', 'disabled');
            $("#edit_secondary_end_date").siblings('label').remove();
        });

        for (var i = 0; i < groups.length; i++) {
            $("#edit_subgroup_container").append("<div class=\"col-md-4 col-sm-6 col-xs-12 col\"><input maxlength=\"50\"  pattern=\"^[a-zA-Z0-9 _-]+$\"  type=\"text\" name=\"participants_subgroups[" + groups[i].id + "]\" value=\"" + groups[i].name + "\" class=\"form-control ip non_specific_char\"\n" +
                "       placeholder=\"\"></div>");
            used_index.push(parseInt(groups[i].id));
            current_groups++;

        }

        $('#edit_add_subgroup').click(function (e) {
            e.preventDefault();
            if (current_groups < <?php echo Fundraiser_Subgroups::MAX_SUBGROUPS ?>) {
                var aux_id = current_groups;
                while( used_index.indexOf(aux_id) !== -1 ){
                    aux_id++;
                }
                used_index.push(aux_id);
                $("#edit_subgroup_container").append(`<div style="display: flex;justify-content: space-between;" class="col-md-4 col-sm-6 col-xs-12 col">
                        <input style="width: 90%;" maxlength="50" pattern="^[a-zA-Z0-9 _-]+$" type="text" name="participants_subgroups[${aux_id}]" class="form-control ip non_specific_char" placeholder=""/>
                        <a class="removeItem" data-id="${current_groups}" href="#"><img style="width: 25px;height: 25px;" src="<?php bloginfo( "template_directory" ); ?>/assets/images/close-btn1.png" alt=""/></a>
                    </div>`);
                current_groups++;
            }
            return false;
        });

        $(document).on('click', '.removeItem', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var index = used_index.indexOf(id);
            used_index.splice(index, 1);
            current_groups--;
            $(this).parent().remove();
            return false;
        });

        $("[name='fundraiser_name']").change(function () {
            console.log($(this).val());
            const fundraiser_name = $(this).val();
            const fundraiser_id = $(this).val();
            const data = {
                'action': 'check_fundraiser_name',
                'fundraiser_name': fundraiser_name,
                'fundraiser_id': <?php echo $f_id; ?>,
            };

            $.ajax(
                "/wp-admin/admin-ajax.php",
                {
                    type: 'POST',
                    data: data,
                    complete: function (jqXHR, textStatus) {
                        var status = jqXHR.status;
                        var responseJSON = jqXHR.responseJSON;
                        console.log(status);
                        console.log(responseJSON);

                        if (status === 200) {
                            if (responseJSON.duplicated) {
                                $("[name='fundraiser_name']").rules("add", {

                                    pattern: `/^(?!${fundraiser_name}).*$/`,
                                    messages: {
                                        pattern: "Duplicated Fundraiser name",
                                    }
                                });
                                $("[name='fundraiser_name']").valid();
                            } else {
                                $("[name='fundraiser_name']").rules("remove", 'pattern');
                                $("[name='fundraiser_name']").valid();
                            }
                        }

                    }
                }
            );
        })


    });
</script>
