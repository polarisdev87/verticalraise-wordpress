<?php /* Template Name: Facebook Update Cron */ ?>
<?php
require_once TEMPLATEPATH.'/facebook-php-sdk-v4-5.0.0/vendor/autoload.php';

$fbData = array(
    'app_id' => '1567989536863012',
    'app_secret' => '7bfa46a00782b3624d38e0c91fc459dc',
    'default_graph_version' => 'v2.2'
);

$fb = new Facebook\Facebook($fbData);

$args = array(
    'post_type' => 'fundraiser',
    'post_status' => 'publish',
    'posts_per_page' => -1
);
$fundraiser_query = new WP_Query($args);
while ($fundraiser_query->have_posts()) : $fundraiser_query->the_post();

    $facebook_token = get_post_meta(get_the_ID(), 'facebook_token', true);
    
    if(!empty($facebook_token)){
        
        $facebook_token_array = json_decode($facebook_token);
        foreach($facebook_token_array->user_array as $u) {
    
            $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full' ); 
            $title = get_the_title(get_the_ID());
            $targetUrl = get_the_permalink(get_the_ID());
            $imgUrl = $thumb[0];
            $description = get_post_meta(get_the_ID(), 'campaign_msg', true);

            $start_date = get_post_meta(get_the_ID(), 'start_date', true);
            $start_date = strtotime($start_date);
            $end_date = get_post_meta(get_the_ID(), 'end_date', true);
            $end_date = strtotime($end_date);
            if($_GET['period'] == 5) {
                $current_date = strtotime(date("Ymd", strtotime("-5 days")));
                if($start_date == $current_date) {
                    $description = "Hi everyone, I am 5 days into my ".$title.". Please Help me reach my individual goal of raising $350 by donating whatever you can and sharing. Thank you!";

                    $params["link"] = $targetUrl;
                    $params["message"] = $description;
                    //$params["picture"] = $imgUrl;
                    //$params["description"] = $description;

                    $access_token = trim($u->access_token);

                    try {
                        $response = $fb->post('/me/feed', $params, $access_token);
                    } catch(Facebook\Exceptions\FacebookResponseException $e) {
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                    } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                    }

                    $graphNode = $response->getGraphNode();

                    echo 'Posted with id: ' . $graphNode['id'].'<br />';
                }
            }
            if($_GET['period'] == 7) {
                $current_date = strtotime(date("Ymd", strtotime("+1 week")));
                if($end_date == $current_date) {
                    $description = "Hi everyone, there is only one week remaining for my ".$title.". Please Help me reach my individual goal of raising $350 by donating whatever you can and sharing. Thank you!. 
                    Thank you!";

                    $params["link"] = $targetUrl;
                    $params["message"] = $description;
                    //$params["picture"] = $imgUrl;
                    //$params["description"] = $description;

                    $access_token = trim($u->access_token);

                    try {
                        $response = $fb->post('/me/feed', $params, $access_token);
                    } catch(Facebook\Exceptions\FacebookResponseException $e) {
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                    } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                    }

                    $graphNode = $response->getGraphNode();

                    echo 'Posted with id: ' . $graphNode['id'].'<br />';
                }
            }
            if($_GET['period'] == 2) {
                $current_date = strtotime(date("Ymd", strtotime("+2 days")));
                if($end_date == $current_date) {
                    $description = "Hi everyone, there is only one day remaining for my ".$title.". Please Help me reach my individual goal of raising $350 by donating whatever you can and sharing. Thank you!";

                    $params["link"] = $targetUrl;
                    $params["message"] = $description;
                    //$params["picture"] = $imgUrl;
                    //$params["description"] = $description;

                    $access_token = trim($u->access_token);

                    try {
                        $response = $fb->post('/me/feed', $params, $access_token);
                    } catch(Facebook\Exceptions\FacebookResponseException $e) {
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                    } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                    }

                    $graphNode = $response->getGraphNode();

                    echo 'Posted with id: ' . $graphNode['id'].'<br />';
                }
            }

        }
        
    }
endwhile;
?>