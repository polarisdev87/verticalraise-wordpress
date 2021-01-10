<?php

ini_set('memory_limit', '-1');

use \classes\app\admin_reports\Fundraisers_Started;
use classes\models\tables\Reports_Fundraisers_Reference;


if (isset($_GET['migrate']) && !empty(isset($_GET['migrate'])) ) {
    
    $reference = new Reports_Fundraisers_Reference;
    $args = array(
        'post_type'      => 'fundraiser',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'order' => 'DESC',
        'orderby' => 'date',
        'offset' => $_GET['offset'],
        'posts_per_page' => '1000'
    );

    $fundraiser_query = new WP_Query( $args );

    while ( $fundraiser_query->have_posts() ) : $fundraiser_query->the_post();

        $f_id   = get_the_ID();
        $f_name = get_the_title();
        $f_created_date = get_the_date();

        $f_start_date = get_post_meta( $f_id, 'start_date', true );
        $f_end_date   = get_post_meta( $f_id, 'end_date', true );

        $f_start_date = date("Y-m-d H:i:s", strtotime( $f_start_date, current_time( 'timestamp' ) ) );
        $f_end_date = date("Y-m-d H:i:s", strtotime( $f_end_date, current_time( 'timestamp' ) ) );

        $reference->migrate_insert($f_id, $f_name, $f_created_date, $f_start_date, $f_end_date);


       endwhile;
        wp_reset_postdata();

}

/**
 * Page to see Reports of Fundraiser_started
 */

?>

<h2>Fundraisers</h2>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.8.21/themes/base/jquery-ui.css" />
<!--<link rel="stylesheet" href="https://cdn.datatables.net/responsive/1.0.4/css/dataTables.responsive.css" />-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.bootstrap.min.css" />


<style>
    .right-td {
        float:right;
    }
    .left-td {
        float: left;
    }
    .center-td {
        text-align: center
    }
    .range {
        margin-bottom: 5px;
    }
    .dateRangeArea {
        display: none
    }

    #fundraisersTable_wrapper{
        margin-right: 25px; 
        /*overflow-x: scroll;*/
    }

    #fundraisersTable {
        position: relative
    }

    #fundraisersTable_wrapper .dataTables_scroll {
        max-height: 600px;
        overflow-x: auto;
        position: relative;

    }
    #fundraisersTable_wrapper .dataTables_scroll .dataTables_scrollHead{
        overflow: hidden !important;
        position: absolute !important;
        border: 0px !important;
        width: auto !important;
        z-index: 1 !important;
        background: gainsboro;
    }
    #fundraisersTable_wrapper .dataTables_scroll .dataTables_scrollBody{
        overflow: initial !important;
    }
    table.dataTable {
        width:100% !important;
        margin: 0 !important;
    }
    table.dataTable tr.child ul li{
        display:flex;
    }

    .buttons-excel {
        display: inline-block;
        text-decoration: none;
        font-size: 13px;
        line-height: 26px;
        height: 28px;
        margin: 0;
        padding: 0 10px 1px;
        cursor: pointer;
        border-width: 1px;
        border-style: solid;
        -webkit-appearance: none;
        border-radius: 3px;
        white-space: nowrap;
        box-sizing: border-box;
        background: #0085ba;
        border-color: #0073aa #006799 #006799;
        box-shadow: 0 1px 0 #006799;
        color: #fff;
        margin-bottom: 5px;
    }
    .buttons-excel:hover {
        background: #008ec2;
        border-color: #006799;
        color: #fff;
        box-shadow: 0 1px 0 #0073aa,0 0 2px 1px #33b3db;
    }

