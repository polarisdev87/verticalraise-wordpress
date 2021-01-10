<?php
/* Template Name: Thank You Payment */

use classes\app\fundraiser\Fundraiser_Media;

get_header( 'general' );

if ( empty( $_GET['uid'] ) ) {
    $uid = 0;
} else {
    $uid = $_GET['uid'];
}
$spreadClose = false;
if ( isset( $_GET['spreadClose'] ) && $_GET['spreadClose'] == '1' ) {
    $spreadClose = true;
}
//logo image url
$fundraise_mediaObj = new Fundraiser_Media();
$image_url          = $fundraise_mediaObj->get_fundraiser_logo( $_GET['fundraiser_id'] );

$permalink          = get_permalink( $_GET['fundraiser_id'] );
$permalink_copy     = $permalink . $_GET['media'] . '/' . $uid;

$return_url = $permalink_copy;
$args       = array(
    'post_type'   => 'fundraiser',
    'post_status' => array( 'pending', 'publish', 'rejected' ),
    'p'           => $_GET['fundraiser_id']
);

$fundraiser_query = new WP_Query( $args );

while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();
    ?>
    <!--MAIN start-->
    <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/assets/js/thankyou.js"></script>
    <script>

        $(document).ready(function () {
            <?php if ( !is_mobile_new() ) { ?>
                $(".fancyboxThankyou").fancybox({
                    width: 780,
                    maxWidth: 780,
                    minWidth: 300,
                    minHeight: 1500,
                    maxHeight: 5000,
                    height: 4000,
                    scrolling: 'no',
                    wrapCSS: 'FancyThankyou',
                    helpers: {
                        overlay: { closeClick: false }
                    }
                });
            <?php } ?>

        });

        var spreadClose = '<?php echo $spreadClose ?>';

        $(window).load(function () {
            console.log("loading")
            <?php if ( !is_mobile_new() ) { ?>

                setTimeout(function () {
                    $(".fancyboxThankyou").trigger("click");
                })

            <?php } else { ?>

                setTimeout(function () {
                    if ( !spreadClose ) {
                        window.location.href = $(".fancyboxThankyou").attr('href')
                    }
                }, 300);
            <?php } ?>
        });
    </script>
    <main>

        <!--THANK YOU PAGE start-->
        <div class="thank_you_page">

            <div class="container">

                <div class="modal-header">
                    <h3>Thank you <?php echo $_GET['fname']; ?> <?php echo $_GET['lname']; ?> <br>
                        <?php
                        if ( !empty( $_GET['uid'] ) ) {
                            $uid_info = get_userdata( $_GET['uid'] );
                            echo ' ' . $uid_info->display_name . ' received credit for your donation.';
                        }
                        ?>
                    </h3>
                    <p>a receipt has been emailed to <?php echo $_GET['email']; ?> for your records</p>
                </div>
                <div class="modal-body">
                    <div class="thankyou fundraiser_img">
                        <!--- fundraiser youtube video thumbnail image -->
                        <?php
                        $youtubeUrl = get_post_meta( $_GET['fundraiser_id'], 'youtube_url', true );
                        $youtubeImg = $image_url;
                        if ( ! empty( $youtubeUrl ) ) {
	                        $imgFlag = $fundraise_mediaObj->get_fundraiser_youtube_image( $youtubeUrl );
	                        if ( $imgFlag ) {
		                        //$youtubeImg = $imgFlag;
	                        }
                        }
                        ?>
                        <img src="<?php echo $youtubeImg ?>" class="thank-logo" alt="">
                        <!--- fundraiser youtube video thumbnail image -->
                        <b><?php the_title(); ?></b>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="<?php echo $return_url; ?>" class="go_back" data-dismiss="modal">
                        Go back to fundraiser
                    </a>
                </div>
                <a class="fancyboxThankyou" data-fancybox-type="iframe"
                   href="<?php echo get_site_url() ?>/invite-start/?fundraiser_id=<?php echo $_GET['fundraiser_id']; ?>&uid=<?php echo $_GET['uid'] ?>&display_type=single&page=thankyou&fname=<?php echo $_GET['fname'] ?>&lname=<?php echo $_GET['lname'] ?>&email=<?php echo $_GET['email'] ?>&media=<?php echo $_GET['media'] ?>"></a>

            </div>
        </div>
    </main>
    <!--MAIN end-->
    <?php
endwhile;

get_footer();
