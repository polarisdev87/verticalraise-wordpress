
<?php
/* Template Name: Participant Check Upload Upload */


// Load Classes
use classes\app\fundraiser\Fundraiser_Media;  //Fundraiser Media Class Object
use classes\app\utm\UTM;
use \classes\models\mixed\Admins;

if ( !is_user_logged_in() ) {
	header( 'Location: ' . get_bloginfo( 'url' ) );
	exit();
}

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
$uid    = $user_ID;

$user_data = get_userdata( $uid );
$participant_name = $user_data->first_name . ' ' . $user_data->last_name;


$permalink          = get_permalink( $post_id );
$permalink_facebook = $permalink . 'f/' . $uid;
$clipboard_share_text = $permalink . 'c/' . $uid;
$image_url = $fundraise_mediaObj->get_fundraiser_logo( $post_id );

$fundraiser_name = get_the_title( $fundraiser_ID );

$args = array(
	'post_type'   => 'fundraiser',
	'post_status' => array( 'pending', 'publish', 'rejected' ),
	'p'           => $_GET['fundraiser_id']
);

$participants = get_fundraiser_participants($fundraiser_ID);
$admins = new Admins();
$is_admin = $admins->is_fundraiser_admin_or_site_admin( $user_ID, $fundraiser_ID );

if ( $is_admin ) {
	$redirect_to = "/single-fundraiser/?fundraiser_id=$fundraiser_ID";
} else {
	$redirect_to = "/participant-fundraiser/?fundraiser_id=$fundraiser_ID";
}

$loader_image = get_template_directory_uri() . "/assets/images/ajax-loader.gif";
$success_image = get_template_directory_uri() . "/assets/images/success.png";
$error_image = get_template_directory_uri() . "/assets/images/error.png";

