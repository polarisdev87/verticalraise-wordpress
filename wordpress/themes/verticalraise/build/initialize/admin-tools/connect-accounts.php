<?php

 /**
  * Includes Connect Accounts in Dashboard Admin
  */

use classes\app\stripe\Stripe_Form;
use classes\models\tables\Stripe_Transfers;

if (!defined('ABSPATH'))
     exit;
 
 if (is_admin()) {
     add_action( 'admin_menu', 'add_connect_to_menu' );
 }


 function add_connect_to_menu() {

     add_menu_page(
         __( 'Connect', 'textdomain' ),
         'Connect Accounts',
         'manage_options',
         'connect_accounts_page',
         'connect_accounts_page_html',
         'dashicons-clipboard',
         8
     );

     add_submenu_page(
         null,
         'Transfers',
         'Transfers',
         'manage_options',
         'transfers_page',
         'transfers_page_html'
     );

	 add_submenu_page(
		 null,
		 'Payout',
		 'Payout',
		 'manage_options',
		 'payouts_page',
		 'payouts_page_html'
	 );
 }


function connect_accounts_page_html() {

    load_class( 'connect_accounts_list.class.php' );

    ?>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
        #wpcontent {
            padding-right: 20px;
        }
    </style>

    <h2>Connect Accounts</h2><?php

    $Donations_List = new \Connect_Accounts_List();
    $Donations_List->prepare_items();
	?>

    <?php

	if ( isset( $_GET['show'] ) && $_GET['show'] === 'ended' ){
        $extraPropertyEnded = "checked";
        $extraPropertyAll = "";

	}else{
		$extraPropertyAll = "checked";
		$extraPropertyEnded = "";
    }

	$hideChecked = '';
	if ( isset( $_GET['raised'] ) && $_GET['raised'] === "on" ) {
		$hideChecked = 'checked';
	}

	?>
    <div style="">
        <form method="get" style="display: flex;justify-content: flex-end;">
            <input type="hidden" name="page" value="connect_accounts_page" />
            <div style="display: flex;justify-content: space-around;align-items: center;width: 900px;">
                <div>
                    <label for="raised">Hide $0 </label>
                    <input type="checkbox" id="raised" name="raised" <?php echo $hideChecked; ?>>
                </div>

                <label for="radio_all">Show All</label>
                <input type="radio" name="show" id="radio_all" value="all" <?php echo $extraPropertyAll; ?>>
                <label for="radio_ended">Show Ended</label>
                <input type="radio" name="show" id="radio_ended" value="ended" <?php echo $extraPropertyEnded; ?>>


                <label for="start_date">Start Date: </label> <input type="text" id="start_date" name="start_date" class="datepicker" value="<?php if(!empty($_GET['start_date'])) echo $_GET['start_date']; ?>">
                <label for="end_date">End Date: <input type="text" id="end_date"  name="end_date" class="datepicker" value="<?php if(!empty($_GET['end_date'])) echo $_GET['end_date']; ?>"></label>
                <div style="width: 200px;">
                    <label>Search by</label>
                    <input type="radio" name="t" value="fundraiser_name" checked>Fundraiser Name<br>
                </div>

            </div>

			<?php $Donations_List->search_box('Get results', 'search_email_field'); ?>
        </form>
    </div>


    <script>
        $(document).ready(function(){
            $( ".datepicker" ).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
            });
        })
    </script>

	<?php
    $Donations_List->display();

}

