<?php
use classes\app\fundraiser\Fundraiser_Subgroups;
use classes\app\fundraiser\Fundraiser;

if(!empty($_GET['duplicate_f_id'])){
	$fundraiser = Fundraiser::getFundraiser($_GET['duplicate_f_id']);
}
?>
<!--CREATE FUNDRAISER POPUP start-->
<div class="modal fade create_fundraiser_modal" data-backdrop="static" id="create_fundraiser">
    <div class="modal-dialog">

        <div class="modal-content">

            <!-- body -->
            <div class="modal-body">

                <div class="modal-header model_title">
                    <button type="button" class="close show_in_mob1" data-dismiss="modal"
                            aria-label="Close"></button>
                    <img src="<?php bloginfo(
                    	'template_directory'
                    ); ?>/assets/images/icon1.png" alt="">
                    <h3 class="modal-title">Create a fundraiser</h3>
                </div>
                <div class="modal-body">

                    <form id="createFundraiserForm" class="create-fund" action="" method="POST"
                          enctype="multipart/form-data" role="form">

                        <h4>Your information</h4>

                        <div class="row level-0">
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="con_name" placeholder="Primary Contact Name*"
                                       class="form-control ip non_specific_char" required="" value="<?php if(isset($fundraiser)){ echo $fundraiser->getPrimaryContactName() ;} ?>" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="tel" name="phone" placeholder="Phone Number*" class="form-control ip phone"
                                       required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="email" name="email" placeholder="Email*" class="form-control ip"
                                       required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <select name="org_type" required="">
                                    <option value="">Select Organization Type*</option>
                                    <option value="High School">High School</option>
                                    <option value="Jr. High School">Jr. High School</option>
                                    <option value="Elementary School">Elementary School</option>
                                    <option value="University">University</option>
                                    <option value="Club Sports Team">Club Sports Team</option>
                                    <option value="Charity">Charity</option>
                                    <option value="Individual">Individual</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <select name="hear_about_us" required="">
                                    <option value="">Select Referral Type*</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="Google">Google</option>
                                    <option value="Bing">Bing</option>
                                    <option value="Fundraising Rep">Fundraising Rep</option>
                                    <option value="Referral">Referral</option>
                                    <option value="Athletic Director">Athletic Director</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>


                            <div class="coach_info " style="display: none;">
                                <div class="col-md-6 col-sm-6 col-xs-12 col">
                                    <input type="text"  non_specific_char name="coach_name" placeholder="Fundraising Rep Name*"
                                           class="form-control ip" required="required" />
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12 col">
                                    <input type="email" name="coach_email" placeholder="Fundraising Rep Email*"
                                           class="form-control ip" required="required" />
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12 col">
                                    <input type="number" name="coach_code" placeholder="Fundraising Rep Code*"
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
                                <select name="payment_option" required="">
                                    <option value="" >Payment Option*</option>
                                    <option value="0" class="checkpay">Check by Mail</option>
                                </select>
                            </div>
                        </div>
                        <div class="row direct_diposit" style="display:none">
                            <input type="hidden" name="b_token" />
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="bank_account_name" placeholder="Payee Name (Team Name on Bank Account)*" class="form-control ip" required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="routing" placeholder="Routing Number*" class="form-control ip" required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="direct_account" placeholder="Account Number*" class="form-control ip account" required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="confirm_account" placeholder="Confirm Account Number*"
                                       class="form-control ip" required="" />
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="tax_id" placeholder="Tax ID*"
                                    class="form-control ip" required="" />
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12 col">

                                <select name="our_fee" required="">
                                    <option value="">Our Fee*</option>
                                    <option value="2">Take VR fee out before deposit</option>
                                    <option value="1">Deposit 100% of funds to client</option>
                                </select>
                            </div>
                            <label class="col-md-12 col-sm-12 col-xs-12 col error" id="bank-error-message" style="display:none;"></label>

                        </div>

                        <div class="row  check_by_mail" style="display:none">
                            <input type="hidden" name="address" value="update" />
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="check_pay" placeholder="Make Check Payable to*"
                                       class="form-control ip non_specific_char" required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" non_specific_char name="mailing_address" placeholder="Mailing Address Name*"
                                       class="form-control ip " required="" />
                            </div>

                        </div>

                        <div class="row address_field"  style="display:none">
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="street" placeholder="Street Address*"
                                       class="form-control ip " required="" />
                            </div>

                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <div class="row">
                                    <div class="col-md-5 col-sm-5 col-xs-12 city">
                                        <input type="text" name="city" placeholder="City*" class="form-control ip "
                                               required="" />
                                    </div>
                                    <div class="col-md-3 col-sm-3 col-xs-6 state">
                                        <select name="state" required="">
                                            <option value="">State*</option>
                                            <?php foreach (
                                            	_US_STATES
                                            	as $key => $state
                                            ): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $key; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-6 zip_code">
                                        <input type="text" name="zipcode" placeholder="Zip Code*"
                                               class="form-control ip non_specific_char" required="" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row check_by_mail" style="display:none">
                            <div class="col-md-4 col-sm-6 col-xs-12 col">
                                <input type="text" name="tax_id2" placeholder="Tax ID (optional)"
                                       class="form-control ip" />
                            </div>
                        </div>
                        <h4 class="level-1">Fundraiser Details</h4>
                        <p>
                           Please end the fundraiser name with "Fundraiser"
                        </p>
                        <div class="row level-0">
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="fundraiser_name" placeholder="Fundraiser Name*"
                                       class="form-control ip non_specific_char" required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <input type="text" name="team_name" placeholder="Team Name*" class="form-control ip non_specific_char"
                                       required="" />
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 margin">
                                        <input type="text" name="start_date" id="start_date"
                                               placeholder="Start Date*" class="form-control ip" required="" />
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" name="end_date" id="end_date" placeholder="End Date*"
                                               class="form-control ip" required="" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 col">
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-xs-12 margin">
                                        <input type="text" id="fundraising_goal" name="fundraising_goal"
                                               placeholder="$ Fundraising Goal ($USD)*"
                                               class="form-control ip" required="" />
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" name="est_team_size"
                                               placeholder="Estimated Participants*"
                                               class="form-control ip" required="" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <blockquote>
                                    Campaign message to donors (please include what you will be using the donations
                                    for, why they are necessary, and a personal message)* <br>
                                    <a href="javascript:void(0)" class="fill_compaign_msg">Click here</a> to input a default message
                                    <textarea row="7" id="campaign_msg" name="campaign_msg" required=""></textarea>
                                </blockquote>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="checkboxDIv">
                                    <input type="checkbox" class="icheckbox_flat" checked name="showCheck"
                                           tabindex="2" />
                                    <label for="showPc_table" class="">
                                        Show Participation Table to all registered participants.
                                    </label>
                                    <input type="hidden" name="showPc_table" id="showPc_table" value="1"/>
                                </div>
                                <div class="checkboxDIv">
                                    <input type="checkbox" class="icheckbox_flat" name="sport_scope_integration" id="sport_scope_integration"
                                           tabindex="2" />
                                    <label for="sport_scope_integration_value" class="">
                                        Sport Scope Integration
                                    </label>
                                    <input type="hidden" name="sport_scope_integration_value" id="sport_scope_integration_value" value="0"/>

                                </div>

                                <div class="col-md-4 col-sm-6 col-xs-12" >
                                    <input type="text" name="secondary_end_date" id="secondary_end_date" style="display: none" placeholder="Secondary End Date*"
                                           class="form-control ip" disabled/>
                                </div>
                            </div>


                            <input type="hidden" name="submit_for_approval" />
                        </div>
                        
                        <h4 class="level-1">Participants Subgroups</h4>
                        <div class="row level-0">
                            <div class="col-md-12 col-sm-12 col-xs-12 col">
                                <a id="add_subgroup" href="#">
                                    <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/plus-icon.png"
                                         alt=""
                                         style="background: white;border: 25;border-radius: 100px;max-width: 35px;">
                                    Add Subgroup</a>
                                <div id="subgroup_container">
                                </div>
                            </div>
                        </div>

                        <div class="terms_cond_point">
                            <p>By clicking continue, you are agreeing to the <a
                                    href="<?php echo get_the_permalink(
                                    	157
                                    ); ?>" target="_blank">Terms and
                                    Conditions</a> of the
                                site.</p>
                        </div>

                        <div class="btn_row">
                            <button type="button" id="create_fundraiser"
                                    data-loading="Creating..." class="submit_btn has-spinner">
                                Submit for approval →
                            </button>
                        </div>
                        <input type="hidden" name="submit_for_approval" />

                        <div id="fundraiser_create_error">
                            <p id="fundraiser_create_error_message"></p>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--CREATE FUNDRAISER POPUP end-->

