<?php

 /**
  * Includes Refund in Dashboard Admin
  */

 if (!defined('ABSPATH'))
     exit;
 
 if (is_admin()) {
     add_action( 'admin_menu', 'add_refund_to_menu' );
 }


 function add_refund_to_menu() {

     add_menu_page(
         __( 'Donations', 'textdomain' ),
         'Donations',
         'manage_options',
         'donations_page',
         'donations_page_html',
         'dashicons-list-view',
         8
     );

     add_submenu_page(
         null,
         'Refunds',
         'Refunds',
         'manage_options',
         'refunds_page',
         'refunds_page_html'
     );

	 add_submenu_page(
		 null,
		 'DeleteDonation',
		 'DeleteDonation',
		 'manage_options',
		 'delete_donation',
		 'delete_donation_page_html'
	 );
 }


function donations_page_html() {

    load_class( 'donations_list.class.php' );

    ?><h2>Donations</h2><?php

    $Donations_List = new \Donations_List();
    $Donations_List->prepare_items();
    ?>

    <div style="">
        <form method="get" style="display: flex;justify-content: flex-end;">
            <input type="hidden" name="page" value="donations_page" />
            <div style="display: inline-flex;justify-content: flex-end;align-items: center;max-width: 500px;">
                <label>Search by</label>
                <input type="radio" name="t" value="email" checked>Email<br>
                <input type="radio" name="t" value="donor_name">Donor Name<br>
                <input type="radio" name="t" value="participant_name">Participant Name<br>
                <input type="radio" name="t" value="fundraiser_name">Fundraiser Name<br>
            </div>

            <?php $Donations_List->search_box('Get results', 'search_email_field'); ?>
        </form>
    </div>

    <style>
        td.name.column-name {
            cursor: cell;
        }

        td.user_name.column-user_name {
            cursor: copy;
        }
    </style>

    <?php
    $Donations_List->display();
    ?>

    <script>
        jQuery(document).ready(function () {
            jQuery("td.name.column-name").click(function () {

                if (jQuery(this).find("input").length === 0) {

                    if (jQuery(this).siblings("td.id.column-id.has-row-actions.column-primary").length) {
                        let ID = jQuery(this).siblings("td.id.column-id.has-row-actions.column-primary")[0].innerText;
                        let content = jQuery(this).text();
                        jQuery(this).text("");

                        let input = document.createElement("input");
                        input.type = "text";
                        input.value = content;
                        input.name = "donor_name";
                        input.required = true;

                        jQuery(input).blur(function (e) {
                            let content = jQuery(this).val();
                            if (content) {
                                jQuery(this).parent().text(content);
                                jQuery(this).parent().find("input").remove();
                            }
                        });

                        jQuery(input).change(function (e) {

                            let content = jQuery(this).val();
                            if (!content) {
                                return false;
                            }
                            console.log(content);

                            var data = {
                                'action': 'change_donor_name',
                                'd_id': ID,
                                'donor_name': content,
                            };

                            jQuery.ajax(
                                "/wp-admin/admin-ajax.php",
                                {
                                    type: 'POST',
                                    data: data,
                                    complete: function (jqXHR) {
                                        let status = jqXHR.status;
                                        let responseJSON = jqXHR.responseJSON;

                                        if (status !== 200) {
                                            alert(responseJSON.message);
                                        }

                                    }
                                }
                            );
                        });

                        jQuery(this).html(input);
                        jQuery(this).find("input").focus();

                    }

                }

            });


            jQuery("td.user_name.column-user_name").click(function () {

                if (jQuery(this).find("select").length === 0) {

                    if (jQuery(this).siblings("td.id.column-id.has-row-actions.column-primary").length) {
                        let ID = jQuery(this).siblings("td.id.column-id.has-row-actions.column-primary")[0].innerText;

                        let element = jQuery(this);
                        element.css("cursor" , "progress");
                        let content = element.text();
                        element.text("");

                        let data = {
                            'action': 'get_f_participants',
                            'd_id': ID,
                        };

                        jQuery.ajax(
                            "/wp-admin/admin-ajax.php",
                            {
                                type: 'POST',
                                data: data,
                                complete: function (jqXHR) {
                                    let status = jqXHR.status;
                                    let responseJSON = jqXHR.responseJSON;
                                    element.css("cursor" , "copy");

                                    if (status === 200) {
                                        let select = document.createElement("select");
                                        select.name = "participant_id";

                                        let option = document.createElement("option");
                                        option.value = "0";
                                        option.text = "None";
                                        select.add(option, null);

                                        for (let i = 0; i < responseJSON.length; i++) {
                                            let option = document.createElement("option");
                                            option.value = responseJSON[i].participant_id;
                                            option.text = responseJSON[i].participant_name;
                                            if (content === option.text) {
                                                option.selected = true;
                                            }
                                            select.add(option, null);
                                        }

                                        select.addEventListener("blur", function (e) {
                                            let content = jQuery(this).find('option:selected').text();
                                            if (content) {
                                                jQuery(this).parent().text(content);
                                                jQuery(this).parent().find("select").remove();
                                            }
                                        });

                                        select.addEventListener("change", function (e) {

                                            let participant_id = jQuery(this).val();
                                            if (!participant_id) {
                                                return false;
                                            }

                                            let data = {
                                                'action': 'change_donation_participant',
                                                'd_id': ID,
                                                'uid': participant_id,
                                            };

                                            jQuery.ajax(
                                                "/wp-admin/admin-ajax.php",
                                                {
                                                    type: 'POST',
                                                    data: data,
                                                    complete: function (jqXHR, textStatus) {
                                                        let status = jqXHR.status;
                                                        let responseJSON = jqXHR.responseJSON;

                                                        if (status !== 200) {
                                                            alert(responseJSON.message);
                                                        }

                                                    }
                                                }
                                            );
                                        });

                                        element.append(select);
                                        element.find("select").focus();

                                    }

                                }
                            }
                        );
                    }
                }

            });


        });
    </script>

	<?php
}

