<?php
/*
Plugin Name: Test List Table Example
*/

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Game_List_Table extends WP_List_Table {
    function __construct(){
    global $status, $page;

        parent::__construct( array(
            'singular'  => __( 'game', 'gamelisttable' ),     //singular name of the listed records
            'plural'    => __( 'games', 'gameslisttable' ),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?

    ) );   

    }

  function no_items() {
    _e( 'Đã có lỗi xảy ra, hiện tại không thể kết nối được tới Mobigate hoặc bạn có thể vui lòng kiểm tra lại API Key của mình! Cảm ơn!' );
  }

  function column_default( $item, $column_name ) {
    switch( $column_name ) { 
        case 'icon':
          return "<img src='".$item[ $column_name ]."' height='50px' weight='50px'>";
          break;
        case 'title':
          return "<a href='http://mobigate.vn/kho-game/view/content-i". $item[ "id" ] ."' target='_blank'>".$item[ $column_name ]."</a>";
          break;
        case 'desc':
          return $item[ $column_name ];
          break;
        case 'rate':
          return $item[ $column_name ]."%";
          break;
        case 'download_count':
          return number_format($item[ $column_name ])."+";
          break;
        case 'date':
          return $item[ $column_name ];
          break;
        case 'platform':
          return join(", ", $item[ $column_name ]);
          break;
        case 'actions':
          if(Mobigate_Helper::isAddedGame($item['requestId'])){
            $url = "admin.php?page=list_of_game&action=updategame&noheader=true&requestId=".$item['requestId'];
            return "<a href='". admin_url( $url ) ."' class='button button-secondary'>Cập nhật</a>";
          }else{
            $url = "admin.php?page=select_category_for_game&requestId=".$item['requestId'];
            return "<a href='". admin_url( $url ) ."' class='button button-primary'>Phân phối</a>";
          }
          
          break;
        default:
            //return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
    }
  }

  function get_sortable_columns() {
    $sortable_columns = array(
      'title'  => array('title',false),
      'rate' => array('rate',false),
      'download_count'   => array('download_count',true),
      'date'   => array('date',true)
    );
    return $sortable_columns;
  }

  function get_columns(){
          $columns = array(
              'cb'        => '<input type="checkbox" />',
              'icon' => __( 'Icon', 'gameslisttable' ),
              'title'    => __( 'Tên game', 'gameslisttable' ),
              'desc'      => __( 'Miêu tả', 'gameslisttable' ),
              'rate'      => __( 'Chia sẻ', 'gameslisttable' ),
              'download_count'      => __( 'Lượt tải', 'gameslisttable' ),
              'platform'      => __( 'HĐH', 'gameslisttable' ),
              'date'      => __( 'Ngày', 'gameslisttable' ),
              'actions'      => __( 'Actions', 'gameslisttable' )
          );
           return $columns;
    }

  function usort_reorder( $a, $b ) {
    // If no sort, default to download_count
    $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'date';
    // If no order, default to desc
    $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
    // Determine sort order
    if($orderby === 'download_count'){
      $result = $a[$orderby] - $b[$orderby];
    }else{
      $result = strcmp( $a[$orderby], $b[$orderby] );
    }
    
    // Send final sort direction to usort
    return ( $order === 'desc' ) ? -$result : $result;
  }

  function get_bulk_actions() {
    $actions = array(
      'publish_or_update'    => 'Phân phối hoặc cập nhật game'
    );
    return $actions;
  }

  function column_cb($item) {
          return sprintf(
              '<input type="checkbox" name="game[]" value="%s" />', $item['requestId']
          );    
  }

  function prepare_items($data_source) {
    $columns  = $this->get_columns();
    $hidden   = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array( $columns, $hidden, $sortable );
    usort( $data_source, array( &$this, 'usort_reorder' ) );
    
    $per_page = 50;
    $current_page = $this->get_pagenum();
    $total_items = count( $data_source );

    // only ncessary because we have sample data
    $this->found_data = array_slice( $data_source,( ( $current_page-1 )* $per_page ), $per_page );

    $this->set_pagination_args( array(
      'total_items' => $total_items,                  //WE have to calculate the total number of items
      'per_page'    => $per_page                     //WE have to determine how many items to show on a page
    ) );
    $this->items = $this->found_data;
  }

} //class


