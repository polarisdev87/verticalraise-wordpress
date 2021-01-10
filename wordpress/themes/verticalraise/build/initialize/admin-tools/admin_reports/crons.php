<?php

use \classes\app\admin_reports\Cron_Reports;

/**
 * Page to run Crons
 */
$cron_reports = new Cron_Reports();
$result       = $cron_reports->get_cron_data();
?>

<h2>Cron logs</h2>

<p>Current datetime: <?php echo current_time('Y-m-d H:i:s') ?></p>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.8.21/themes/base/jquery-ui.css" />
<!--<link rel="stylesheet" href="https://cdn.datatables.net/responsive/1.0.4/css/dataTables.responsive.css" />-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" />

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
    table.dataTable {
        width:100% !important;
        margin: 0 !important;
        table-layout: fixed;

    }
    table.dataTable tr.child ul li{
        display:flex;
    }
    #cron_logs_table_wrapper{
        margin-right: 25px; 
        overflow: hidden
    }

    #cron_logs_table {
        position: relative
    } 

    #cron_logs_table_wrapper .dataTables_length {
        float: none !important
    }
    #cron_logs_table_wrapper .inner-table {
        overflow-x: auto;
        max-height: 600px
    }


    .dataTables_scrollHead {
        overflow: hidden !important;
        position: absolute !important;
        border: 0px !important;
        width: auto !important;
        z-index: 1 !important;
        background: gainsboro;
    }
    .dataTables_scrollBody {
        padding-top: 42px !important;
        
    }
    .cron_item.active{
        background: #dedede;
    }
    #cron_date {
        display: none
    }
    .btn.primary {
        cursor: pointer
    }
</style>