<div class="modal fade" data-backdrop="static" id="create_fundraiser_100_deposit">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header model_title">
                <button type="button" class="close show_in_mob1" data-dismiss="modal"
                        aria-label="Close"></button>
                <img src="<?php bloginfo(
                	'template_directory'
                ); ?>/assets/images/icon1.png" alt="">
                <h3 class="modal-title">Important</h3>
            </div>

            <div class="terms_cond_point">
                <p> If 100% Funds Deposit option is selected, the Direct Deposit Fundraisers Contract must be completed and emailed
                    to support before the fundraiser will be approved.
                </p>
                <br/>
            </div>

            <div class="modal-footer">
                <div class="" style="display: flex;align-items: center;justify-content: center; padding-top: 40px;">
                    <button type="button" id="create_fundraiser_100_deposit_accept">Download Contract PDF</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #subgroup_container label.error{
        position: absolute;
        right: 50px;
        top: 15px;
    }
</style>

<script>
    $(document).ready(function() {

        var current_groups = 0;
        const used_index = [];

        $('#add_subgroup').click(function(e){
            e.preventDefault();
            if (current_groups < <?php echo Fundraiser_Subgroups::MAX_SUBGROUPS; ?>) {
                var aux_id = current_groups;
                while( used_index.indexOf(aux_id) !== -1 ){
                    aux_id++;
                }
                used_index.push(aux_id);
                $("#subgroup_container").append(`<div style="display: flex;justify-content: space-between;" class="col-md-4 col-sm-6 col-xs-12 col">
                        <input style="width: 90%;" maxlength="50" pattern="^[a-zA-Z0-9 _-]+$" type="text" name="participants_subgroups[${aux_id}]" class="form-control ip non_specific_char" placeholder=""/>
                        <a class="removeItem" data-id="${current_groups}" href="#"><img style="width: 25px;height: 25px;" src="<?php bloginfo( "template_directory" ); ?>/assets/images/close-btn1.png" alt=""/></a>
                    </div>`);
                current_groups++;
            }
            return false;
        });

        $("#sport_scope_integration").on('ifChecked', function (event) {
            $("#sport_scope_integration_value").val(1);
            $("#secondary_end_date").show().datetimepicker({format: 'm/d/Y', timepicker: false}).removeProp('disabled').prop('required', 'required');
        });
        $("#sport_scope_integration").on('ifUnchecked', function (event) {
            $("#sport_scope_integration_value").val(0);
            $("#secondary_end_date").hide().removeProp('required').prop('disabled', 'disabled');
            $("#secondary_end_date").siblings('label').remove();
        });

        var fundraiser = false;

	    <?php if ( isset( $fundraiser ) ) { ?>
        fundraiser = <?php echo $fundraiser->__toJSON(); ?>;
	    <?php } ?>


        if (fundraiser) {

            setTimeout(function () {

                $(".create_fundraiser_modal ").modal("show");

                $("[name='con_name']").val(fundraiser.con_name);
                $("[name='phone']").val(fundraiser.phone);
                $("[name='email']").val(fundraiser.email);
                $("[name='org_type']").val(fundraiser.org_type);
                $("[name='hear_about_us']").val(fundraiser.hear_about_us).trigger('change');
                $("[name='payment_option']").val(fundraiser.payment_option).trigger('change');

                if (fundraiser.payment_option == "1") {
                    $("[name='coach_name']").val(fundraiser.coach_name);
                    $("[name='coach_email']").val(fundraiser.coach_email);
                    $("[name='coach_code']").val(fundraiser.coach_code);
                } else {
                    $("[name='check_pay']").val(fundraiser.check_pay);
                    $("[name='mailing_address']").val(fundraiser.mailing_address);
                }

                $("[name='street']").val(fundraiser.street);
                $("[name='city']").val(fundraiser.city);
                $("[name='state']").val(fundraiser.state);
                $("[name='zipcode']").val(fundraiser.zipcode);
                $("[name='tax_id']").val(fundraiser.tax_id);

                $("[name='fundraiser_name']").val(fundraiser.fundraiser_name).trigger('change');
                $("[name='team_name']").val(fundraiser.team_name);
                //$("[name='start_date']").datetimepicker({value: fundraiser.start_date, format: 'm/d/Y'});
                //$("[name='end_date']").datetimepicker({value: fundraiser.end_date, format: 'm/d/Y'});
                $("[name='fundraising_goal']").val(fundraiser.fundraising_goal);
                $("[name='est_team_size']").val(fundraiser.est_team_size);
                $("[name='campaign_msg']").val(fundraiser.campaign_msg);


                for (var i = 0; i < fundraiser.subgroups.length; i++) {
                    $("#subgroup_container").append("<div class=\"col-md-4 col-sm-6 col-xs-12 col\"><input maxlength=\"50\"  pattern=\"^[a-zA-Z0-9 _-]+$\"  type=\"text\" name=\"participants_subgroups[" + fundraiser.subgroups[i].id + "]\" value=\"" + fundraiser.subgroups[i].name + "\" class=\"form-control ip non_specific_char\"\n" +
                        "       placeholder=\"\"></div>");
                    used_index.push(parseInt(fundraiser.subgroups[i].id));
                    current_groups++;
                }

            }, 1000);

        }

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

            const data = {
                'action': 'check_fundraiser_name',
                'fundraiser_name': fundraiser_name,
                'fundraiser_id': '',
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

