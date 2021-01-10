<?php

use classes\app\emails\Custom_Mail; //Load Custom Mail Object;

function fundraiser_register_function() {
    $labels = array(
        'name'               => _x( 'Fundraisers', 'post type general name' ),
        'singular_name'      => _x( 'Fundraiser', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'Fundraiser item' ),
        'add_new_item'       => __( 'Add New Fundraiser' ),
        'edit_item'          => __( 'Edit Fundraiser Item' ),
        'new_item'           => __( 'New Fundraiser Item' ),
        'view_item'          => __( 'View Fundraiser Item' ),
        'search_items'       => __( 'Search Fundraiser' ),
        'not_found'          => __( 'Nothing found' ),
        'not_found_in_trash' => __( 'Nothing found in Trash' ),
        'parent_item_colon'  => ''
    );

    $args = array(
        'labels'                    => $labels,
        'fundraiserlic'             => true,
        'fundraiserlicly_queryable' => true,
        'show_ui'                   => true,
        'query_var'                 => true,
        'public'                    => true,
        'menu_icon'                 => 'dashicons-lightbulb',
        'rewrite'                   => true,
        'capability_type'           => 'post',
        'hierarchical'              => false,
        'supports'                  => array( 'title', 'thumbnail', 'comments' )
    );

    register_post_type( 'fundraiser', $args );
}

add_action( 'init', 'fundraiser_register_function' );

function custom_post_status() {
    register_post_status( 'rejected', array(
        'label'                     => 'Rejected',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>' ),
    ) );
}

add_action( 'init', 'custom_post_status' );

function jc_append_post_status_list() {
    global $post;

    $complete = '';
    $label    = '';

    if ( $post->post_type == 'fundraiser' ) {
        if ( $post->post_status == 'rejected' ) {
            $complete = ' selected="selected"';
            $label    = '<span id="post-status-display"> Rejected</span>';
        }
        ?>
        <script>
            jQuery(document).ready(function ($) {
                $("select#post_status").append('<option value="rejected" <?php echo $complete; ?>>Rejected</option>');
                $(".misc-pub-section label").append('<?php echo $label; ?>');
                //$("select[name='_status']").append('<option value="rejected" <?php echo $complete; ?>>Rejected</option>');

                $('#post input#title').keyup(function () {
                    $(this).val(function (index, value) {
                        return value.replace(/[^A-Z0-9a-z ,.]+/g, '');
                    });
                });
            });
        </script>
        <?php
    }
}

add_action( 'admin_footer-post.php', 'jc_append_post_status_list' );

function jc_display_archive_state( $states ) {
    global $post;

    $arg = get_query_var( 'post_status' );
    if ( $arg != 'rejected' ) {
        if ( $post->post_status == 'rejected' ) {
            return array( 'Rejected' );
        }
    }

    return $states;
}

add_filter( 'display_post_states', 'jc_display_archive_state' );

function generateJoinCode() {
    return mt_rand( 11111111, 99999999 );
}

function on_fundraiser_update( $meta_id, $post_id, $meta_key, $meta_value ) {

    try {
        $custom_mail = new Custom_Mail();

        $post = get_post( $post_id );
        $post_type = get_post_type( $post_id );

        if ( $post_type == 'fundraiser' ) {
            if ( get_post_status( $post_id ) == 'publish' ) {
                if ( $meta_key == 'email' ) {
                    $user_id = $post->post_author;
                    $user_info = get_userdata( $user_id );

                    $join_code = get_post_meta( $post->ID, 'join_code', true );
                    if ( empty( $join_code ) ) {
                        $join_code = generateJoinCode();
                        update_post_meta( $post->ID, 'join_code', $join_code );
                    }

                    $join_code_sadmin = get_post_meta( $post->ID, 'join_code_sadmin', true );
                    if ( empty( $join_code_sadmin ) ) {
                        $join_code_sadmin = generateJoinCode();
                        update_post_meta( $post->ID, 'join_code_sadmin', $join_code_sadmin );
                    }

                    // Mail to User
                    $title = get_the_title( $post->ID );
                    $to = get_post_meta( $post->ID, 'email', true );
                    $from = _TRANSACTIONAL_FROM_EMAIL;
                    $subject = $title . " Has Been Approved";
                    $cc = null;
                    $reply = null;

                    $template_args = array(
                        'DISPLAY_NAME'           => get_post_meta( $post->ID, 'con_name', true ),
                        'FUNDRAISER_NAME'        => $title,
                        'SIGNATURE_EMAIL'        => _SIGNATURE_EMAIL,
                        'SIGNATURE_FAX_NUMBERS'  => _SIGNATURE_FAX_NUMBER,
                        'SIGNATURE_PHONE_NUMBER' => _SIGNATURE_OFFICE_PHONE_NUMBER,
                        'TEMPLATE_DIRECTORY'     => get_template_directory_uri()
                    );

                    $sent = $custom_mail->send_api( $to, $from, $cc, $subject, 'approved_fundraiser_author', $template_args );

                }

                if ( $meta_key == 'coach_email' ) {
                    if ( !empty( get_post_meta( $post->ID, 'coach_email', true ) ) ) {
                        $user_id = $post->post_author;
                        $user_info = get_userdata( $user_id );

                        $join_code = get_post_meta( $post->ID, 'join_code', true );
                        if ( empty( $join_code ) ) {
                            $join_code = generateJoinCode();
                            update_post_meta( $post->ID, 'join_code', $join_code );
                        }

                        $join_code_sadmin = get_post_meta( $post->ID, 'join_code_sadmin', true );
                        if ( empty( $join_code_sadmin ) ) {
                            $join_code_sadmin = generateJoinCode();
                            update_post_meta( $post->ID, 'join_code_sadmin', $join_code_sadmin );
                        }

                        // Mail to Coach
                        $title = get_the_title( $post->ID );
                        $to = get_post_meta( $post->ID, 'coach_email', true );
                        $from = _TRANSACTIONAL_FROM_EMAIL;
                        $subject = $title . " Has Been Approved";
                        $cc = null;
                        $reply = null;

                        $template_args = array(
                            'DISPLAY_NAME'           => get_post_meta( $post->ID, 'coach_name', true ),
                            'FUNDRAISER_NAME'        => $title,
                            'PARTICIPANT_CODE'       => $join_code,
                            'ADMIN_CODE'             => $join_code_sadmin,
                            'SIGNATURE_EMAIL'        => _SIGNATURE_EMAIL,
                            'SIGNATURE_FAX_NUMBERS'  => _SIGNATURE_FAX_NUMBER,
                            'SIGNATURE_PHONE_NUMBER' => _SIGNATURE_OFFICE_PHONE_NUMBER,
                            'TEMPLATE_DIRECTORY'     => get_template_directory_uri()
                        );

                        $sent = $custom_mail->send_api( $to, $from, $cc, $subject, 'approved_fundraiser_coach', $template_args );

                    }
                }
            }
        }
    } catch ( \Exception $e ) {
        if ( extension_loaded( 'newrelic' ) ) {
            newrelic_notice_error( $e->getMessage(), $e );
        }
    }
}

