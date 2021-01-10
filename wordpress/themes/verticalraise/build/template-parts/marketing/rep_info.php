<style>
    .area-info {
        display: flex;
        flex-direction: row;
        justify-content: center;
        position: relative;
        top: -25px;
    }
    .area-info h6 {
        color: white;
        font-weight: 100;
        font-size: 30px;
        width: 275px;
        margin-left: 150px;
    }

    .logo-container {
        display: flex;
        flex-direction: row;
        align-items: center;
        position: absolute;
        width: 99%;
        top: 50px;
        overflow-x: hidden;
    }
    .logo-container .border-line {
        width: 100%;
        height: 10px;
        margin-left: 10px;
        margin-right: 10px;
        background-image: linear-gradient(90deg, #ade8cd 0%, #60d2e1 50%, #52b7d5 100%);
    }
    .logo-container .border-line:nth-of-type(2) {
        background-image: linear-gradient(270deg, #ade8cd 0%, #60d2e1 50%, #52b7d5 100%);
    }
    .logo-container img {
        flex-shrink: 0;
    }

    .rep_info {
        display: flex;
        justify-content: center;
    }
    .rep_info .picture_container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-end;
        padding-right: 20px;
        flex-grow: 0;
    }
    .rep_info .picture_container img {
        border-radius: 50%;
        height: 275px;
        width: 275px;
        object-fit: cover;
        padding: 20px;
        border: solid 1px #FFFFFF;
        z-index: 10;
    }
    .rep_info .data_container {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        height: 150px;
        margin-top: 45px;
        position: relative;
        max-width: 855px;
        flex-grow: 1;
    }
    .rep_info .data_container .float-at {
        position: absolute;
        top: 92px;
        text-align: justify;
    }
    .rep_info .data_container .float-at button {
        border-radius: 0;
        font-size: 32px;
        min-width: 240px;
        margin-right: 25px;
        z-index: 10;
    }
    .rep_info .data_container .float-at .btn-container {
        padding-top: 25px;
        display: flex;
        justify-content: normal;
    }
    .rep_info .data_container .float-at a {
        color: white;
        z-index: 10;
    }
    .rep_info .data_container h3 {
        text-align: left;
        color: white;
        font-weight: 100;
        z-index: 10;
        font-size: 40px;
    }
    .rep_info .data_container h5 {
        text-align: left;
        color: white;
        font-weight: 300;
        font-family: "Lato";
        font-size: 16px;
        z-index: 10;
    }

    .careers_oportunities {
        background-image: url(<?= get_template_directory_uri() . "/assets/images/background2-noborder.jpg";?>);
        background-position: top right;
        background-size: cover;
        background-repeat: no-repeat;
        position: relative;
        padding-left: 5px;
        padding-right: 5px;
        padding-top: 150px;
        padding-bottom: 300px;
    }
    .careers_oportunities .careers_main_text {
        margin: auto auto;
        text-align: center;
        height: 290px;
        max-width: 996px;
    }
    .careers_oportunities .careers_main_text h1 {
        color: white;
        font-weight: 400;
        font-size: 52px;
        margin-bottom: 20px;
    }
    .careers_oportunities .careers_main_text p {
        font-size: 16px;
    }
    .careers_oportunities .careers_main_text p:nth-of-type(1) {
        margin-bottom: 20px;
    }
    .careers_oportunities .careers_main_text button {
        font: 100 36px "DKM-Night";
        color: #FFF;
        text-align: center;
        text-decoration: none;
        text-transform: uppercase;
        background-color: #52B6D5;
        height: 60px;
        line-height: 60px;
        border-radius: 5px;
        border: none;
    }

    .careers_oportunities::before {
        content: "";
        display: block;
        width: 100%;
        height: 50%;
        background: linear-gradient(90deg, #ade8cd 0%, #60d2e1 50%, #52b7d5 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#7aa791", endColorstr="#4b949f", GradientType=1);
        position: absolute;
        bottom: 0;
        left: 0;
        opacity: 0.6;
        z-index: 0;
    }

    .rep_info .data_container .float-at button {
        border-radius: 0;
        font-size: 32px;
        min-width: 0;
        margin-right: 0;
    }

    .data_container .soc_icons .soc_link {
        width: 32px;
        height: 32px;
        min-width: 50px;
    }
    .data_container .soc_icons {
        position: relative;
        top: -60px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        height: 40px;
        z-index: 20;
    }
    .data_container .soc_icons .soc_link.instagram_link {
        background: url(<?= get_template_directory_uri() . "/assets/images/soc2.png" ; ?>) no-repeat;
        background-size: 25px;
        background-position: center center;
    }
    .data_container .soc_icons .soc_link.facebook_link {
        background: url(<?= get_template_directory_uri() . "/assets/images/soc3.png" ; ?>) no-repeat;
        background-size: 14px;
        background-position: center center;
    }
    .data_container .soc_icons .soc_link.linkedin_link {
        background: url(<?= get_template_directory_uri() . "/assets/images/soc4.png"; ?>) no-repeat;
        background-size: 25px;
        background-position: center center;
    }
    .data_container .soc_icons .soc_link.twitter_link {
        background: url(<?= get_template_directory_uri() . "/assets/images/soc5.png" ; ?>) no-repeat;
        align-self: flex-end;
        background-size: 25px;
        background-position: center center;
    }

    @media (min-width: 768px) and  (max-width: 991px) {
        .careers_oportunities .careers_main_text {
            height: 260px;
            width: 100%;
        }
        .rep_info .picture_container img {
            height: 225px;
            width: 225px;
        }
        .rep_info .data_container .float-at {
            top: 67px;
        }
    }

    @media (max-width: 767px) {
        .careers_oportunities {
            background-image: url(<?= get_template_directory_uri() . "/assets/images/mobile-background2-noborder.jpg" ; ?>);
            background-size: cover;
            padding-top: 150px;
        }
        .careers_oportunities .careers_main_text {
            width: 100%;
        }
        .rep_info .data_container h3 {
            text-align: left;
            color: white;
            font-weight: 100;
            font-size: 36px;
        }

        .logo-container {
            top: 40px;
        }

        .area-info {
            top: -40px;
        }
        .area-info h6{
            margin-left: 38%;
        }
        .rep_info {
            grid-template-columns: 50% 50%;
        }
        .rep_info .picture_container img {
            height: 175px;
            width: 175px;
        }
        .rep_info .data_container .float-at {
            position: absolute;
            top: 150px;
            text-align: justify;
            right: 6px;
            width: 96vw;
        }
        .rep_info .data_container .float-at .btn-container {
            padding-top: 10px;
            display: flex;
            justify-content: space-around;
            padding-bottom: 10px;
        }
        .rep_info .data_container .float-at .btn-container .career_opportunities {
            min-width: 40%;
            line-height: 40px;
            height: 40px;
        }

        .data_container .soc_icons {
            top: 0;
            justify-content: space-between;
        }
        .data_container .soc_icons .soc_link {
            width: 32px;
            height: 32px;
            min-width: 0;
        }

        .rep_info .data_container .float-at button {
            border-radius: 0;
            font-size: 32px;
            min-width: 0;
            margin-right: 0;
        }

        .gradient_container .gradient_section .landing_page_input {
            width: 80vw;
        }
    }
    @media (max-width: 350px) {
        .rep_info .picture_container img {
            height: 150px;
            width: 150px;
            padding: 5px;
        }

        .rep_info .data_container .float-at button {
            font-size: 25px;
        }
    }

    @media (min-width: 768px) {
        .rep_info .data_container .float-at button {
            min-width: 220px;
            margin-right: 20px;
        }
    }

</style>

<div class="image_container careers_oportunities">
    <a href="<?php echo get_bloginfo('url') ?>" title="VERTICALRAISE" class="logo-container">
        <div class="border-line"></div>
        <img alt="band fundraisers" class="" src="<?php echo get_template_directory_uri() . "/assets/images/logo.png" ?>" >
        <div class="border-line"></div>
    </a>
    <div class="area-info">
        <h6><?= get_field( "area" ); ?></h6>
    </div>

    <div class="">
        <div class="careers_main_text">
            <div class="rep_info">
                <div class="picture_container">
                    <img src="<?= get_field( "picture" ); ?>">

                </div>
                <div class="data_container">
                    <h3><?= get_field( "full_name" ); ?></h3>
                    <h5><?= get_field( "title" ); ?></h5>
                    <?php
                    $social_networks = get_field('social_networks');
                    if( $social_networks ): ?>

                        <div class="soc_icons">

                            <a class="linkedin_link soc_link" href="<?= $social_networks['linkedin']; ?>" target="_blank">
                            </a>

                            <a class="twitter_link soc_link" href="<?= $social_networks['twitter']; ?>" target="_blank">
                            </a>

                            <a class="facebook_link soc_link" href="<?= $social_networks['facebook']; ?>" target="_blank">
                            </a>

                            <a class="instagram_link soc_link" href="<?= $social_networks['instagram']; ?>" target="_blank">
                            </a>

                        </div>
                    <?php endif; ?>


                    <div class="float-at">
                        <p>
                            <?= get_field( "resume" ); ?>
                        </p>

                        <p><a id="mail-link" href="mailto:<?= get_field( "email" ); ?>"><?= get_field( "email" ); ?></a></p>
                        <p><a id="phone-link" href="tel:<?= get_field( "phone" ); ?>"><?= get_field( "phone" ); ?></a></p>


                        <div class="btn-container">
                            <button class="career_opportunities">
                                call rep now
                            </button>
                            <button class="career_opportunities">
                                email rep now
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>


<script type="text/javascript">

    jQuery( document ).ready( function( $ ) {

        $(".float-at button:nth-child(1)").click(function () {
            window.location.href = $("#phone-link").attr("href");
        });
        $(".float-at button:nth-child(2)").click(function () {
            window.location.href = $("#mail-link").attr("href");
        });

        function changeBoxSize() {
            if (window.innerWidth > 767) {
                var height_text = parseInt($(".float-at").css('height'));
                var half_box_size = parseInt($(".careers_main_text").css('height')) / 2;
                var padding = height_text - half_box_size;

                $(".careers_oportunities").css({
                    paddingBottom: padding,
                });

                $('head').append("<style>@media (min-width: 767px) { .careers_oportunities:before{height:" + ( height_text + 15 )  + "px!important;} }</style>");

            } else {
                var height_text = parseInt($(".float-at").css('height'));
                var box_size = parseInt($(".careers_main_text").css('height'));
                var real_box_size = parseInt($(".rep_info").css('height'));
                var overflow_box_size = box_size - real_box_size;
                var padding = height_text - overflow_box_size;

                $(".careers_oportunities").css({
                    paddingBottom: padding,
                });

                $('head').append("<style>@media (max-width: 767px) { .careers_oportunities:before{height:" + height_text  + "px!important;} }</style>");
            }
        }

        changeBoxSize();

        function delay() {
            setTimeout(changeBoxSize, 250);
        }

        window.addEventListener("resize", delay);
    });
</script>
