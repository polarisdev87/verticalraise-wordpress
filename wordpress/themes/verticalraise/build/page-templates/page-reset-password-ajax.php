<?php /* Template Name: Reset Password Ajax Template */

global $user_ID;

if ( isset($_POST['reset_password']) && isset($_POST['key'])) {
    $key = decripted($_POST['key']);
    $user = get_user_by('email', $key);
    $pwd = $_POST['pwd1'];
    //$user_data = $wpdb->get_row($wpdb->prepare("SELECT ID  FROM $wpdb->users WHERE user_email = %s", $key));
    wp_set_password($pwd, $user -> ID);
//    header("Location: " . get_bloginfo('home') . "/reset-password/?success=1");

    $result['status']=true;
    $result['data']= '<div class="successMsg">Your password updated successfully. Please Login.</div>';


} else {
    $result['status']=false;
    $result['data']= '<div class="errorMsg">Valid action key is not exist. Please check your email again</div>';

}

die(json_encode($result));
exit();
