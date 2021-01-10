<h2>Flyers</h2>
<?php
    if(!empty($_POST['update'])){
        $flyers = $_POST['flyers'];
        update_option('flyers', stripslashes(wpautop($flyers)));
    }
?>
<form method="POST" action="">
    <?php    
        $flyers = get_option('flyers');
        if(!empty($flyers)) {
            $content = $flyers;
        } else {
            $content = '';
        }
        
        $editor_id = 'flyers';
        
        wp_editor( stripslashes($content), $editor_id );
    ?>
    <p><input type="submit" name="update" value="Update" class="button button-primary button-large" /></p>
</form>