<?php /* Template Name: Import DB */ ?>
<?php
if(!empty($_GET['fundraiser_id'])) {
	global $wpdb;
	$args = array(
		'p'              => $_GET['fundraiser_id'],
		'post_type'      => 'fundraiser',
		'post_status'    => 'publish',
		'posts_per_page' => 1
	);
	$fundraiser_query = new WP_Query( $args );
	while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();
		echo '<h1>' . get_the_ID() . '</h1><br/>';
		$fundraiser_id            = get_the_ID();
		$email_share              = json_decode( get_post_meta( $fundraiser_id, 'email_share', true ), true );
		$sms_share                = json_decode( get_post_meta( $fundraiser_id, 'sms_share', true ), true );
		$facebook_share           = json_decode( get_post_meta( $fundraiser_id, 'facebook_share', true ), true );
		$twitter_share            = json_decode( get_post_meta( $fundraiser_id, 'twitter_share', true ), true );
		$flyer_share              = json_decode( get_post_meta( $fundraiser_id, 'flyer_share', true ), true );
		$campaign_participations1 = json_decode( get_post_meta( $fundraiser_id, 'campaign_participations', true ) );
		if ( $campaign_participations1 === null ) {
			$campaign_participations1 = array();
		}
		$campaign_participations2 = array();
		$user_query               = new WP_User_Query( array( 'role' => '' ) );
		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				$user_participation = json_decode( get_user_meta( $user->ID, 'campaign_participations', true ) );
				if ( ! empty( $user_participation ) ) {
					if ( in_array( $fundraiser_id, $user_participation ) ) {
						array_push( $campaign_participations2, $user->ID );
					}
				}
			}
		}
		$campaign_participations = array_unique( array_merge( $campaign_participations1, $campaign_participations2 ) );
		if ( ! empty( $campaign_participations ) ) {
			$campaign_participations_green = array();
			$campaign_participations_blue  = array();
			$campaign_participations_red   = array();
			foreach ( $campaign_participations as $participant ) {
				$net_amount      = 0;
				$email           = 0;
				$sms             = 0;
				$args            = array(
					'post_type'      => 'supporter',
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'post_parent'    => $fundraiser_id,
					'meta_query'     => array(
						array(
							'key'     => 'uid',
							'value'   => $participant,
							'type'    => 'CHAR',
							'compare' => '='
						)
					)
				);
				$supporter_query = new WP_Query( $args );
				if ( $supporter_query->have_posts() ) :
					while ( $supporter_query->have_posts() ) : $supporter_query->the_post();
						$amount     = get_post_meta( get_the_ID(), 'amount', true );
						$net_amount = $net_amount + $amount;
					endwhile;
				endif;
				if ( ! empty( $email_share ) ) {
					foreach ( $email_share['user_array'] as $user_array ) {
						if ( $user_array['uid'] == $participant ) {
							$email = $user_array['total'];
						}
					}
				} else {
					$email = 0;
				}
				if ( ! empty( $sms_share ) ) {
					foreach ( $sms_share['user_array'] as $user_array ) {
						if ( $user_array['uid'] == $participant ) {
							$sms = $user_array['total'];
						}
					}
				} else {
					$sms = 0;
				}
				if ( $net_amount >= _PARTICIPATION_GOAL ) {
					$campaign_participations_green[ $participant ] = $net_amount;
				} else {
					if ( $email < 10 ) {
						$campaign_participations_red[ $participant ] = $net_amount;
					}
					if ( $email >= 10 && $email < 20 ) {
						$campaign_participations_blue[ $participant ] = $net_amount;
					}
					if ( $email >= 20 ) {
						$campaign_participations_green[ $participant ] = $net_amount;
					}
				}
			}
			arsort( $campaign_participations_red );
			arsort( $campaign_participations_blue );
			arsort( $campaign_participations_green );
			$participations_array_marged = array_replace( $campaign_participations_green, $campaign_participations_blue, $campaign_participations_red );
		}
		if ( ! empty( $participations_array_marged ) ) {
			foreach ( $participations_array_marged as $participant => $value ) {
				$net_amount      = 0;
				$supporters      = 0;
				$email           = 0;
				$facebook        = 0;
				$twitter         = 0;
				$sms             = 0;
				$flyerp          = 0;
				$smsp            = 0;
				$args            = array(
					'post_type'      => 'supporter',
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'post_parent'    => $fundraiser_id,
					'meta_query'     => array(
						array(
							'key'     => 'uid',
							'value'   => $participant,
							'type'    => 'CHAR',
							'compare' => '='
						)
					)
				);
				$supporter_query = new WP_Query( $args );
				if ( $supporter_query->have_posts() ) :
					while ( $supporter_query->have_posts() ) : $supporter_query->the_post();
						$amount     = get_post_meta( get_the_ID(), 'amount', true );
						$net_amount = $net_amount + $amount;
						$media      = get_post_meta( get_the_ID(), 'media', true );
						if ( $media == 'flyer' ) {
							$flyerp = $flyerp + $amount;
						}
						if ( $media == 'sms' ) {
							$smsp = $smsp + $amount;
						}
					endwhile;
					$supporters = $supporter_query->found_posts;
				endif;

				$email    = 0;
				$sms      = 0;
				$facebook = 0;
				$twitter  = 0;
				$flyer    = 0;

				if ( ! empty( $email_share ) ) {
					foreach ( $email_share['user_array'] as $user_array ) {
						if ( $user_array['uid'] == $participant ) {
							$email = $user_array['total'];
						}
					}
				} else {
					$email = 0;
				}
				if ( ! empty( $sms_share ) ) {
					foreach ( $sms_share['user_array'] as $user_array ) {
						if ( $user_array['uid'] == $participant ) {
							$sms = $user_array['total'];
						}
					}
				} else {
					$sms = 0;
				}
				if ( ! empty( $facebook_share ) ) {
					foreach ( $facebook_share['user_array'] as $user_array ) {
						if ( $user_array['uid'] == $participant ) {
							$facebook = $user_array['total'];
						}
					}
				} else {
					$facebook = 0;
				}
				if ( ! empty( $twitter_share ) ) {
					foreach ( $twitter_share['user_array'] as $user_array ) {
						if ( $user_array['uid'] == $participant ) {
							$twitter = $user_array['total'];
						}
					}
				} else {
					$twitter = 0;
				}
				if ( ! empty( $flyer_share ) ) {
					foreach ( $flyer_share['user_array'] as $user_array ) {
						if ( $user_array['uid'] == $participant ) {
							$flyer = $user_array['total'];
						}
					}
				} else {
					$flyer = 0;
				}

				$user_info = get_userdata( $participant );
				$results   = $wpdb->update( 'participant_fundraiser_details', array(
					'participant_name' => $user_info->display_name,
					'participant_id'   => $participant,
					'email'            => $email,
					'twitter'          => $twitter,
					'facebook'         => $facebook,
					'sms'              => $smsp,
					'flyer'            => $flyerp,
					'supporters'       => $supporters,
					'total'            => $net_amount,
					'fundraiser'       => $fundraiser_id
				),
					array(
						'participant_id' => $participant,
						'fundraiser'     => $fundraiser_id
					),
					array( '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d' ) ,
					array( '%d', '%d' )
				);
			}
		}
	endwhile;
}
?>