<?php

/**
 * Flows:
 * - Logged in - Invite Wizard
 * - Not logged in - Parent Invite Wizard
 * - Not logged in - Spread the Word
 *
 * If logged in, the done buttons close the modal
 */ 

$parent_flow = ['invite-parent-start', 'invite-by-email', 'invite-by-text-message-sms', 'share-on-facebook', 'parent-did-you-know'];
$invite_flow = ['invite-start', 'parent', 'invite-by-email', 'invite-by-text-message-sms', 'share-on-facebook'];
$spread_flow = ['invite-start', 'share-on-twitter', 'invite-by-text-message-sms', 'invite-by-email'];

// <a href="javascript:void(0);" onclick="javascript:parent.jQuery.fancybox.close();">


// Decide the flow
if ( isset($_GET['parent']) && $_GET['parent'] == 1 ) {
    $flow = $parent_flow;
    $last_page = 'parent-did-you-know';
} else if ( isset($_GET['display_type']) && $_GET['display_type'] == 'single' ) {
    $flow = $spread_flow;
    $last_page = 'invite-by-email';
} else {
    $flow = $invite_flow;
    $last_page = 'share-on-facebook';
}

// Get current page
global $post;
$post_slug = $post -> post_name;

// Get current, next and previous pages
$current = array_search($post_slug, $flow);

$next = $current + 1;
$prev = $current - 1;

$button_class = "";
if ( $current == 0 ) {
    $button_class = "full_btn";
} else {
    $button_class = "half_btn";
}

// Fundraiser redirects

// When to use the 'done' js close links
$count = 0;
if ( isset($_GET['fundraiser_id']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "fundraiser_id={$_GET['fundraiser_id']}";
}
if ( isset($_GET['uid']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "uid={$_GET['uid']}";
}
if ( isset($_GET['display_type']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "display_type={$_GET['display_type']}";
}

if ( isset($_GET['page']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "page={$_GET['page']}";
}

if ( isset($_GET['type']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "type={$_GET['type']}";
}
if ( isset($_GET['parent']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "parent=1";
}

if ( isset($_GET['action']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "action={$_GET['action']}";
}
// additional parameter in Thank you page popup
if ( isset($_GET['fname']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "fname={$_GET['fname']}";
}

if ( isset($_GET['lname']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "lname={$_GET['lname']}";
}
if ( isset($_GET['email']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "email={$_GET['email']}";
}
if ( isset($_GET['media']) ) {
    $count++;
    if ( $count == 1 ) {
        $append = '?';
    } else {
        $append .= '&';
    }
    $append .= "media={$_GET['media']}";
}
// Determine urls

$base = get_site_url();

$on_click = "";
$on_click2 = "";


if ( $prev == -1 ) {
    if ( is_mobile_new() ) {
        if ( is_user_logged_in() ) {
            // Send the person to the participant page since they are logged in
            $prev_url = "{$base}/participant-fundraiser/?fundraiser_id={$_GET['fundraiser_id']}";
        } else {
            // Otherwise, send them to the fundraiser page
            $prev_url = get_permalink($_GET['fundraiser_id']) . 'sms/' . $_GET['uid'];
        }
    } else {
        $prev_url = "javascript:void(0)";
        $on_click = ' onclick="javascript:parent.$.fancybox.close();" ';
    }
} else {
    $prev_url = "{$base}/{$flow[$prev]}/{$append}";
}

if ( $post_slug == $last_page ) {
    if ( is_mobile_new() ) {

        if ( isset($_GET['display_type']) && $_GET['display_type'] == 'single' ) {
            //thank you page popup

            $next_url = "/thank-you-payment" . $append ."&spreadClose=1";

        } else {
//        participant and admin popup
            if ( isset($_GET['type']) && $_GET['type'] == 'participant' ) {

                $next_url = "/participant-fundraiser" . $append;

            } elseif ( isset($_GET['type']) && $_GET['type'] == 'admin' ) {

                $next_url = "/single-fundraiser" . $append;

            } else if (isset($_GET['type']) && $_GET['type'] == 'permalink') {
                $next_url = get_permalink($_GET['fundraiser_id']);
            }
        }

        if ( isset($_GET['parent']) && $_GET['parent'] == '1' ) {

            $next_url = get_permalink($_GET['fundraiser_id']) . 'sms/' . $_GET['uid'];
        }


    } else {
        $next_url = "javascript:void(0)";
        $on_click2 = ' onclick="javascript:parent.$.fancybox.close();" ';
    }

    //$next_button = "/assets/images/invite_done.png";

} else {
    $next_url = "{$base}/{$flow[$next]}/{$append}";

}


?>

<!--//navigation button-->


<!--    <a href="#" class="prev" data-toggle="modal" data-target="#tnx_b">← Back (facebook)</a>-->
<!--    <a href="#" class="next" data-toggle="modal" data-target="#tnx_d">Next (twitter) →</a>-->

<?php if ( isset($_GET['display_type']) && $_GET['display_type'] == 'single' ) { ?>
    <div class="pop_nav next_prev_buttons clearfix">

        <?php if ( $current != 0 ) { ?>
            <a class="prev " href="<?php echo $prev_url; ?>" <?php echo $on_click; ?>>
                ← Back
            </a>
            <a class="next" href="<?php echo $next_url; ?>" <?php echo $on_click2; ?>>
                Next →
            </a>
        <?php } else { ?>
            <a class="next" href="<?php echo $next_url; ?>" <?php echo $on_click2; ?>>
                Next →
            </a>
        <?php } ?>


    </div>
<?php } else { ?>
    <div class="next_prev_buttons">
        <?php if ( $current != 0 ) { ?>
            <a class="<?php echo $button_class; ?>" href="<?php echo $prev_url; ?>"<?php echo $on_click; ?>>
                <input type="submit" value="← Back" class="submit_btn btn back" />
            </a>
        <?php } ?>

        <a class="<?php echo $button_class; ?>" href="<?php echo $next_url; ?>"<?php echo $on_click2; ?>>
            <input type="submit" value="<?php echo ($post_slug == $last_page) ? 'Finish →' : 'Next →' ?>"
                   class="submit_btn btn <?php echo ($current == 0) ? '' : 'next'; ?>" />
        </a>
    </div>

<?php } 