<?php


if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class Rep_Landing_Pages_List
 * Displays the Rep landing pages list in Admin Dashboard
 */
class Rep_Landing_Pages_List extends WP_List_Table {

    public $rep_pages;

    /**
     * Rep_Landing_Pages_List constructor.
     * Search for rep landing pages
     * @param array $args
     */
    public function __construct( $args = array() ) {

        $pages = get_pages(array(
            'posts_per_page'=> -1,
            'post_type'		=> 'page',
            'post_status'   => 'publish,draft',
            'meta_key'      => '_wp_page_template',
            'meta_value'    => 'RepLandingPage.php',
            'hierarchical'  => 0,
            'sort_order'    => 'asc',
            'sort_column'   => 'post_title',
        ));

        foreach ( $pages as $page ){
            $this->rep_pages[] = (array) $page;
        }
        parent::__construct( $args );
    }

    /**
     * Default columns in dashboard table
     * @return array
     */
    function get_columns(){
        $columns = array(
            'ID' => 'ID',
            'post_title'    => 'Title',
            'post_name'     => 'Slug',
            'post_status'   => 'Status',
            'post_date'     => 'Created',
            'post_modified' => 'Updated',
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
            case 'ID':
            case 'post_title'   :
            case 'post_name'    :
            case 'post_status'  :
            case 'post_date'    :
            case 'post_modified':
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
            'ID'            => array('ID',false),
            'post_title'    => array('post_title',false),
            'post_name'     => array('post_title',false),
            'post_status'   => array('post_status',false),
            'post_date'     => array('post_date',false),
            'post_modified' => array('post_date',false),
        );
        return $sortable_columns;
    }

    /**
     * To sort data in table clicking columns
     * @param $a
     * @param $b
     * @return int
     */
    function usort_reorder( $a, $b ) {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'post_title';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }

    /**
     * Includes action edit link in post_title column
     * @param $item
     * @return string
     */
    function column_post_title( $item) {
        $actions = array(
            'edit'          => sprintf('<a href="/wp-admin/post.php?post=%s&action=edit">Edit</a>' ,$item['ID']),
            'view'          => sprintf('<a href="/%s">View</a>' ,$item['post_name']),
            'clone'         => sprintf('<a href="/wp-admin/admin.php?action=dt_duplicate_post_as_draft&post=%s&nonce=%s">Clone</a>' , $item['ID'], wp_create_nonce( 'dt-duplicate-page-'.$item['ID'] )),
            'delete'        => sprintf('<a href="/wp-admin/post.php?action=trash&post=%s&_wpnonce=%s">Delete</a>' , $item['ID'], wp_create_nonce( 'trash-post_'.$item['ID'] )),
        );

        return sprintf('%1$s %2$s', $item['post_title'], $this->row_actions($actions) );
    }

    /**
     *  Prepares the data for display
     */
    function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        usort( $this->rep_pages, array( &$this, 'usort_reorder' ) );
        $this->items = $this->rep_pages;
    }
}
