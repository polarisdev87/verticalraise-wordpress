<?php 

/**
 * Template Name: Donation Payment 
 */
require_once( get_template_directory() . '/stripe-php/config.php' );

// Get the fundraiser ID
if ( isset($_GET['fundraiser_id']) ) {
    $fundraiser_id = $_GET['fundraiser_id'];
} else {
    $fundraiser_id = '';
}

// Get the user id
if ( isset($_GET['uid']) ) {
    $uid = '/' . $_GET['uid'];
} else {
    if ( is_user_logged_in() ) {
        global $user_ID;
        $campaign_participations = get_user_meta($user_ID, 'campaign_participations', true); // Single lookup
        $participations_array = json_decode($campaign_participations);
        if ( !empty($participations_array) ) {
            if ( !in_array($user_ID, $participations_array) ) {
                    $uid = '/' . $user_ID;
            }
        }
    } else {
        $uid = '';
    }
}

// Include the theme header
get_header(); 

?>
    <script>
        jQuery(document).ready(function() {
            jQuery('.card-number').focus();
        });
    </script>
<?php

$args = array(
	'post_type' => 'fundraiser',
	'post_status' => 'publish',
	'p' => $fundraiser_id
);
$fundraiser_query = new WP_Query($args);

while ($fundraiser_query->have_posts()) : $fundraiser_query->the_post();

	if ( !empty($_GET['amount']) ) {
		$price = $_GET['amount'];
	} else {
		$price = 0;
	}