function refunds_page_html() {
    echo "<h2>Donations</h2>";

    if ( isset ( $_POST['donation_id'] ) ) {
        if ( wp_verify_nonce( $_POST['wpnonce'] , 'refund-donation-id-'. $_POST['donation_id'] ) ) {
            load_class( 'payment.class.php' );
            try {
                $success = \Payments::refundPayment( $_POST['donation_id'] );
                if ( $success ) { 
                    
                    ?> 
                    <div class="notice notice-success">
                        <p>Donation refunded.</p>
                    </div>
                    <?php 
                }
            } catch ( \Exception $e) {
                if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                    newrelic_notice_error( $e->getMessage(), $e );
                } ?> 
                <div class="notice notice-error">
                    <p><?php echo $e->getMessage(); ?></p>
                </div>
                <?php
            }
            
        } else { ?> 
            <div class="notice notice-warning">
                <p>Invalid wordpress security token.</p>
            </div>
            <?php 
        }
        
    } else {?>
    
    <p>Please, make click on REFUND button to confirm.</p>
    <form method="post" >

    <table>
        <tr>
            <td>
                <label for="donation_id">Donation ID:</label>
            </td>
            <td>
                <input type="text" name="donation_id" value="<?php if ( isset ( $_GET['donation'] ) ) echo $_GET['donation'];?>" readonly="readonly"/>
            </td>
        </tr>
        <tr>
            <td>
                <label for="email">Email:</label>
            </td>
            <td>            
                <input type="text" name="email" value="<?php if ( isset ( $_GET['email'] ) ) echo $_GET['email'];?>" readonly="readonly"/>
            </td>
        </tr>    
        <tr>
            <td>
                <label for="amount">Amount:</label>
            </td>
            <td>             
                <input type="text" name="amount" value="<?php if ( isset ( $_GET['amount'] ) ) echo $_GET['amount'];?>" readonly="readonly"/>
            </td>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="wpnonce" value="<?php if ( isset ( $_GET['donation'] ) ) echo wp_create_nonce( 'refund-donation-id-'. $_GET['donation'] ); ?>">
            </td>
            <td>
                <input type="submit" id="search-submit" class="button" value="REFUND">
            </td>
        </tr>
    </table>
    
    </form>
    <?php 
    }

}

function delete_donation_page_html() {

	if ( empty( $_GET['donation_id'] ) ) {
		echo "Missing donation id";
	} else {

		$donation_id = $_GET['donation_id'];
		$donations   = new \classes\models\tables\Donations();
		$donation    = $donations->get_donation( $donation_id );

		?>

        <style>
            #donation_info {
                max-width: 500px;
                display: flex;
                flex-direction: column;
                height: 300px;
                justify-content: space-evenly;
            }

            #donation_info > div {
                display: grid;
                grid-template-columns: 25% 75%;
            }

            #donation_info > div > div:first-of-type {
                font-weight: bold;
            }

            #messages{
                max-width: 500px;
            }

        </style>
		<?php if ( $donation ) { ?>
            <h1>DELETE DONATION <?php echo $donation->id; ?></h1>

            <div id="donation_info">
                <div>
                    <div>Fundraiser</div>
                    <div><?php echo $donation->f_id; ?></div>
                </div>
                <div>
                    <div>Participant</div>
                    <div><?php echo $donation->uid ?></div>
                </div>
                <div>
                    <div>Amount</div>
                    <div><?php echo $donation->amount ?></div>
                </div>
                <div>
                    <div>Email</div>
                    <div><?php echo $donation->email ?></div>
                </div>
                <div>
                    <div>Name</div>
                    <div><?php echo $donation->name ?></div>
                </div>
                <div>
                    <div></div>
                    <div>
                        <button class="button" id="confirm_deletion">DELETE</button>
                    </div>
                </div>
            </div>

            <div id="messages">
                <div id="success_message" class="notice notice-success" style="display: none"></div>
                <div id="error_message" class="notice notice-error" style="display: none"></div>
            </div>

            <script>
                jQuery(document).ready(function () {
                    jQuery("#confirm_deletion").click(function () {
                        let data = {
                            action: 'delete_donation',
                            d_id: '<?php echo $donation->id; ?>'
                        };
                        jQuery.ajax("/wp-admin/admin-ajax.php", {
                            data: data,
                            type: 'POST',
                            complete: function (jqXHR) {
                                let status = jqXHR.status;
                                let responseJSON = jqXHR.responseJSON;
                                console.log(responseJSON);
                                if (status === 200) {
                                    jQuery("#success_message").text(responseJSON.message).show();
                                } else {
                                    jQuery("#error_message").text(responseJSON.message).show();
                                }

                                jQuery("#confirm_deletion").prop('disabled', 'disabled');
                            }
                        });
                    });
                });
            </script>

			<?php
		} else {
			echo "Cannot find donation";
		}
	}
}