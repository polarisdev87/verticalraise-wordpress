<?php /* Template Name: About */ ?>
<?php get_header(); ?>
<div id="about">
	<div class="maincontent">
	    <div class="section group">
	        <div class="col span_12_of_12"> 
                <div class="container">
                    <?php while (have_posts()) : the_post(); ?>
                    <div><?php the_content(); ?></div>
                    <?php endwhile; ?>  
                </div>                         
	        </div>
	    </div>
	</div>
</div>
<script>
    function faqFill(faqli, content) {
        jQuery('.faqContent').html(content);
        jQuery('#faq ul li a').removeClass('active');
        jQuery('.'+faqli).addClass('active');
    }
</script>
<div id="faq">
    <div class="faq_title"><h1>Frequently Asked Questions</h1></div>
    <div class="maincontent noPadding">
        <div class="section group mobile_hide">
            <div class="col span_6_of_12 matchheight noMargin">
                <ul>
                    <li><a class="faqli1" onclick="faqFill('faqli1', '<p>It’s simple. Participants will follow our process step-by-step. By collecting emails and then following the steps of our invite wizard, participants will launch our mobile friendly campaign within 15 minutes. After that our team and platform will do the rest.</p>')" href="javascript: void(0);">How does it work?</a></li>
                    <li><a class="faqli2" onclick="faqFill('faqli2', '<p>It takes 15-20 minutes to setup a fundraiser and launch the campaign</p>')" href="javascript: void(0);">How long does it take to setup?</a></li>
                    <li><a class="faqli3" onclick="faqFill('faqli3', '<p>With participation, our system will average $350+ per participant. The total amount raised will depend upon the size of the group. The larger the group, the more you will raise.</p>')" href="javascript: void(0);">How much can we expect to raise?</a></li>
                    <li><a class="faqli4" onclick="faqFill('faqli4', '<p>There are no up-front fees or built-in elevators associated with launching a fundraiser. We do however, take a small percentage of funds raised to cover the costs associated with launching, programming and maintaining your fundraiser.</p>')" href="javascript: void(0);">How much does it cost?</a></li>
                    <li><a class="faqli5" onclick="faqFill('faqli5', '<p>Our site is protected by 256-bit Secure Sockets Layer encryption. You can be certain that all information is secure and protected. </p>')" href="javascript: void(0);">How do I know my information & payments are secure?</a></li>
                    <li><a class="faqli6" onclick="faqFill('faqli6', '<p>Yes, if the donation is made to a non-profit 501(c)3. Each donor will receive a tax deductible receipt by email after donating. Please consult your tax professional for clarification.</p>')" href="javascript: void(0);">Are my donations tax deductible?</a></li>
                    <li><a class="faqli7" onclick="faqFill('faqli7', '<p>We know that bills add up and we are dedicated to distributing your funds via check/direct deposit as soon as possible. Usually within 5-10 business days.</p>')" href="javascript: void(0);">How will I receive the funds?</a></li>
                    <li><a class="faqli8" onclick="faqFill('faqli8', '<p>Our system has the highest average donation and some of the lowest fees. That means, your team will raise and keep more. </p>')" href="javascript: void(0);">Why WeFund4u?</a></li>
                    <li><a class="faqli9" onclick="faqFill('faqli9', '<p>Please don’t hesitate to contact us via email, or a simple phone call. Our customer service team is dedicated to making sure your questions are answered and you have all the pertinent information.</p>')" href="javascript: void(0);">More Questions?</a></li>
                </ul>
            </div>
            <div class="col span_6_of_12 matchheight noMargin">
                <div class="faqContent">
                    <p>It’s simple. Participants will follow our process step-by-step. By collecting emails and then following the steps of our invite wizard, participants will launch our mobile friendly campaign within 15 minutes. After that our team and platform will do the rest.</p>
                </div>
            </div>
        </div>
        <div class="section group mobile_display">
            <div class="col span_12_of_12">
                <ul>
                    <li><a class="faqli1" onclick="faqFill1('faqli1', '<p>It’s simple. Participants will follow our process step-by-step. By collecting emails and then following the steps of our invite wizard, participants will launch our mobile friendly campaign within 15 minutes. After that our team and platform will do the rest.</p>')" href="javascript: void(0);">How does it work?</a></li>
                    <div class="faqContent faqli1"></div>
                    <li><a class="faqli2" onclick="faqFill1('faqli2', '<p>It takes 15-20 minutes to setup a fundraiser and launch the campaign</p>')" href="javascript: void(0);">How long does it take to setup?</a></li>
                    <div class="faqContent faqli2"></div>
                    <li><a class="faqli3" onclick="faqFill1('faqli3', '<p>With participation, our system will average $350+ per participant. The total amount raised will depend upon the size of the group. The larger the group, the more you will raise.</p>')" href="javascript: void(0);">How much can we expect to raise?</a></li>
                    <div class="faqContent faqli3"></div>
                    <li><a class="faqli4" onclick="faqFill1('faqli4', '<p>There are no up-front fees or built-in elevators associated with launching a fundraiser. We do however, take a small percentage of funds raised to cover the costs associated with launching, programming and maintaining your fundraiser.</p>')" href="javascript: void(0);">How much does it cost?</a></li>
                    <div class="faqContent faqli4"></div>
                    <li><a class="faqli5" onclick="faqFill1('faqli5', '<p>Our site is protected by 256-bit Secure Sockets Layer encryption. You can be certain that all information is secure and protected. </p>')" href="javascript: void(0);">How do I know my information & payments are secure?</a></li>
                    <div class="faqContent faqli5"></div>
                    <li><a class="faqli6" onclick="faqFill1('faqli6', '<p>Yes, if the donation is made to a non-profit 501(c)3. Each donor will receive a tax deductible receipt by email after donating. Please consult your tax professional for clarification.</p>')" href="javascript: void(0);">Are my donations tax deductible?</a></li>
                    <div class="faqContent faqli6"></div>
                    <li><a class="faqli7" onclick="faqFill1('faqli7', '<p>We know that bills add up and we are dedicated to distributing your funds via check/direct deposit as soon as possible. Usually within 5-10 business days.</p>')" href="javascript: void(0);">How will I receive the funds?</a></li>
                    <div class="faqContent faqli7"></div>
                    <li><a class="faqli8" onclick="faqFill1('faqli8', '<p>Our system has the highest average donation and some of the lowest fees. That means, your team will raise and keep more. </p>')" href="javascript: void(0);">Why WeFund4u?</a></li>
                    <div class="faqContent faqli8"></div>
                    <li><a class="faqli9" onclick="faqFill1('faqli9', '<p>Please don’t hesitate to contact us via email, or a simple phone call. Our customer service team is dedicated to making sure your questions are answered and you have all the pertinent information.</p>')" href="javascript: void(0);">More Questions?</a></li>
                    <div class="faqContent faqli9"></div>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    function faqFill1(faqli, content) {
        jQuery('.faqContent').slideUp();
        jQuery('.faqContent.'+faqli).html(content);
        jQuery('.faqContent.'+faqli).slideDown();
        //jQuery('.faqContent').html(content);
        jQuery('#faq ul li a').removeClass('active');
        jQuery('.'+faqli).addClass('active');
    }
</script>
<!--<div id="bio">
	<div class="maincontent">
	    <div class="section group">
	        <div class="col span_12_of_12">
                <?php /*if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Bio Title') ) : */?> <?php /*endif; */?>
	        </div>
	    </div>
	    <div class="section group">
            <?php /*if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Bio') ) : */?> <?php /*endif; */?>
	    </div>
	</div>
</div>-->
<!--<div id="home_row2">
	<div class="maincontent">
	    <div class="section group">
	        <div class="col span_12_of_12">
                <?php /*if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Home Row 2 Title') ) : */?> <?php /*endif; */?>
	        </div>
	    </div>
	    <div class="section group how_img">
	        <?php /*if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Home Row 2 Images') ) : */?> <?php /*endif; */?>
	    </div>
	    <div class="section group">
	        <div class="col span_12_of_12">
                <?php /*if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Home Row 2 Lists') ) : */?> <?php /*endif; */?>
	        </div>
	    </div>
	    <div class="section group how_img">
	        <div class="col span_12_of_12">  
                <?php /*if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Home Row 2 Button') ) : */?> <?php /*endif; */?>
	        </div>
	    </div>
	</div>
</div>-->
<?php get_footer(); ?>