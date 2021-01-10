<?php

/**
 * Page for admins to view `edit_log` entries
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

if ( is_admin() ) {
    add_action( 'admin_menu', 'edit_log_menu' );
}

function edit_log_menu() {
	add_menu_page( 'Edit Log', 'Edit Log', 'manage_options', 'edit-log', 'edit_log_page', 'dashicons-search', 2 );
}

function edit_log_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    echo "<link rel='stylesheet' href='//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css'>";
    echo "<script src='//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js'></script>";
	echo "<div class='wrap'>";
    echo "<h2>Edit Log</h2>";
    edit_log_date_range();
	edit_log_output();
	echo "</div>";
    echo "<script>
    jQuery(document).ready(function($){
        $('#edit-log').DataTable({
            'order': [[ 8, 'desc' ]],
            'pageLength': 50
        });
    });
    </script>";
}

function edit_log_output() {
    global $wpdb;
    
    $_date = ( !empty($_GET['date']) ) ? (int) $_GET['date'] : 0 ;
    
    switch($_date) {
        case 0:
            $range = strtotime("3 months ago", current_time("timestamp"));
            $sql_date = date("Y-m-d", $range);
            break;
        case 1;
            $range = strtotime("6 months ago", current_time("timestamp"));
            $sql_date = date("Y-m-d", $range);
            break;
        case 2;
            $range = strtotime("9 months ago", current_time("timestamp"));
            $sql_date = date("Y-m-d", $range);
            break;
        case 3;
            $range = strtotime("12 months ago", current_time("timestamp"));
            $sql_date = date("Y-m-d", $range);
            break;
        default:
            $range = strtotime("3 months ago", current_time("timestamp"));
            $sql_date = date("Y-m-d", $range);
            break;
    }

    $results = $wpdb->get_results( 
        "
        SELECT * 
        FROM `edit_log`
        WHERE date(`date`) >= '{$sql_date}'
        ORDER BY `date` DESC
        "
    );

    echo "<table id='edit-log' class='display nowrap dataTable dtr-inline'>
            <thead>
                <tr>
                    <td>F_ID</td>
                    <td>Fundraiser Name</td>
                    <td>User ID</td>
                    <!-- td>Username</td -->
                    <td>Name</td>
                    <td>User Type</td>
                    <td>Edit Type</td>
                    <td>New Value</td>
                    <td>Original Value</td>
                    <td>Date</td>
                </tr>
            </thead>
            <tbody>";
    
    foreach ( $results as $r ) {
        
        // Get user names
        if ( empty($user_names[$r->u_id]) ) {
            $temp_u = get_userdata($r->u_id);
            if ( !empty($temp_u) ) {
                $user_names[$r->u_id] = $temp_u->user_login;
                $names[$r->u_id] = $temp_u->first_name . ' ' . $temp_u->last_name;
            } else {
                $user_names[$r->u_id] = '';
                $names[$r->u_id] = '';
            }
        }
        // Get fundraiser names
        if ( empty($fundraiser_names[$r->f_id]) ) {
            $temp_f = get_the_title($r->f_id);
            $fundraiser_names[$r->f_id] = $temp_f;
        }
        // Get type: sadmin, post author?
        if ( empty($user_status[$r->u_id]) ) {
            $_post = get_post($r->f_id);
            if ( $_post->post_author == $r->u_id ) {
                $user_status[$r->u_id] = 'author';
            } else {
                $user_status[$r->u_id] = 's_admin';
            }
        }
        
        $f_url = get_permalink($r->f_id);
        $u_url = "/wp-admin/user-edit.php?user_id={$r->u_id}";
        
        echo "<tr>";
        echo "<td>{$r->f_id}</td>";
        echo "<td><a href='{$f_url}' target='_blank'>{$fundraiser_names[$r->f_id]}</td>";
        echo "<td>{$r->u_id}</td>";
        //echo "<td>{$user_names[$r->u_id]}</a></td>";
        echo "<td><a href='{$u_url}' target='_blank'>{$names[$r->u_id]}</a></td>";
        echo "<td>{$user_status[$r->u_id]}</td>";
        echo "<td>{$r->edit_type}</td>";
        echo "<td>{$r->new_value}</td>";
        echo "<td>{$r->old_value}</td>";
        echo "<td>{$r->date}</td>";
        echo "</tr>";
    }
    echo "</tbody>
    </table>";
}

function edit_log_date_range() {
    
    $date = ( !empty($_GET['date']) ) ? $_GET['date'] : 0 ;
    
    $options = [
        "The last 3 months",
        "The last 6 months",
        "The last 9 months",
        "The last 12 months"
    ];

    echo "<span style='font-weight: 600; font-size: 1.3em;'>Date Range: {$options[$date]}</span> &nbsp;&nbsp;";
    echo "<select id='date-select'>";
    echo "<option value='0'>-- Change date range</option>";
    foreach ( $options as $key => $option ) {
        echo "<option value='{$key}'>{$option}</option>";
    }
    echo "</select>";
    echo " ";
    echo "<a href='#' onclick='return false;' id='date-button' class='button button-primary'>GO</a>";
    
    echo "<script>
    jQuery(document).ready(function($){
        $('#date-button').on('click',function(){
        var value = $('#date-select').val();
        location.href = '/wp-admin/admin.php?page=edit-log&date='+ value; 
        });
    });
    </script>";
}