?>
    <div id="content">
        <div class="maincontent">
            <div class="section group">
                <div class="col span_8_of_12">
                    <div class="section group">
                        <div class="col span_8_of_12">
                            <h1 style="margin: 0;">Amount: $<?php echo $_GET['amount']; ?></h1>
                        </div>
                        <div class="col span_4_of_12">
                            <img class="payment_img" src="<?php bloginfo('template_directory'); ?>/assets/images/secure_checkout.png">
                        </div>
                    </div>
                    <hr />
                    <div class="PaymentError" style="text-align: center; display: none;">
                        <p class="errorMsg">There was an error please try again.</p>
                    </div>
                    <div class="PaymentOptions">
                        <div class="mobile_hide">
                            <div class="section group">
                                <div class="col span_8_of_12">
                                    <p><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;Your receipt will be emailed to: <strong><?php echo $_GET['email']; ?></strong></p>
                                </div>
                                <div class="col span_4_of_12">
                                    <p><img class="payment_img" src="<?php bloginfo('template_directory'); ?>/assets/images/cc-logos.jpg"></p>
                                </div>
                            </div>
                            <div class="section group">
                                <div class="col span_12_of_12">
                                    <p style="text-align: center;">This participant will receive the credit for your donation</p>
                                </div>
                            </div>
                            <div class="section group">
                                <div class="col span_4_of_12"></div>
                                <div class="col span_4_of_12">
                                    <?php
                                    $campaign_participations1 = json_decode(get_post_meta($fundraiser_id, 'campaign_participations', true));
                                    if ( $campaign_participations1 === null ) {
                                            $campaign_participations1 = array();
                                    }
                                    
                                    /**
                                     * Lookup user ids attached to this fundraiser
                                     */
                                    
                                    // Get fundraiser participants
                                    $participants_f = get_fundraiser_participants($fundraiser_id);

									if ( !empty($participants_f) ) {
 
                                        echo '<select name="participants">';
                                        echo '<option value="">'.get_post_meta($_GET['fundraiser_id'], 'team_name', true).'</option>';
                                        foreach ( $participants_f as $p_key => $p_user) {
                                            $participants_by_id[$p_user->ID] = $p_user->display_name;
                                            $participant_ids[] = $p_user->ID;                                              if ( isset($_GET['uid']) && $_GET['uid'] == $p_user->ID ) {
                                                echo '<option selected="selected" value="' . $p_user->ID . '">' . $p_user->display_name . '</option>';
                                            } else {
                                                echo '<option value="' . $p_user->ID . '">' . $p_user->display_name . '</option>';
                                            }
                                        }
                                        echo '</select>';
                                    }
                                ?>
                                </div>
                                <div class="col span_4_of_12"></div>
                            </div>
                        </div>
                        <div class="mobile_display">
                            <div class="section group">
                                <div class="col span_12_of_12">
                                    <p><i class="fa fa-envelope-o"></i>&nbsp;&nbsp;Your receipt will be emailed to: <strong><?php echo $_GET['email']; ?></strong></p>
                                </div>
                            </div>
                            <div class="section group">
                                <div class="col span_12_of_12">
                                    <p style="text-align: center;">This participant will receive the credit for your donation</p>
                                    <?php
                                    if ( !empty($participants_f) ) {
                                            echo '<select name="participants">';
                                            echo '<option value="">'.get_post_meta($_GET['fundraiser_id'], 'team_name', true).'</option>';
                                            foreach ( $participants_f as $p_key => $p_user ) {
                                                    if ( $_GET['uid'] == $p_user->ID ) {
                                                            echo '<option selected="selected" value="' . $p_user->ID . '">' . $p_user->display_name . '</option>';
                                                    } else {
                                                            echo '<option value="'. $p_user->ID . '">' . $p_user->display_name . '</option>';
                                                    }
                                            }
                                            echo '</select>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="section group">
                                <div class="col span_12_of_12">
                                    <img class="payment_img" src="<?php bloginfo('template_directory'); ?>/assets/images/cc-logos.jpg">
                                </div>
                            </div>
                        </div>
                        <div class="checkout_button" style="text-align: center;">
                            <button id="customButton" class="custom_button">Pay with Card</button>
                        </div>
                    </div>
                    <div id="loading" style="text-align: center; display: none;"><img src="<?php bloginfo('template_directory'); ?>/assets/images/loading_spinner.gif" /></div>
                    <script>
                        jQuery('select[name="participants"]').on('change', function() {
                            var url = location.href;
                            if (url.toLowerCase().indexOf("&uid=") >= 0) {
                                var regEx = /([?&]uid)=([^#&]*)/g;
                                var newurl = url.replace(regEx, '&uid='+this.value);
                            } else {
                                var newurl = url + '&uid=' +this.value;
                            }
                            //alert( newurl );
                            location.href = newurl;
                        });
                    </script>
                    <script src="https://checkout.stripe.com/checkout.js"></script>
                        <?php
                            $amount = ( !empty($_GET['amount']) ) ? $_GET['amount']: '';
                            $anonymous = ( !empty($_GET['anonymous']) ) ? $_GET['anonymous']: '';
                            $fname = ( !empty($_GET['fname']) ) ? $_GET['fname']: '';
                            $lname = ( !empty($_GET['lname']) ) ? $_GET['lname']: '';
                            $email = ( !empty($_GET['email']) ) ? $_GET['email']: '';
                            $uid = ( !empty($_GET['uid']) ) ? $_GET['uid']: '';
                            $media = ( !empty($_GET['media']) ) ? $_GET['media']: '';
                        ?>
                    <script>
                        var handler = StripeCheckout.configure({
                            key: '<?php echo $stripe['publishable_key'] ?>',
                            image: '',
                            locale: 'auto',
                            token: function(token) {
                                jQuery('.PaymentOptions').hide();
                                jQuery('#loading').show();
                                jQuery.ajax({
                                    type: "POST",
                                    url: "<?php get_bloginfo('url'); ?>/ajax-payment",
                                    data: {
                                        stripeToken: token.id,
                                        stripeEmail: token.email,
										<?php if ( !empty($amount) ) { ?>
                                        amount: <?php echo (float) $amount; ?>,
										<?php } 
                                        if ( !empty($anonymous) ) { ?>
                                        anonymous: <?php echo $anonymous; ?>,
										<?php }
                                        if ( !empty($fname) ) { ?>
                                        fname: '<?php echo $fname; ?>',
										<?php } 
                                        if ( !empty($lname) ) { ?>
                                        lname: '<?php echo $lname; ?>',
										<?php }
                                        if ( !empty($email) ) { ?>
                                        email: '<?php echo $email; ?>',
										<?php } 
                                        if ( !empty($fundraiser_id) ) { ?>
                                        fundraiser_id: <?php echo $fundraiser_id; ?>,
										<?php } 
                                        if ( !empty($uid) ) { ?>
                                        uid: <?php echo $uid; ?>,
										<?php } 
                                        if ( !empty($media) ) { ?>
                                        media: '<?php echo $media; ?>',
										<?php } ?>
                                        nonce: '<?php echo wp_create_nonce( 'make-payment_' . $fundraiser_id . '_' ); ?>'
                                    },
                                    success: function(data) {
                                        jQuery('.PaymentError').hide();
                                        if ( data == 'success' ) {
                                            window.location.href = "<?php echo get_bloginfo( 'url' ) . "/thank-you-payment/?fundraiser_id=" . $fundraiser_id . "&fname=" . $fname . "&lname=" . $lname . "&email=" . $email . "&uid=" . $uid; ?>";
                                        } else {
                                            console.log(data);
                                            jQuery('.PaymentError').show();
                                            jQuery('#loading').hide();
                                            jQuery('.PaymentOptions').show();
                                        }
                                    },
                                    error: function(e) {
                                        console.log(e);
                                        jQuery('.PaymentError').show();
                                        jQuery('#loading').hide();
                                        jQuery('.PaymentOptions').show();
                                    },
                                });
                            }
                        });

                        document.getElementById('customButton').addEventListener('click', function(e) {
                            handler.open({
                                name: 'VerticalRaise',
                                description: '<?php echo get_the_title($fundraiser_id); ?>',
                                amount: <?php echo $amount * 100; ?>,
                                email: '<?php echo $email; ?>',
                                allowRememberMe: false
                            });
                            e.preventDefault();
                        });

                        window.addEventListener('popstate', function() {
                            handler.close();
                        });
                    </script>
                </div>
                <div class="col span_4_of_12">
					<?php get_sidebar('fundraiser'); ?>
                </div>
            </div>
        </div>
    </div>
<?php 

endwhile; 

get_footer(); 