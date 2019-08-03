<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to Drivers at the admin end
 * @author Casperon
 *
 * */
class Drivers extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation','excel'));
        $this->load->model(array('driver_model'));
        $this->load->model(array('mileage_model'));

        if ($this->checkPrivileges('driver', $this->privStatus) == FALSE) {
            redirect('admin');
        }

        $c_fun = $this->uri->segment(3);
        $restricted_function = array('delete_driver', 'change_driver_status_global', 'delete_category', 'change_category_status_global');
        if (in_array($c_fun, $restricted_function) && $this->data['isDemo'] === TRUE) {
            $this->setErrorMessage('error', 'You are in demo version. you can\'t do this action.','admin_template_demo_version');
            redirect($_SERVER['HTTP_REFERER']);
            die;
        }
    }

    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            redirect('admin/drivers/display_driver_dashboard');
        }
    }

    /**
     * 
     * This function loads the drivers dashboard
     *
     * */
    public function display_driver_dashboard() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->lang->line('admin_drivers_drivers_dashboard') != '') 
            $this->data['heading']= stripslashes($this->lang->line('admin_drivers_drivers_dashboard')); 
            else  $this->data['heading'] = 'Drivers Dashboard'; 
            
            $condition = 'order by `created` desc';
            $this->data['totalUsersList'] = $this->driver_model->get_selected_fields(DRIVERS, array(), array('email'), array('_id' => 'DESC'))->num_rows();
            $this->data['totalActiveUser'] = $this->driver_model->get_selected_fields(DRIVERS, array('status' => 'Active'), array('email'), array('_id' => 'DESC'))->num_rows();
            $this->data['totalInactiveUser'] = $this->driver_model->get_selected_fields(DRIVERS, array('status' => 'Inactive'), array('email'), array('_id' => 'DESC'))->num_rows();
            $selectedFileds = array('driver_name', 'email', 'image', 'status');
            $this->data['recentdriversList'] = $this->driver_model->get_selected_fields(DRIVERS, array(), $selectedFileds, array('_id' => 'DESC'), 3, 0);
            $this->load->view('admin/drivers/display_drivers_dashboard', $this->data);
        }
    }

    /**
     *
     * This function loads the drivers list page
     *
     * */
    public function display_drivers_list() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }
        if ($this->lang->line('driver_disp_driver_list') != '') {
            $this->data['heading']= stripslashes($this->lang->line('driver_disp_driver_list'));
        } else {
            $this->data['heading'] = 'Display Drivers List';
        }
        $sortArr  = array('created' => 'DESC');
        $sortby = '';
        if (isset($_GET['sortby'])) {
            $this->data['filter'] = 'filter';
            $sortby = $_GET['sortby'];
            if($sortby =="doj_asc"){
                $sortArr  = array('created' => 'ASC');
            }
            if($sortby =="doj_desc"){
                $sortArr  = array('created' => 'DESC');
            }
            if($sortby =="rides_asc"){
                $sortArr  = array('no_of_rides' => 'ASC');
            }
            if($sortby =="rides_desc"){
                $sortArr  = array('no_of_rides' => 'DESC');
            }
        }
        $this->data['sortby'] = $sortby;        
        $filterArr = array();
        $filterCondition = array();
        if (isset($_GET['type']) && (isset($_GET['value']) || isset($_GET['vehicle_category'])) && $_GET['type'] != '' && ($_GET['value'] != '' || $_GET['vehicle_category'] != '')) {
            if (isset($_GET['type']) && $_GET['type'] != '') {
                $this->data['type'] = $_GET['type'];
            }
            if (isset($_GET['value']) && $_GET['value'] != '') {
                $this->data['value'] = $_GET['value'];
                $filter_val = $this->data['value'];
            }
            $this->data['filter'] = 'filter';

            $filterCondition = array();
            if ($_GET['type'] == 'vehicle_type') {
                $vehicle_category = trim($_GET['vehicle_category']);
                $categoryVal=$this->user_model->get_all_details(CATEGORY, '', '', '', '', array('name' => $vehicle_category));
                $filterCondition = array('category' => $categoryVal->row()->_id);
            } else if ($_GET['type'] == 'driver_location') {
                $location=$this->user_model->get_all_details(LOCATIONS,'','','','',array('city'=>$_GET['value']));
                $filterArr = array($this->data['type'] => $location->row()->_id);
            } else if ($_GET['type'] == 'mobile_number') {
                $filterCondition = array_merge($filterCondition, array('dail_code' => $_GET['country']));
                $filterArr = array($this->data['type'] => $filter_val);
            } else{ 
                $filterArr = array($this->data['type'] => $filter_val);
            }                 
        } else if (isset($_GET['type']) && $_GET['type'] == 'mobile_number') {
            if (isset($_GET['type']) && $_GET['type'] != '') {
                $this->data['type'] = $_GET['type'];
            }
            $filterCondition = array_merge($filterCondition, array('dail_code' => $_GET['country']));
        }
        $driversCount = $this->user_model->get_all_counts(DRIVERS, $filterCondition,$filterArr);
        if ($driversCount > 1000) {
            $searchPerPage = 500;
            $paginationNo = $this->uri->segment(4);
            if ($paginationNo == '') {
                $paginationNo = 0;
            } else {
                $paginationNo = $paginationNo;
            }
            $this->data['driversList'] = $this->user_model->get_all_details(DRIVERS, $filterCondition, $sortArr, $searchPerPage, $paginationNo,$filterArr);
            $searchbaseUrl = 'admin/drivers/display_drivers_list/'; 
            $config['num_links'] = 3;
            $config['display_pages'] = TRUE;
            $config['base_url'] = $searchbaseUrl;
            $config['total_rows'] = $driversCount;
            $config["per_page"] = $searchPerPage;
            $config["uri_segment"] = 4;
            $config['first_link'] = '';
            $config['last_link'] = '';
            $config['full_tag_open'] = '<ul class="tsc_pagination tsc_paginationA tsc_paginationA01">';
            $config['full_tag_close'] = '</ul>';
            if ($this->lang->line('pagination_prev_lbl') != '') $config['prev_link'] =stripslashes($this->lang->line('pagination_prev_lbl'));  else  $config['prev_link'] ='Prev';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            if ($this->lang->line('pagination_next_lbl') != '') $config['next_link'] =stripslashes($this->lang->line('pagination_next_lbl'));  else  $config['next_link'] ='Next';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="current"><a href="javascript:void(0);" style="cursor:default;">';
            $config['cur_tag_close'] = '</a></li>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['first_tag_open'] = '<li>';
            $config['first_tag_close'] = '</li>';
            $config['last_tag_open'] = '<li>';
            $config['last_tag_close'] = '</li>';
            if ($this->lang->line('pagination_first_lbl') != '') {
                $config['first_link'] =stripslashes($this->lang->line('pagination_first_lbl'));
            } else {
                $config['first_link'] ='First';
            }
            if ($this->lang->line('pagination_last_lbl') != '') {
                $config['last_link'] = stripslashes($this->lang->line('pagination_last_lbl'));
            } else {
                $config['last_link'] ='Last';
            }
            $this->pagination->initialize($config);
            $paginationLink = $this->pagination->create_links();
            $this->data['paginationLink'] = $paginationLink;
        } else {
            $this->data['paginationLink'] = '';
            $condition = array();          
            $this->data['driversList'] = $this->driver_model->get_all_details(DRIVERS, $filterCondition, $sortArr, '', '', $filterArr);
        }        
        $cabCats = $this->driver_model->get_selected_fields(CATEGORY, array(), array('_id', 'name'))->result();
        $cabsTypeArr = array();
        foreach ($cabCats as $cab) {
            $cabId = (string) $cab->_id;
            $cabsTypeArr[$cabId] = $cab;
        }
        $this->data['cabCats'] = $cabsTypeArr;
        $this->load->view('admin/drivers/display_drivers_list', $this->data);
    }
    
    /**
     *
     * This function loads the mileage list page
     *
     * */
    public function mileage_list() {
    if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }
        if ($this->lang->line('admin_menu_mileage_list') != '') 
        $this->data['heading']= stripslashes($this->lang->line('admin_menu_mileage_list')); 
        else  $this->data['heading'] = 'Mileage List';  
        $sortArr  = array('created' => 'DESC');
        $sortby = '';
        if (isset($_GET['sortby'])) {
            $this->data['filter'] = 'filter';
            $sortby = $_GET['sortby'];
            if($sortby=="doj_asc"){
                $sortArr  = array('created' => 'ASC');
            }
            if($sortby=="doj_desc"){
                $sortArr  = array('created' => 'DESC');
            }
            if($sortby=="rides_asc"){
                $sortArr  = array('no_of_rides' => 'ASC');
            }
            if($sortby=="rides_desc"){
                $sortArr  = array('no_of_rides' => 'DESC');
            }
        }
        $this->data['sortby'] = $sortby;
        
        $filterArr = array();
        $filterCondition = array();
        if (isset($_GET['type']) && (isset($_GET['value']) || isset($_GET['vehicle_category'])) && $_GET['type'] != '' && ($_GET['value'] != '' || $_GET['vehicle_category'] != '')) {
                if (isset($_GET['type']) && $_GET['type'] != '') {
                    $this->data['type'] = $_GET['type'];
                }
                if (isset($_GET['value']) && $_GET['value'] != '') {
                    $this->data['value'] = $_GET['value'];
                    $filter_val = $this->data['value'];
                }
                $this->data['filter'] = 'filter';
                $filterCondition = array();
                if($_GET['type'] == 'vehicle_type'){
                    $vehicle_category = trim($_GET['vehicle_category']);
                    $categoryVal=$this->user_model->get_all_details(CATEGORY,'','','','',array('name'=>$vehicle_category));
                    $filterCondition = array('category' => $categoryVal->row()->_id);
                } else if($_GET['type'] == 'driver_location') {
                  $location=$this->user_model->get_all_details(LOCATIONS,'','','','',array('city'=>$_GET['value']));
                  $filterArr = array($this->data['type'] => $location->row()->_id);
                  
                } else if($_GET['type'] == 'mobile_number') {
                  #$filterCondition = array('dail_code' => $_GET['country']);
                  $filterArr = array($this->data['type'] => $filter_val,'dail_code' => $_GET['country']);
                
                }else{ 
                    $filterArr = array($this->data['type'] => $filter_val);
                }                 
            }
            
            $this->data['driversList'] = $this->driver_model->get_all_details(DRIVERS,  $filterCondition, $sortArr, '', '', $filterArr);
            
            $driver_array=array();
            $driver_data=array();
            if($this->data['driversList']->num_rows() > 0 ){
                foreach($this->data['driversList']->result() as $row){
                
                    $categoryVal=$this->user_model->get_selected_fields(CATEGORY,array('_id'=>$row->category),array('name','_id'));
                    $driver_array[]=$row->_id;
                    $driver_data[(string)$row->_id]=array('name'=>$row->driver_name,'catgeory_name'=>$categoryVal->row()->name);
                    #break;
                }
            }
            
            
            $start_date='';
            $end_date='';
            if(isset($_GET['date_range']) && $_GET['date_range']!='') {
               $start_date = strtotime($_GET['date_range'] . ' 00:00:00');
               
                 #$start_date =date('Y-m-d',$start_date);
               
                 $start_d = $_GET['date_range'];
               
            }
            if(isset($_GET['dateto']) && $_GET['dateto']!='') {
              $end_date = strtotime($_GET['dateto'] . ' 23:59:59');
              $end_d = $_GET['dateto'];
               
            }
            $mileage_data=$this->mileage_model->get_mileage_list($driver_array,$start_date,$end_date);
            $total_duration=0;
            $total_distance=0;
            $tot_free_distance=0;
            $tot_free_duration=0;
            $tot_pick_duration=0;
            $tot_pick_distance=0;
            $tot_drop_distance=0;
            $tot_drop_duration=0;
            $final_Arr=array();

            foreach($mileage_data as $key=>$data) {
                
                $final_Arr[$key]=array_merge($data,$driver_data[$key]);
                
                $total_duration+= $data['free_duration']+$data['drop_duration']+$data['pickup_duration'];   
                $total_distance+= $data['drop_distance']+$data['pickup_distance']+$data['free_distance'];
                
                $tot_free_distance+=$data['free_distance'];
                $tot_pick_distance+=$data['pickup_distance'];
                $tot_drop_distance+=$data['drop_distance'];
                
                $tot_free_duration+=$data['free_duration'];
                $tot_pick_duration+=$data['pickup_duration'];
                $tot_drop_duration+=$data['drop_duration'];
            }
            $this->data['total_duration']=$total_duration;
            $this->data['total_distance']=$total_distance;
            
            $this->data['tot_free_distance']=$tot_free_distance;
            $this->data['tot_pick_distance']=$tot_pick_distance;
            $this->data['tot_drop_distance']=$tot_drop_distance;
            
            $this->data['tot_free_duration']=$tot_free_duration;
            $this->data['tot_pick_duration']=$tot_pick_duration;
            $this->data['tot_drop_duration']=$tot_drop_duration;
        
            $cabCats = $this->driver_model->get_selected_fields(CATEGORY, array(), array('_id', 'name'))->result();
            $cabsTypeArr = array();
            foreach ($cabCats as $cab) {
                $cabId = (string) $cab->_id;
                $cabsTypeArr[$cabId] = $cab;
            }
            $this->data['cabCats'] = $cabsTypeArr;
            $this->data['mileage_data'] = $final_Arr;
            $this->data['start_date'] = $start_d;
            $this->data['end_date'] = $end_d;
        
           $this->load->view('admin/drivers/display_mileage_list', $this->data);
    }
    /**
     *
     * This function loads the mileage list page
     *
     * */
    public function mileage_list_report() {
    if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }
        if ($this->lang->line('admin_menu_mileage_list') != '') 
        $this->data['heading']= stripslashes($this->lang->line('admin_menu_mileage_list')); 
        else  $this->data['heading'] = 'Mileage List';  
        $sortArr  = array('created' => 'DESC');
        $sortby = '';
        if (isset($_GET['sortby'])) {
            $this->data['filter'] = 'filter';
            $sortby = $_GET['sortby'];
            if($sortby=="doj_asc"){
                $sortArr  = array('created' => 'ASC');
            }
            if($sortby=="doj_desc"){
                $sortArr  = array('created' => 'DESC');
            }
            if($sortby=="rides_asc"){
                $sortArr  = array('no_of_rides' => 'ASC');
            }
            if($sortby=="rides_desc"){
                $sortArr  = array('no_of_rides' => 'DESC');
            }
        }
        $this->data['sortby'] = $sortby;
        
        $filterArr = array();
        $filterCondition = array();
        if (isset($_GET['type']) && (isset($_GET['value']) || isset($_GET['vehicle_category'])) && $_GET['type'] != '' && ($_GET['value'] != '' || $_GET['vehicle_category'] != '')) {
                if (isset($_GET['type']) && $_GET['type'] != '') {
                    $this->data['type'] = $_GET['type'];
                }
                if (isset($_GET['value']) && $_GET['value'] != '') {
                    $this->data['value'] = $_GET['value'];
                    $filter_val = $this->data['value'];
                }
                $this->data['filter'] = 'filter';
                $filterCondition = array();
                if($_GET['type'] == 'vehicle_type'){
                    $vehicle_category = trim($_GET['vehicle_category']);
                    $categoryVal=$this->user_model->get_all_details(CATEGORY,'','','','',array('name'=>$vehicle_category));
                    $filterCondition = array('category' => $categoryVal->row()->_id);
                } else if($_GET['type'] == 'driver_location') {
                  $location=$this->user_model->get_all_details(LOCATIONS,'','','','',array('city'=>$_GET['value']));
                  $filterArr = array($this->data['type'] => $location->row()->_id);
                } else if($_GET['type'] == 'mobile_number') {
                  #$filterCondition = array('dail_code' => $_GET['country']);
                  $filterArr = array($this->data['type'] => $filter_val,'dail_code' => $_GET['country']);
                
                }else{ 
                    $filterArr = array($this->data['type'] => $filter_val);
                }                 
            }
            
            $this->data['driversList'] = $this->driver_model->get_all_details(DRIVERS,  $filterCondition, $sortArr, '', '', $filterArr);
            $driver_array=array();
            $driver_data=array();
            if($this->data['driversList']->num_rows() > 0 ){
                foreach($this->data['driversList']->result() as $row){
                
                    $categoryVal=$this->user_model->get_selected_fields(CATEGORY,array('_id'=>$row->category),array('name','_id'));
                    $driver_array[]=$row->_id;
                    $driver_data[(string)$row->_id]=array('name'=>$row->driver_name,'catgeory_name'=>$categoryVal->row()->name);
                    #break;
                }
            }
            
            $start_date='';
            $end_date='';
            if(isset($_GET['date_range']) && $_GET['date_range']!='') {
               $start_date = strtotime($_GET['date_range'] . ' 00:00:00');
               $start_d = $_GET['date_range'];
               
            }
            if(isset($_GET['dateto']) && $_GET['dateto']!='') {
              $end_date = strtotime($_GET['dateto'] . ' 23:59:59');
              $end_d = $_GET['dateto'];
               
            }
            $mileage_data=$this->mileage_model->get_mileage_list($driver_array,$start_date,$end_date);
            
            $final_Arr=array();
            foreach($mileage_data as $key=>$data) {
            
                $final_Arr[$key]=array_merge($data,$driver_data[$key]);
                    
            }
            $limit = 1000;
            
            $no_of_rows = count($final_Arr);
            $no_of_sheets = floor($no_of_rows/$limit);
            if($no_of_rows%$limit > 0){
                $no_of_sheets++;
            }
            $headers_array = array('Driver Name','Category','Total Distance','Total Duration [Min(s)]','Free Roaming Distance '.$this->data['d_distance_unit_name'],'Free Roaming Duration [Min(s)]','Pickup Distance '.$this->data['d_distance_unit_name'],'Pickup Duration [Min(s)]','Drop Distance '.$this->data['d_distance_unit_name'],'Drop Duration [Min(s)]');
            $next_limit = 0;
            for($i=0; $i<$no_of_sheets; $i++){
                $this->excel->setActiveSheetIndex($i);
                $current_limit = $next_limit;
                /* Setting Header Name */
                $headerLetter = 'A';
                foreach($headers_array as $key => $val){
                $this->excel->getActiveSheet()->setCellValue($headerLetter++."1", $val);
                }
                $this->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setSize(12);
                
                /* Setting Header Name --- Ends here */
                
                $m = $i+1;
                $next_limit = $m*$limit;
                $row = 2;
                $countflag = 0;
                $final_Arr=array_values($final_Arr);
                
                foreach($final_Arr as $key=>$val){
                    if($key >= $current_limit && $key < $next_limit){
                        $contentLetter = 'A';
                        
                        $driver_name = (string)$final_Arr[$key]['name'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $driver_name);
                        $contentLetter++;
                        
                        $catgeory_name = (string)(string)$final_Arr[$key]['catgeory_name'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $catgeory_name);
                        $contentLetter++;
                        
                        $total_distance = $final_Arr[$key]['free_distance']+$final_Arr[$key]['pickup_distance']+$final_Arr[$key]['drop_distance'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $total_distance);
                        $contentLetter++;
                        
                        $total_duration = $final_Arr[$key]['free_duration']+$final_Arr[$key]['pickup_duration']+$final_Arr[$key]['drop_duration'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $total_duration);
                        $contentLetter++;
                        
                        $free_distance = (string)(string)$final_Arr[$key]['free_distance'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $free_distance);
                        $contentLetter++;
                        
                        $free_duration = (string)(string)$final_Arr[$key]['free_duration'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $free_duration);
                        $contentLetter++;
                        
                        $pickup_distance = (string)(string)$final_Arr[$key]['pickup_distance'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $pickup_distance);
                        $contentLetter++;
                        
                        $pickup_duration = (string)(string)$final_Arr[$key]['pickup_duration'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $pickup_duration);
                        $contentLetter++;
                        
                        $drop_distance = (string)(string)$final_Arr[$key]['drop_distance'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $drop_distance);
                        $contentLetter++;
                        
                        $drop_duration = (string)(string)$final_Arr[$key]['drop_duration'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $drop_duration);
                        $contentLetter++;
                        

                        $countflag = $countflag +1;
                        $row = $row +1;
                    }
                    
                    
                }
                
            /* Creating Multiple Sheets*/
            $this->excel->getActiveSheet()->setTitle('sheet'.$i);
            $this->excel->createSheet();
            
            }
            
            $filename='Mileage_report'.date("Y-m-d").'.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache
                         
            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
            
        
            
    }
    
    /**
     *
     * This function loads the mileage list page
     *
     * */
    public function view_mileage() {
        if ($this->checkLogin('A') == '') {
                $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
                redirect('admin');
            }
            if ($this->lang->line('admin_menu_mileage_list') != '') 
            $this->data['heading']= stripslashes($this->lang->line('admin_menu_mileage_list')); 
            else  $this->data['heading'] = 'Mileage List';  
            $driver_id=$this->uri->segment(4,0);
            $start_date='';
            $end_date='';
            
            if(isset($_GET['date_from']) && $_GET['date_from']!='') {
               $start_date = strtotime($_GET['date_from'] . ' 00:00:00');
               $start_d =$_GET['date_from'] ;
               
            }
            if(isset($_GET['date_to']) && $_GET['date_to']!='') {
              $end_date = strtotime($_GET['date_to'] . ' 23:59:59');
              $end_d =$_GET['date_to'] ; 
            }
            
            $ride_id='';
            if(isset($_GET['ride_id']) && $_GET['ride_id']!='') {
                $ride_id=$_GET['ride_id'];
            }
            $total_duration=0;
            $total_distance=0;
            $tot_free_distance=0;
            $tot_free_duration=0;
            $tot_pick_duration=0;
            $tot_pick_distance=0;
            $tot_drop_distance=0;
            $tot_drop_duration=0;
            $mileage_data=$this->mileage_model->view_mileage_list($driver_id,$start_date,$end_date,$ride_id);
            
            foreach($mileage_data['result'] as $row) {
            
                $total_duration+=$row['mileage_data']['duration_min'];  
                $total_distance+=$row['mileage_data']['distance'];
                if($row['mileage_data']['type']=='free-roaming'){
                    
                   $tot_free_distance+=$row['mileage_data']['distance'];
                   $tot_free_duration+=$row['mileage_data']['duration_min'];
                
                } else if($row['mileage_data']['type']=='customer-pickup') {
                
                    $tot_pick_distance+=$row['mileage_data']['distance'];
                    $tot_pick_duration+=$row['mileage_data']['duration_min'];
                    
                } else if($row['mileage_data']['type']=='customer-drop') {
                    $tot_drop_distance+=$row['mileage_data']['distance'];
                    $tot_drop_duration+=$row['mileage_data']['duration_min'];
                }
            }
            $this->data['total_duration']=$total_duration;
            $this->data['total_distance']=$total_distance;
            $this->data['tot_free_distance']=$tot_free_distance;
            $this->data['tot_pick_distance']=$tot_pick_distance;
            $this->data['tot_drop_distance']=$tot_drop_distance;
            $this->data['tot_free_duration']=$tot_free_duration;
            $this->data['tot_pick_duration']=$tot_pick_duration;
            $this->data['tot_drop_duration']=$tot_drop_duration;
            $this->data['mileage_data'] = $mileage_data;
            $this->data['start_date'] = $start_d;
            $this->data['end_date'] = $end_d;
            $this->data['ride_id'] = $ride_id;
            $this->data['driver_id'] = $driver_id;
        
           $this->load->view('admin/drivers/view_mileage_list', $this->data);
    }
    
    
    /**
     *
     * This function loads the mileage list page
     *
     * */
    public function view_mileage_list_report() {
        if ($this->checkLogin('A') == '') {
                $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
                redirect('admin');
            }
            if ($this->lang->line('admin_menu_mileage_list') != '') 
            $this->data['heading']= stripslashes($this->lang->line('admin_menu_mileage_list')); 
            else  $this->data['heading'] = 'Mileage List';  
            $driver_id=$this->uri->segment(4,0);
            $start_date='';
            $end_date='';
            
            if(isset($_GET['date_from']) && $_GET['date_from']!='') {
               $start_date = strtotime($_GET['date_from'] . ' 00:00:00');
               $start_d =$_GET['date_from'] ;
               
            }
            if(isset($_GET['date_to']) && $_GET['date_to']!='') {
              $end_date = strtotime($_GET['date_to'] . ' 23:59:59');
              $end_d =$_GET['date_to'] ; 
            }
            
            $ride_id='';
            if(isset($_GET['ride_id']) && $_GET['ride_id']!='') {
                $ride_id=$_GET['ride_id'];
            }
            $mileage_data=$this->mileage_model->view_mileage_list($driver_id,$start_date,$end_date,$ride_id);
            
            $limit = 1000;
        
            $no_of_rows = count($mileage_data['result']);
            $no_of_sheets = floor($no_of_rows/$limit);
            if($no_of_rows%$limit > 0){
                $no_of_sheets++;
            }
            $headers_array = array('S.No','From Time','To Time','Type','Duration [Min(s)]','distance '.$this->data['d_distance_unit_name']);
            $next_limit = 0;
            for($i=0; $i<$no_of_sheets; $i++){
                $this->excel->setActiveSheetIndex($i);
                $current_limit = $next_limit;
                /* Setting Header Name */
                $headerLetter = 'A';
                foreach($headers_array as $key => $val){
                $this->excel->getActiveSheet()->setCellValue($headerLetter++."1", $val);
                }
                $this->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('A1:'.$headerLetter.'1')->getFont()->setSize(12);
                
                /* Setting Header Name --- Ends here */
                
                $m = $i+1;
                $next_limit = $m*$limit;
                $row = 2;
                $countflag = 0;
                $final_Arr=array_values($final_Arr);
                $sno=1;
                foreach($mileage_data['result'] as $key=>$val){
                
                
                 
                    if($key >= $current_limit && $key < $next_limit){
                        $contentLetter = 'A';
                        
                        $sno = (string)$sno;
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $sno);
                        $contentLetter++;
                        
                        $from_time=date('h:i:s A',$mileage_data['result'][$key]['mileage_data']['start_time']->sec);
                        
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $from_time);
                        $contentLetter++;
                        
                        $end_time=date('h:i:s A',$mileage_data['result'][$key]['mileage_data']['end_time']->sec);
                        
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $end_time);
                        $contentLetter++;
                        $ride_id='';
                        if(isset($mileage_data['result'][$key]['mileage_data']['ride_id']) && $mileage_data['result'][$key]['mileage_data']['ride_id']!='') {
                            
                            $ride_id='('.$mileage_data['result'][$key]['mileage_data']['ride_id'].')';
                                            
                        }
                        
                        
                        $type = $mileage_data['result'][$key]['mileage_data']['type'].' '.$ride_id;
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $type);
                        $contentLetter++;
                        
                        
                        $duration_min = $mileage_data['result'][$key]['mileage_data']['duration_min'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $duration_min);
                        $contentLetter++;
                        
                        $distance = $mileage_data['result'][$key]['mileage_data']['distance'];
                        $this->excel->getActiveSheet()->setCellValue($contentLetter.$row, $distance);
                        $contentLetter++;
                        
                        

                        $countflag = $countflag +1;
                        $row = $row +1;
                        $sno = $sno +1;
                    }
                    
                    
                }
                
            /* Creating Multiple Sheets*/
            $this->excel->getActiveSheet()->setTitle('sheet'.$i);
            $this->excel->createSheet();
            
            }
            
            $filename='Mileage_report_view'.date("Y-m-d").'.xls'; //save our workbook as this file name
            header('Content-Type: application/vnd.ms-excel'); //mime type
            header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
            header('Cache-Control: max-age=0'); //no cache
                         
            //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
            //if you want to save it as .XLSX Excel 2007 format
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
            //force user to download the Excel file without writing it to server's HD
            $objWriter->save('php://output');
            
            
     }
    /**
     *
     * This function loads the drivers add form
     *
     * */
    public function add_driver_form() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }
        if ($this->lang->line('driver_add_new_driver') != '') 
        $this->data['heading']= stripslashes($this->lang->line('driver_add_new_driver')); 
        else  $this->data['heading'] = 'Add New Driver';    
        $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, array('status' => 'Active'));
        $this->data['docx_list'] = $this->driver_model->get_all_details(DOCUMENTS, array('status' => 'Active'));
        $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, array('status' => 'Active'), array('brand_name' => 'ASC'));
        $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['locationList'] = $this->driver_model->get_all_details(LOCATIONS, array('status' => 'Active'), array('city' => 'ASC'));
        $this->load->view('admin/drivers/add_driver', $this->data);
    }

    /**
     *
     * This function loads the drivers edit form
     *
     * */
    public function edit_driver_form() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }
        $driver_id = $this->uri->segment(4);
                                 if ($this->lang->line('dash_edit_driver') != '') 
               $this->data['heading']= stripslashes($this->lang->line('dash_edit_driver')); 
              else  $this->data['heading'] = 'Edit Driver';
        $condition = array('_id' => new \MongoId($driver_id));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
        if ($driver_details->num_rows() == 0) {
            $this->setErrorMessage('error', 'No record found for this driver','admin_driver_no_record_found');
            redirect('admin/drivers/display_drivers_list');
        }
        $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, array('status' => 'Active'));
        $this->data['docx_list'] = $this->driver_model->get_all_details(DOCUMENTS, array('status' => 'Active'));
        $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, array('status' => 'Active'), array('brand_name' => 'ASC'));
        $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['locationList'] = $this->driver_model->get_all_details(LOCATIONS, array('status' => 'Active'), array('city' => 'ASC'));
        $this->load->view('admin/drivers/edit_driver', $this->data);
    }

    /**
     *
     * This function Inserts & Edits the drivers
     *
     * */
    public function insertEdit_driver() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }
        /**
         * clear the temp folders
         */
        $driver_id = $this->input->post('driver_id');

        $dir = getcwd() . "/drivers_documents_temp"; //dir absolute path
        $interval = strtotime('-24 hours'); //files older than 24hours
        foreach (glob($dir . "*.*") as $file) {
            if (filemtime($file) <= $interval) {
                unlink($file);
            }
        }

        #echo '<pre>'; print_r($_POST); die;

        if ($this->input->post('email') == '') {
            $this->setErrorMessage('error', 'Some of the fields are missing','admin_driver_field_missing');
            redirect('admin/drivers/add_driver_form');
        }

        $email = strtolower($this->input->post('email'));

        if ($driver_id == '') {
            $checkEmail = $this->driver_model->check_driver_exist(array('email' => $this->input->post('email')));
            if ($checkEmail->num_rows() >= 1) {
                $this->setErrorMessage('error', 'This email already exist, please register with different email address.','admin_driver_register_different_email');
                redirect('admin/drivers/display_drivers_list');
            }
        }
        
        
        
        $old_number = '';
        $mobile_number = $this->input->post('mobile_number');
        if($driver_id != ''){
            $checkDriver = $this->driver_model->get_selected_fields(DRIVERS,array('_id'=>new MongoId($driver_id)),array('_id','mobile_number'));
            $old_number = $checkDriver->row()->mobile_number;
        }
        if($old_number  != $mobile_number){
            $checkMobile = $this->driver_model->get_selected_fields(DRIVERS,array('mobile_number'=>$mobile_number),array('_id','mobile_number'));
            if ($checkMobile->num_rows() >= 1) {
                $this->setErrorMessage('error', 'This mobile number already exist, please register with different mobile number.','admin_driver_register_mobile_number_exist');
                redirect('admin/drivers/display_drivers_list');
            }
        }
        


        if ($this->input->post('status') == 'on') {
            $status = 'Active';
        } else {
            $status = 'Inactive';
        }
        if ($this->input->post('ac') == 'on') {
            $ac = 'Yes';
        } else {
            $ac = 'No';
        }

        $excludeArr = array("driver_id", "confirm_password", "new_password", "address", "county", "state", "city", "postal_code", "driver_docx", "driver_docx_expiry", "vehicle_docx", "vehicle_docx_expiry", "vehicle_type", "mobile_number", "dail_code", 'ac', "email");

        $addressArr['address'] = array('address' => $this->input->post('address'),
            'county' => $this->input->post('county'),
            'state' => $this->input->post('state'),
            'city' => $this->input->post('city'),
            'postal_code' => $this->input->post('postal_code')
        );

        $image_data = array();

        if ($_FILES['thumbnail']['name'] != '') {
            $config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = 'jpg|jpeg|gif|png';
            $config['max_size'] = 2000;
            $config['upload_path'] = './images/users';
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('thumbnail')) {
                $logoDetails = $this->upload->data();
                $this->ImageResizeWithCrop(600, 600, $logoDetails['file_name'], './images/users/');
                @copy('./images/users/' . $logoDetails['file_name'], './images/users/thumb/' . $logoDetails['file_name']);
                $this->ImageResizeWithCrop(210, 210, $logoDetails['file_name'], './images/users/thumb/');
                $profile_image = $logoDetails['file_name'];
                $image_data['image'] = $logoDetails['file_name'];
            } else {
                $logoDetails = $this->upload->display_errors();
                $this->setErrorMessage('error', $logoDetails);
                echo "<script>window.history.go(-1);</script>";
                exit;
            }
        }

        /*         * *
         *
         * document section 
         */
        $documents = array();
        $dr_documentArr = $this->input->post('driver_docx');  #echo '<pre>'; print_r($dr_documentArr); die;
        $dr_expiryArr = $this->input->post('driver_docx_expiry');
        for ($i = 0; $i < count($dr_documentArr); $i++) {
            $fileArr = @explode('|:|', $dr_documentArr[$i]);
            $fileArr = array_filter($fileArr);
            if (count($fileArr) > 0) {
                $docxName = $fileArr[0];
                $fileName = $fileArr[1];
                $fileTypeId = new \MongoId($fileArr[2]);
                if ($docxName != '' && $fileName != '' && count($fileArr) == 3) {
                    @copy('./drivers_documents_temp/' . $fileName, './drivers_documents/' . $fileName);
                }
                if ($dr_expiryArr[$i] == 'Yes') {
                    $expiryDate = $this->input->post('driver-' . url_title($docxName));
                    $excludeArr[] = url_title('driver-' . $docxName);
                } else {
                    $expiryDate = '';
                }
                if($docxName != '' || $fileTypeId != ''){
                    if ($driver_id == '') {
                        $documents['driver'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate, 'verify_status' => 'No');
                    } else {
                        $documents['driver'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate);
                    }
                }
            }
        }


        $veh_documentArr = $this->input->post('vehicle_docx');  #echo '<pre>'; print_r($veh_documentArr); die;
        $veh_expiryArr = $this->input->post('vehicle_docx_expiry');
        for ($i = 0; $i < count($veh_documentArr); $i++) {
            $fileArr = @explode('|:|', $veh_documentArr[$i]);
            $docxName = $fileArr[0];
            $fileName = $fileArr[1];
            $fileTypeId = new \MongoId($fileArr[2]);
            if ($docxName != '' && $fileName != '' && count($fileArr) == 3) {
                @copy('./drivers_documents_temp/' . $fileName, './drivers_documents/' . $fileName);
            }
            if ($veh_expiryArr[$i] == 'Yes') {
                $expiryDate = $this->input->post('vehicle-' . url_title($docxName));
                $excludeArr[] = 'vehicle-' . url_title($docxName);
            } else {
                $expiryDate = '';
            }
            if($docxName != '' || $fileTypeId != ''){
                if ($driver_id == '') {
                    $documents['vehicle'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate, 'verify_status' => 'No');
                } else {
                    $documents['vehicle'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate);
                }
            }
        }

        $driver_data = array('created' => date('Y-m-d H:i:s'),
            'password' => md5($this->input->post('password')),
            'email' => $email,
            'vehicle_type' => new \MongoId($this->input->post('vehicle_type')),
            'status' => $status,
            'ac' => $ac,
            'no_of_rides' => 0,
            'availability' => 'No',
            'mode' => 'Available',
            'dail_code' => (string) $this->input->post('dail_code'),
            'mobile_number' => (string) $this->remove_leading_zeros($this->input->post('mobile_number')),
            'category' => new \MongoId($this->input->post('category'))
        );
        $cond=array('_id'=> new \MongoId($this->input->post('driver_location')));
        $get_loc_commison = $this->driver_model->get_selected_fields(LOCATIONS,$cond,array('site_commission','currency','currency_symbol'));
        if($this->input->post('driver_commission') == ''){
            
            if(isset($get_loc_commison->row()->site_commission)){ 
                $driver_data['driver_commission'] = floatval($get_loc_commison->row()->site_commission);
            }
            
        }
        $currencyCode = $this->data['dcurrencyCode'];
        $currencySymbol = $this->data['dcurrencySymbol'];
        if(isset($get_loc_commison->row()->currency)){
            $currencyCode  = $get_loc_commison->row()->currency;
            $currencySymbol  = $get_loc_commison->row()->currency_symbol;
            if($currencySymbol  == '') $currencySymbol = $currencyCode;
        }
        $driver_data['currency']  = $currencyCode;
        $driver_data['currency_symbol']  = $currencySymbol;
        
        if ($driver_id != '') {
            unset($driver_data['no_of_rides']);
            unset($driver_data['availability']);
            unset($driver_data['password']);
            unset($driver_data['mode']);
            unset($driver_data['created']);
            unset($driver_data['email']);
        }

        $dataArr = array_merge($driver_data, $image_data, $addressArr, array('documents' => $documents));  
        #echo '<pre>'; print_r($dataArr); die;
        if ($driver_id == '') {
            $condition = array();
            $this->driver_model->commonInsertUpdate(DRIVERS, 'insert', $excludeArr, $dataArr, $condition);

            $last_insert_id = $this->cimongo->insert_id();
            $fields = array(
                'username' => (string) $last_insert_id,
                'password' => md5((string) $last_insert_id)
            );
            $url = $this->data['soc_url'] . 'create-user.php';
            $this->load->library('curl');
            $output = $this->curl->simple_post($url, $fields);
            
            
            /*  send welcome email to driver */
            $post_data = array('driver_id' =>  (string)$last_insert_id ); #echo '<pre>'; print_r($post_data);
            $url = base_url().'welcome-mail';
            $this->curl->simple_post($url, $post_data);
            

            /* Update Stats Starts */
            $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
            $field = array('driver.hour_' . date('H') => 1, 'driver.count' => 1);
            $this->driver_model->update_stats(array('day_hour' => $current_date), $field, 1);
            /* Update Stats End */
            $this->setErrorMessage('success', 'Driver added successfully','admin_driver_added_success');
        } else {
            $excludeArr[] = 'promo_code';
            $condition = array('_id' => new \MongoId($driver_id));
            $this->driver_model->commonInsertUpdate(DRIVERS, 'update', $excludeArr, $dataArr, $condition);
            $this->setErrorMessage('success', 'Driver details updated successfully','admin_driver_updated_success');
        }
        redirect('admin/drivers/display_drivers_list');
    }

    /**
     *
     * This function uploads the documents via ajax for driver add & edit 
     *
     * */
    public function ajax_document_upload() {

        $docx_name = $this->input->get('docx_name');
        $docResult = array();


        $path = "drivers_documents_temp/";
        $imgRnew = @explode('.', $_FILES[$docx_name]["name"]);
        $NewImg = url_title($imgRnew[0], '-', TRUE) . '-' . time();
        $fileName = urlencode($NewImg);

        $extension = $imgRnew[count($imgRnew) - 1];


        $max_file_size = 2097152;
        $allowed = array("image/jpeg", "image/jpg", "image/png", "image/gif", "application/pdf");
        $file_type = $_FILES[$docx_name]['type'];
        $file_size = $_FILES[$docx_name]['size'];
        $filetmpName = $fileName . '.' . $extension;

        if ($filetmpName != '') {
            if (in_array($file_type, $allowed)) {
                if ($_FILES[$docx_name]["size"] < $max_file_size) {
                    $uploadsdocx = move_uploaded_file($_FILES[$docx_name]["tmp_name"], $path . $filetmpName);
                    if ($uploadsdocx == true) {
                        $docResult['docx_name'] = $filetmpName;
                        $docResult['err_msg'] = 'Success';
                    } else {
                        $docResult['err_msg'] = 'File could not be uploaded';
                    }
                } else {
                    $docResult['err_msg'] = 'File too large. File must be less than 2 megabytes.';
                }
            } else {
                $docResult['err_msg'] = 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
            }
        }

        echo json_encode($docResult);
    }

    /**
     *
     * This function change the driver status
     *
     * */
    public function change_driver_vrification_status() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $mode = $this->uri->segment(4, 0);
            $driver_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'No' : 'Yes';
            $newdata = array('verify_status' => $status);
            $condition = array('_id' => new \MongoId($driver_id));
            $this->driver_model->update_details(DRIVERS, $newdata, $condition);
            $this->setErrorMessage('success', 'Driver Verification Status Changed Successfully','admin_driver_verification_status');
            redirect('admin/drivers/display_drivers_list');
        }
    }
    
    
   

    /**
     *
     * This function change the driver status
     *
     * */
    public function change_driver_status() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $mode = $this->uri->segment(4, 0);
            $driver_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => new \MongoId($driver_id));
            $this->driver_model->update_details(DRIVERS, $newdata, $condition);
            $this->setErrorMessage('success', 'Driver Status Changed Successfully','admin_driver_status_changed');
            redirect('admin/drivers/display_drivers_list');
        }
    }

    public function unblock() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $driver_id = $this->uri->segment(4, 0);
            $newdata = array(
                'outstanding_amount.is_due' => false,
                'outstanding_amount.updated_on' => new MongoDate(time())
            );
            $condition = array('_id' => new \MongoId($driver_id));
            $this->driver_model->update_details(DRIVERS, $newdata, $condition);
            $this->setErrorMessage('success', 'Driver Unblocked Successfully','admin_driver_unblocked');
            redirect('admin/drivers/display_drivers_list');
        }
    }

    /**
     * 
     * This function delete the driver from db
     *
     * */
    public function delete_driver() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $promo_id = $this->uri->segment(4, 0);
            $condition = array('_id' => new \MongoId($promo_id));
            $this->driver_model->commonDelete(DRIVERS, $condition);
            $this->setErrorMessage('success', 'Driver deleted successfully','admin_driver_deleted_changed');
            redirect('admin/drivers/display_drivers_list');
        }
    }

    /**
     * 
     * This function change the driver status
     *
     * */
    public function change_driver_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->driver_model->activeInactiveCommon(DRIVERS, '_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'Driver deleted successfully','admin_driver_deleted_changed');
            } else {
                $this->setErrorMessage('success', 'Driver status changed successfully','admin_driver_status_change');
            }
            redirect('admin/drivers/display_drivers_list');
        }
    }

    /**
     * 
     * This function loads the driver password
     *
     * */
    public function change_password_form() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        }
        $driverId = $this->uri->segment(4);
                                if ($this->lang->line('driver_change_password') != '') 
              $this->data['heading']= stripslashes($this->lang->line('driver_change_password')); 
              else  $this->data['heading'] = 'Change Driver Password';
        $condition = array('_id' => new \MongoId($driverId));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
        $this->load->view('admin/drivers/change_password', $this->data);
    }

    /**
     * 
     * This function loads the driver password
     *
     * */
    public function change_password() {
        if ($this->checkLogin('A') == '' || $this->input->post('new_password') == '') {
            redirect('admin');
        }
        $password = $this->input->post('new_password');
        $driverId = $this->input->post('driver_id');
        $dataArr = array('password' => md5($this->input->post('new_password')));
        $condition = array('_id' => new \MongoId($driverId));
        $driver_details = $this->driver_model->update_details(DRIVERS, $dataArr, $condition);

        /*         * **  send password to driver through email **** */
        $driverinfo = $this->driver_model->get_all_details(DRIVERS, $condition);
        $this->send_driver_pwd($password, $driverinfo);

        $this->setErrorMessage('success', 'Driver password changed and sent to driver successfully','admin_driver_password_changed');
        redirect('admin/drivers/display_drivers_list');
    }

    /**
     * 
     * This function send the new password to driver email
     *
     * */
    public function send_driver_pwd($pwd = '', $driverinfo) {
        $default_lang=$this->config->item('default_lang_code');
        $driver_name = $driverinfo->row()->driver_name;
        $newsid = '2';
        $template_values = $this->driver_model->get_email_template($newsid,$default_lang);
        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
        extract($adminnewstemplateArr);
        $message = '<!DOCTYPE HTML>
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width"/>
            <title>' . $template_values['subject'] . '</title>
            <body>';
        include($template_values['templateurl']);
        $message .= '</body>
            </html>';
        $sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $driverinfo->row()->email,
            'subject_message' => $template_values['subject'],
            'body_messages' => $message
        );
        $email_send_to_common = $this->driver_model->common_email_send($email_values);
    }

    /**
     * 
     * This function load's the driver view 
     *
     * */
    public function view_driver() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }
        $driverId = $this->uri->segment(4);
                                if ($this->lang->line('driver_view_details') != '') 
                                $this->data['heading']= stripslashes($this->lang->line('driver_view_details')); 
                                else  $this->data['heading'] = 'View Driver Details';
        $condition = array('_id' => new \MongoId($driverId));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);

        if ($driver_details->num_rows() == 0) {
            $this->setErrorMessage('error', 'No records found','admin_driver_no_records_found');
            redirect('admin/drivers/display_drivers_list');
        }

        $veh_condition = array('_id' => new \MongoId($driver_details->row()->vehicle_type));
        $this->data['vehicle_types'] = $vehicle_types = $this->driver_model->get_all_details(VEHICLES, $veh_condition);

        $cat_condition = array('_id' => new \MongoId($driver_details->row()->category));
        $this->data['driver_category'] = $driver_category = $this->driver_model->get_all_details(CATEGORY, $cat_condition);

        $maker_condition = array('_id' => new \MongoId($driver_details->row()->vehicle_maker));
        $this->data['vehicle_maker'] = $vehicle_maker = $this->driver_model->get_all_details(BRAND, $maker_condition);

        $vehicle_model_model = array('_id' => new \MongoId($driver_details->row()->vehicle_model));
        $this->data['vehicle_model'] = $vehicle_model = $this->driver_model->get_all_details(MODELS, $vehicle_model_model);

        $this->load->view('admin/drivers/view_driver', $this->data);
    }

    /**
     * 
     * This function updates the drivers document verification status via ajax
     *
     * */
    public function document_verify_status_ajax() {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            echo 'error';
            redirect('admin');
        }
        $driverId = $this->input->get('driverId');
        $docxId = $this->input->get('docxId');
        $docxType = $this->input->get('docxType');

        if ($this->input->get('docx_state') == 'Verify') {
            $docx_state = 'Yes';
        } else {
            $docx_state = 'No';
        }
        $dataArr = array("documents." . $docxType . "." . $docxId . ".verify_status" => $docx_state);
        $condition = array('_id' => new \MongoId($driverId));
        $this->data['driver_details'] = $driver_details = $this->driver_model->update_details(DRIVERS, $dataArr, $condition);
        echo 'Success';
    }

    /**
     *
     * This function Displays the Driver Category List
     *
     * */
    public function display_drivers_category() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            if ($this->lang->line('driver_drivers_category') != '') 
            $this->data['heading']= stripslashes($this->lang->line('driver_drivers_category')); 
            else  $this->data['heading'] = 'Drivers Category';
            $condition = array();
            $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, $condition);
            $this->load->view('admin/drivers/display_drivers_category', $this->data);
        }
    }

    /**
     *
     * This function loads the add/Edit Category form
     *
     * */
    public function add_edit_category() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $category_id = $this->uri->segment(4, 0);
            $form_mode = FALSE;
            if ($this->lang->line('driver_add_new_category') != '') 
            $this->data['heading']= stripslashes($this->lang->line('driver_add_new_category')); 
            else  $this->data['heading'] = 'Add New Category';
            if ($category_id != '') {
                $condition = array('_id' => new \MongoId($category_id));
                $this->data['categorydetails'] = $this->driver_model->get_all_details(CATEGORY, $condition);
                if ($this->data['categorydetails']->num_rows() != 1) {
                    redirect('admin/drivers/display_drivers_category');
                }
                $form_mode = TRUE;
                                                                    if ($this->lang->line('driver_edit_category') != '') 
                                         $heading= stripslashes($this->lang->line('driver_edit_category')); 
                                        else  $heading = 'Edit Category'; 
            }
            $this->data['form_mode'] = $form_mode;
            
            $this->load->view('admin/drivers/add_edit_category', $this->data);
        }
    }

    /**
     *
     * This function insert vehicle informations into databse
     *
     * */
    public function insertEditCategory() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $category_id = $this->input->post('category_id');
            $name = $this->input->post('name');

            if ($category_id == '') {
                $condition = array('name' => $name);
                $duplicate_name = $this->driver_model->get_selected_fields(CATEGORY, $condition, array('name'));
                if ($duplicate_name->num_rows() > 0)
                    $isDuplicate = TRUE;
            }else {
                $condition = array('name' => $name);
                $duplicate_name = $this->driver_model->get_selected_fields(CATEGORY, $condition, array('name'));
                if ($duplicate_name->num_rows() > 1)
                    $isDuplicate = TRUE;
            }

            if ($isDuplicate) {
                $this->setErrorMessage('error', 'Category already exists','admin_driver_category_already_exist');
                redirect('admin/drivers/add_edit_category/' . $category_id);
            }

            $excludeArr = array("status", "image");

            if ($this->input->post('status') != '') {
                $status = 'Active';
            } else {
                $status = 'Inactive';
            }
            if ($this->input->post('isdefault') != '' || $this->input->post('isdefault') == 'on') {
                $isdefault = 'Yes';
                $this->driver_model->update_details(CATEGORY, array('isdefault' => 'No'), array());
            } else {
                $isdefault = 'No';
            }

            $inputArr = array('name' => $name,
                'status' => $status,
                'isdefault' => $isdefault,
                'created' => date('Y-m-d H:i:s')
            );

            if ($_FILES['image']['name'] != '') {
                $config['encrypt_name'] = TRUE;
                $config['overwrite'] = FALSE;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['max_size'] = 2000;
                $config['upload_path'] = './images/category/';
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('image')) {
                    $imageDetails = $this->upload->data();
                    $category_image = $imageDetails['file_name'];
                } else {
                    $imageDetails = $this->upload->display_errors();
                    $this->setErrorMessage('error', $imageDetails);
                    redirect('admin/drivers/add_edit_category/' . $category_id);
                }
                $vehicle_data = array('image' => $category_image);
            } else {
                $vehicle_data = array();
            }
            if ($_FILES['icon_normal']['name'] != '') {
                $config['encrypt_name'] = TRUE;
                $config['overwrite'] = FALSE;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['max_size'] = 2000;
                $config['upload_path'] = './images/icons/';
                $this->load->library('upload', $config);
                
                $image_info = getimagesize($_FILES["icon_normal"]["tmp_name"]);
                $image_width = $image_info[0];
                $image_height = $image_info[1];
                if($image_width == 150 && $image_height == 150){
                     if ($this->upload->do_upload('icon_normal')) {
                    $imageDetails = $this->upload->data();
                    $icon_normal = $imageDetails['file_name'];
                    } else {
                        $imageDetails = $this->upload->display_errors();
                        $this->setErrorMessage('error', $imageDetails);
                        redirect('admin/drivers/add_edit_category/' . $category_id);
                    }
                }else{
                    $this->setErrorMessage('error',"Image size should be 150 X 150 Pixels",'admin_driver_image_size');
                    redirect('admin/drivers/add_edit_category/' . $category_id);
                }
               
                $vehicle_data['icon_normal'] = $icon_normal;
            }
            if ($_FILES['icon_active']['name'] != '') {
                $config['encrypt_name'] = TRUE;
                $config['overwrite'] = FALSE;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['max_size'] = 2000;
                $config['upload_path'] = './images/icons/';
                $this->load->library('upload', $config);
                
                $image_info = getimagesize($_FILES["icon_active"]["tmp_name"]);
                $image_width = $image_info[0];
                $image_height = $image_info[1];
                if($image_width == 150 && $image_height == 150){
                     if ($this->upload->do_upload('icon_active')) {
                    $imageDetails = $this->upload->data();
                    $icon_active = $imageDetails['file_name'];
                    } else {
                        $imageDetails = $this->upload->display_errors();
                        $this->setErrorMessage('error', $imageDetails);
                        redirect('admin/drivers/add_edit_category/' . $category_id);
                    }
                }else{
                    $this->setErrorMessage('error',"Image size should be 150 X 150 Pixels",'admin_driver_image_size');
                    redirect('admin/drivers/add_edit_category/' . $category_id);
                }
               
                $vehicle_data['icon_active'] = $icon_active;
            }
            if ($_FILES['icon_car_image']['name'] != '') {
                $config['encrypt_name'] = TRUE;
                $config['overwrite'] = FALSE;
                $config['allowed_types'] = 'jpg|jpeg|gif|png|bmp';
                $config['max_size'] = 2000;
                $config['upload_path'] = './images/icons/';
                $this->load->library('upload', $config);
                
                $image_info = getimagesize($_FILES["icon_car_image"]["tmp_name"]);
                $image_width = $image_info[0];
                $image_height = $image_info[1];
                if($image_width == 70 && $image_height == 70){
                     if ($this->upload->do_upload('icon_car_image')) {
                    $imageDetails = $this->upload->data();
                    $icon_car_image = $imageDetails['file_name'];
                    } else {
                        $imageDetails = $this->upload->display_errors();
                        $this->setErrorMessage('error', $imageDetails);
                        redirect('admin/drivers/add_edit_category/' . $category_id);
                    }
                }else{
                    $this->setErrorMessage('error',"Image size should be 70 X 70 Pixels",'admin_driver_image_size_pixel');
                    redirect('admin/drivers/add_edit_category/' . $category_id);
                }
               
                $vehicle_data['icon_car_image'] = $icon_car_image;
            }

            $dataArr = array_merge($inputArr, $vehicle_data);

            if ($category_id == '') {
                $this->driver_model->simple_insert(CATEGORY, $dataArr);
                $this->setErrorMessage('success', 'Category added successfully','admin_driver_category_added_successfully');
            } else {
                $condition = array('_id' => new \MongoId($category_id));
                $this->driver_model->update_details(CATEGORY, $dataArr, $condition);
                $this->setErrorMessage('success', 'Category updated successfully','admin_driver_category_updated_successfully');
            }
            redirect('admin/drivers/display_drivers_category');
        }
    }

    /**
     *
     * This function change the status of the Driver Category
     *
     * */
    public function change_category_status() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $mode = $this->uri->segment(4, 0);
            $category_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => new \MongoId($category_id));
            $this->driver_model->update_details(CATEGORY, $newdata, $condition);
            $this->setErrorMessage('success', 'Category Status Changed Successfully','admin_driver_category_status_change');
            redirect('admin/drivers/display_drivers_category');
        }
    }

    /**
     * 
     * This function delete the category from db
     *
     * */
    public function delete_category() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $category_id = $this->uri->segment(4, 0);
            $condition = array('_id' => new \MongoId($category_id));
            $this->driver_model->commonDelete(CATEGORY, $condition);
            $this->setErrorMessage('success', 'Category deleted successfully','admin_driver_category_deleted_success');
            redirect('admin/drivers/display_drivers_category');
        }
    }

    /**
     *
     * This function change the status of the Driver Category globally
     *
     * */
    public function change_category_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->driver_model->activeInactiveCommon(CATEGORY, '_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'Category records deleted successfully','admin_driver_category_records_deleted_success');
            } else {
                $this->setErrorMessage('success', 'Category records status changed successfully','admin_driver_category_records_status_change');
            }
            redirect('admin/drivers/display_drivers_category');
        }
    }

    /**
     *
     * This function loads the add/Edit vehicle types in a category
     *
     * */
    public function add_edit_category_types() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $category_id = $this->uri->segment(4, 0);
            if ($category_id != '') {
                $condition = array('_id' => new \MongoId($category_id));
                $this->data['categorydetails'] = $this->driver_model->get_all_details(CATEGORY, $condition);
                $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, array('status' => 'Active'));
                if ($this->data['categorydetails']->num_rows() != 1) {
                    redirect('admin/drivers/display_drivers_category');
                }
            }
                                                if ($this->lang->line('admin_error_msg_vehicle_type_category') != '') 
                  $this->data['heading']= stripslashes($this->lang->line('admin_error_msg_vehicle_type_category')); 
                  else  $this->data['heading'] = 'Vehicle types under category';
            $this->load->view('admin/drivers/add_edit_category_types', $this->data);
        }
    }

    /**
     *
     * This function iadd/Edit vehicle types in a category informations into databse
     *
     * */
    public function insertEditTypes() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $category_id = $this->input->post('category_id');
            if ($category_id != '' && $this->input->post('vehicle_type') != NULL) {
                $vehicle_type = $this->input->post('vehicle_type');
                $dataArr = array('vehicle_type' => $vehicle_type);
                $condition = array('_id' => new \MongoId($category_id));
                $this->driver_model->update_details(CATEGORY, $dataArr, $condition);
                $this->setErrorMessage('success', 'Category\'s types updated successfully','admin_driver_category_update_sccess');
                redirect('admin/drivers/display_drivers_category');
            } else {
                $this->setErrorMessage('error', 'Category\'s types cannot be updated','admin_driver_category_not_update_sccess');
                redirect('admin/drivers/add_edit_category_types/' . $category_id);
            }
        }
    }

    /**
     *
     * This function loads the add/Edit Banking Informations form
     *
     * */
    public function banking() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $driver_id = $this->uri->segment(4, 0);
            $form_mode = FALSE;
                    if ($this->lang->line('dash_add_banking_details') != '') 
                  $this->data['heading']= stripslashes($this->lang->line('dash_add_banking_details')); 
                  else  $this->data['heading'] = 'Add Banking Details';
            if ($driver_id != '') {
                $condition = array('_id' => new \MongoId($driver_id));
                $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
                if ($this->data['driver_details']->num_rows() != 1) {
                    redirect('admin/drivers/display_drivers_list');
                }
                $form_mode = TRUE;
                                                                if ($this->lang->line('dash_edit_banking_details') != '') 
                      $heading = stripslashes($this->lang->line('dash_edit_banking_details')); 
                      else  $heading = 'Edit Banking Details';
            }
            $this->data['form_mode'] = $form_mode;
            $this->data['heading'] = $heading;
            $this->load->view('admin/drivers/add_edit_banking', $this->data);
        }
    }

    /**
     *
     * This function inserts / edit the driver banking informations
     * */
    public function insertEditDriverBanking() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        }
        $driver_id = $this->input->post('driver_id');
        $postedValues = $_POST;
        unset($postedValues['driver_id']);

        $dataArr = array('banking' => $postedValues);  #echo '<pre>'; print_r($dataArr);
        $condition = array('_id' => new \MongoId($driver_id));
        $this->driver_model->update_details(DRIVERS, $dataArr, $condition);
        $this->setErrorMessage('success', 'Driver banking details updated successfully','admin_driver_banking_details_update');
        redirect('admin/drivers/banking/' . $driver_id);
    }
    
     /**
     *
     * This function change the driver status
     *
     * */
    public function change_driver_mode_status() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $driver_id = $this->uri->segment(5, 0);
            
        //  Check driver last ride status
        $dri_LastRide_satate = $this->driver_model->get_driver_last_ride_status($driver_id );
        
        $doAction = FALSE;
        if($dri_LastRide_satate->num_rows() == 0){
            $doAction = TRUE;
        }
        if(isset($dri_LastRide_satate->row()->ride_status) && ($dri_LastRide_satate->row()->ride_status == 'Completed' ||  $dri_LastRide_satate->row()->ride_status == 'Finished' || $dri_LastRide_satate->row()->ride_status == 'Cancelled') ){
            $doAction = TRUE;
        }
        
        if($doAction){
              $newdata = array('mode' => 'Available');
              $condition = array('_id' => new \MongoId($driver_id));
              $this->driver_model->update_details(DRIVERS, $newdata, $condition);
              $this->setErrorMessage('success', 'Driver Verification Status Changed Successfully','admin_driver_verification_status');
           } else {
                $this->setErrorMessage('error', 'Sorry! This driver is on ride,  You can not make him available right now','this_driver_on_ride_make_him_available_now');
          }
             redirect('admin/drivers/display_drivers_list');
        }
    }
    public function edit_language_category(){
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $category_id = $this->uri->segment(4, 0);
            if ($category_id != '') {
                $condition = array('_id' => new \MongoId($category_id));
                $this->data['categorydetails'] = $categorydetails = $this->driver_model->get_all_details(CATEGORY, $condition);
                $this->data['languagesList'] = $this->driver_model->get_all_details(LANGUAGES, array('status' => 'Active'));
                #echo '<pre>'; print_r( $this->data['languagesList']->result()); die;
                if ($this->data['categorydetails']->num_rows() != 1) {
                    redirect('admin/drivers/display_drivers_category');
                }
            }
            
            if ($this->lang->line('edit_category_language') != '') 
            $heading = stripslashes($this->lang->line('edit_category_language')); 
            else  $heading = 'Edit category language';
             
             $this->data['heading'] = $heading;
            $this->load->view('admin/drivers/edit_category_language_form', $this->data);
        }
    }
    
    public function update_language_content(){
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        }  
        $language_content = $this->input->post('name_languages');  
        $category_id = $this->input->post('category_id');
        $updCond = array('_id' => new MongoId($category_id ));
        $dataArr = array('name_languages' => $language_content);  #echo '<pre>'; print_r($dataArr ); die;
        $this->driver_model->update_details(CATEGORY,$dataArr ,$updCond);
        $this->setErrorMessage('success', 'Language content updated successfully','language_content_updated_successfully');
        redirect('admin/drivers/display_drivers_category');
    }

    /**
     *  Method to export Users List
     *  @return .xls file
     */
    public function export_drivers_list() 
    {        
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }

        $selectedFileds=array("driver_name","email", "status", "verify_status", "availability", "address", "mobile_number", 'currency', 'last_login_date');
        $usersList = $this->user_model->get_selected_fields(DRIVERS, array(), $selectedFileds, array('created' => 'DESC'));

        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->SetCellValue('A1', 'Driver Name');        
        $this->excel->getActiveSheet()->SetCellValue('B1', 'Email');
        $this->excel->getActiveSheet()->SetCellValue('C1', 'Status');
        $this->excel->getActiveSheet()->SetCellValue('D1', 'Verified');        
        $this->excel->getActiveSheet()->SetCellValue('E1', 'Availability');       
        $this->excel->getActiveSheet()->SetCellValue('F1', 'Address');
        $this->excel->getActiveSheet()->SetCellValue('G1', 'Mobile number');
        $this->excel->getActiveSheet()->SetCellValue('H1', 'Currency');
        $this->excel->getActiveSheet()->SetCellValue('I1', 'Last login date');
        $this->excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setSize(12);
        
        $rowCount = 2;
        foreach ($usersList->result() as $element) {
            $this->excel->getActiveSheet()->SetCellValue('A' . $rowCount, $element->driver_name);
            $this->excel->getActiveSheet()->SetCellValue('B' . $rowCount, $element->email);
            $this->excel->getActiveSheet()->SetCellValue('C' . $rowCount, $element->status);
            $this->excel->getActiveSheet()->SetCellValue('D' . $rowCount, $element->verify_status);
            $this->excel->getActiveSheet()->SetCellValue('E' . $rowCount, $element->availability);
            $this->excel->getActiveSheet()->SetCellValue('F' . $rowCount, json_decode($element->address));
            $this->excel->getActiveSheet()->SetCellValue('G' . $rowCount, (string)$element->mobile_number);
            $this->excel->getActiveSheet()->SetCellValue('H' . $rowCount, $element->currency);
            $this->excel->getActiveSheet()->SetCellValue('I' . $rowCount, $element->last_login_date);
            $rowCount++;
        }

        $filename='DriversList: '.date("Y-m-d").'.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
        $objWriter->save('php://output');
    }

}

/* End of file drivers.php */
/* Location: ./application/controllers/admin/drivers.php */