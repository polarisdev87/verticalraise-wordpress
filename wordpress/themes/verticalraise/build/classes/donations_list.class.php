<?php


if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class Donations_List
 * Displays the donation list in Admin Dashboard
 */
class Donations_List extends WP_List_Table {

    public $donations;
    
    const LIMIT = 15; // max results

    /**
     * Donations_List constructor.
     * Search for donation
     * @param array $args
     */
    public function __construct( $args = array() ) {
        parent::__construct( $args );
    }

    /**
     * Get total results
     */

    function get_donations_count() {
        global $wpdb;
        if ( isset ( $_GET['s'] ) && ! empty ($_GET['s'] ) ) {
            $total = 0;
            if ( $_GET['t'] === 'email') {
                $query = $wpdb->prepare( "SELECT count('id') as 'total' FROM donations WHERE `email` = %s", array( trim($_GET['s']) ) );
                $total = $wpdb->get_var( $query );
            } else if ( $_GET['t'] === 'donor_name' ) {
                $query = $wpdb->prepare( "SELECT count('id') as 'total' FROM donations WHERE `name` LIKE %s", array( "%" . trim($_GET['s']) . "%" ) );
                $total = $wpdb->get_var( $query );
            } else if ( $_GET['t'] === 'participant_name' ) {
                $query = $wpdb->prepare( "SELECT count(*) FROM donations d LEFT JOIN wp_users u ON d.uid = u.ID LEFT JOIN wp_posts f ON d.f_id = f.ID WHERE u.display_name LIKE %s ", array( "%" . trim($_GET['s']) . "%" ) );
                $total = $wpdb->get_var( $query );
            } else if ( $_GET['t'] === 'fundraiser_name' ) {
                $query = $wpdb->prepare( "SELECT count(*) FROM donations d LEFT JOIN wp_users u ON d.uid = u.ID LEFT JOIN wp_posts f ON d.f_id = f.ID WHERE f.post_title LIKE %s ", array( "%" . trim($_GET['s']) . "%" ) );
                $total = $wpdb->get_var( $query );
            }
            return $total;
        } else {
            $query = "SELECT count('id') as 'total' FROM donations";
            $total = $wpdb->get_var( $query );
            return $total;
        }
        
     }

    /**
     * Get paginated results
     */

     function get_donations( $page = 0, $limit = 15 ) {
        global $wpdb;

        if ( isset ( $_GET['s'] ) && ! empty ($_GET['s'] ) ) {
            if ( $_GET['t'] === 'email' ) {
                $query = $wpdb->prepare( "SELECT d.*, u.display_name as 'user_name', u.ID as 'user_id', f.post_title as 'fundraiser_title', fd.end_date FROM donations d LEFT JOIN wp_users u ON d.uid = u.ID LEFT JOIN wp_posts f ON d.f_id = f.ID LEFT JOIN fundraiser_details fd ON fd.f_id = f.ID WHERE d.`email` = %s  ORDER BY `{$this->get_order_field()}` {$this->get_sort_order()} LIMIT %d OFFSET %d ", array( trim($_GET['s']), $limit, $page * $limit ) );
                $result = $wpdb->get_results( $query , ARRAY_A );
                $this->donations = $result;
            } else if ( $_GET['t'] === "donor_name" ) {
                $query = $wpdb->prepare( "SELECT d.*, u.display_name as 'user_name', u.ID as 'user_id', f.post_title as 'fundraiser_title', fd.end_date FROM donations d LEFT JOIN wp_users u ON d.uid = u.ID LEFT JOIN wp_posts f ON d.f_id = f.ID LEFT JOIN fundraiser_details fd ON fd.f_id = f.ID WHERE d.`name` LIKE %s  ORDER BY `{$this->get_order_field()}` {$this->get_sort_order()} LIMIT %d OFFSET %d ", array( "%" . trim($_GET['s']) . "%" , $limit, $page * $limit ) );
                $result = $wpdb->get_results( $query , ARRAY_A );
                $this->donations = $result;
            } else if ( $_GET['t'] === 'participant_name' ) {
                $query = $wpdb->prepare( "SELECT d.*, u.display_name as 'user_name', u.ID as 'user_id', f.post_title as 'fundraiser_title', fd.end_date FROM donations d LEFT JOIN wp_users u ON d.uid = u.ID LEFT JOIN wp_posts f ON d.f_id = f.ID LEFT JOIN fundraiser_details fd ON fd.f_id = f.ID WHERE u.display_name LIKE %s  ORDER BY `{$this->get_order_field()}` {$this->get_sort_order()} LIMIT %d OFFSET %d ", array( "%" . trim($_GET['s']) . "%" , $limit, $page * $limit ) );
                $result = $wpdb->get_results( $query , ARRAY_A );
                $this->donations = $result;
            } else if ( $_GET['t'] === 'fundraiser_name') {
                $query = $wpdb->prepare( "SELECT d.*, u.display_name as 'user_name', u.ID as 'user_id', f.post_title as 'fundraiser_title', fd.end_date FROM donations d LEFT JOIN wp_users u ON d.uid = u.ID LEFT JOIN wp_posts f ON d.f_id = f.ID LEFT JOIN fundraiser_details fd ON fd.f_id = f.ID WHERE f.post_title LIKE %s  ORDER BY `{$this->get_order_field()}` {$this->get_sort_order()} LIMIT %d OFFSET %d ", array( "%" . trim($_GET['s']) . "%" , $limit, $page * $limit ) );
                $result = $wpdb->get_results( $query , ARRAY_A );
                $this->donations = $result;
            }
            return $result;
        } else {
            
            $query = $wpdb->prepare( "SELECT d.*, u.display_name as 'user_name', u.ID as 'user_id', f.post_title as 'fundraiser_title', fd.end_date FROM donations d LEFT JOIN wp_users u ON d.uid = u.ID LEFT JOIN wp_posts f ON d.f_id = f.ID LEFT JOIN fundraiser_details fd ON fd.f_id = f.ID ORDER BY `{$this->get_order_field()}` {$this->get_sort_order()} LIMIT %d OFFSET %d ", array( $limit, $page * $limit ) );
            $result = $wpdb->get_results( $query , ARRAY_A );
            $this->donations = $result;
            return $result;
        }
        
     }

