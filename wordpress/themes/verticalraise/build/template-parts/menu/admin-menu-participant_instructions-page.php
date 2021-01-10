<h2>Participant Instructions</h2>
<?php
    if(!empty($_POST['update'])){
        $participant_instructions1 = $_POST['participant_instructions1'];
        //$participant_instructions = json_encode($participant_instructions);
        //$participant_instructions = json_decode($participant_instructions);
        update_option('participant_instructions1', stripslashes(wpautop($participant_instructions1)));
        
        $participant_instructions2 = $_POST['participant_instructions2'];
        update_option('participant_instructions2', stripslashes(wpautop($participant_instructions2)));
    }
?>
<form method="POST" action="">
    <h2>Page 1</h2>
    <?php    
        $participant_instructions1 = get_option('participant_instructions1');
        if(!empty($participant_instructions1)) {
            $content1 = $participant_instructions1;
        } else {
            $content1 = '';
        }
        
        $editor_id1 = 'participant_instructions1';
        
        wp_editor( stripslashes($content1), $editor_id1 );
    ?>
    <p></p>
    <h2>Page 2</h2>
    <?php        
        $participant_instructions2 = get_option('participant_instructions2');
        if(!empty($participant_instructions2)) {
            $content2 = $participant_instructions2;
        } else {
            $content2 = '';
        }
        
        $editor_id2 = 'participant_instructions2';
        
        wp_editor( stripslashes($content2), $editor_id2 );
    ?>
    <p><input type="submit" name="update" value="Update" class="button button-primary button-large" /></p>
</form>