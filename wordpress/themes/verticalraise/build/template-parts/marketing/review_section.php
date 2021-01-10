<style>
    .review_section {
        text-align: center;
        background-color: white;
    }

    #review_image .sa_logo {
        margin-top: 15px !important;
    }
    #review_header{
        padding-bottom: 10px!important;
    }
    @media (max-width: 767px) {

        .review_section {
            padding-top: 50px;
            padding-bottom: 50px;
        }

        .review_section h2 {
            font-size: 47px;
            padding-bottom: 0;
        }

        .review_section #get_more_info {
            margin-top: 70px;
            font-size: 32px;
        }

        .review_section .block {
            padding-bottom: 0!important;
            margin-top: 5px!important;

        }
    }

    @media (min-width: 768px) and  (max-width: 991px) {

        .review_section {
            padding-top: 75px;
            padding-bottom: 75px;
        }

        .review_section h2 {
            font-size: 45px;
            padding-bottom: 15px;
        }

        .review_section #get_more_info {
            margin-top: 30px;
        }
    }


    @media (min-width: 992px) {

        #shopper_review_page {
            width: 996px;
            margin: auto;
        }

        .review_section {
            padding-top: 100px;
            padding-bottom: 100px;
        }

        .review_section h2 {
            font-size: 53px;
            padding-bottom: 15px;
        }

        .review_section #get_more_info {
            margin-top: 45px;
        }
        #review_header{
            padding-bottom: 25px;
        }

    }

</style>

<div class="review_section">
    <div class="container-fluid">
        <div class="row">
            <h2>Testimonials</h2>

            <div id="shopper_review_page">
                <div id="review_header"></div>
                <div id="merchant_page"></div>
                <div id="review_image"><a href="https://www.shopperapproved.com/reviews/verticalraise.com/"
                                          target="_blank" rel="nofollow"></a></div>
            </div>
            <?php if ( get_page_template_slug() === "SportFundraising.php" ) { ?>
            <a class="link" href="#" id="get_more_info">Get More Info</a>
            <?php } ?>

        </div>
    </div>
</div>

<script type="text/javascript">
    var sa_review_count = 5;
    var sa_date_format = 'F j, Y';

    function saLoadScript(src) {
        var js = window.document.createElement("script");
        js.src = src;
        js.type = "text/javascript";
        document.getElementsByTagName("head")[0].appendChild(js);
    }

    saLoadScript('//www.shopperapproved.com/merchant/27987.js');
    setTimeout(function () {
        if ( jQuery("#review_header").length )  {
            let content = jQuery("#review_header").html();
            if( content !== "") {
                let media = window.matchMedia("(max-width: 767px)");
                if( media.matches ) {
                    jQuery('#review_header').css({
                            display: "flex",
                            flexDirection: "column",
                            alignItems: "center",
                    }).children(".block").css({
                            width: "80%",
                            height: "auto",
                    });
                } else {
                    jQuery('#review_header').css({
                        textAlign : "center",
                        paddingBottom : '0px!important'
                    }).children(".block").css({
                        width: "20%",
                        height: "auto",
                    });
                }
            }
        }
    }, 2500);
</script>
