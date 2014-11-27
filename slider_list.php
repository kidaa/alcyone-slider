<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Alcyone_Slider_List_Table extends WP_List_Table {
   
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'slider',     //singular name of the listed records
            'plural'    => 'sliders',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
    function column_default($item, $column_name){
        switch($column_name){
            case 'size':
            case 'shortcode':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_title($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&banner=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&banner=%s" class="delete">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
        );
        
        //Return the title contents
        return sprintf('<strong><a href="?page=%4$s&action=%5$s&banner=%6$s">%1$s</a></strong> <span style="color:silver">(images:%2$s)</span>%3$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $item['count'],
            /*$3%s*/ $this->row_actions($actions),
			/*$4%s*/ $_REQUEST['page'],
			/*$5%s*/ 'edit',
			/*$6%s*/ $item['ID']
        );
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Name',
            'size'    => 'Size',
            'shortcode'  => 'Shortcode'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'size'    => array('size',false),
            'shortcode'  => array('shortcode',false)
        );
        return $sortable_columns;
    }    
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }
    function process_bulk_action() {
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
			$slider_to_delete = $_GET['slider'];
			//var_dump($slider_to_delete);
			foreach ($slider_to_delete as $del_slide){
				wp_delete_post( $del_slide);
			}
			$redir = get_admin_url().'/admin.php?page=alcyoneslider&settings-updated=true';
            //wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 20;
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
         $my_query = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'alcyoneslider' AND post_status = 'publish' ", ARRAY_A);
		 foreach ($my_query as $my_row) {
		 
			$active = get_post_meta($my_row['ID'], 'active_headers');
			
			$my_row_data['title'] = $my_row['post_title'];
			$my_row_data['count'] = count($active[0]);
			$my_row_data['ID'] = $my_row['ID'];			
			$my_row_data['size'] = get_post_meta($my_row_data['ID'],'width',true).'px/'.get_post_meta($my_row_data['ID'],'height',true).'px';
			$my_row_data['shortcode'] = '[alcyone_slider id="'.$my_row['ID'].'"]';
			$data[] = $my_row_data;
		 }
		
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
		if (!empty($data))
        usort($data, 'usort_reorder');
        
        $current_page = $this->get_pagenum();
        
        $total_items = count($data);
        
		 if (!empty($data))
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}

?>