</style>
<h4>Select Date Range</h4>
<form id="submitForm" method="post" >
    <?php $range_type  = (isset($_POST['date_range']) && $_POST['date_range'] != '') ? $_POST['date_range'] : ''; ?>
    <select class="range" id="range_dropdown" name="date_range"> 
        <option <?php if ( $range_type == '' ) {
            echo ' selected="selected"';
        } ?>>-- Select date type</option>
        <option value="this_week" <?php
        if ( $range_type == 'this_week' ) {
            echo ' selected="selected"';
        }
        ?>>This Week</option>
        <option value="last_week" <?php
        if ( $range_type == 'last_week' ) {
            echo ' selected="selected"';
        }
        ?>>Last Week</option>
        <option value="this_month" <?php
        if ( $range_type == 'this_month' ) {
            echo ' selected="selected"';
        }
        ?>>This Month</option>
        <option value="last_month" <?php
        if ( $range_type == 'last_month' ) {
            echo ' selected="selected"';
        }
        ?>>Last Month</option>
        <option value="today" <?php
        if ( $range_type == 'today' ) {
            echo ' selected="selected"';
        }
        ?>>Today</option>
        <option value="yesterday" <?php
        if ( $range_type == 'yesterday' ) {
            echo ' selected="selected"';
        }
        ?>>Yesterday</option>
        <option value="pick_date" <?php
        if ( $range_type == 'pick_date' ) {
            echo ' selected="selected"';
        }
        ?>>Select Range</option>
    </select>

    <?php
    $check_date_start = '';
    $check_date_end   = '';
    if ( isset($_POST['check_date_start']) ) {
        $check_date_start = $_POST['check_date_start'];
    }

    if ( isset($_POST['check_date_end']) ) {
        $check_date_end = $_POST['check_date_end'];
    }
    ?>
    <div class="dateRangeArea" style="<?php echo ($range_type == 'pick_date') ? 'display:block !important' : 'display:none !important' ?>">
        <input type="text" name="check_date_start" id="start_date_picker"
               value="<?php echo $check_date_start ?>"  
               placeholder="m/d/Y" autocomplete="off" >
        <span id="slash"> - </span>
        <input type="text" name="check_date_end" id="end_date_picker"
               value="<?php echo $check_date_end ?>"  
               placeholder="m/d/Y" autocomplete="off" >
        <button id="rangeSubmit" class="button button-primary" >Submit</button>

    </div>
    <input type="hidden" name="range_picker">
</form>
<script src ="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src ="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<!--<script src ="https://cdn.datatables.net/responsive/1.0.4/js/dataTables.responsive.js"></script>-->
<script src ="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src ="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src ="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>

<script>
    jQuery(document).ready(function ($) {

        $("#range_dropdown").change(function () {
            if ($(this).val() == 'pick_date') {
                $(".dateRangeArea").show();
            } else {
                $(".dateRangeArea").hide();
                $("#start_date_picker").val('');
                $("#end_date_picker").val('');
                $("#submitForm").submit();
            }
        })

        $("#start_date_picker").datepicker({
            changeMonth: true
        });
        $("#end_date_picker").datepicker({
            changeMonth: true
        });

//        $("#ended_date_picker").change(function () {
//            $("#submitForm").submit();
//        })

        $("#rangeSubmit").click(function (e) {
            e.preventDefault();
            var start = $("#start_date_picker").val();
            var end = $("#end_date_picker").val();
            if (start == '' || end == '') {
                alert("Please select date range");
            } else {
                var a = new Date(start);
                var b = new Date(end);
                if (a > b) {
                    alert("Last date should greater than first date.");
                    return false
                }

                $("#submitForm").submit();
            }
        })
        var table = $('#fundraisersTable').DataTable({
            "bPageinate": true,
            "searching": false,
            "bFilter": false,
            "bInfo": false,
            "responsive": true,
            "lengthChange": true,
            "pageLength": 100,
            "order": [[1, "desc"]],
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export Excel Spreadsheet',
                    title: 'Fundraisers'
                }],
            fixedHeader: {
                header: true,
                headerOffset: 45
            },
            scrollX: true
        });

        $(".dataTables_scrollBody").css('padding-top', $(".dataTables_scrollHead").height());
        $(".dataTables_scroll").scroll(function () {
            $(".dataTables_scrollHead").css("top", $(this).scrollTop())
        })

    });
</script>

<hr>

