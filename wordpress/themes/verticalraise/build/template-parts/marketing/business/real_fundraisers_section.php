<?php if ( have_rows('fundraisers') ): ?>

<style>
    @media (min-width: 992px) {
        .blackbox_container{
            background: rgba(35, 31, 32, 1);
            padding-top: 100px;
            padding-bottom: 100px;
        }
        .blackbox_container .blackbox_section {
            width: 996px;
            margin: auto;
            text-align: center;
        }
        .blackbox_container .blackbox_section h2{
            color: #ffffff;
            text-align: center;
            font-size: 53px;
        }

        .blackbox_container .blackbox_section .fund_logo {
            max-height: 175px;
            border-radius: 50%;
        }


        .blackbox_container .blackbox_section .funds_container{
            padding-top: 50px;
        }

        .blackbox_container .blackbox_section .fund_container{
            padding: 10px;
            border-radius: 50%;
            border: 3px solid white;
            width: 200px;
            height: 200px;
            margin: auto;
        }

        .blackbox_container .blackbox_section .bf_text_section{
            padding-top: 25px;
            width: 300px;
            margin: auto;
        }
        .blackbox_container .blackbox_section .bf_text_section h3{
            color: #FFFFFF;
            font-size: 35px;
            font-weight: 100;
            width: 280px;
            margin: auto;
            height: 75px;
            overflow: hidden;
        }
        .blackbox_container .blackbox_section .bf_text_section p{
            color: rgb(211,211,211);
            font-size: 20px;
        }

        .blackbox_container .blackbox_section .bf_progress{
            width: 280px;
            padding-top: 18px;
        }
        .blackbox_container .blackbox_section .bf_text_section h4{
            color: #FFFFFF;
            font-weight: 100;
            font-size: 32px;
            padding-top: 15px;
        }
        .blackbox_container .blackbox_section .link {
            min-width: inherit;
            line-height: inherit;
            height: inherit;
            font-weight: 100;
            border-radius: 4px;
            font-size: 25px;
            padding: 15px 25px;
            margin-top: 25px;
        }
    }

    @media (min-width: 768px) and  (max-width: 991px) {
        .blackbox_section {
            padding-top: 75px;
            padding-bottom: 75px;
            background: rgba(35, 31, 32, 1);
            text-align: center;
        }
        .blackbox_container .blackbox_section h2{
            color: #ffffff;
            text-align: center;
            font-size: 45px;
            padding-bottom: 25px;
        }

        .blackbox_container .blackbox_section .fund_logo {
            max-height: 175px;
            border-radius: 50%;
        }

        .blackbox_container .blackbox_section.funds_container{
            padding-top: 50px;
        }

        .blackbox_container .blackbox_section .fund_container{
            padding: 6px;
            margin-left: 25%;
            margin-right: 25%;
            border-radius: 50%;
            border: 3px solid white;
        }

        .blackbox_container .blackbox_section .bf_text_section{
            padding-top: 25px;
        }
        .blackbox_container .blackbox_section .bf_text_section h3{
            color: #FFFFFF;
            padding: 0 9%;
            font-size: 23px;
            font-weight: 100;
            height: 50px;
            overflow: hidden;
        }
        .blackbox_container .blackbox_section .bf_text_section p{
            color: rgb(211,211,211);
            font-size: 15px;
        }

        .blackbox_container .blackbox_section .bf_progress{
            padding: 15px 16%;
        }
        .blackbox_container .blackbox_section .bf_text_section h4{
            color: #FFFFFF;
            font-weight: 100;
            font-size: 20px;
        }
        .blackbox_container .blackbox_section .link {
            min-width: unset;
            line-height: unset;
            height: unset;
            font-weight: 100;
            border-radius: 4px;
            font-size: 20px;
            padding: 3% 7%;
            margin-top: 7%;
        }
    }

    @media (max-width: 767px) {
        .blackbox_section {
            padding-top: 75px;;
            padding-bottom: 75px;;
            background: rgba(35, 31, 32, 1);
            text-align: center;
        }
        .blackbox_container .blackbox_section h2{
            color: #ffffff;
            text-align: center;
            font-size: 47px;

        }

        .blackbox_container .blackbox_section .fund_logo {
            border-radius: 50%;
            width: 200px;
        }

        .blackbox_container .blackbox_section .funds_container{
            padding-top: 15px;
        }

        .blackbox_container .blackbox_section .fund_container{
            padding: 12px;
            border-radius: 50%;
            margin: auto;
            width: 150px;
            height: 150px;
            border: 3px solid white;
        }

        .blackbox_container .blackbox_section .bf_text_section{
            padding-top: 15px;
            max-width: 300px;
            margin: 0 auto;
        }
        .blackbox_container .blackbox_section .bf_text_section h3{
            color: #FFFFFF;
            padding: 0px 10%;
            font-size: 32px;
            font-weight: 100;
        }
        .blackbox_container .blackbox_section .bf_text_section p{
            color: rgb(211,211,211);
            font-size: 22px;

        }

        .blackbox_container .blackbox_section .bf_progress{
            padding: 15px 16%;
        }
        .blackbox_container .blackbox_section .bf_text_section h4{
            color: #FFFFFF;
            font-weight: 100;
        }
        .blackbox_container .blackbox_section .link {
            min-width: unset;
            line-height: unset;
            height: unset;
            font-weight: 100;
            border-radius: 4px;
            font-size: 32px;
            padding: 2% 8%;
            margin-top: 7%;
            margin-bottom: 40px;
        }

    }

</style>

<div class="blackbox_container">
    <div class="blackbox_section">
        <!--CONTAINER start-->
        <div class="container-fluid">
            <h2>Vertical Raise Business Fundraisers</h2>
            <div class="row funds_container">

                <?php if ( have_rows('fundraisers') ): ?>

                    <?php while ( have_rows('fundraisers') ): the_row();

                        // vars
                        $image = get_sub_field('image');
                        $url = get_sub_field('url');
                        $title = get_sub_field('title');
                        $goal = get_sub_field('goal');
                        $raised = get_sub_field('raised');

                        ?>

                        <div class="col-sm-4 col-xs-12">
                            <div class="fund_container">
                                <img alt="band fundraising" class="fund_logo" src="<?= $image; ?>" />
                            </div>
                            <div class="bf_text_section">
                                <h3><?= $title; ?></h3>
                                <img alt="band fundraisers" class="bf_progress" src="<?php echo get_template_directory_uri(); ?>/assets/images/bf_bar_progress.png" />
                                <h4>$<?= number_format($raised); ?></h4>
                                <p>of $<?= number_format($goal); ?> goal</p>
                            </div>
                        </div>

                    <?php endwhile; ?>

                <?php endif; ?>

            </div>
        </div>
        <!--CONTAINER end-->
    </div>
</div>
<?php endif;