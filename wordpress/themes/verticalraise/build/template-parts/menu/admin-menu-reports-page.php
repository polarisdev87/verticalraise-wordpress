<link rel="stylesheet" href="//cdn.datatables.net/1.10.11/assets/css/jquery.dataTables.min.css" />
<script src="//cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
<script>
jQuery(document).ready(function(){
    jQuery('#myTable').DataTable();
});
</script>
<h1>Fundraiser List</h1>
<?php
$args = array(
    'post_type' => 'fundraiser',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'p' => $_GET['id']
);
$fundraiser_query = new WP_Query($args);
if ( $fundraiser_query->have_posts() ) :
?>
    <table id="myTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Perticipant Join Code</th>
                <th>Secondary Admin Join Code</th>
                <th>Time Period</th>
            </tr>
        </thead>
        <tbody>
<?php while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post(); ?>
        <tr>
            <td class="title"><strong><a href="<?php bloginfo('home'); ?>/wp-admin/admin.php?page=single-report&id=<?php echo get_the_ID(); ?>"><?php the_title(); ?></a></strong></td>
            <td style="text-align: center;"><?php echo get_post_meta(get_the_ID(), 'join_code', true); ?></td>
            <td style="text-align: center;"><?php echo get_post_meta(get_the_ID(), 'join_code_sadmin', true); ?></td>
            <td style="text-align: center;">
                <?php
                    $format_in = 'Ymd';
                    $format_out = 'd-m-Y';
                    $start_date = get_post_meta(get_the_ID(), 'start_date', true);
                    $start_date = DateTime::createFromFormat($format_in, $start_date);
                    $start_date = $start_date->format( $format_out );
                    $end_date = get_post_meta(get_the_ID(), 'end_date', true);
                    $end_date = DateTime::createFromFormat($format_in, $end_date);
                    $end_date = $end_date->format( $format_out );
                    echo $start_date.' - '.$end_date;
                ?>
            </td>
        </tr>
<?php endwhile; ?>
        </tbody>
    </table>    
<?php
    endif;
    wp_reset_postdata();
?>