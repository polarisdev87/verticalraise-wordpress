<style>
    .hero-container {
        position: relative;
    }
    .hero-container .hero-cf7 {
        position: relative;
    }
    .hero-container .hero-cf7  h1{
        color: #ffffff;
    }
    .hero-container .hero-cf7  .wpcf7-form .wpcf7-form-control-wrap span.wpcf7-not-valid-tip,.hero-container .hero-cf7  .wpcf7-form label.error,.hero-container .hero-cf7  .wpcf7-form .classip span.wpcf7-not-valid-tip,.hero-container .hero-cf7  .wpcf7-form .phone span.wpcf7-not-valid-tip {
        font: 400 18px 'DKM-Night';
        padding-bottom: 0px;
    }

    .hero-container  .hero-bgimage {
        position: absolute;
        width: 100%;
        height: 100%;
    }
    .hero-container  .hero-bgimage img{
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
    }
    .hero-container .hero-bgimage source{
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: top;
    }

    .hero-cf7 .wpcf7-form-control {
        text-transform: uppercase!important;
    }

    @media (min-width: 1000px) {
        .hero-container {
            /*min-height: 1050px;*/
        }
        .hero-container .hero-cf7 {
            padding-top: 200px;
            padding-left: 50px;
        }
        .hero-container .hero-cf7 .hero_content {
            width: 520px;
            margin: auto;
        }
        .hero-container .hero-cf7 h1:nth-child(1) {
            max-width: 1000px;
        }
        .hero-container .hero-cf7 h1:nth-child(1),  .hero-container .hero-cf7 h1:nth-child(3) {
            font-size: 53px;
            font-weight: 100;
            font-family: "DKM-Night";
        }

        .hero-container .hero-cf7 h1:nth-child(3) {
            line-height: 80px;
        }

        .hero-container .hero-cf7 h1:nth-child(2) {
            font-size: calc(1.81 * 53px);
            font-family: "DKM-Night";
        }

        .hero-container .hero-cf7 p {
            font-size: 38px;
            font-weight: 100;
            font-family: "DKM-Night";
            line-height: 140px
        }

        .hero-container .hero-cf7 .wpcf7-form {
            padding-top: 100px;
        }

        .hero-container .hero-cf7 .display_table {
            display: flex;
            flex-direction: row-reverse;
            padding-top: 40px;
            padding-right: 150px;
            padding-bottom: 150px;
        }

        .hero-container .hero-cf7 .landing_page_input {
            border: none;
            background: none;
            all: initial;
            display: block;
            padding-left: 25px;
            padding-bottom: 35px;
            color: #ffffff;
            font-family: "Lato Light";
            font-weight: 100;
            font-size: 17px;
            background-image: url(<?= get_template_directory_uri() . "/assets/images/box-border.png"; ?>);
            background-repeat: no-repeat;
            background-position: bottom;
            margin-top: -110px;
            width: 450px;

        }

        .hero-container .hero-cf7 .wpcf7-validation-errors {
            border-color: #ea5f5f !important;
            color: #ea5f5f !important;
            background: none !important;
            /*width: unset !important;
            margin: auto !important;*/
            border: none !important;
            font-family: 'DKM-Night';
            font-size: 29px;
            border-bottom: none !important;
            font-weight: 400;
        }

        .hero-container .hero-cf7 .wpcf7-mail-sent-ok {
            color: #3CAB56 !important;
            border-color: #3CAB56 !important;
            background: none !important;
            /*width: 400px !important;*/
            margin: auto !important;
            border: none !important;
            font-family: 'DKM-Night';
            font-size: 29px;
            border-bottom: none !important;
            font-weight: 400;
        }

        .hero-container .hero-cf7 .wpcf7-submit {
            position: relative !important;
            top: -30px !important;
            left: 240px !important;
            width: 230px;
            font: 400 36px 'DKM-Night';
            color: #FFF;
            text-align: center;
            text-decoration: none;
            text-transform: uppercase;
            background-color: #52B6D5;
            height: 60px;
            line-height: 60px;
            border-radius: 5px;
            text-align: justify;
        }

        .hero-container .hero-cf7 .ajax-loader {
            position: relative !important;
            top: -38px !important;
            left: 190px !important;
            background-image: url(<?= get_template_directory_uri() . "/assets/images/ajax-loader.gif"; ?>) !important;
            width: 32px !important;
            height: 32px !important;
        }
        .wpcf7-response-output{
            position: relative;
            top: -127px;
            /*left: -350px;*/
        }
        .wpcf7-form div.wpcf7-validation-errors{
            text-align: left;
        }
        .wpcf7-not-valid-tip{
            position: absolute;
            min-width: 210px;
            /*top: -55px;*/
            left: 25px;
        }

    }
    @media (min-width: 768px) and  (max-width: 999px) {

        .hero-container {
            /*min-height: 911px;*/
        }

        .hero-cf7 {
            padding-top: 160px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        .hero-cf7 .hero_content {
            width: 450px;
            margin: auto;
        }

        .hero-cf7 h1:nth-child(1), .hero-cf7 h1:nth-child(3) {
            font-size:45px;
            font-weight: 100;
            font-family: "DKM-Night";
        }

        .hero-cf7 h1:nth-child(3) {
            line-height: 50px;
        }

        .hero-cf7 h1:nth-child(2) {
            font-size: calc(1.81 * 45px);
            font-family: "DKM-Night";
        }

        .hero-cf7 p {
            font-size: 32px;
            font-weight: 100;
            font-family: "DKM-Night";
            line-height: 80px
        }

        .hero-cf7 .signupLink {
            font-weight: 100;
            border-radius: 4px;
        }

        .hero-cf7 form > p:nth-child(2) {
            /*display: flex;
            flex-direction: column;
            align-items: center;*/
        }

        .hero-cf7 .display_table {
            display: flex;
            flex-direction: row-reverse;
            padding-top: 40px;
            padding-right: 50px;
            padding-bottom: 150px;
        }


        .hero-cf7 .band-fundraiser-logo {
            margin-bottom: 12.5px;
            margin-top: 12.5px;
        }


        .hero-cf7 .landing_page_input {
            border: none;
            background: none;
            all: initial;
            display: block;
            padding-left: 25px;
            padding-bottom: 35px;
            color: #ffffff;
            font-family: "Lato Light";
            font-weight: 100;
            font-size: 17px;
            background-image: url(<?= get_template_directory_uri() . "/assets/images/box-border.png" ?>);
            background-repeat: no-repeat;
            background-position: bottom;
            margin-top: -70px;
            width: 400px;
        }

        .hero-cf7 div.wpcf7 {
            padding-top: 80px !important;
        }

        .hero-cf7 .wpcf7-validation-errors {
            border-color: #ea5f5f !important;
            color: #ea5f5f !important;
            background: none !important;
            width: unset !important;
            margin: auto !important;
            border: none !important;
            font-family: 'DKM-Night';
            font-size: 32px;
            font-weight: 400;
        }

        .hero-cf7 .wpcf7-mail-sent-ok {
            color: #3CAB56 !important;
            border-color: #3CAB56 !important;
            background: none !important;
            margin: auto !important;
            border: none !important;
            font-family: 'DKM-Night';
            font-size: 32px;
            font-weight: 400;
        }

        .hero-cf7 .wpcf7-submit {
            position: relative !important;
            top: -10px !important;
            left: 190px !important;
            width: 230px;
            font: 400 36px 'DKM-Night';
            color: #FFF;
            text-align: center;
            text-decoration: none;
            text-transform: uppercase;
            background-color: #52B6D5;
            height: 60px;
            line-height: 60px;
            border-radius: 5px;
            text-align: justify;
        }

        .hero-cf7 .ajax-loader {
            position: relative !important;
            top: -18px !important;
            left: 140px !important;
            background-image: url(<?= get_template_directory_uri() . "/assets/images/ajax-loader.gif" ?>) !important;
            width: 32px !important;
            height: 32px !important;
        }
        .wpcf7-response-output{
            position: relative;
            top: -20px;
            left: 0;
        }
        .wpcf7-not-valid-tip{
            position: absolute;
            /*top: -50px;*/
            min-width: 150px;

            left: 25px
        }

    }

    @media (max-width: 767px) {
        .hero-container {
            /*min-height: 815px;*/
        }
        .hero-cf7 {
            padding-top: 110px;
            padding-bottom: 100px;
            width: 100%;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
        }
        .hero-cf7 .hero_content {
            width: 345px;
            margin: auto;
        }
        .hero-cf7 h1:nth-child(1), .hero-cf7 h1:nth-child(3){
            font-size: 32px;
            font-weight: 100;
            font-family: "DKM-Night";
        }

        .hero-cf7 h1:nth-child(3){
            line-height: 50px;
        }

        .hero-cf7 h1:nth-child(2) {
            font-size: calc(1.81 * 32px);
            font-family: "DKM-Night";
        }

        .hero-cf7 p {
            font-size: 32px;
            font-weight: 100;
            font-family: "DKM-Night";
            line-height: 80px
        }

        .hero-cf7 .signupLink {
            font-weight: 100;
            border-radius: 4px;
        }

        .hero-cf7 .display_table {
            display: flex;
            flex-direction: row-reverse;
            padding-top: 40px;
            padding-right: 50px;
            padding-bottom: 150px;
        }


        .hero-cf7 .landing_page_input{
            border: none;
            background: none;
            all: initial;
            display: block;
            padding-bottom: 35px;
            color: #ffffff;
            font-family: "Lato Light";
            font-weight: 100;
            font-size: 17px;
            background-image: url(<?= get_template_directory_uri() . "/assets/images/box-border.png" ?>);
            background-repeat: no-repeat;
            background-position: bottom;
            margin-top: -60px;
            width: 100%;
        }

        .hero-cf7 div.wpcf7 {
            padding-top: 80px!important;
        }

        .hero-cf7 .wpcf7-validation-errors{
            border-color: #ea5f5f !important;
            color: #ea5f5f !important;
            background: none !important;
            margin: auto!important;
            border: none!important;
            font-family: 'DKM-Night';
            font-size: 20px;
            font-weight: 400;
        }

        .hero-cf7 .wpcf7-mail-sent-ok{
            color: #3CAB56 !important;
            border-color: #3CAB56 !important;
            background: none !important;
            margin: auto!important;
            border: none!important;
            font-family: 'DKM-Night';
            font-size: 32px;
            font-weight: 400;
        }

        .hero-cf7 .wpcf7-submit{
            position: relative!important;
            top: 25px !important;
            left: 100px !important;
            width: 230px;
            font: 400 36px 'DKM-Night';
            color: #FFF;
            text-align: center;
            text-decoration: none;
            text-transform: uppercase;
            background-color: #52B6D5;
            height: 60px;
            line-height: 60px;
            border-radius: 5px;
            text-align: justify;
        }

        .hero-cf7  .ajax-loader{
            position: relative!important;
            top: 15px !important;
            left: 50px !important;
            background-image: url(<?= get_template_directory_uri() . "/assets/images/ajax-loader.gif" ?>)!important;
            background-repeat: no-repeat;
            width: 32px!important;
            height: 32px!important;
        }
        .wpcf7-response-output{
            position: relative;
            top: 12px;
            float: none;
        }

        .wpcf7-not-valid-tip{
            position: absolute;
            min-width: 150px;
            /*top: -37px;*/
        }


    }

    @media (min-width: 1092px) {
        .wpcf7-response-output{
            position: relative;
            top: -127px;
            /*left: -430px;*/
        }
    }

    .wpcf7-form .wpcf7-mail-sent-ok , .wpcf7-form .wpcf7-validation-errors{
        float: none;
    }
    .hero-cf7 .wpcf7-form-control {
        text-transform: uppercase !important;
    }

</style>

<div class="hero-container">
    <picture class="hero-bgimage">
        <?php if ( have_rows('images') ): ?>

            <?php while ( have_rows('images') ): the_row();

                $desktop = get_sub_field('desktop');
                $mobile = get_sub_field('mobile');
                $tablet = get_sub_field('tablet');

                ?>
                <source srcset="<?= $mobile; ?>"
                        media="(max-width: 766px)" >
                <source srcset="<?= $tablet; ?>"
                        media="(max-width: 992px)">

                <img src="<?= $desktop; ?>" />

            <?php endwhile; ?>

        <?php endif; ?>
    </picture>


    <div class="hero-cf7">
        <h1>Use our online donation platform to raise money for your business or your employees during this difficult time.</h1>

        <p id="interested_p">Want more info?</p>

        <?php
        $shortcode = get_field( "cf7_shortcode" );
        echo do_shortcode($shortcode);
        ?>
    </div>
</div>
