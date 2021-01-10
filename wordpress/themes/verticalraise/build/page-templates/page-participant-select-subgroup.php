<?php
/* Template Name: Participant Select Subgroup */

// Load Classes
use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object
use classes\app\utm\UTM;
use classes\models\tables\Subgroups;

load_class( 'invite_wizard.class.php' );
load_class( 'sharing.class.php' );
load_class( 'participant_records.class.php' );

get_header( 'invite' );

$p_invite_wizard    = new Invite_Wizard();                           // Parent Invite Wizard class object
$sharing            = new Sharing();                                 // Sharing class object
$user_ID            = $sharing->user_ID;                             // Define user ID
$fundraiser_ID      = $sharing->fundraiser_ID;                       // Define fundraiser ID
$post_id            = $fundraiser_ID;
$fundraise_mediaObj = new Fundraiser_Media();
$sharing_records    = new Participant_Sharing_Totals();

$campaign_participations = get_user_meta( $user_ID, 'campaign_participations', true );
$participations_array    = json_decode( $campaign_participations );

if ( $participations_array == NULL )
    $participations_array = array();

// Set the source - (is the user logged in and is he part of this campaign?)
if ( ( is_user_logged_in() && in_array( $post_id, $participations_array ) ) || (!empty( $_GET['parent'] ) && $_GET['parent'] == 1 ) ) {
    $source = 'invite';
} else {
    $source = '';
}

$single = false;
$uid    = 0;

if ( isset( $_GET['display_type'] ) && $_GET['display_type'] == 'single' ) {
    $single = true;
    if ( isset( $_GET['uid'] ) && !empty( $_GET['uid'] ) ) {
        $uid = $_GET['uid'];
    }

    if ( isset( $wp_query->query_vars['uid'] ) ) {
        $uid = urldecode( $wp_query->query_vars['uid'] );
    }
} else {
    if ( is_user_logged_in() ) {
        $uid = $user_ID;
    }
}

$permalink          = get_permalink( $post_id );
$permalink_facebook = $permalink . 'f/' . $uid;
$clipboard_share_text = $permalink . 'c/' . $uid;
$image_url = $fundraise_mediaObj->get_fundraiser_logo( $post_id );

$utm = new UTM;
if ( $single == true ) {
    $clipboard_share_text = $utm->createUTMLink($clipboard_share_text, 'URL_Share');
    if( isset( $_GET['page'] ) && $_GET['page'] == 'thankyou') {
        // Tahnks you facebook share
        $permalink_facebook = $utm->createUTMLink( $permalink_facebook, 'Thank_You_Facebook_Share' );
    }else {
        // Facebook share
        $permalink_facebook = $utm->createUTMLink( $permalink_facebook, 'Facebook_Share' );
    }
}else{
    
    if (!empty( $_GET['parent'] ) && $_GET['parent'] == 1 ) {
        $clipboard_share_text = $utm->createUTMLink($clipboard_share_text, 'Parent_URL_Invite');
    }else{
        $clipboard_share_text = $utm->createUTMLink($clipboard_share_text, 'URL_Invite');
    }
}

$args = array(
    'post_type'   => 'fundraiser',
    'post_status' => array( 'pending', 'publish', 'rejected' ),
    'p'           => $_GET['fundraiser_id']
);