while ( have_posts() ) : the_post();
	?>

    <main>

		<?php


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
		?>

            <div class="modal invite_step " id="invite_step" tabindex="-1" role="" data-backdrop="static"
                 aria-labelledby=""
                 aria-hidden="" style="display: block;">
                <div class="" role="document">
                    <div class="modal-content">
                        <div class="modal-header model_title">
                            <form id="form" method="post" enctype="multipart/form-data" action="/participant-check-upload-ajax">
                                <div id="wizard" class="participant_check_upload">
                                    <h1></h1>
                                    <section>
                                        <h3>Mobile Check Upload</h3>
                                        <p>
                                            Use the button below to upload a picture of a donation check
                                            that you have in your possession. We will deposit it and
                                            you will receive credit for the donation within 24 hours.
                                        </p>
                                        <p>Step 1 - Click the button below to take a picture of the check and upload the image</p>
                                        <p>*Make sure the image is clear*</p>
                                        <p>Step 2 - Tear up the physical check and you're done!</p>
                                        <button id="take_picture">Take picture of the check</button>
                                    </section>

                                    <h1></h1>
                                    <section>
                                        <h3>Mobile Check Image</h3>
                                        <p>Please double check the image is clear and readable. If not, retake the photo. </p>
                                        <img id="check_image_preview" src=""/>

                                        <p><button id="cut_picture" >CUT Picture</button></p>

                                        <p><button id="retake_picture">Retake Picture</button></p>
                                        <p>OR</p>
                                        <p><button id="confirm_picture">Use current check image</button></p>

                                        <input style="display:none" id="check_image" type="file" accept="image/*" capture="camera">

                                    </section>

                                    <h1></h1>
                                    <section>

                                        <div style="<?php if ( !$is_admin ){ echo 'visibility:hidden;position:absolute' ;} ?>"  >
                                            <h3>Select Participant who will get credit for donation</h3>
                                            <p class="d_advice">*If generic team donation, leave blank</p>
                                            <select name="uid" class="form-control">
                                                <option value="0" <?php if ( $uid == 0 ) { echo "selected";  } ?>></option>
                                                <?php foreach ( $participants as $participant ) { ?>
                                                <option value="<?php echo $participant->ID ?>" <?php if ( $uid == $participant->ID ) { echo "selected";  } ?> ><?php echo $participant->display_name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <h3>Confirm Statements and tear up check</h3>
                                        <p>
                                            I, <?php echo $participant_name ?>, confirm that the check I uploaded was given
                                            to me as a donation to the <?php echo $fundraiser_name ?>. I give Vertical Raise consent to
                                            deposit this check via ACH and add the donation amount to my profile
                                            per the Terms & Conditions of the site.
                                        </p>
                                        <p><input title="Consent Deposit" type="checkbox" id="consent_deposit"  name="consent_deposit" required/> I Confirm </p>
                                        <p>
                                            I, <?php echo $participant_name ?>, confirm that I torn up the check
                                            I just uploaded and I have thrown it away.
                                        </p>
                                        <p><input title="Consent Torn up" type="checkbox" id="consent_torn_up" name="consent_torn_up" required /> I Confirm </p>

                                        <button id="confirm_upload">Confirm Check Upload</button>
                                        <input type="hidden" name="fundraiser_id" value="<?php echo $fundraiser_ID  ?>" />
                                        <input type="hidden" name="image_b64" id="image_b64"/>
                                        <input type="submit" name="submit" id="submit_btn" style="display: none" />
                                    </section>
                                </div>
                            </form>
                            <br>
                            <br>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                    onclick="javascript:redirect_to();">
                                <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/close-btn1.png"
                                     alt="">
                            </button>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">

                        </div>
                    </div>
                </div>
            </div>

    </main>

<?php
endwhile;
?>


<script>

    function selectCords(c){
        cords = c;
        $('#cut_picture').css('display', 'block')
    }

    function releaseSelect(){
        $('#cut_picture').css('display', 'none')
    }

    var cords;
    var jcrop_api;

    $(document).ready(function (){

        $('#form').validate({
            rules : {
                check_image: {
                    required: true,
                }
            },
            messages: {
                check_image: {
                    accept: "Please take a picture of the check, or upload a jpg image"
                }
            }
        });

        $('#consent_deposit,#consent_torn_up').prop('checked', false);
        $("#wizard").steps({
            bodyTag: "section",
            stepsOrientation: 0,
            enablePagination: false,
            enableKeyNavigation: false,
            enableFinishButton: false,
            enableContentCache: false,
            onFinished: function (event, currentIndex) {
                if ($("#form").valid()) {
                    $("#error_container").remove();
                    const loading_container = document.createElement("div");
                    loading_container.style.marginTop = "50%";
                    var image = document.createElement("img");
                    image.src = '<?php echo $loader_image; ?>';
                    loading_container.appendChild(image);

                    $("#wizard ").append(loading_container);
                    $("#wizard > div.content.clearfix").css("display", "none");

                    var formData = new FormData(document.getElementById("form"));
                    $.ajax({
                        data: formData,
                        processData: false,
                        contentType: false,

                        type: "POST",
                        url: '/participant-check-upload-ajax',
                        success: function (data) {
                            loading_container.style.display = "none";
                            var success_container = document.createElement("div");
                            success_container.style.marginTop = "50%";
                            var image = document.createElement("img");
                            image.src = '<?php echo $success_image; ?>';
                            image.style.maxHeight = "50px";
                            var success_message = document.createElement("p");
                            success_message.textContent = data.message;
                            success_message.style.lineHeight = "30px";
                            success_message.style.fontSize = "25px";
                            success_message.style.color = "#41bf29";
                            success_message.style.paddingBottom = "10px";
                            success_container.appendChild(success_message);
                            success_container.appendChild(image);

                            $("#wizard ").append(success_container);
                            if (data.redirect_to) {
                                setTimeout(function () {
                                    window.location.href = data.redirect_to;
                                }, 2000);
                            }

                        },
                        error: function (e) {
                            loading_container.style.display = "none";
                            var error_container = document.createElement("div");
                            error_container.id = "error_container";
                            error_container.style.marginTop = "50%";
                            var image = document.createElement("img");
                            image.src = '<?php echo $error_image; ?>';
                            image.style.maxHeight = "50px";

                            var error_message = document.createElement("p");
                            error_message.textContent = e.responseJSON.message;
                            error_message.style.lineHeight = "30px";
                            error_message.style.fontSize = "25px";
                            error_message.style.color = "#da3d32";
                            error_message.style.paddingBottom = "10px";
                            error_container.appendChild(error_message);
                            error_container.appendChild(image);
                            $("#wizard ").append(error_container);
                        }
                    });
                }
            }
        });

        $("div.steps.clearfix > ul").css("display", "none");
        $("#take_picture").click(function(e){
            e.preventDefault();
            $("#check_image").click();
        });

        $("#check_image").change(function (e){
            e.preventDefault();
            if ( $("#wizard").steps("getCurrentIndex") === 0 ){
                $("#wizard").steps("next");
            }
        });

        $("#retake_picture").click(function(e){
            e.preventDefault();
            $("#check_image").click();
        });

        $("#confirm_picture").click(function(e){
            e.preventDefault();
            $("#wizard").steps("next");
        });


        $("#confirm_upload").click(function(e){
            e.preventDefault();
            $("#wizard").steps("finish");
        });


        $("#check_image").change(function() {

            var upload_url = window.URL.createObjectURL(this.files[0]);

            var img = new Image();
            img.src = upload_url;

            img.addEventListener( 'load', function(event) {

                let width = img.width;
                let height = img.height;

                if ( width > 1280 ){
                    let f = 1280 / width;
                    width = width * f;
                    height = height * f;
                }

                const canvas = document.createElement('canvas');

                canvas.height = height;
                canvas.width = width;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                canvas.toBlob(function (blob) {
                    let url = URL.createObjectURL(blob);
                    $('#check_image_preview').attr('src', url );
                    
                    window.URL.revokeObjectURL(upload_url);
                    /*if ( jcrop_api ){
                        jcrop_api.destroy();
                    }
                    $('#check_image_preview').Jcrop({
                        onSelect: selectCords,
                        onRelease: releaseSelect,
                    }, function(){
                         jcrop_api = this;
                    });*/

                    let image_b64 = canvas.toDataURL('image/jpeg');
                    image_b64 = image_b64.slice( image_b64.search(",") + 1);
                    $('#image_b64').val( image_b64 );
                });
            });

        });


        $("#cut_picture").click(function (e) {
            e.preventDefault(); 

            var visible_width = parseInt($("#check_image_preview").css('width'));
            var real_width = document.getElementById('check_image_preview').width;
            var f = real_width/visible_width;

            var visible_height = parseInt($("#check_image_preview").css('height'));
            var real_height = document.getElementById('check_image_preview').height;
            var f2 = real_height/visible_height;

            var img = new Image();
            img.src = document.getElementById('check_image_preview').src;


            img.addEventListener('load', function(event) {

                const canvas = document.createElement('canvas');

                canvas.height = cords.h * f2;
                canvas.width = cords.w * f;

                const ctx = canvas.getContext('2d');

                ctx.drawImage(img, cords.x * f , cords.y * f2 , cords.w * f, cords.h * f2, 0, 0, cords.w *f, cords.h * f2);
                canvas.toBlob(function (blob) {
                    let url = URL.createObjectURL(blob);
                    $('#check_image_preview').attr('src', url );
                    /*if ( jcrop_api ){
                        jcrop_api.destroy();
                    }

                    $('#check_image_preview').Jcrop({
                        onSelect: selectCords,
                        onRelease: releaseSelect,
                    }, function(){
                        jcrop_api = this;
                    });*/

                    let image_b64 = canvas.toDataURL('image/jpeg');
                    image_b64 = image_b64.slice( image_b64.search(",") + 1);
                    $('#image_b64').val( image_b64 );

                    $("#cut_picture").css('display', 'none');

                });
            });
        })

    });

    function redirect_to(){
        window.location.href = '<?php echo $redirect_to; ?>';
    }


</script>