add_action( 'added_post_meta', 'on_fundraiser_update', 10, 4 );
add_action( 'updated_post_meta', 'on_fundraiser_update', 10, 4 );

function on_fundraiser_publish( $post ) {
    try {
        $custom_mail = new Custom_Mail();

        $post_type = get_post_type( $post );

        if ( $post_type == 'fundraiser' ) {

            $user_id = $post->post_author;
            $user_info = get_userdata( $user_id );

            $join_code = get_post_meta( $post->ID, 'join_code', true );
            if ( empty( $join_code ) ) {
                $join_code = generateJoinCode();
                update_post_meta( $post->ID, 'join_code', $join_code );
            }

            $join_code_sadmin = get_post_meta( $post->ID, 'join_code_sadmin', true );
            if ( empty( $join_code_sadmin ) ) {
                $join_code_sadmin = generateJoinCode();
                update_post_meta( $post->ID, 'join_code_sadmin', $join_code_sadmin );
            }
            // Mail to User
            $title = get_the_title( $post->ID );
            $to = get_post_meta( $post->ID, 'email', true );
            $from = _TRANSACTIONAL_FROM_EMAIL;
            $subject = $title . " Has Been Approved";
            $cc = null;
            $reply = null;

            $template_args = array(
                'DISPLAY_NAME' => get_post_meta( $post->ID, 'con_name', true ),
                'FUNDRAISER_NAME' => $title,
                'SIGNATURE_EMAIL' => _SIGNATURE_EMAIL,
                'SIGNATURE_FAX_NUMBERS' => _SIGNATURE_FAX_NUMBER,
                'SIGNATURE_PHONE_NUMBER' => _SIGNATURE_OFFICE_PHONE_NUMBER,
                'TEMPLATE_DIRECTORY' => get_template_directory_uri()
            );

            $sent = $custom_mail->send_api( $to, $from, $cc, $subject, 'approved_fundraiser_author', $template_args );


            // Mail to Coach
            if ( !empty( get_post_meta( $post->ID, 'coach_email', true ) ) ) {
                $title = get_the_title( $post->ID );
                $to = get_post_meta( $post->ID, 'coach_email', true );
                $from = _TRANSACTIONAL_FROM_EMAIL;
                $subject = $title . " Has Been Approved";
                $cc = null;
                $reply = null;

                $template_args = array(
                    'DISPLAY_NAME' => get_post_meta( $post->ID, 'coach_name', true ),
                    'FUNDRAISER_NAME' => $title,
                    'PARTICIPANT_CODE' => $join_code,
                    'ADMIN_CODE' => $join_code_sadmin,
                    'SIGNATURE_EMAIL' => _SIGNATURE_EMAIL,
                    'SIGNATURE_FAX_NUMBERS' => _SIGNATURE_FAX_NUMBER,
                    'SIGNATURE_PHONE_NUMBER' => _SIGNATURE_OFFICE_PHONE_NUMBER,
                    'TEMPLATE_DIRECTORY' => get_template_directory_uri()
                );

                $sent = $custom_mail->send_api( $to, $from, $cc, $subject, 'approved_fundraiser_coach', $template_args );

            }
        }
    } catch ( \Exception $e ) {
        if ( extension_loaded( 'newrelic' ) ) {
            newrelic_notice_error( $e->getMessage(), $e );
        }
    }
}

add_action( 'pending_to_publish', 'on_fundraiser_publish', 10, 1 );
add_action( 'rejected_to_publish', 'on_fundraiser_publish', 10, 1 );
