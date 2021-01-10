<?php /* Template Name: Profile Image Upload Facebook */


if ( is_user_logged_in() ) {

    get_header();
    
    global $user_ID;
    
    $user_info = get_userdata($user_ID);
    
    error_reporting (E_ALL ^ E_NOTICE);

    $upload_dir = wp_upload_dir();
    $upload_path_rel = $upload_dir['basedir'] . '/profile_img_thumb/';
    $upload_path = get_bloginfo('url').'/wp-content/uploads/profile_img_thumb/';

    $thumb_width = "150";
    $thumb_height = "150";


    function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale) {
        
        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);

        $newImageWidth  = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
        
        switch($imageType) {
                
            case "image/gif":
                $source=imagecreatefromgif($image);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source=imagecreatefromjpeg($image);
                break;
            case "image/png":
            case "image/x-png":
                $source=imagecreatefrompng($image);
                break;
                
        }
        
        imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
        
        switch($imageType) {
            case "image/gif":
                imagegif($newImage,$thumb_image_name);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage,$thumb_image_name,100);
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage,$thumb_image_name);
                break;
        }
        
        chmod($thumb_image_name, 0777);
        return $thumb_image_name;
    }



    if ( isset($_POST["upload_thumbnail"]) ) {

        // Get the file name
        $filename = trim($_POST['filename_thumb']);

        // Image locations
        $large_image_location = $upload_path_rel . $filename;
        $thumb_image_location = $upload_path_rel . "thumb_" . $filename;

        $thumb_image_url = $upload_path . "thumb_" . $filename;

        // Get crop coordinates
        $x1 = $_POST["x1"];
        $y1 = $_POST["y1"];
        $x2 = $_POST["x2"];
        $y2 = $_POST["y2"];
        $w = $_POST["w1"];
        $h = $_POST["h1"];

        // Determine the scale
        $scale = $thumb_width/$w;
        
        // Crop the image
        $cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);

        // Upload the image
        $upload_dir = wp_upload_dir();                      // Set upload folder
        $image_data = file_get_contents($thumb_image_url);  // Get image data
        $filename   = basename($thumb_image_url);           // Create image file name
        
        // Check folder permission and define file location
        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
            $file = $upload_dir['path'] . '/' . $filename;
        } else {
            $file = $upload_dir['basedir'] . '/' . $filename;
        }
        
        // Create the image  file on the server
        file_put_contents( $file, $image_data );
        
        // Check image file type
        $wp_filetype = wp_check_filetype( $filename, null );
        
        // Set attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name( $filename ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
        
        // Create the attachment
        $attach_id = wp_insert_attachment( $attachment, $file );
        
        // Add meta to hide it from the wordpress media library
        add_post_meta($attach_id, 'hide_form_library', 1);
        
        // Include image.php
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        // Define attachment metadata
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        
        // Assign metadata to attachment
        wp_update_attachment_metadata( $attach_id, $attach_data );
        global $user_ID;
        delete_metadata('post', null, '_wp_attachment_wp_user_avatar', $user_ID, true);
        update_user_meta($user_ID, '_wp_attachment_wp_user_avatar', $attach_id);
        update_user_meta($user_ID, $wpdb->get_blog_prefix() . 'user_avatar', $attach_id);
        
        if ( $_GET['from_file'] ) {
    ?>
        <script>
            parent.closeFancyboxAndRedirectToUrl();
        </script>
    <?php
        } else {
            if ( !empty($_GET['fundraiser_id']) ) {
                header("location:".get_bloginfo('url') . "/participant-fundraiser/?fundraiser_id=" .$_GET['fundraiser_id']);
            } else {
                header("location:".get_bloginfo('url') . "/my-account/");
            }
        }
        //exit();
    }

    ?>
    <div id="myacc" class="mobile_popup_fix">
        <div class="maincontent noPadding">
            <div class="section group">
                <!--<div class="col span_3_of_12 noMargin matchheight">
                    <?php /*get_sidebar('acc'); */?>
                </div>-->
                <div class="col span_12_of_12 noMargin">
                    <div id="title">
                        <div class="maincontent">
                            <div class="section group">
                                <div class="col span_12_of_12">
                                    <h1 style="margin: 0"><?php the_title(); ?></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="acc_sec" style="min-height: 400px;">
                        <?php
                        if ( $_GET['token'] == 1 ) {
                            if ( isset($_GET['fundraiser_id']) ) {
                                $redirect_url = get_bloginfo('url') . '/profile-image-upload-facebook?token=1&fundraiser_id=' . $_GET['fundraiser_id'];
                            } else {
                                $redirect_url = get_bloginfo('url').'/profile-image-upload-facebook?token=1';
                            }
                            $return_url = urlencode($redirect_url);
                            $accessToken = processURL('https://graph.facebook.com/oauth/access_token?client_id=' . _FACEBOOK_CLIENT_ID . '&redirect_uri=' . $return_url . '&client_secret=' . _FACEBOOK_CLIENT_SECRET . '&code=' . $_GET['code']);
                            $accessTokenArray = json_decode($accessToken);
                            
                            /*echo "<pre>";
                            print_r($accessTokenAarray);
                            echo "</pre>";*/
                            //echo 'https://graph.facebook.com/oauth/access_token?client_id=1567989536863012&redirect_uri='.$return_url.'&client_secret=7bfa46a00782b3624d38e0c91fc459dc&code='.$_GET['code'];
                            /*$startsAt = strpos($accessToken, "access_token=") + strlen("access_token=");
                            $endsAt = strpos($accessToken, "&expires=", $startsAt);
                            $result = substr($accessToken, $startsAt, $endsAt - $startsAt);*/

                            $result = $accessTokenArray->access_token;

                            $userid_json = processURL('https://graph.facebook.com/me?fields=id&access_token='.trim($result));
                            $userid = json_decode($userid_json);
                            $albums = json_decode(processURL('https://graph.facebook.com/'.$userid->id.'/albums?access_token='.trim($result)), true);
                            
                            /*echo "result: {$result}";
                            echo "<pre>";
                            print_r($albums);
                            echo "</pre>";*/
                            
                            if ( !empty($albums) ) {
                                ?>
                                <h2 style="text-align: center">Select Album</h2>
                                <div id="facebook_albums">
                                    <?php
                                    foreach ($albums['data'] as $data) {
                                        ?>
                                        <div class="facebook_album">
                                            <?php
                                            $album_thumb = "https://graph.facebook.com/v2.8/".$data['id']."/picture?access_token=".trim($result);
                                            ?>
                                            <div class="matchheight">
                                                <a href="<?php echo get_bloginfo('url').'/profile-image-upload-facebook?facebook_album=1&album_id='.$data['id'].'&access_token='.trim($result).'&fundraiser_id='.$_GET['fundraiser_id']; ?>"><img src="<?php echo $album_thumb; ?>"></a>
                                                <h3><a href="<?php echo get_bloginfo('url').'/profile-image-upload-facebook?facebook_album=1&album_id='.$data['id'].'&access_token='.trim($result); ?>"><?php echo $data['name']; ?></a></h3>
                                            </div>
                                            <?php
                                            $photos = json_decode(processURL('https://graph.facebook.com/'.$data['id'].'/photos?access_token='.trim($result)), true);
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        }

                        if ( $_GET['facebook_album'] == 1 ) {
                            $photos = json_decode(processURL('https://graph.facebook.com/' . $_GET['album_id'] . '/photos?access_token=' . trim($_GET['access_token'])), true);
                            if ( !empty($photos) ) {
                                ?>
                                <div id="facebook_albums">
                                    <h2 style="text-align: center">Select Image</h2>
                                    <?php
                                    foreach ( $photos['data'] as $data ) {
                                        ?>
                                        <div class="facebook_album">
                                            <?php
                                            $img_thumb = "https://graph.facebook.com/".$data['id']."/picture?type=normal&access_token=".trim($_GET['access_token']);
                                            $ch = curl_init();
                                            curl_setopt($ch, CURLOPT_URL, $img_thumb);
                                            curl_setopt($ch, CURLOPT_HEADER, true);
                                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                            $a = curl_exec($ch);
                                            $img_thumb = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                                            ?>
                                            <div class="matchheight">
                                                <a href="<?php echo get_bloginfo('url').'/profile-image-upload-facebook/?img_thumb='.urlencode($img_thumb).'&fundraiser_id='.$_GET['fundraiser_id']; ?>"><img src="<?php echo $img_thumb; ?>"></a>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        }
                        if ( isset($_GET['img_thumb']) ) {
                            $url = $_GET['img_thumb'];
                        }
                        ?>
                        <?php if ( $_GET['token'] != 1 && $_GET['facebook_album'] != 1 && !isset($_GET['img_thumb']) ) { ?>
                            <div class="crop_box">
                                <form id="uploadForm" class="uploadform" method="post" enctype="multipart/form-data" action='upload.php' name="photo">
                                    <div class="section group">
                                        <div class="col span_2_of_12">
                                            <label for="imagefile">Upload files: </label>
                                        </div>
                                        <div class="col span_8_of_12">
                                            <input type="file" name="imagefile" id="imagefile" class="hide_broswe" required="required" />
                                            <p id="fp"></p>
                                        </div>
                                        <div class="col span_2_of_12">
                                            <input style="display: none;" type="submit" value="Upload" class="upload_button" name="submitbtn" id="submitbtn" />
                                        </div>
                                    </div>
                                    <div class="section group">
                                        <div class="col span_12_of_12">
                                            <label id="noFile"></label>
                                        </div>
                                    </div>
                                </form>
                                <p id="loading" style="text-align: center; display: none;"><img src="<?php bloginfo('template_directory'); ?>/assets/images/loading_spinner.gif" /></p>
                                <p id="message" style="text-align: center; display: none; padding: 0">*Please select and crop your profile image below*</p>
                                <div class="crop_preview">
                                    <div class="section group">
                                        <div class="col span_12_of_12">
                                            <div class="crop_preview_box_big" id='viewimage'></div>
                                            <div id="thumbviewimage" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <form name="thumbnail" action="" method="POST">
                                        <input type="hidden" name="x1" value="" id="x1" />
                                        <input type="hidden" name="y1" value="" id="y1" />
                                        <input type="hidden" name="x2" value="" id="x2" />
                                        <input type="hidden" name="y2" value="" id="y2" />
                                        <input type="hidden" name="w1" value="" id="w" />
                                        <input type="hidden" name="h1" value="" id="h" />
    
                                        <input type="hidden" name="filename_thumb" value="" id="filename" />
                                        <input type="submit" name="upload_thumbnail" value="Save Image" id="save_thumb" class="submit_button" style="display: none;" />
                                    </form>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ( isset($_GET['img_thumb']) ) { ?>
                            <div class="crop_box">
                                <form style="display: none;" class="uploadform" method="post" enctype="multipart/form-data" action='upload.php' name="photo">
                                    <div class="section group">
                                        <div class="col span_12_of_12">
                                            <input type="hidden" name="imagefile" id="imagefile" class="hide_broswe" value="<?php echo $_GET['img_thumb']; ?>" />
                                            <input type="hidden" name="facebook_img" value="Upload" >
                                        </div>
                                    </div>
                                </form>
                                <p id="loading" style="text-align: center; display: none;"><img src="<?php bloginfo('template_directory'); ?>/assets/images/loading_spinner.gif" /></p>
                                <p id="message" style="text-align: center; display: none; padding: 0">*Please select and crop your profile image below*</p>
                                <div class="crop_preview">
                                    <div class="section group">
                                        <div class="col span_12_of_12">
                                            <div class="crop_preview_box_big" id='viewimage'></div>
                                            <div id="thumbviewimage" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <form name="thumbnail" action="" method="POST">
                                        <input type="hidden" name="x1" value="" id="x1" />
                                        <input type="hidden" name="y1" value="" id="y1" />
                                        <input type="hidden" name="x2" value="" id="x2" />
                                        <input type="hidden" name="y2" value="" id="y2" />
                                        <input type="hidden" name="w1" value="" id="w" />
                                        <input type="hidden" name="h1" value="" id="h" />

                                        <input type="hidden" name="filename_thumb" value="" id="filename" />
                                        <input type="submit" name="upload_thumbnail" value="Save Image" id="save_thumb" class="submit_button" style="display: none;" />
                                    </form>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" >
        <?php if ( isset($_GET['img_thumb']) ) { ?>
        jQuery(document).ready(function() {
            jQuery("#loading").show();
            jQuery(".uploadform").hide();
            jQuery(".uploadform").ajaxForm({
                url: '<?php bloginfo("url"); ?>/profile-image-upload-ajax',
                success:  showResponse
            }).submit();
        });
        <?php } ?>
        jQuery(document).on('change','#imagefile' , function(){
            /*var imagefile = jQuery('input[name="imagefile"]').val();
            if(imagefile == "") {
                event.preventDefault();
                jQuery('input[name="imagefile"]').css('border', '1px solid red');
                jQuery(window).scrollTop(jQuery('input[name="imagefile"]').position().bottom);
                jQuery('#noFile').css('color', 'red');
                jQuery('#noFile').text('*Please choose file before uploading');
                jQuery('input[name="imagefile"]').focus();
            } else {
                jQuery('input[name="imagefile"]').css('border', 'none');
                jQuery('#noFile').text('');
                jQuery("#submitbtn").show();
            }*/

            var fi = document.getElementById('imagefile');
            //console.log(fi.files[0].size);
            if (fi.files.length > 0) {
                var fsize = fi.files.item(0).size;
                if(Math.round((fsize / 1024)) >= 3072) {
                    jQuery('input[name="imagefile"]').css('border', '1px solid red');
                    jQuery(window).scrollTop(jQuery('input[name="imagefile"]').position().bottom);
                    jQuery('#noFile').css('color', 'red');
                    jQuery('#noFile').text('File is too large. Please upload file that is less than 3MB');
                    jQuery("#submitbtn").hide();
                    jQuery('input[name="imagefile"]').val('');
                    jQuery('input[name="imagefile"]').focus();
                } else {
                    jQuery('input[name="imagefile"]').css('border', '1px solid #ccc');
                    jQuery('#noFile').text('');
                    jQuery("#submitbtn").show();
                }
            } else {
                event.preventDefault();
                jQuery('input[name="imagefile"]').css('border', '1px solid red');
                jQuery(window).scrollTop(jQuery('input[name="imagefile"]').position().bottom);
                jQuery('#noFile').css('color', 'red');
                jQuery('#noFile').text('*Please choose file before uploading');
                jQuery('input[name="imagefile"]').focus();
            }
        });
        jQuery(document).ready(function() {
            jQuery('#submitbtn').click(function(event) {
                var imagefile = jQuery('input[name="imagefile"]').val();
                if(imagefile == "") {
                    event.preventDefault();
                    jQuery('input[name="imagefile"]').css('border', '1px solid red');
                    jQuery(window).scrollTop(jQuery('input[name="imagefile"]').position().bottom);
                    jQuery('#noFile').css('color', 'red');
                    jQuery('#noFile').text('*Please choose file before uploading');
                    jQuery('input[name="imagefile"]').focus();
                } else {
                    jQuery("#loading").show();
                    jQuery(".uploadform").hide();
                    jQuery(".uploadform").ajaxForm({
                        url: '<?php bloginfo("url"); ?>/profile-image-upload-ajax',
                        success: showResponse
                    }).submit();
                }
            });
        });

        function GetFileSize() {
            var fi = document.getElementById('imagefile');
            if ( fi.files.length > 0 ) {
                var fsize = fi.files.item(0).size;
                if ( Math.round((fsize / 1024)) >= 3072 ) {
                    jQuery('input[name="imagefile"]').css('border', '1px solid red');
                    jQuery(window).scrollTop(jQuery('input[name="imagefile"]').position().bottom);
                    jQuery('#noFile').css('color', 'red');
                    jQuery('#noFile').text('File is too large. Please upload file that is less than 3MB');
                    jQuery('input[name="imagefile"]').val('');
                    jQuery('input[name="imagefile"]').focus();
                }
            }
        }

        function showResponse(responseText, statusText, xhr, jQueryform){

            if(responseText.indexOf('.')>0){
                jQuery("#loading").hide();
                jQuery('#thumbviewimage').html('<img src="<?php echo $upload_path; ?>'+responseText+'" style="position: relative;" alt="Thumbnail Preview" />');
                jQuery('#viewimage').html('<img class="preview" alt="" src="<?php echo $upload_path; ?>'+responseText+'" id="thumbnail" />');
                jQuery('#filename').val(responseText);
                jQuery('#save_thumb').show();
                jQuery('#thumbnail').imgAreaSelect({ x1: 0, y1: 0, x2: 100, y2: 100, aspectRatio: '1:1', handles: true  , onSelectChange: preview });
                jQuery("#message").show();

            }else{
                //jQuery('#thumbviewimage').html(responseText);
                //jQuery('#viewimage').html(responseText);
            }
        }

    </script>
    <script type="text/javascript">
        function preview(img, selection) {
            var scaleX = <?php echo $thumb_width;?> / selection.width;
            var scaleY = <?php echo $thumb_height;?> / selection.height;

            /*jQuery('#thumbviewimage > img').css({
                width: Math.round(scaleX * img.width) + 'px',
                height: Math.round(scaleY * img.height) + 'px',
                marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
                marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
            });*/

            var x1 = Math.round((img.naturalWidth/img.width)*selection.x1);
            var y1 = Math.round((img.naturalHeight/img.height)*selection.y1);
            var x2 = Math.round(x1+selection.width);
            var y2 = Math.round(y1+selection.height);

            jQuery('#x1').val(x1);
            jQuery('#y1').val(y1);
            jQuery('#x2').val(x2);
            jQuery('#y2').val(y2);

            jQuery('#w').val(Math.round((img.naturalWidth/img.width)*selection.width));
            jQuery('#h').val(Math.round((img.naturalHeight/img.height)*selection.height));

        }

        jQuery(document).ready(function () {
            jQuery('#save_thumb').click(function() {
                var x1 = jQuery('#x1').val();
                var y1 = jQuery('#y1').val();
                var x2 = jQuery('#x2').val();
                var y2 = jQuery('#y2').val();
                var w = jQuery('#w').val();
                var h = jQuery('#h').val();
                if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
                    alert("Please Make a Selection First");
                    return false;
                }else{
                    return true;
                }
            });
        });
    </script>
    <?php if ( $_GET['from_file'] ) { ?>
    <style>
        #header, #footer {
            display: none;
        }
    </style>
    <?php }
    
    get_footer();

} else {
    header('Location: '.get_bloginfo('url').'/login');
}