    /**
     * Default columns in dashboard table
     * @return array
     */
    function get_columns(){
        $columns = array(
            'id' => 'ID',
            'fundraiser_title'    => 'Fundraiser Title',
            'user_name'    => 'Participant Name',
            'user_id'    => 'Participant ID',
            'name'     => 'Name',
            'email'   => 'Email',
            'amount'     => 'Amount',
            'time' => 'Date',
            'end_date' => 'End Date',
            'refunded' => 'Refunded',
            'deleted' => 'Deleted',
        );
        return $columns;
    }

    /**
     * @param object $item
     * @param string $column_name
     * @return bool
     */
    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'id':
            case 'fundraiser_title':
            case 'user_name':
	        case 'user_id':
            case 'name':
            case 'email':
            case 'amount':
            case 'time':
            case 'refunded':
	        case 'deleted':
                return $item[ $column_name ];
                break;
	        case 'end_date':
		        $date =  $item[ $column_name ];
		        $date_object = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
		        if ( $date_object ) {
			        return $date_object->format( 'Y-m-d' );
		        } else {
			        return $item[ $column_name ];
		        }
		        break;
            default:
                return false;
                break;
        }
    }

    /**
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'id'            => array('ID',false),
            'fundraiser_title'    => array('fundraiser_title',false),
            'user_name'    => array('user_name',false),
            'name'     => array('name',false),
            'email'   => array('email',false),
            'amount'     => array('amount',false),
            'time'     => array('time',false),/*
            'refunded' => array('refunded',false),*/
        );
        return $sortable_columns;
    }

    /**
     * Includes action edit link in f_id column
     * @param $item
     * @return string
     */
    function column_amount( $item) {
        $actions = array(
            'refund'        => sprintf('<a href="/wp-admin/admin.php?page=refunds_page&donation=%s&email=%s&amount=%d">Refund</a>' ,$item['id'], $item['email'], $item['amount'] ),
        );

        return sprintf('%1$s %2$s', $item['amount'], $this->row_actions($actions) );
    }


	/**
	 * Includes action delete link
	 * @param $item
	 *
	 * @return string
	 */
	function column_deleted($item){
		if(!$item['deleted']){
			$actions= array(
				'delete' => sprintf('<a href="/wp-admin/admin.php?page=delete_donation&donation_id=%s">Delete</a>', $item['id'] )
			);
			return sprintf('%1$s %2$s', $item['deleted'], $this->row_actions($actions) );
		}
		return $item['deleted'];
	}

    private function get_page_num() {

        if ( ! isset( $_GET['paged'] ) ) {
            return 0;
        } else if ( $_GET['paged'] > 1 ) {
            return $_GET['paged'] - 1;
        } else {
            return 0;
        }

    }

    private function get_order_field () {

        $defaultOrder = 'id';
        $allowedOrder = ['id', 'amount', 'time', 'name', 'email', 'fundraiser_title', 'user_name'];
        if ( isset( $_GET['orderby'] ) ) {
            $orderBy = $_GET['orderby'];
            if ( in_array( strtolower( $orderBy ) , $allowedOrder ) ) {
                return $orderBy;
            }
        }
        return $defaultOrder;

    }

    private function get_sort_order () {

        $defaultSort = 'DESC';
        $allowedSort = ['ASC', 'DESC'];

        if ( isset( $_GET['order'] ) ){
            $sort = $_GET['order'];
            if ( in_array( strtoupper( $sort ) , $allowedSort ) ) {
                return $sort;
            }
        }
        return $defaultSort;

    }

    /**
     *  Prepares the data for display
     */
    function prepare_items() {

        $total = $this->get_donations_count();

        $this->set_pagination_args( array(
            'total_items' => $total,
            'per_page'    => self::LIMIT
          ) );
          
        $donations = $this->get_donations( $this->get_page_num() ,  self::LIMIT);
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->items = $this->donations;
    }
}
