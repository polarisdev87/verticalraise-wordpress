<style>
    .landing_page_input_hidden, .rep-email{
        display: none!important;
    }
    .gradient_container {
        background: -webkit-linear-gradient(93deg, #7aa791 0%, #4b949f 50%);
        background: -o-linear-gradient(93deg, #7aa791 0%, #4b949f 50%);
        background: -ms-linear-gradient(93deg, #7aa791 0%, #4b949f 50%);
        background: -moz-linear-gradient(93deg, #7aa791 0%, #4b949f 50%);
        background: linear-gradient(93deg, #7aa791 0%, #4b949f 50%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#7aa791", endColorstr="#4b949f", GradientType=1);
        padding-top: 100px;
        padding-bottom: 100px;
    }
    .gradient_container .gradient_section {
        max-width: 996px;
        margin-left: auto;
        margin-right: auto;
    }
    .gradient_container .gradient_section h1 {
        color: white;
        text-align: center;
        font-size: 52px;
        font-weight: 100;
    }
    .gradient_container .gradient_section form > p:nth-child(2) {
        display: flex;
        flex-wrap: wrap;
        justify-content: baseline;
        justify-content: center;
    }
    .gradient_container .gradient_section form > p:nth-child(3) {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 50px;
    }
    .gradient_container .gradient_section .wpcf7-form-control-wrap {
        padding-left: 25px;
        padding-right: 25px;
        height: 100px;
    }
    .gradient_container .gradient_section .landing_page_input {
        border: none;
        background: none;
        all: initial;
        display: block;
        line-height: 70px;
        color: #ffffff;
        font-family: "Lato Light";
        font-weight: 100;
        font-size: 20px;
        background-image: url(<?= get_template_directory_uri() . "/assets/images/border8.png"; ?>);
        background-repeat: no-repeat;
        background-position-x: right;
        background-position-y: bottom;
        width: 400px;
        background-origin: padding-box;
        height: 70px;
    }
    .gradient_container .gradient_section div.wpcf7 form {
        padding-top: 40px !important;
    }
    .gradient_container .gradient_section .wpcf7-validation-errors {
        border-color: #ea5f5f !important;
        color: #ea5f5f !important;
        background: none !important;
        width: unset !important;
        margin: auto !important;
        border: none !important;
        font-family: "DKM-Night";
        font-size: 32px;
        font-weight: 400;
        float: none !important;
    }
    .gradient_container .gradient_section .wpcf7-mail-sent-ok {
        color: white !important;
        border-color: #3CAB56 !important;
        background: none !important;
        width: unset !important;
        margin: auto !important;
        border: none !important;
        font-family: "DKM-Night";
        font-size: 32px;
        font-weight: 400;
        float: none !important;
    }
    .gradient_container .gradient_section .wpcf7-submit {
        width: 230px;
        font: 400 36px "DKM-Night";
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
    .gradient_container .gradient_section .ajax-loader {
        background-image: url(<?= get_template_directory_uri() . "/assets/images/ajax-loader.gif"; ?>) !important;
        width: 32px !important;
        height: 32px !important;
        position: relative !important;
        top: -46px !important;
        left: 80px !important;
    }

    @media (max-width: 680px) {

        .gradient_container {
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .gradient_container .gradient_section h1 {
            font-size: 40px;
        }
        .gradient_container .gradient_section .wpcf7-form-control:not(.wpcf7-submit) {
            width: 100%;
        }
    }

</style>
<div class="gradient_container">
    <div class="gradient_section">
        <h1>have questions? Contact Us!</h1>
        <?php
        $shortcode = get_field( "cf7_tag" );
        if ( ! $shortcode ) {
            $shortcode = get_field( "cf7_shortcode" );
        }
        echo do_shortcode($shortcode);

        ?>
    </div>
</div>
