<?php

function download_report() {
    if ( isset($_GET['download_report']) ) {
        ob_start();
        include_once(TEMPLATEPATH."/xlsxwriter.class.php");
        
        $data1                    = array();
        $email_share              = json_decode(get_post_meta($_GET['fundraiser_id'], 'email_share', true), true);
        $sms_share                = json_decode(get_post_meta($_GET['fundraiser_id'], 'sms_share', true), true);
        $facebook_share           = json_decode(get_post_meta($_GET['fundraiser_id'], 'facebook_share', true), true);
        $twitter_share            = json_decode(get_post_meta($_GET['fundraiser_id'], 'twitter_share', true), true);
        $flyer_share              = json_decode(get_post_meta($_GET['fundraiser_id'], 'flyer_share', true), true);
        $campaign_participations1 = json_decode(get_post_meta($_GET['fundraiser_id'], 'campaign_participations', true));
        
        if ($campaign_participations1 === null) {
            $campaign_participations1 = array();
        }
        
        $campaign_participations2 = array();
        $user_query = new WP_User_Query(array( 'role' => '' ));
        
        if ( ! empty( $user_query->results ) ) {
            foreach ( $user_query->results as $user ) {
                $user_participation = json_decode(get_user_meta($user->ID, 'campaign_participations', true));
                if ( !empty($user_participation) ) {
                    if ( in_array($_GET['fundraiser_id'], $user_participation) ) {
                        array_push($campaign_participations2, $user->ID);
                    }
                }
            }
        }
        
        $campaign_participations = array_unique(array_merge($campaign_participations1, $campaign_participations2));
        if ( !empty($campaign_participations) ) {
            foreach ( $campaign_participations as $participant ) {
                $net_amount = 0;
                $supporters = 0;
                $email      = 0;
                $facebook   = 0;
                $twitter    = 0;
                $sms        = 0;
                $args = array(
                    'post_type' => 'supporter',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'post_parent' => $_GET['fundraiser_id'],
                    'meta_query' => array(
                        array(
                            'key' => 'uid',
                            'value' => $participant,
                            'type' => 'CHAR',
                            'compare' => '='
                        )
                    )
                );
                
                $supporter_query = new WP_Query($args);
                
                if ( $supporter_query->have_posts() ) :
                    while ($supporter_query->have_posts()) : $supporter_query->the_post();
                        $amount     = get_post_meta(get_the_ID(), 'amount', true);
                        $net_amount = $net_amount + $amount;
                        /*$media = get_post_meta(get_the_ID(), 'media', true);
                        switch ($media) {
                            case "email":
                                $email++;
                                break;
                            case "f":
                                $facebook++;
                                break;
                            case "t":
                                $twitter++;
                                break;
                            case "sms":
                                $sms++;
                                break;
                            default:
                        }*/
                    endwhile;
                    $supporters = $supporter_query->found_posts;
                endif;

                $email    = 0;
                $sms      = 0;
                $facebook = 0;
                $twitter  = 0;
                $flyer    = 0;

                if ( !empty($email_share) ) {
                    foreach ( $email_share['user_array'] as $user_array ) {
                        if ( $user_array['uid'] == $participant ) {
                            $email = $user_array['total'];
                        }
                    }
                } else {
                    $email = 0;
                }
                
                if ( !empty($sms_share) ) {
                    foreach ( $sms_share['user_array'] as $user_array ) {
                        if ( $user_array['uid'] == $participant ) {
                            $sms = $user_array['total'];
                        }
                    }
                } else {
                    $sms = 0;
                }
                
                if ( !empty($facebook_share) ) {
                    foreach ( $facebook_share['user_array'] as $user_array ) {
                        if ( $user_array['uid'] == $participant ) {
                            $facebook = $user_array['total'];
                        }
                    }
                } else {
                    $facebook = 0;
                }
                
                if ( !empty($twitter_share) ) {
                    foreach ( $twitter_share['user_array'] as $user_array ) {
                        if ( $user_array['uid'] == $participant ) {
                            $twitter = $user_array['total'];
                        }
                    }
                } else {
                    $twitter = 0;
                }
                
                if ( !empty($flyer_share) ) {
                    foreach ( $flyer_share['user_array'] as $user_array ) {
                        if ( $user_array['uid'] == $participant ) {
                            $flyer = $user_array['total'];
                        }
                    }
                } else {
                    $flyer = 0;
                }

                $user_info = get_userdata($participant);
                //array_push($data1, array($user_info->display_name, $user_info->user_email, $email, $sms, $twitter, $facebook, $supporters, $net_amount));
                array_push($data1, array($user_info->display_name, $user_info->user_email, $email, $sms, $twitter, $facebook, $flyer, $supporters, $net_amount));
            }
        } else {
            array_push($data1, array('No Participant Found', '', '', '', '', '', '', '', ''));
        }
        $filename = 'Report-'.$_GET['fundraiser_id'].'-'.date('d/m/Y').'.xlsx';
        header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        /*$header1 = array(
            'Participant Name'=>'string',
            'Participant Email'=>'string',
            'Email Shares'=>'string',
            'SMS Shares'=>'string',
            'Twitter Shares'=>'string',
            'Facebook Shares'=>'string',
            'Total Donations'=>'string',
            'Total Raised'=>'string'
        );*/
        $header1 = array(
            'Participant Name'=>'string',
            'Participant Email'=>'string',
            'Email Shares'=>'string',
            'SMS Shares'=>'string',
            'Twitter Shares'=>'string',
            'Facebook Shares'=>'string',
            'Flyer Shares'=>'string',
            'Total Donations'=>'string',
            'Total Raised'=>'string'
        );
        $header2 = array(
            'Donor Name'=>'string',
            'Donor Email'=>'string',
            'Total'=>'string',
            'Recipient'=>'string',
            'Date'=>'date'
        );
        $data2 = array();
        $args = array(
            'post_type' => 'supporter',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'post_parent' => $_GET['fundraiser_id']
        );
        $supporter_query = new WP_Query($args);
        
        if ( $supporter_query->have_posts() ) :
            while ($supporter_query->have_posts()) : $supporter_query->the_post();
                $user_info1 = get_userdata(get_post_meta(get_the_ID(), 'uid', true));
                $title      = get_the_title();
                $email      = get_post_meta(get_the_ID(), 'email', true);
                $amount     = get_post_meta(get_the_ID(), 'amount', true);
                $recipient  = $user_info1->display_name;
                $date       = get_the_time('j/n/Y');
                array_push($data2, array($title, $email, $amount, $recipient, $date));
            endwhile;
        endif;
        $potential_donors = json_decode(get_post_meta($_GET['fundraiser_id'], 'potential_donors_array', true));
        if ( !empty($potential_donors) ) {
            array_push($data2, array('', '', '', '', ''));
            array_push($data2, array('', '', 'Potential Donors', '', ''));
            array_push($data2, array('', '', '', '', ''));
            
            foreach ( $potential_donors as $pd ) {
                $user_info = get_userdata($pd[0]);
                if ( $pd[0] == 0 ) {
                    array_push($data2, array('', $pd[1], '', '', ''));
                } else {
                    $det = $user_info->display_name.' <'.$user_info->user_email.'>';
                    array_push($data2, array($det, $pd[1], '', '', ''));
                }
            }
        }
        $writer = new XLSXWriter();
        $writer->setAuthor('Some Author');
        $writer->writeSheet($data1,'Participants',$header1);
        $writer->writeSheet($data2,'Donors', $header2);
        $writer->writeToStdOut();
        exit(0);
    }
}
add_action('admin_init','download_report');