<style>
    @font-face {
        font-family: DK Midnight Chalker;
        src: url(<?= get_template_directory_uri() . "/assets/fonts/DKMidnightChalker.otf"; ?>) format("opentype");
        src: url(<?= get_template_directory_uri() . "/assets/fonts/dk-midnight-chalker-webfont-new.eot"; ?>);
    }
    @font-face {
        font-family: "Lato Light";
        src: url(<?= get_template_directory_uri() . "/assets/fonts/Lato-Light.ttf"; ?>) format("truetype");
    }
    @font-face {
        font-family: "Lato Italic";
        src: url(<?= get_template_directory_uri() . "/assets/fonts/Lato-Italic.ttf"; ?>) format("truetype");
    }


    .logo_container {
        display: flex;
        justify-content: center;
    }
    .default_opacity:before {
        opacity: unset;
    }

    .logo img:not(.band-fundraiser-logo) {
        max-height: 80px;
    }

    .band-fundraiser-logo {
        margin-bottom: 12.5px;
        margin-top: 12.5px;
    }


    .gradient_container {
        position: relative;
    }

    .gradient_container {
        background: -webkit-linear-gradient(93deg, #7aa791 0%, #4b949f 50%);
        background: -o-linear-gradient(93deg, #7aa791 0%, #4b949f 50%);
        background: -ms-linear-gradient(93deg, #7aa791 0%, #4b949f 50%);
        background: -moz-linear-gradient(93deg, #7aa791 0%, #4b949f 50%);
        background: linear-gradient(93deg, #7aa791 0%, #4b949f 50%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#7aa791", endColorstr="#4b949f", GradientType=1);
    }


    @media (min-width: 992px) {
        .gradient_container {
            padding-top: 75px;
            padding-bottom: 75px;
        }

        .gradient_section {
            width: 996px;
            margin: auto;
        }
        .gradient_section .text_info {
            font-family: "Lato Italic";
            text-align: center;
            font-size: 17px;
            width: 757px;
            margin: auto;
            line-height: 20px;
        }
        .gradient_section .title h2 {
            font: 400 53px "DKM-Night";
            width: 869px;
            margin: auto;
            color: white;
        }
    }
    @media (min-width: 768px) and (max-width: 991px) {
        .gradient_container {
            padding-top: 50px;
            padding-bottom: 50px;
        }

        .gradient_section {
            width: 750px;
            margin: auto;
        }
        .gradient_section .text_info {
            font-family: "Lato Italic";
            text-align: center;
            font-size: 15px;
            width: 669px;
            margin: auto;
            line-height: 20px;
        }
        .gradient_section .title h2 {
            font: 400 45px "DKM-Night";
            width: 740px;
            margin: auto;
            color: white;
        }
    }
    @media (max-width: 767px) {
        .gradient_container {
            padding-top: 50px;
            padding-bottom: 50px;
        }

        .gradient_section {
            padding-left: 16px;
            padding-right: 16px;
        }
        .gradient_section .text_info {
            font-family: "Lato Italic";
            text-align: center;
            font-size: 20px;
            margin: auto;
            line-height: 26px;
        }
        .gradient_section .title h2 {
            font: 400 32px "DKM-Night";
            margin: auto;
            color: white;
        }
        .gradient_section .container-fluid {
            padding-top: 75px;
            padding-bottom: 75px;
        }
    }
</style>

<div class="gradient_container">
    <div class="gradient_section">
        <div class="title">
            <h2>Looking for the easiest, most effective fundraiser for your team or group? </h2>
        </div>
        <p class="text_info">
            Then look no further. Vertical Raise has the best online donation platform on the market. We utilize email, text messaging and social media campaigns to exponentially increase the reach of your fundraiser. The foundation of our company's success is built upon our best-in-class email deliverability, detailed tracking and the complete personalization of every page. Please contact us above for more information or if you would like to setup a fundraiser for your team.
        </p>
    </div>
</div>
