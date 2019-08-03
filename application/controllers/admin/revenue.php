<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to Revenue and Commission management 
 * @author Casperon
 *
 * */
class Revenue extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation', 'excel'));
        $this->load->model(array('revenue_model', 'billing_model'));
        $this->load->library('encrypt');

        if ($this->checkPrivileges('revenue', $this->privStatus) == FALSE) {
            redirect('admin');
        }
    }

    /**
     *
     * This function loads the users list page
     *
     * */
    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            redirect('admin/revenue/display_site_revenue');
        }
    }

    /**
     * 
     * This function loads the revenue list page
     *
     * */
    public function display_site_revenue() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $billingsList = $this->revenue_model->get_all_details(TRANSACTION, array(), array('bill_generated' => 'DESC'));

            $req = (empty($_GET['range'])) ? '' : $_GET['range'];
            $this->data['range'] = $range = (empty($_GET['range'])) ? '' : base64_decode(rawUrlDecode($_GET['range']));
			
			$dateFrom = $this->input->get('from');  
			$dateTo = $this->input->get('to');
			if($range == '' && $dateFrom != '' && $dateTo != ''){
				$range = $dateFrom.' - '.$dateTo; 
			}
			$this->data['locationList'] = $location = $this->revenue_model->get_all_details(LOCATIONS, array('status' => 'Active'), array('city' => 'ASC'));
			
			 if(isset($_GET['location_id']) &&  $_GET['location_id'] != ''){
				  $location_id = $_GET['location_id'];
				  $checkLocation = $this->revenue_model->get_selected_fields(LOCATIONS,array('_id' => new MongoId($location_id)),array('currency_symbol','currency'));
			} else {
				$location_id = (string)$location->row()->_id;
				$this->data['dcurrencyCode'] =  $location->row()->currency;
				$checkLocation = $this->revenue_model->get_selected_fields(LOCATIONS,array('_id' => new MongoId($location_id)),array('currency_symbol','currency'));
			}
			
			if($checkLocation->num_rows()>0 && isset($checkLocation->row()->currency)){
				$this->data['dcurrencyCode'] = $checkLocation->row()->currency;
				$this->data['dcurrencySymbol'] = $checkLocation->row()->currency_symbol;
				if($checkLocation->row()->currency_symbol == ''){
					$this->data['dcurrencySymbol'] = $checkLocation->row()->currency;
				}
			}
			
			$this->data['default_location'] =(string)$location_id;
			$driverCond = array('driver_location' => $location_id);
			
            $selectFields = array('email', 'image', 'driver_name', 'vehicle_number', 'vehicle_model', 'no_of_rides', 'cancelled_rides', 'mobile_number', 'dail_code');
            $driversCount = $this->revenue_model->get_all_counts(DRIVERS,$driverCond);
            if ($driversCount > 500) {
                $limitPerPage = 100;
                $offsetVal = 0;
                if (isset($_GET['per_page'])) {
                    $offsetVal = $this->input->get('per_page');
                }

                $driverDetails = $this->revenue_model->get_selected_fields(DRIVERS, $driverCond, $selectFields, array(), $limitPerPage, $offsetVal);

                if(!isset($_GET['range']) && $dateFrom != '' && $dateTo != ''){
					$manRange = 'from='.urlencode($dateFrom).'&to='.urlencode($dateTo);
					$searchbaseUrl = 'admin/revenue/display_site_revenue?' . $manRange;
				} else {
					$searchbaseUrl = 'admin/revenue/display_site_revenue?range=' . $req;
				}
				
                $config['num_links'] = 3;
                $config['display_pages'] = TRUE;
                $config['page_query_string'] = TRUE;
                $config['base_url'] = $searchbaseUrl;
                $config['total_rows'] = $driversCount;
                $config["per_page"] = $limitPerPage;
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
                $driverDetails = $this->revenue_model->get_selected_fields(DRIVERS, $driverCond, $selectFields);
            }
            #echo $driverDetails->num_rows(); die;


            $dates = @explode('-', $range);
            $mfrom = '';
            $mto = '';

            if (count($dates) == 2) {
                $mfrom = trim($dates[0]);
                $mto = trim($dates[1]);
            } else {
                $mto = date("m/d/Y");
                if ($billingsList->num_rows() == 0) {
                    $mfrom = date("m/d/Y", strtotime("first day of this month"));
                } else {
                    $mfrom = date("m/d/Y", strtotime("+1 day", $billingsList->row()->bill_period_to->sec));
                }
            }

            if ($billingsList->num_rows() == 0) {
                $last_bill = date("m/d/Y", strtotime("first day of this month"));
            } else {
                $last_bill = date("m/d/Y", strtotime("+1 day", $billingsList->row()->bill_period_to->sec));
            }

            $filter = '';
            $fromdate = '';
            $todate = '';
            $fdate = '';
            $tdate = '';
            if ($mfrom != "" && $mto != "") {
                $filter = 'dummy';
                $fromdate = strtotime($mfrom . ' 00:00:00');
                $todate = strtotime($mto . ' 23:59:59');
            }
            $this->data['filter'] = $filter;
            $this->data['last_bill'] = $last_bill;

            $cB = date("m/d/Y", $fromdate) . ' - ' . date("m/d/Y", $todate);
            $this->data['cB'] = $cB;

            if ($fromdate != '' && $todate != '') {
                $fdate = $fromdate;
                $tdate = $todate;
            }

            $totalRides = 0;
            $totalRevenue = 0;
            $siteRevenue = 0;
            $driverRevenue = 0;
            if ($driverDetails->num_rows() > 0) {
                foreach ($driverDetails->result() as $driver) {
                    $total_rides = 0;
                    $cancelled_rides = 0;
                    $successfull_rides = 0;
                    $total_revenue = 0;
                    $in_site = 0;
                    $couponAmount = 0;
                    $in_driver = 0;
                    $total_due = 0;
                    $site_earnings = 0;
                    $driver_earnings = 0;
                    $tips_amount = 0;
                    $tips_in_site = 0;
                    $tips_in_driver = 0;

                    $driver_id = (string) $driver->_id;
                    $driver_name = (string) $driver->driver_name;
                    $driver_email = (string) $driver->email;
                    $driver_phone = (string) $driver->dail_code . $driver->mobile_number;
                    $driver_image = USER_PROFILE_IMAGE_DEFAULT;
                    if (isset($driver->image)) {
                        if ($driver->image != '') {
                            $driver_image = USER_PROFILE_IMAGE . $driver->image;
                        }
                    }
                     $rideDetails = $this->revenue_model->get_ride_details($driver_id, $fdate, $tdate,'',(string)$location_id);

                    if (!empty($rideDetails['result'])) {
                        $total_rides = $rideDetails['result'][0]['totalTrips'];
                        $total_revenue = $rideDetails['result'][0]['totalAmount'];
                        #$in_site = $rideDetails['result'][0]['by_wallet'];
                        $couponAmount = $rideDetails['result'][0]['couponAmount'];
                        $site_earnings = $rideDetails['result'][0]['site_earnings'];
                        $driver_earnings = $rideDetails['result'][0]['driver_earnings'];
						
                        if (isset($rideDetails['result'][0]['tipsAmount'])) {
                            $tips_amount = $rideDetails['result'][0]['tipsAmount'];
                        }
                        if (isset($rideDetails['result'][0]['amount_in_site'])) {
                            $in_site = $rideDetails['result'][0]['amount_in_site'];
                        }
                        if (isset($rideDetails['result'][0]['amount_in_driver'])) {
                            $in_driver = $rideDetails['result'][0]['amount_in_driver'];
                        }
                    }

                    /*  tips will not included in total revenue so driver tips amount need to be added with driver earnings */
                    $driver_earnings = $driver_earnings + $tips_amount;

                    $this->data['driversList'][$driver_id]['id'] = $driver_id;
                    $this->data['driversList'][$driver_id]['driver_name'] = $driver_name;
                    $this->data['driversList'][$driver_id]['driver_email'] = $driver_email;
                    $this->data['driversList'][$driver_id]['driver_image'] = base_url() . $driver_image;
                    $this->data['driversList'][$driver_id]['driver_phone'] = $driver_phone;




                    $this->data['driversList'][$driver_id]['total_rides'] = $total_rides;
                    $this->data['driversList'][$driver_id]['total_revenue'] = $total_revenue;
                    $this->data['driversList'][$driver_id]['in_site'] = $in_site;
                    $this->data['driversList'][$driver_id]['couponAmount'] = $couponAmount;
                    $this->data['driversList'][$driver_id]['in_driver'] = $in_driver;
                    $this->data['driversList'][$driver_id]['total_due'] = $total_due;

                    $this->data['driversList'][$driver_id]['site_earnings'] = $site_earnings;
                    $this->data['driversList'][$driver_id]['driver_earnings'] = $driver_earnings;
                    $this->data['driversList'][$driver_id]['driver_tips'] = $tips_amount;

                    $outstanding_amount = ($site_earnings > $in_site) ? ($site_earnings - $in_site) : 0.00;
                    $this->data['driversList'][$driver_id]['outstanding_amount'] = $outstanding_amount;

                    /* $totalRides+=$total_rides;
                      $totalRevenue+=$total_revenue;
                      $siteRevenue+=$site_earnings;
                      $driverRevenue+=$driver_earnings; */
                }
            } 
             $rideSummary = $this->revenue_model->get_ride_summary($fdate, $tdate,'',(string)$location_id);
            #echo '<pre>'; print_r($rideSummary); die;
            if (!empty($rideSummary['result'])) {
                $totalRides = $rideSummary['result'][0]['totalTrips'];
                $siteRevenue = $rideSummary['result'][0]['site_earnings'];
                $driverRevenue = $rideSummary['result'][0]['driver_earnings'];
                $totalRevenue = $siteRevenue + $driverRevenue;
            }

            $this->data['totalRides'] = $totalRides;
            $this->data['totalRevenue'] = $totalRevenue;
            $this->data['siteRevenue'] = $siteRevenue;
            $this->data['driverRevenue'] = $driverRevenue;

            $this->data['fromdate'] = $mfrom;
            $this->data['todate'] = $mto;


            $this->data['billingsList'] = $billingsList;
			


            #echo '<pre>'; print_r($this->data['driversList']); die;
            if ($this->lang->line('admin_site_earnings_total_revenue_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_site_earnings_total_revenue_list')); 
		    else  $this->data['heading'] = 'Total Revenue List';
            $this->load->view('admin/revenue/display_site_revenue', $this->data);
        }
    }

    /**
     * 
     * This function loads the revenue list page
     *
     * */
    public function driver_trip_summary($driver_id) {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $driverDetails = $this->revenue_model->get_all_details(DRIVERS, array('_id' => new \MongoId($driver_id)));
            if ($driverDetails->num_rows() > 0) {
                $mfrom = (empty($_GET['trip_from'])) ? '' : $_GET['trip_from'];
                $mto = (empty($_GET['trip_to'])) ? '' : $_GET['trip_to'];
                $mfrom = base64_decode($mfrom);
                $mto = base64_decode($mto);

                $fromdate = '';
                $todate = '';
                $fdate = '';
                $tdate = '';
                if ($mfrom != "" && $mto != "") {
                    $fromdate = strtotime($mfrom . ' 00:00:00');
                    $todate = strtotime($mto . ' 23:59:59');
                }

                if ($fromdate != '' && $todate != '') {
                    $fdate = $fromdate;
                    $tdate = $todate;
                }
                $rideSummary = $this->revenue_model->get_trip_summary($driver_id, $fdate, $tdate);
                $rideList = array();
                if (!empty($rideSummary['result'])) {
                    $rideList = $rideSummary['result'];
                }

                #echo "<pre>"; print_r($rideList); die;

                $billingsList = $this->revenue_model->get_all_details(TRANSACTION, array('bill_period_from' => new \MongoDate($fromdate), 'bill_period_to' => new \MongoDate($todate)));

                $billingARR = array();
                if ($billingsList->num_rows() > 0) {
                    $bill_id = $billingsList->row()->bill_id;
                    $billingArr = $this->revenue_model->get_all_details(BILLINGS, array('bill_id' => $bill_id, 'driver_id' => $driver_id))->result_array();
                    $billingARR = $billingArr[0];
                }

                $this->data['rideList'] = $rideList;

                $this->data['fromdate'] = $mfrom;
                $this->data['todate'] = $mto;

                #echo "<pre>"; print_r($billingARR); die;
				$location_id = (string)$driverDetails->row()->driver_location; 
				$location=$this->revenue_model->get_selected_fields(LOCATIONS,array('_id' => new MongoId($location_id)),array('currency_symbol','currency'));
				if($location->num_rows() > 0 && isset($location->row()->currency)){
					$this->data['dcurrencySymbol'] = $location->row()->currency_symbol;
					$this->data['dcountryCode'] = $location->row()->currency;
				}

                $this->data['bill_details'] = $billingARR;
                if ($this->lang->line('admin_site_earnings_total_revenue_list') != '') 
		        $this->data['heading']= stripslashes($this->lang->line('admin_site_earnings_total_revenue_list')); 
		        else  $this->data['heading'] = 'Total Revenue List';
                $this->load->view('admin/revenue/display_driver_trip_summary', $this->data);
            }
        }
    }

    /**
     * 
     * This function save the trasaction for a billing
     *
     * */
    public function transaction($type = '') {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $invoice_id = $this->input->post('invoice_id');
            $transaction_id = $this->input->post('transaction_id');
            $paid_date = $this->input->post('paid_date');
            $paid_details = $this->input->post('paid_details');
            $billingArr = $this->revenue_model->get_all_details(BILLINGS, array('invoice_id' => floatval($invoice_id)));
            if ($billingArr->num_rows() > 0) {
                $driver_id = $billingArr->row()->driver_id;
                $txn_type = '';
                $billArr = array();
                if ($type == 'received') {
                    $amount = $billingArr->row()->driver_pay_amount;
                    $txn_type = 'CREDIT';
                    $billArr = array('driver_paid' => 'Yes');
                } else if ($type == 'paid') {
                    $amount = $billingArr->row()->site_pay_amount;
                    $txn_type = 'DEBIT';
                    $billArr = array('site_paid' => 'Yes');
                }
                $txn_date = strtotime($paid_date);

                if ($txn_type != '') {
                    $txn_arr = array('invoice_id' => (string) $invoice_id,
                        'driver_id' => (string) $driver_id,
                        'txn_type' => (string) $txn_type,
                        'txn_id' => (string) $transaction_id,
                        'txn_date' => new \MongoDate($txn_date),
                        'txn_details' => (string) $paid_details,
                    );
                    $this->revenue_model->simple_insert(PAYMENT_TRANSACTION, $txn_arr);
                    if (!empty($billArr)) {
                        $this->revenue_model->update_details(BILLINGS, $billArr, array('_id' => new \MongoId((string) $billingArr->row()->_id)));
                    }
                    $this->setErrorMessage('success', 'transaction updated successfully','admin_revenue_transaction_update');
                }else{
					$this->setErrorMessage('error', 'transaction updation failed','admin_revenue_transaction_update_failed');
				}
            } else {
                $this->setErrorMessage('error', 'transaction updation failed','admin_revenue_transaction_update_failed');
            }
            echo "<script>window.history.go(-1);</script>";
            exit;
        }
    }

    /**
     * Export site earning in excel format
     */
    public function export_site_earning()
    {
        if ($this->checkLogin('A') == '') {
            $this->setErrorMessage('error', 'You must login first','admin_driver_login_first');
            redirect('admin');
        }

        $daysSet = $monthSet = 0;

        if (!empty($_GET['days'])) {
            $range = $_GET['days'];

            switch ($range) {
                case 'last 7 days':
                    $daysSet = 1;
                    $lastDateOfDays = date('Y-m-d', strtotime("-1 week +1"));
                    break;

                case 'last 14 days':
                    $daysSet = 1;
                    $lastDateOfDays = date('Y-m-d', strtotime("-2 week +2"));
                    break;
                
                case 'last 21 days':
                    $daysSet = 1;
                    $lastDateOfDays = date('Y-m-d', strtotime("-3 week +3"));
                    break;
                
                case 'last month':
                    $monthSet = 1;
                    $firstDateOfMonths = date('Y-m-d', strtotime("first day of -1 month"));
                    $lastDateOfMonths = date('Y-m-d', strtotime("last day of -1 month"));
                    break;
                
                case 'last 2 months':
                    $monthSet = 1;
                    $firstDateOfMonths = date('Y-m-d', strtotime("first day of -2 month"));
                    $lastDateOfMonths = date('Y-m-d', strtotime("last day of -1 month"));
                    break;
                
                case 'last 3 months':
                    $monthSet = 1;
                    $firstDateOfMonths = date('Y-m-d', strtotime("first day of -3 month"));
                    $lastDateOfMonths = date('Y-m-d', strtotime("last day of -1 month"));
                    break;
                
                default:
                    break;
            }
        }

        $selectedFileds=array("_id", "bill_id","bill_date", "bill_from","bill_to", "driver_email", "driver_name", "total_revenue", "site_earnings");
        $siteEarnings = $this->billing_model->get_selected_fields(BILLINGS, array(), $selectedFileds);

        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->SetCellValue('A1', 'ID');
        $this->excel->getActiveSheet()->SetCellValue('B1', 'Bill id');
        $this->excel->getActiveSheet()->SetCellValue('C1', 'Bill date');
        $this->excel->getActiveSheet()->SetCellValue('D1', 'Bill from');
        $this->excel->getActiveSheet()->SetCellValue('E1', 'Bill to');
        $this->excel->getActiveSheet()->SetCellValue('F1', 'Driver name');
        $this->excel->getActiveSheet()->SetCellValue('G1', 'Email');
        $this->excel->getActiveSheet()->SetCellValue('H1', 'Total revenue');
        $this->excel->getActiveSheet()->SetCellValue('I1', 'Site earning');
        $this->excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:I1')->getFont()->setSize(12);

        $rowCount = 2;
        
        foreach ($siteEarnings->result() as $element) {
            $export = false;

            if ($daysSet) {
                if ((date('Y-m-d', $element->bill_date->sec) >= $lastDateOfDays) && (date('Y-m-d', $element->bill_date->sec) < date("today"))) {
                    $export = true;
                }
            }

            if ($monthSet) {
                if ((date('Y-m-d', $element->bill_date->sec) >= $firstDateOfMonths && date('Y-m-d', $element->bill_date->sec) <= $lastDateOfMonths)) {
                    $export = true;
                }
            }

            if ($export) {
                $this->excel->getActiveSheet()->SetCellValue('A' . $rowCount, (string) $element->_id);
                $this->excel->getActiveSheet()->SetCellValue('B' . $rowCount, $element->bill_id);
                $this->excel->getActiveSheet()->SetCellValue('C' . $rowCount, date('Y-m-d', $element->bill_date->sec));
                $this->excel->getActiveSheet()->SetCellValue('D' . $rowCount, date('Y-m-d', $element->bill_from->sec));
                $this->excel->getActiveSheet()->SetCellValue('E' . $rowCount, date('Y-m-d', $element->bill_to->sec));
                $this->excel->getActiveSheet()->SetCellValue('F' . $rowCount, $element->driver_name);
                $this->excel->getActiveSheet()->SetCellValue('G' . $rowCount, $element->driver_email);
                $this->excel->getActiveSheet()->SetCellValue('H' . $rowCount, $element->total_revenue);
                $this->excel->getActiveSheet()->SetCellValue('I' . $rowCount, $element->site_earnings);

                $rowCount++;
            }
        }
        
        $filename='Site-Earning: '.date("Y-m-d").'.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }
}

/* End of file revenue.php */
/* Location: ./application/controllers/admin/revenue.php */