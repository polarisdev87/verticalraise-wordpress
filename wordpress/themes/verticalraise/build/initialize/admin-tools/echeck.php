<?php

 /**
  * Includes e check in Dashboard Admin
  */

 if (!defined('ABSPATH'))
     exit;
 
 if (is_admin()) {
     add_action( 'admin_menu', 'add_echeck_to_menu' );
 }


 function add_echeck_to_menu() {

     add_menu_page(
         'Add E-check or check',
         'Add e-check or check',
         'manage_options',
         'echeck_and_check_page',
         'echeck_page_new_html',
         'dashicons-list-view',
         8
     );

 }



function echeck_page_new_html() {

	echo "<h1>Add Donation</h1>";
	?>
    <script type="application/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/jquery.validate.min.js"></script>

    <style>

        #save_donation {
            max-width: 500px;
            height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
        }

        #save_donation > div {
            display: grid;
            grid-template-columns: 20% 75%;
        }

        #save_donation > div:nth-of-type(9) {
            display: grid;
            grid-template-columns: 20% 37.5% 37.5%;
        }

        #anonymous {
            align-self: center;
        }

        #submit {
            text-transform: uppercase;
            font-weight: bold;
        }

        #messages {
            max-width: 500px;
            text-align: center;
        }

        #success_message{
            font-size: 16px;
            font-weight: bold;
        }

    </style>


    <form id="save_donation" action="" method="post">
        <div>
            <label for="fundraiser_id">Fundraiser</label>
            <input type="text" id="fundraiser_id" name="fundraiser_id" placeholder="Fundraiser Id"
                   required="required">
        </div>
        <div>
            <label for="user_id">Participant</label>
            <input type="text" id="user_id" name="user_id" placeholder="Participant Id">
        </div>

        <div>
            <label for="anonymous">Anonymous</label>
            <input type="checkbox" id="anonymous" name="anonymous">
        </div>

        <div>
            <label for="donor_name">Name</label>
            <input type="text" id="donor_name" name="donor_name" placeholder="Donor Name">
        </div>

        <div>
            <label for="donor_name">Email</label>
            <input type="text" id="donor_email" name="donor_email" placeholder="Donor Email">
        </div>

        <div>
            <label for="donation_amount">Amount</label>
            <input type="text" id="donation_amount" name="donation_amount" placeholder="Donation Amount"
                   required="required">
        </div>

        <div>
            <label for="donor_comment">Comment</label>
            <textarea id="donor_comment" name="donor_comment" placeholder="Donor Comment"></textarea>
        </div>

        <div>
            <label for="donation_type">Donation Type</label>
            <select id="donation_type" name="donation_type" required="required">
                <option selected disabled>Select donation type</option>
                <option value="check">Check</option>
                <option value="echeck">E-check</option>
            </select>
        </div>

        <div>
            <div></div>
            <div></div>
            <button type="submit" id="submit" class="button">Save</button>
        </div>
    </form>

    <div id="messages">
        <p id="success_message" style="color: green;"></p>
        <p id="error_message" style="color: red;"></p>
    </div>

    <script>
        jQuery(document).ready(function () {

            jQuery("#save_donation").submit(function (e) {

                e.preventDefault();
                jQuery('#submit').prop("disabled", "disabled");

                const formData = new FormData( jQuery("#save_donation")[0] );
                const data = {
                    'action': 'save_echeck_donation',
                    'f_id': formData.get('fundraiser_id'),
                    'uid': formData.get('user_id'),
                    'anonymous': formData.get('anonymous'),
                    'donor_name': formData.get('donor_name'),
                    'donor_email': formData.get('donor_email'),
                    'donor_comment': formData.get('donor_comment'),
                    'donation_amount': formData.get('donation_amount'),
                    'donation_type': formData.get('donation_type'),
                };

                jQuery.ajax(
                    "/wp-admin/admin-ajax.php",
                    {
                        type: 'POST',
                        data: data,
                        complete: function (jqXHR, textStatus) {
                            let status = jqXHR.status;
                            let responseJSON = jqXHR.responseJSON;

                            console.log(status);
                            console.log(responseJSON);

                            if (status === 200) {
                                jQuery('#success_message').text(responseJSON.message);
                            } else {
                                jQuery('#error_message').text("Something went wrong, donation wasn't saved.");
                                jQuery('#submit').removeProp("disabled", "disabled");
                            }

                        }
                    }
                );


            });
        });
    </script>
	<?php

}