<h4>Select Date Range</h4>
<form id="submitForm" method="post" action="/wp-admin/admin.php?page=crons">
    <?php $range_type   = (isset($_POST['date_range']) && $_POST['date_range'] != '') ? $_POST['date_range'] : ''; ?>

    <select class="range" id="range_dropdown" name="date_range"> 
        <option value="this_week" <?php
        if ( $range_type == 'this_week' || $range_type == '' ) {
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
        ?>>Select Date</option>
    </select>

    <?php
    $check_date = '';
    if ( isset($_POST['check_date']) ) {
        $check_date = $_POST['check_date'];
    }
    ?>
    <input type="text" name="check_date" id="cron_date"
           value="<?php echo $check_date ?>"  
           placeholder="m/d/Y" autocomplete="off" style="<?php echo ($range_type == 'pick_date') ? 'display:inline !important' : 'display:none !important' ?>">
    <input type="hidden" name="get_cron_log">
</form>

<script src ="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src ="https://cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
<!--<script src ="https://cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>-->

<script>
    jQuery(document).ready(function ($) {
        $("#range_dropdown").change(function () {
            if ($(this).val() == 'pick_date') {
                $("#cron_date").show();
            } else {
                $("#cron_date").hide();
                $("#cron_date").val('');
                $("#submitForm").submit();
            }
        })

        $("#cron_date").datepicker({
            changeMonth: true
        });
        $("#cron_date").change(function () {
            $("#submitForm").submit();
        })

        $('#cron_logs_table').removeAttr('width').DataTable({
            "bPageinate": false,
            "searching": false,
            "bFilter": false,
            "bInfo": false,
            "responsive": true,
            "lengthChange": true,
            "pageLength": 100,
            "order": [[2, "desc"]],
            "dom": 'l<"inner-table"t>p',
            fixedHeader: {
                header: true,
                headerOffset: 45
            },
            scrollX: true
        });

        $(".cron_item").hover(function () {
            $(this).toggleClass('active')
        })

        $(".cron_item").click(function () {
            $(".json_content").text('');
            $(".cron_data_content").text('');
            $(".modalHeader").text('');
            var $self = $(this);
            var file_name = $self.attr("file_key") + ".json";
            var cron_type = $self.attr("cron_type");
            var cron_date = $self.attr("cron_date");
            var started = $self.attr("started");
            //////////////////
//            TODO HERE 
            $.post(
                    '<?php echo admin_url('admin-ajax.php') ?>',
                    {
                        filename: file_name,
                        cron_type: cron_type,
                        cron_date: cron_date,
                        started: started,
                        action: 'get_jsondata'
                    },
                    function (data) {
                        if (data.status) {
                            $(".modalHeader").text(file_name);

                            $(".json_content").JSONView(JSON.stringify(data.json_data.jsonData));
                            $(".cron_data_content").JSONView(JSON.stringify(data.cron_data_db.dbData));
                            $(".json_content").JSONView('collapse');
                            $(".cron_data_content").JSONView('collapse');

                            insert_fund_table(data);

                            $("#jsonModal").show();
                        }
                    },
                    'json'
                    );

        })

        $(".close").click(function () {
            $("#jsonModal").hide();
        })
        
    });

    function insert_fund_table(data) {
//        console.log(data)
        jQuery(".jsontable").children().remove();
        jQuery(".dbtable").children().remove();

        var jsonTableHTML = '';
        var DbTableHTML = '';

//json table
        jsonTableHTML += '<tr>' +
                '<th>FID</th>' +
                '<th>FUNDRAISER</th>' +
                '<th>EMAILS</th>' +
                '</tr>';

        data.json_data.fundraiser_array.forEach(function (item, index) {
            jsonTableHTML += '<tr>' +
                    '<td><span class="right-td">' + item.FID + '</td></span>' +
                    '<td><span class="right-td">' + item.FNAME + '</td></span>' +
                    '<td><span class="right-td">' + item.EMAILS + '</td></span>' +
                    '</tr>';

        })

//db table

        DbTableHTML += '<tr>' +
                '<th>FID</th>' +
                '<th>FUNDRAISER</th>' +
                '<th>EMAILS</th>' +
                '</tr>';

        data.cron_data_db.fundraiser_array.forEach(function (item, index) {
            DbTableHTML += '<tr>' +
                    '<td><span class="right-td">' + item.FID + '</td></span>' +
                    '<td><span class="right-td">' + item.FNAME + '</td></span>' +
                    '<td><span class="right-td">' + item.EMAILS + '</td></span>' +
                    '</tr>';

        })

        jQuery(".jsontable").append(jsonTableHTML)
        jQuery(".dbtable").append(DbTableHTML)

    }

</script>

<hr>

<h3>Results</h3>

<!-- Show Database Custom Table Info -->
<table cellpadding="4" cellspacing="0" border="1" class="dataTable" id="cron_logs_table">
    <thead>
        <tr>
            <th class="center-td" ><strong>ID</strong></th>
            <th class="center-td" ><strong>Cron Type</strong></th>
            <th class="center-td" ><strong>Started</strong></th>
            <th class="center-td" ><strong>Ended</strong></th>
            <th class="center-td" ><strong>Fundraisers</strong></th>
            <th class="center-td" ><strong>Emails</strong></th>
            <th class="center-td"><strong>Duration</strong></th>
            <th class="center-td" ><strong>Completed</strong></th>
            <th class="center-td" ><strong>Action</strong></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ( !empty($result) ) {
            $n = 0;
            foreach ( $result as $item ) {
                $cron_date = date("m-d-Y", strtotime($item['started'], strtotime(current_time("m-d-Y", 0))));
                $n++;
                ?>
                <tr class="" >
                    <td  ><span class="right-td"><?php echo $item['ID']; ?></span></td>                   
                    <td  ><span class="left-td"><?php echo $item['type'] ?></span></td>
                    <td  ><span class="left-td"><?php echo $item['started'] ?></span></td>
                    <td  ><span class="left-td"><?php echo $item['ended'] ?></span></td>
                    <td  ><span class="left-td"><?php echo $item['fundraiser_count'] ?></span></td>
                    <td  ><span class="left-td"><?php echo $item['email_count'] ?></span></td>
                    <td  ><span class="left-td"><?php echo $item['duration'] ?></span></td>
                    <td   class="center-td"><span ><?php echo ($item['status']) ? '&#9989' : '&#10060' ?></span></td>
                    <td   class="center-td" >
                        <button class="btn button-primary cron_item" 
                                file_key="<?php echo $cron_date . "_" . $item['filename'] ?>" 
                                cron_type = "<?php echo $item['type'] ?>"
                                cron_date = "<?php echo $cron_date; ?>"
                                started = "<?php echo $item['started'] ?>">View log
                        </button>
                    </td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td class='center-td'>No data</td>
                <td class='center-td'></td>
                <td class='center-td'></td>
                <td class='center-td'></td>
                <td class='center-td'></td>
                <td class='center-td'></td>
                <td class='center-td'></td>
                <td class='center-td'></td>
                <td class='center-td'></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<style>
    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        position: relative;
        background-color: #fefefe;
        margin: auto;
        padding: 0;
        border: 1px solid #888;
        width: 70%;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
        -webkit-animation-name: animatetop;
        -webkit-animation-duration: 0.4s;
        animation-name: animatetop;
        animation-duration: 0.4s
    }

    /* Add Animation */
    @-webkit-keyframes animatetop {
        from {top:-300px; opacity:0} 
        to {top:0; opacity:1}
    }

    @keyframes animatetop {
        from {top:-300px; opacity:0}
        to {top:0; opacity:1}
    }

    /* The Close Button */
    .close {
        color: white;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .modal-header {
        padding: 2px 16px;
        background-color: #dedede;
        color: white;
    }

    .modal-body {
        padding: 2px 16px;
        height: 800px;        
        display: flex
    }
    .modal-body > div{
        width: 100%;
        overflow-y: scroll;
    }

    .modal-footer {
        padding: 2px 16px;
        background-color: #dedede;
        color: white;
    }
    .jsontable, .dbtable {
        width:100%;
        border-collapse: collapse
    }
    .jsontable tr, .dbtable tr {
        height: 30px;
    }
    .jsontable tr th, .dbtable tr th,
    .jsontable tr td, .dbtable tr td{
        border: solid 1px #000
    }
    pre {
        white-space: -moz-pre-wrap; /* Mozilla, supported since 1999 */
        white-space: -pre-wrap; /* Opera */
        white-space: -o-pre-wrap; /* Opera */
        white-space: pre-wrap; /* CSS3 - Text module (Candidate Recommendation) http://www.w3.org/TR/css3-text/#white-space */
        word-wrap: break-word; /* IE 5.5+ */
    }
</style>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/assets/css/jquery.jsonview.css" />
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/assets/js/jquery.jsonview.js"></script>

<!-- The Modal -->
<div id="jsonModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2 class="modalHeader"></h2>
        </div>
        <div class="modal-body">
            <div>
                <h3>Json Output log</h3>
                <div class="json_table">
                    <table class='jsontable'></table>
                </div>
                <p class="json_content"></p>
            </div>            
            <div >
                <h3>Cron data from DB</h3>
                <div class="db_table">
                    <table class='dbtable'></table>
                </div>
                <p class="cron_data_content"></p>
            </div>            
        </div>   
    </div>
</div>
