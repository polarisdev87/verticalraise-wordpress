<?php


if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class Donations_List
 * Displays the donation list in Admin Dashboard
 */
class Connect_Accounts_List extends WP_List_Table {

    public $accounts;
    
    const LIMIT = 15; // max results

    /**
     * Connect_Accounts_List constructor.
     * Search for accounts
     * @param array $args
     */
    public function __construct( $args = array() ) {
        parent::__construct( $args );
    }

    /**
     * Get total results
     */

    function get_accounts_number() {
        global $wpdb;

	    $sql_ended = $sql_end_date = $sql_start_date = $endedSQL = $raised_sql = "";

	    if ( !empty( $_GET['start_date'] ) ){
		    $sql_start_date = $wpdb->prepare( " INNER JOIN wp_postmeta pm2 on pm2.post_id = p.ID AND pm2.meta_key = 'start_date' AND CAST(pm2.meta_value as DATE) >= CAST(%s as DATE)" , array( trim( $_GET['start_date'] )  ) );
	    }

	    if ( !empty( $_GET['end_date'] ) ){
		    $sql_end_date = $wpdb->prepare( " INNER JOIN wp_postmeta pm3 on pm3.post_id = p.ID AND pm3.meta_key = 'end_date' AND CAST(pm3.meta_value as DATE) <= CAST(%s as DATE)" , array( trim( $_GET['end_date'] )  ) );
	    }

	    if ( !empty( $_GET['show'] ) &&  $_GET['show'] === "ended"  ){
		    $sql_ended = " INNER JOIN wp_postmeta pm on pm.post_id =  p.ID AND pm.meta_key = 'end_date' AND ADDDATE( CAST(pm.meta_value as DATE), 3) < NOW() " ;
	    }

	    if ( !empty ( $_GET['show'] ) ||  !empty ( $_GET['start_date'] ) || !empty ( $_GET['end_date'] )  ) {
	    	$endedSQL = " HAVING p.ID IN 
                  ( 
                    SELECT DISTINCT p.ID
					FROM wp_posts p
                    $sql_ended 
                    $sql_start_date
                    $sql_end_date
                  ) ";
	    }


	    if ( !empty( $_GET['raised'] ) ){
		    $raised_sql = " AND ds.amount > 0 " ;
	    }

	    if ( isset ( $_GET['s'] ) && ! empty ( $_GET['s'] ) ) {
		    $total = 0;
		    if ( $_GET['t'] === 'fundraiser_name' ) {
			    $query = $wpdb->prepare( "SELECT p.ID FROM fundraiser_details f
                  LEFT JOIN wp_posts p ON f.f_id = p.ID
                  LEFT JOIN wp_postmeta pm ON f.f_id = pm.post_id    
                  LEFT JOIN donations_sum ds ON ds.f_id = p.ID 
                  WHERE f.transferred = 0  AND p.post_status = 'publish' AND  pm.meta_key = 'stripe_connect' AND p.post_title LIKE %s $raised_sql GROUP by p.ID "  . $endedSQL , array( "%" . trim( $_GET['s'] ) . "%" ) );
			    $results = $wpdb->get_results( $query, ARRAY_A  );
			    $total = count($results);
		    }

		    return $total;
	    } else {
		    $query = "SELECT p.ID  FROM fundraiser_details f
                  LEFT JOIN wp_posts p ON f.f_id = p.ID
                  LEFT JOIN wp_postmeta pm ON f.f_id = pm.post_id
                  LEFT JOIN donations_sum ds ON ds.f_id = p.ID 
                  WHERE f.transferred = 0  AND p.post_status = 'publish' AND  pm.meta_key = 'stripe_connect' $raised_sql GROUP by p.ID " . $endedSQL ;
		    $results = $wpdb->get_results( $query, ARRAY_A  );
		    $total = count($results);

		    return $total;
	    }

     }

    /**
     * Get paginated results
     */

     function get_accounts($page = 0, $limit = 15 ) {
        global $wpdb;

	     $sql_ended = $sql_end_date = $sql_start_date = $endedSQL =  $raised_sql = "";

	     if ( !empty( $_GET['start_date'] ) ){
		     $sql_start_date = $wpdb->prepare( " INNER JOIN wp_postmeta pm2 on pm2.post_id = p.ID AND pm2.meta_key = 'start_date' AND CAST(pm2.meta_value as DATE) >= CAST(%s as DATE)" , array( trim( $_GET['start_date'] )  ) );
	     }

	     if ( !empty( $_GET['end_date'] ) ){
		     $sql_end_date = $wpdb->prepare( " INNER JOIN wp_postmeta pm3 on pm3.post_id = p.ID AND pm3.meta_key = 'end_date' AND CAST(pm3.meta_value as DATE) <= CAST(%s as DATE)" , array( trim( $_GET['end_date'] )  ) );
	     }

	     if ( !empty( $_GET['show'] ) &&  $_GET['show'] === "ended"  ){
		     $sql_ended = " INNER JOIN wp_postmeta pm on pm.post_id =  p.ID AND pm.meta_key = 'end_date' AND ADDDATE( CAST(pm.meta_value as DATE), 3) < NOW() " ;
	     }

	     if ( !empty ( $_GET['show'] ) ||   !empty ( $_GET['start_date'] ) || !empty ( $_GET['end_date'] )  ) {
		     $endedSQL = " HAVING f.f_id IN 
                  ( 
                    SELECT DISTINCT p.ID
					FROM wp_posts p
                    $sql_ended 
                    $sql_start_date
                    $sql_end_date
                  ) ";
	     }

	     if ( !empty( $_GET['raised'] ) ){
		     $raised_sql = " AND ds.amount > 0 " ;
	     }

	     if ( isset ( $_GET['s'] ) && ! empty ( $_GET['s'] ) ) {
		     if ( $_GET['t'] === 'fundraiser_name' ) {
			     $query          = $wpdb->prepare( "SELECT pm.meta_value as stripe_connect, ds.amount, f.*, p.post_name, p.post_title FROM fundraiser_details f 
            LEFT JOIN wp_posts p ON f.f_id = p.ID
            LEFT JOIN wp_postmeta pm ON f.f_id = pm.post_id            
            LEFT JOIN donations_sum ds ON ds.f_id = p.ID 
            WHERE f.transferred = 0 AND p.post_status = 'publish' AND  pm.meta_key = 'stripe_connect'  
            AND p.post_title LIKE %s $raised_sql "
            . $endedSQL .
            " ORDER BY `{$this->get_order_field()}` {$this->get_sort_order()} LIMIT %d OFFSET %d "
				     , array( "%" . trim( $_GET['s'] ) . "%", $limit, $page * $limit ) );
			     $result         = $wpdb->get_results( $query, ARRAY_A );
			     $this->accounts = $result;
		     }

		     return $result;
	     } else {

		     $query          = $wpdb->prepare( "SELECT pm.meta_value as stripe_connect, ds.amount, f.*, p.post_name, p.post_title FROM fundraiser_details f 
            LEFT JOIN wp_posts p ON f.f_id = p.ID
            LEFT JOIN wp_postmeta pm ON f.f_id = pm.post_id            
            LEFT JOIN donations_sum ds ON ds.f_id = p.ID 
            WHERE f.transferred = 0 AND p.post_status = 'publish' AND  pm.meta_key = 'stripe_connect' $raised_sql "
            . $endedSQL .
            " ORDER BY `{$this->get_order_field()}` {$this->get_sort_order()} LIMIT %d OFFSET %d "
			     , array( $limit, $page * $limit ) );
		     $result         = $wpdb->get_results( $query, ARRAY_A );
		     $this->accounts = $result;

		     return $result;
	     }
     }

    /**
     * Default columns in dashboard table
     * @return array
     */
    function get_columns(){
        $columns = array(
            'f_id' => 'Fundraiser ID',
            'post_title' => 'Fundraiser Title',
            'post_name' => 'URL',
            'stripe_connect' => 'Direct Deposit',
            'goal'     => 'Goal',
            'amount'     => 'Raised',
            'start_date'   => 'Start Date',
            'end_date'     => 'End Date',
            'transferred' => '',

        );
        return $columns;
    }

    /**
     * @param object $item
     * @param string $column_name
     * @return Mixed
     */
    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'start_date':
                return ( new \DateTime( $item[ $column_name ] ))->format("Y-m-d");
                break;
            case 'end_date':
                if ( ( new \DateTime( $item['end_date'] ) ) >  ( new \DateTime() ) ) {
                    return ( new \DateTime( $item[ $column_name ] ))->format("Y-m-d");
                    break;
                }
                return '<p style="color: red">' . ( new \DateTime( $item[ $column_name ] ))->format("Y-m-d") . '</p>';
                break;
            case 'post_name':
                return "<a href='/fundraiser/{$item[ $column_name ]}'>{$item[ $column_name ]}</a>";
                break;
	        case 'stripe_connect':
		        if($item[ $column_name ]){
			        echo "&#x2714;";
		        }
		        break;
	        case 'transferred':
		        if ( ( new \DateTime( $item['end_date'] ) ) <  ( new \DateTime() )) {
			        if( $item['stripe_connect'] == 0 ){
				        return sprintf('<a class="button-secondary" href="/wp-admin/admin.php?page=transfers_page&fid=%d">Transfer</a>', $item['f_id'] );
			        }else{
				        return sprintf('<a class="button-primary" href="/wp-admin/admin.php?page=payouts_page&fid=%d">Payout</a>', $item['f_id'] );
			        }
		        }
		        break;
            case 'f_id':
            case 'post_title':
            case 'goal':
            case 'amount':
	            return $item[ $column_name ];
            default:
                return false;
        }
    }


	/**
	 * @return array
	 */
	function get_sortable_columns() {
		$sortable_columns = array(
			'f_id'           => array( 'ID', false ),
			'post_title'     => array( 'post_title', false ),
			'post_name'      => array( 'post_name', false ),
			'stripe_connect' => array( 'stripe_connect', false ),
			'goal'           => array( 'goal', false ),
			'amount'         => array( 'raised', false ),
			'transferred'    => array( 'transferred', false ),
			'start_date'     => array( 'start_date', false ),
			'end_date'       => array( 'end_date', false ),
		);

		return $sortable_columns;
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
	    $allowedOrder = [
		    'id',
		    'post_title',
		    'post_name',
		    'stripe_connect',
		    'goal',
		    'amount',
		    'transferred',
		    'start_date',
		    'end_date'
	    ];
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

        $total = $this->get_accounts_number();

        $this->set_pagination_args( array(
            'total_items' => $total,
            'per_page'    => self::LIMIT
          ) );
          
        $accounts = $this->get_accounts( $this->get_page_num() ,  self::LIMIT);
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->items = $this->accounts;
    }
}
