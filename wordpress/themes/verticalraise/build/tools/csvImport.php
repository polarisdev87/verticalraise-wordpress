<?php
/* Template Name: CSV Import */
function readCSV($csvFile){
    $file_handle = fopen($csvFile, 'r');
    while (!feof($file_handle) ) {
        $line_of_text[] = fgetcsv($file_handle, 1024);
    }
    fclose($file_handle);
    return $line_of_text;
}
$post_id = $_GET['post_id'];
echo $json = get_post_meta($post_id, 'potential_donors_array', true);
?>
    <h1>Import CSV</h1>
    <form method="POST" action="" enctype="multipart/form-data">
        Choose a file from your computer: (Maximum size: 32 MB) <input type="file" name="csv">
        <br/>
        <input type="submit" name="csv_upload" class="button button-primary button-large">
    </form>
<?php
if(isset($_POST['csv_upload'])) {
    $file1 = $_FILES['csv'];
    if (($file1["size"] < 4000000)){
        $date = date('Ymd');
        $time = time();
        $file_name = preg_replace("/[\s]+/", "", $file1['name']);
        $filename = $date.'_'.$time.'_'.($file_name);
        $upload_dir = wp_upload_dir();
        $uploaddir = $upload_dir['basedir'];
        $file = $uploaddir . $date.'_'.$time.'_'.($file_name);
        if (move_uploaded_file($file1['tmp_name'], $file)) {
            $csv = readCSV($file);
            //print_r($csv);
            $i = 0;
            foreach($csv as $row) {
                if($i != 0) {
                    if (!empty($row)) {
                        $user = explode('<', $row[0]);
                        //echo $user[1].'<br/>';
                        $email = explode('>', $user[1]);
                        //echo $email[0].'<br/>';
                        $user = get_user_by( 'email', $email[0] );
                        $mail_details = array(
                            'to' => $row[1],
                            'from' => '<' . $user->display_name . '>' . $user->user_email,
                            'subject' => get_the_title($post_id),
                            /*'htmltext' => addslashes(htmlspecialchars($msg)),
                            'plaintext' => $message,*/
                            'datetime' => date('Y-m-d:H:i:s')
                        );
                        /*print_r($mail_details);
                        echo '<br/>';*/
                        $potential_donors_array = json_decode(get_post_meta($post_id, 'potential_donors_array', true));
                        if (!empty($potential_donors_array)) {
                            array_push($potential_donors_array, array($user->ID, $row[1], $mail_details));
                        } else {
                            $potential_donors_array = array();
                            array_push($potential_donors_array, array($user->ID, $row[1], $mail_details));
                        }
                        update_post_meta($post_id, 'potential_donors_array', json_encode($potential_donors_array));
                    }
                }
                $i++;
            }
        }
    }
    echo '<p>'.$i.' items imported successfully</p>';
}
?>