function transfers_page_html() {
    echo "<h2>Transfer</h2>";

    if ( isset ( $_POST['fundraiser_id'] ) ) {

        if ( wp_verify_nonce( $_POST['wpnonce'] , 'transfer-fundraiser-id-'. $_POST['fundraiser_id'] ) ) {
            load_class( 'payment.class.php' );
            try {
                $success = \Payments::transferBackFromAccounts( $_POST['fundraiser_id'], $_POST['account_id'] , $_POST['available_amount'], Stripe_Transfers::TRANSFER_BY_CONNECT_PAGE );
                if ( $success ) { 
                    ?>
                    <div class="notice notice-success">
                        <p>Amount Transferred.</p>
                    </div>
                    <?php 
                } else {
	                ?>
                    <div class="notice notice-error">
                        <p>Failed to transfer.</p>
                    </div>
	                <?php
                }
            } catch ( \Exception $e) {
                if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
                    newrelic_notice_error( $e->getMessage(), $e );
                } ?> 
                <div class="notice notice-error">
                    <p>Stripe Error: <?php echo $e->getMessage(); ?></p>
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
    
    <p>Please, make click on TRANSFER button to confirm.</p>
    <form method="post" >
    <?php

        $fid = $_GET['fid'];
        $title = get_the_title ($fid );

        require_once( TEMPLATEPATH . '/stripe-php/config.php' );


        $stripe_connect = new Stripe_Form();
        $get_account    = $stripe_connect->get_account_id( $fid );
        $account_id     = $get_account->stripe_account_id;

        $balance = \Stripe\Balance::retrieve( array ('stripe_account' => $account_id ) );

        $pending_amount  = $balance->jsonSerialize()["pending"][0]['amount'] ;
        $pending_amount  = $pending_amount / 100;
        $pending_amount  = number_format($pending_amount, 2);

        $available_amount = $formatted_available_amount  = $balance->jsonSerialize()["available"][0]['amount'] ;
        $formatted_available_amount  = $formatted_available_amount / 100;
        $formatted_available_amount  = number_format($formatted_available_amount, 2);

    ?>
    <table>
        <tr>
            <td>
                <label for="fundraiser_id">Fundraiser ID:</label>
            </td>
            <td>
                <input type="text" name="fundraiser_id" value="<?php if ( isset ( $fid ) ) echo $fid; ?>" readonly="readonly"/>
            </td>
        </tr>
        <tr>
            <td>
                <label for="fundraiser_name">Fundraiser Name:</label>
            </td>
            <td>            
                <input type="text" name="fundraiser_name" value="<?php if ( isset ( $title ) ) echo $title;?>" readonly="readonly"/>
            </td>
        </tr>
        <tr>
            <td>
                <label for="pending_amount">Pending Amount:</label>
            </td>
            <td>
                <input type="text" name="pending_amount" value="<?php if ( isset ( $pending_amount ) ) echo $pending_amount;?>" readonly="readonly"/>
            </td>
        </tr>
        <tr>
            <td>
                <label for="available_amount">Available Amount:</label>
            </td>
            <td>             
                <input type="text" name="available_amount_" value="<?php if ( isset ( $formatted_available_amount ) ) echo $formatted_available_amount;?>" readonly="readonly"/>
                <input type="hidden" name="available_amount" value="<?php if ( isset ( $available_amount ) ) echo $available_amount;?>" readonly="readonly"/>
                <input type="hidden" name="account_id" value="<?php if ( isset ( $account_id ) ) echo $account_id;?>" readonly="readonly"/>
            </td>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="wpnonce" value="<?php if ( isset ( $fid ) ) echo wp_create_nonce( 'transfer-fundraiser-id-'. $fid ); ?>">
            </td>
            <td>
                <input type="submit" id="search-submit" class="button" value="TRANSFER">
            </td>
        </tr>
    </table>
    
    </form>
    <?php 
    }

}

function payouts_page_html() {
	echo "<h2>Payout</h2>";

	if ( isset ( $_POST['fundraiser_id'] ) ) {

		if ( wp_verify_nonce( $_POST['wpnonce'] , 'payout-fundraiser-id-'. $_POST['fundraiser_id'] ) ) {
			load_class( 'payment.class.php' );
			try {
				$success = \Payments::payoutToAccount( $_POST['fundraiser_id'], $_POST['account_id'], $_POST['available_amount']);
				if ( $success ) {
					?>
                    <div class="notice notice-success">
                        <p>Amount Payout.</p>
                    </div>
					<?php
				}
			} catch ( \Exception $e) {
				if ( extension_loaded( 'newrelic' ) ) { // Ensure PHP agent is available
					newrelic_notice_error( $e->getMessage(), $e );
				} ?>
                <div class="notice notice-error">
                    <p>Stripe Error: <?php echo $e->getMessage(); ?></p>
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

        <p>Please, make click on PAYOUT button to confirm.</p>
        <form method="post" >
			<?php

			$fid = $_GET['fid'];
			$title = get_the_title ($fid );

			require_once( TEMPLATEPATH . '/stripe-php/config.php' );


			$stripe_connect = new Stripe_Form();
			$get_account    = $stripe_connect->get_account_id( $fid );
			$account_id     = $get_account->stripe_account_id;

			$balance = \Stripe\Balance::retrieve( array ('stripe_account' => $account_id ) );

			$pending_amount  = $balance->jsonSerialize()["pending"][0]['amount'] ;
			$pending_amount  = $pending_amount / 100;
			$pending_amount  = number_format($pending_amount, 2);

			$available_amount = $formatted_available_amount  = $balance->jsonSerialize()["available"][0]['amount'] ;
			$formatted_available_amount  = $formatted_available_amount / 100;
			$formatted_available_amount  = number_format($formatted_available_amount, 2);

			?>
            <table>
                <tr>
                    <td>
                        <label for="fundraiser_id">Fundraiser ID:</label>
                    </td>
                    <td>
                        <input type="text" name="fundraiser_id" value="<?php if ( isset ( $fid ) ) echo $fid; ?>" readonly="readonly"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="fundraiser_name">Fundraiser Name:</label>
                    </td>
                    <td>
                        <input type="text" name="fundraiser_name" value="<?php if ( isset ( $title ) ) echo $title;?>" readonly="readonly"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="pending_amount">Pending Amount:</label>
                    </td>
                    <td>
                        <input type="text" name="pending_amount" value="<?php if ( isset ( $pending_amount ) ) echo $pending_amount;?>" readonly="readonly"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="available_amount">Available Amount:</label>
                    </td>
                    <td>
                        <input type="text" name="available_amount_" value="<?php if ( isset ( $formatted_available_amount ) ) echo $formatted_available_amount;?>" readonly="readonly"/>
                        <input type="hidden" name="available_amount" value="<?php if ( isset ( $available_amount ) ) echo $available_amount;?>" readonly="readonly"/>
                        <input type="hidden" name="account_id" value="<?php if ( isset ( $account_id ) ) echo $account_id;?>" readonly="readonly"/>

                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="hidden" name="wpnonce" value="<?php if ( isset ( $fid ) ) echo wp_create_nonce( 'payout-fundraiser-id-'. $fid ); ?>">
                    </td>
                    <td>
                        <input type="submit" id="search-submit" class="button" value="PAYOUT">
                    </td>
                </tr>
            </table>

        </form>
		<?php
	}

}
