<!-- Participant Landing Page Comments -->
<?php 

// Sidebar
use \classes\app\sidebar\Sidebar;
$sidebar = new Sidebar();

if ( $supporters_total > 0 ) { ?>
    <div class="widgets supporters_comments">
        <h3>Thank you to our supporters!</h3>
        <ul class="supporters_list">
            <?php
            $n = 0;
            foreach ( $supporters as $supporter ) {
                $n++;
                
                // Donation date
                $donation_date = $sidebar->donation_date($supporter['time']);
                
                // Days ago
                $days_ago = $sidebar->days_ago($donation_date);
                
                // Donation amount
                $donation_amount = $sidebar->format_donation_amount($supporter['amount']);
                
                // Donator name
                $donator_name = $sidebar->donator_name($supporter['name'], $supporter['anonymous']);
               
                $default_avatar   = (is_mobile_new()) ? get_template_directory_uri() . "/assets/images/small-user-avatar.png" : get_template_directory_uri() . "/assets/images/user-avatar.png";
                $supporter_avatar = (!isset( $comments[$supporter['id']] ) || $comments[$supporter['id']]['avatar_url'] == 'default' ) ? $default_avatar : $comments[$supporter['id']]['avatar_url'];
                
                ?>
                <li class="<?php echo ( $n > 3 && is_mobile() ) ? 'hideClass' : '' ?>">
                    <div class="user">
                        <div class="img" style="background-color: black;"><img src="<?php echo $supporter_avatar; ?>"></div>
                        <div class="detail">
                            <h5><?php echo $donation_amount; ?></h5>
                            <b><?php echo $donator_name; ?></b>
                            <h6><?php echo $days_ago; ?></h6>
                        </div>
                    </div>
                    <div class="like">
                        <img src="<?php bloginfo( 'template_directory' ); ?>/assets/images/like.png" alt="">
                    </div>

                    <?php if ( isset( $comments[$supporter['id']] ) && !empty( $comments[$supporter['id']] ) ) { ?>

                        <p class="comment_text">
                            &ldquo;<?php echo str_replace( "\\", "", $comments[$supporter['id']]['comment'] ); ?>&rdquo;
                        </p>

                    <?php } ?>
                </li>
            <?php } ?>
            <?php if ( is_mobile() && $n > 3 ) { ?>
                <li class="extraBtn">
                    <a class="morelist">
                        <b>+</b> Show More
                    </a>
                    <div class="donation-count-view">
                        Viewing <span class="js-donation-count">3</span> of <?php echo count( $supporters ) ?> Donations
                    </div>
                </li>
            <?php } ?>

        </ul>
    </div>
<?php } else { ?>
    <div class="widgets supporters_comments">
        <h3>SUPPORTERS 0</h3>
    </div>
<?php }