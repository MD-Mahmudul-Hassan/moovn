<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to rides management 
 * @author Casperon
 *
 * */
class Rides extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'string'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('rides_model');
    }

    /**
     * 
     * This function loads the rides list page
     *
     * */
    public function index() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            redirect('driver/rides/display_rides_list');
        }
    }

    /**
     * 
     * This function loads the rides list page
     *
     * */
    public function display_rides() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {

            $driver_id = $this->checkLogin('D');

            $ride_act = '';
            if (isset($_GET['act'])) {
                $ride_act = $this->input->get('act');
            }
            $offsetVal = 0;
            if (isset($_GET['per_page'])) {
                $offsetVal = $this->input->get('per_page');
            }

            if ($this->lang->line('dash_just_booked') != '')
                $dash_just_booked = stripslashes($this->lang->line('dash_just_booked'));
            else
                $dash_just_booked = 'Just Booked , Not Yet Started Rides';

            if ($this->lang->line('dash_on_rides_list') != '')
                $dash_on_rides_list = stripslashes($this->lang->line('dash_on_rides_list'));
            else
                $dash_on_rides_list = 'On Rides List';

            if ($this->lang->line('dash_completed_rides_list') != '')
                $dash_completed_rides_list = stripslashes($this->lang->line('dash_completed_rides_list'));
            else
                $dash_completed_rides_list = 'Completed Rides List';

            if ($this->lang->line('dash_cancelled_rides_list') != '')
                $dash_cancelled_rides_list = stripslashes($this->lang->line('dash_cancelled_rides_list'));
            else
                $dash_cancelled_rides_list = 'Cancelled Rides List';

            if ($this->lang->line('dash_rider_cancelled_rides_list') != '')
                $dash_rider_cancelled_rides_list = stripslashes($this->lang->line('dash_rider_cancelled_rides_list'));
            else
                $dash_rider_cancelled_rides_list = 'Rider Cancelled Rides List';

            if ($this->lang->line('dash_driver_cancelled_rides_list') != '')
                $dash_driver_cancelled_rides_list = stripslashes($this->lang->line('dash_driver_cancelled_rides_list'));
            else
                $dash_driver_cancelled_rides_list = 'Driver Cancelled Rides List';

            if ($this->lang->line('dash_driver_cancelled_rides_list') != '')
                $dash_all_rides_list = stripslashes($this->lang->line('dash_all_rides_list'));
            else
                $dash_all_rides_list = 'All Rides List';


            if ($ride_act == 'Upcoming') {
                $this->data['heading'] = $dash_just_booked;
            } else if ($ride_act == 'OnRide') {
                $this->data['heading'] = $dash_on_rides_list;
            } else if ($ride_act == 'Completed') {
                $this->data['heading'] = $dash_completed_rides_list;
            } else if ($ride_act == 'Cancelled') {
                $this->data['heading'] = $dash_cancelled_rides_list;
            } else if ($ride_act == 'riderCancelled') {
                $this->data['heading'] = $dash_rider_cancelled_rides_list;
            } else if ($ride_act == 'driverCancelled') {
                $this->data['heading'] = $dash_driver_cancelled_rides_list;
            } else {
                $this->data['heading'] = $dash_all_rides_list;
            }
            $rides_total = $ridesList = $this->rides_model->get_rides_total($ride_act, $driver_id);

            if ($rides_total->num_rows() > 100) {
                $limit = 50;

                $this->data['ridesList'] = $ridesList = $this->rides_model->get_rides_list($ride_act, $limit, $offsetVal, $driver_id);

                $searchbaseUrl = 'driver/rides/display_rides?act=' . $ride_act;
                $config['num_links'] = 3;
                $config['display_pages'] = TRUE;
                $config['page_query_string'] = TRUE;
                $config['base_url'] = $searchbaseUrl;
                $config['total_rows'] = $rides_total->num_rows();
                $config["per_page"] = $limit;
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
                if ($this->lang->line('pagination_first_lbl') != '') $config['first_link'] =stripslashes($this->lang->line('pagination_first_lbl'));  else  $config['first_link'] ='First';
                if ($this->lang->line('pagination_last_lbl') != '') $config['last_link'] = stripslashes($this->lang->line('pagination_last_lbl'));  else  $config['last_link'] ='Last';
                $this->pagination->initialize($config);
                $paginationLink = $this->pagination->create_links();
                $this->data['paginationLink'] = $paginationLink;
            } else {
                $this->data['paginationLink'] = '';
                $this->data['ridesList'] = $ridesList = $this->rides_model->get_rides_list($ride_act, '', '', $driver_id);
            }

            $this->data['offsetVal'] = $offsetVal;

            $this->load->view('driver/rides/display_rides', $this->data);
        }
    }

    /**
     * 
     * This function loads the rides view page
     *
     * */
    public function view_ride_details() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {

            if ($this->lang->line('dash_view_rides') != '')
                $dash_view_rides = stripslashes($this->lang->line('dash_view_rides'));
            else
                $dash_view_rides = 'View Rides';

            $this->data['heading'] = $dash_view_rides;
            $rides_id = $this->uri->segment(4, 0);
            $condition = array('_id' => new \MongoId($rides_id));
            $this->data['rides_details'] = $rides_details = $this->rides_model->get_all_details(RIDES, $condition);

            if (!isset($rides_details->row()->timezone) || !$rides_details->row()->timezone) {
                $lat = $rides_details->row()->booking_information['pickup']['latlong']['lat'];
                $lon = $rides_details->row()->booking_information['pickup']['latlong']['lon'];
                $this->setTimezone($lat, $lon);
            } else {
                set_default_timezone($rides_details->row()->timezone);
            }            
            if ($this->data['rides_details']->num_rows() == 1) {
                $this->load->view('driver/rides/view_rides', $this->data);
            } else {
                $this->setErrorMessage('error', 'No records found', 'driver_no_records_found');
                redirect('driver/rides/display_rides');
            }
        }
    }

    public function delete_rides() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $this->setErrorMessage('error', 'This service is not available', 'driver_service_not_avail');
            redirect('driver/rides/display_rides');
            /* $rides_id = $this->uri->segment(4,0);
              $condition = array('_id' => new \MongoId($rides_id));
              $this->rides_model->commonDelete(RIDES,$condition);
              $this->setErrorMessage('success','Rides deleted successfully');
              redirect('driver/rides/display_rides'); */
        }
    }

    /**
     * 
     * This function change the rides status, delete the rides record
     *
     * */
    public function change_rides_status_global() {
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('error', 'This service is not available', 'driver_service_not_avail');
                redirect('driver/rides/display_rides');
            }
            $this->user_model->activeInactiveCommon(RIDES, '_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'Rides records deleted successfully', 'driver_rides_records_deleted');
            } else {
                $this->setErrorMessage('success', 'Rides records status changed successfully', 'driver_rides_status_changed');
            }
            redirect('driver/rides/display_rides');
        }
    }

}

/* End of file rides.php */
/* Location: ./application/controllers/driver/rides.php */