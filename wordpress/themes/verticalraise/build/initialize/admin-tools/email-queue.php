<?php
 /**
  * Create Reports menu and submenus to display fundraiser ended and run crons.
  */
 
 use classes\app\email_queue\Insert_Check;
 
 if (!defined('ABSPATH'))
     exit;
 
 if (is_admin()) {
     add_action('admin_menu', 'email_queue_check');
 }
 
 function email_queue_check() {
     add_menu_page('Email Queue', 'Email Queue', 'manage_options', 'email_queue', 'email_queue', 'dashicons-migrate', 3);
 }
 
 
 function email_queue() {     
     $email_check = new Insert_Check();
     
     if (isset($_POST['insert_queue']) && $_POST['insert_queue'] == '1') {
         $insert  = $email_check->insert_emails();
         echo '<div class="notice notice-success is-dismissible" style=" margin: 15px 0 !important;padding: 10px !important;">
                 <div><p><strong>'.$insert["success"].' emails were inserted and '.$insert["fail"].' emails were failed.</strong></p></div>
             </div>';
     }
     $result       = $email_check->check_email_queue();
     ?>
     <h2>No inserted emails into Email_Queue</h2>
     <p>Current datetime: <?php echo current_time('Y-m-d H:i:s'); ?></p>
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
 
         #missing_emails_table_wrapper{
             margin-right: 25px; 
             /*overflow-x: scroll;*/
         }
         #missing_emails_table {
             position: relative
         }
 
         #missing_emails_table_wrapper .dataTables_scroll {
             max-height: 600px;
             overflow-x: auto;
             position: relative;
 
         }
         #missing_emails_table_wrapper .dataTables_scroll .dataTables_scrollHead{
             overflow: hidden !important;
             position: absolute !important;
             border: 0px !important;
             width: auto !important;
             z-index: 1 !important;
             background: gainsboro;
         }
         #missing_emails_table_wrapper .dataTables_scroll .dataTables_scrollBody{
             overflow: initial !important;
             /*padding-top: 131px !important;*/
         }
         table.dataTable {
             width:100% !important;
             margin: 0 !important;
         }
 
         table.dataTable tr.child ul li{
             display:flex;
         }
         .insert_emails {
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
         .insert_emails:hover {
             background: #008ec2;
             border-color: #006799;
             color: #fff;
             box-shadow: 0 1px 0 #0073aa,0 0 2px 1px #33b3db;
         }
 
     </style>
     <h4>Select Date Range</h4>
     <form id="submitForm" method="post" action="/wp-admin/admin.php?page=email_queue">      
         <button class="button button-primary insert_emails" >Insert all emails to Email Queue</button>
         <input type="hidden" name="insert_queue" value="0" />
         <input type="hidden" name="id_array" value="" />       
 
      </form>
     <script src ="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
     <script src ="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
     <script src ="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
     <script src ="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
     <script src ="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
 
  
      <script>
         jQuery(document).ready(function ($) {
              
             $('#missing_emails_table').DataTable({
                 "bPageinate": false,
                 "searching": false,
                 "bFilter": false,
                 "bInfo": false,
                 "responsive": true,
                 "lengthChange": true,
                 "pageLength": 100,
                 "order": [[2, "desc"]],               
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
             
             $(".insert_emails").click(function(e){
                 e.preventDefault();
                 var id_array = '';
                 
                 $(".missing_emails").each(function(index){
                     id_array = (index == 0)?$(this).attr('itemid') : id_array+','+$(this).attr('itemid')
                 })
                 if (id_array != '') {
                     $("input[name=insert_queue]").val(1);
                     $("input[name=id_array]").val(id_array);
                 }
                 $("form#submitForm").submit();
             })
         });
     </script>
 
      <hr>
 
      <h3>Results</h3>
 
      <!-- Show Database Custom Table Info -->
     <table cellpadding="4" cellspacing="0" border="1" class="dataTable unsticky" id="missing_emails_table">
         <thead>
             <tr>
                 <th class="center-td"><strong>NO</strong></th>
                 <th class="center-td"><strong>Fundraiser ID</strong></th>
                 <th class="center-td"><strong>User ID</strong></th>
                 <th class="center-td"><strong>Email</strong></th>
                 <th class="center-td"><strong>Type</strong></th>
                 <th class="center-td"><strong>Cannel</strong></th>
                 <th class="center-td"><strong>From Name</strong></th>
                 <th class="center-td"><strong>Create Date</strong></th>
             </tr>
         </thead>
         <tbody>
             <?php
             if (isset($result) && !empty($result)) {
                 $n=0;
                 foreach ($result as $item) {
                     $n++;
                     ?>
                     <tr class="missing_emails" itemid="<?php echo $item['id']?>">     
                         <td ><span class="right-td"><?php echo $n;?></span></td>
                         <td ><span class="right-td"><?php echo $item['f_id']?></span></td>
                         <td ><span class="right-td"><?php echo $item['u_id']?></span></td>
                         <td ><span class="right-td"><?php echo $item['email']?></span></td>
                         <td ><span class="right-td"><?php echo $item['type']?></span></td>
                         <td ><span class="right-td"><?php echo $item['channel']?></span></td>
                         <td ><span class="right-td"><?php echo $item['from_name']?></span></td>
                         <td ><span class="right-td"><?php echo $item['created_at']?></span></td>
                     </tr>
                     <?php
                 }
             } else {
                 ?>
                 <tr>
                     <td class='center-td'>No data</td>
                     <?php for ($i = 0; $i < 7; $i++) : ?>
                         <td class='center-td'></td>                
                     <?php endfor; ?>
                 </tr>
             <?php } ?>
         </tbody>
 
      </table>
 <!-- /Show Database Custom Table Info -->
 <?php } ?>