while ( have_posts() ) : the_post();

    // Get sharing results
    $results = $sharing_records->get_single_row($post_id, $user_ID);

    if ( $results != null ) {
        $email_count    = $results->email;
        $sms_count      = $results->sms;
        $parent_count   = $results->parents;
        $facebook_count = $results->facebook;
    } else {
        $email_count    = 0;
        $sms_count      = 0;
        $parent_count   = 0;
        $facebook_count = 0;
    }

    /*
     * get invite user type $_GET['type']
     * variable: $usertype = particpant or admin
     */

    $usertype = ( isset( $_GET['type'] ) ) ? $_GET['type'] : '';
    $base     = get_site_url();

    $invite_params = "fundraiser_id=" . $_GET['fundraiser_id'] . "&type=" . $usertype;
    if ( current_user_can( 'administrator' ) ) {
        $invite_params = "fundraiser_id=" . $_GET['fundraiser_id'] . "&uid=" . $uid . "&type=" . $usertype;
    }


    $subgroups_table = new Subgroups();
    $subgroups       = $subgroups_table->getSubgroups( $fundraiser_ID );

    ?>

    <main>
            <div class="modal invite_step " id="invite_step" tabindex="-1" role="" data-backdrop="static"
                 aria-labelledby=""
                 aria-hidden="" style="display: block;">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header model_title">

                            <img class="modal_logo" src="<?php
                            echo ( $image_url != null ) ?
                                    $image_url : bloginfo( 'template_directory' ) . '/assets/images/default-logo.png';
                            ?>"
                                 width="220" alt="">
                            <table id="table_button_copy">
                                <tr>
                                    <td>
                                        <p>Welcome to the fundraiser, please select a subgroup</p>
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:parent.$.fancybox.close();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                                     alt="">
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php if ( is_array( $subgroups ) ) { ?>
                                <?php foreach ( $subgroups as $subgroup ) { ?>
                                    <a class="nav_invite" href="#" data-subgroup="<?php echo $subgroup['id'] ?>">
                                        <ul>
                                            <li class="right">
                                                <b><img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/icon16.png"
                                                        alt=""></b><span class="invite_label"><?php echo $subgroup['name']; ?></span>
                                            </li>
                                        </ul>
                                    </a>
	                            <?php } ?>
                            <?php } ?>

                        </div>
                        <div class="modal-footer">
                            <?php //include_once( get_template_directory() . '/prev_next_buttons.php' ); ?>
                        </div>
                    </div>
                </div>
            </div>

    </main>
    <div class="modal fade" tabindex="-1" role="dialog" id="confirmation_modal" style="background: rgba(0, 0, 0, 0.84);" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
                    <h4 class="modal-title">Please confirm selection</h4>
                </div>
                <div class="modal-body">
                    <p>You have selected subgroup: <span id="option_selected"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm" data-loading="Saving..." class="btn btn-primary has-spinner" style="margin-left: 0;">Confirm</button>
                </div>
                <div>
                    <p id="success_response_message" style="text-align: center;padding-top: 25px; color: #02b902; font-weight: bold;"></p>
                    <p id="error_response_message" style="text-align: center;padding-top: 25px;color: red; font-weight: bold;"></p>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <?php
endwhile;
?>
<script>
    $(document).ready(function () {

        const SUBGROUPS = <?php echo json_encode( $subgroups ); ?>;
        const USER_ID = '<?php echo $user_ID; ?>';
        const FUNDRAISER_ID = '<?php echo $fundraiser_ID; ?>';
        const REDIRECT_TO = '<?php echo get_the_permalink(195) . '?fundraiser_id=' .$fundraiser_ID . '&invitepopup=1'; ?>';
        console.log(REDIRECT_TO);

        var id_selected = null;
        $(".nav_invite").click(function (e) {

            e.preventDefault();

            id_selected = $(this).data('subgroup');

            var subgroup_selected = SUBGROUPS.find(function (s) {
                return s.id == id_selected
            });

            $('#option_selected').html(subgroup_selected.name.toUpperCase());

            $('#confirmation_modal').modal('show');
            $('#confirmation_modal').on('hidden.bs.modal', function () {
                $('#error_response_message').text('');
            });

            $('#confirm').unbind();
            $('#confirm').click(function (e) {
                e.preventDefault();

                $('#confirm').buttonLoader("start");

                var data = {
                    'action': 'participant_select_subgroup',
                    'u_id': USER_ID,
                    'f_id': FUNDRAISER_ID,
                    'subgroup_id': id_selected,
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
                                $('#success_response_message').text(responseJSON.message);
                                location.href = REDIRECT_TO;
                            } else {
                                $('#error_response_message').text(responseJSON.error);
                                $('#confirm').buttonLoader("stop");
                            }

                        }
                    }
                );
            });

        });
    });
</script>