<?php if (isset($_POST['date_range']) && !empty($_POST['date_range'])) { 
    $fundraisers = new Fundraisers_Started();
    $result      = $fundraisers->get_fundraisers();
?>

<p>Current datetime: <?php echo $fundraisers->get_current_time_formatted() ?></p>

<h3>Results</h3>

<!-- Show Database Custom Table Info -->
<table cellpadding="4" cellspacing="0" border="1" class="dataTable" id="fundraisersTable">
    <thead>
        <tr>
            <th class="center-td"><strong>Fundraiser name</strong></th>
            <th class="center-td"><strong>Created Date</strong></th>
            <th class="center-td"><strong>Start Date</strong></th>
            <th class="center-td"><strong>End Date</strong></th>
            <th class="center-td"><strong>Secondary End Date</strong></th>
            <th class="center-td"><strong>Primary Contact Name</strong></th>
            <th class="center-td"><strong>Primary Contact #</strong></th>
            <th class="center-td"><strong>Primary Contact email</strong></th>
            <th class="center-td"><strong>Organization Type</strong></th>
            <th class="center-td"><strong>How Did You Hear About Us</strong></th>
            <th class="center-td"><strong>Fundraising Rep Name</strong></th>
            <th class="center-td"><strong>Fundraising Rep Email</strong></th>
            <th class="center-td"><strong>Fundraising Rep Code</strong></th>
            <th class="center-td"><strong>Check Payable To</strong></th>
            <th class="center-td"><strong>Mailing Address Name</strong></th>
            <th class="center-td"><strong>Street Address</strong></th>
            <th class="center-td"><strong>City</strong></th>
            <th class="center-td"><strong>State</strong></th>
            <th class="center-td"><strong>Zip</strong></th>
            <th class="center-td"><strong># Participants</strong></th>
            <th class="center-td"><strong>Total Supporters</strong></th>
            <th class="center-td"><strong>Total Raised</strong></th>
            <th class="center-td"><strong>Number Of Emails Sent</strong></th>
            <th class="center-td"><strong>SMS Donations</strong></th>
            <th class="center-td"><strong># of Parent Shares</strong></th>
            <th class="center-td"><strong>Participation Score</strong></th>
            <th class="center-td"><strong>Email Quality Score</strong></th>
            <th class="center-td"><strong>Participants With At Least 1 Donation</strong></th>
<!--            <th class="center-td"><strong>Zip</strong></th>
            <th class="center-td"><strong>Zip</strong></th>-->
        </tr>
    </thead>
    <tbody>
        <?php
        if ( !empty($result) ) {
            foreach ( $result as $item ) {
                ?>

                <tr f_id="<?php echo $item['f_id'] ?>">
                    <td class=""><a href="<?php echo $item['fundraiser_link'] ?>" target="_blank"><?php echo $item['name'] ?></a></td>
                    <td ><span class="right-td"><?php echo $item['created_date'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['start_date'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['end_date'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['secondary_end_date'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['con_name'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['con_number'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['con_email'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['org_type'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['hear_about_us'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['rep_name'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['rep_email'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['rep_code'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['check_pay'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['mailing_address'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['street'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['city'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['state'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['zipcode'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['participants_count'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['total_supporters'] ?></span></td>
                    <td ><span class="right-td">$<?php echo $item['total_raised'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['emails_sent_num'] ?></span></td>
                    <td ><span class="right-td">$<?php echo $item['sms_donation'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['parents_share'] ?></span></td>
                    <td ><span class="right-td"><?php echo $item['participation_score'] ?>%</span></td>
                    <td ><span class="right-td"><?php echo $item['email_quality_score'] ?>%</span></td>
                    <td ><span class="right-td"><?php echo $item['participant_donation'] ?>%</span></td>

                    <!--                    <td ><span class="left-td"><?php // echo $item['author']          ?></span></td>
                    <td ><span class="right-td"><?php // echo $item['participants_count']          ?></span></td>
                    <td ><span class="right-td">$<?php // echo $item['goal_amount']          ?></span></td>
                    <td ><span class="right-td">$<?php // echo $item['raised_amount']          ?></span></td>-->

                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td class='center-td'>No data</td>
                <?php for ( $i = 0; $i < 23; $i++ ) : ?>
                    <td class='center-td'></td>                
                <?php endfor; ?>
            </tr>
        <?php } ?>
    </tbody>

</table>
<!-- /Show Database Custom Table Info -->
<?php } else { ?>

<h3>Select report criteria</h3>

<?php } ?>