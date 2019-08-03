<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to mail sending
 * @author Casperon
 *
 */

class Mail_model extends My_Model {

    public function __construct() {
        parent::__construct();

        $this->load->model(array('newsletter_model'));
    }

    /**
     * 
     * This function send the email to registred user regarding registration 
     * 	@Param String $user_id
     * */
    public function send_user_registration_mail($user_id = '') {
        if ($user_id != '') {
            $checkUser = $this->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email', 'user_name', 'unique_code'));
            if ($checkUser->num_rows() > 0) {
                $newsid = '3';
				#$template_values = $this->get_newsletter_template_details($newsid);
				$template_values = $this->get_email_template($newsid);
				$adminnewstemplateArr = array('mail_emailTitle' => $this->config->item('email_title'), 
											'mail_logo' => $this->config->item('logo_image'), 
											'mail_footerContent' => $this->config->item('footer_content'), 
											'mail_metaTitle' => $this->config->item('meta_title'), 
											'mail_contactMail' => $this->config->item('site_contact_mail'),
											'mail_referalCode' => $checkUser->row()->unique_code
											);
				extract($adminnewstemplateArr);
                $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
               
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
				 
				 $sender_name = $this->config->item('email_title');
                 $sender_email = $this->config->item('site_contact_mail');
              
				  $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $checkUser->row()->email,
                    'subject_message' => $template_values['subject'],
                    'body_messages' => $message
                );
                $email_send_to_common = $this->common_email_send($email_values);
            }
        }
    }

    /**
    * 
    * This function send the email to  drivers for registration confirmation
    * 	@Param String $user_id
    * */
    public function send_driver_register_confirmation_mail($user_id = '') {
        if ($user_id != '') {
            $checkUser = $this->get_selected_fields(DRIVERS, array('_id' => new \MongoId($user_id)), array('email', 'driver_name'));
            if ($checkUser->num_rows() > 0) {
                $newsid = '8';
                $template_values = $this->get_email_template($newsid);
                $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
                $mailtemplateValues = array('mail_emailTitle' => $this->config->item('email_title'),
                    'mail_logo' => $this->config->item('logo_image'),
                    'mail_footerContent' => $this->config->item('footer_content'),
                    'mail_metaTitle' => $this->config->item('meta_title'),
                    'mail_contactMail' => $this->config->item('site_contact_mail')
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="viewport" content="width=device-width"/>
					<title>' . $subject . '</title>
					<body>';
                include($template_values['templateurl']);
                $message .= '</body>
					</html>';
					
					$sender_email = $this->config->item('site_contact_mail');
                    $sender_name = $this->config->item('email_title');
                
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $checkUser->row()->email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
                $email_send_to_common = $this->common_email_send($email_values);
            }
        }
    }

    /**
     * 
     * This function send the email invoice
     * 	@Param String $ride_id
     * 	@Param String $email
     * */
    public function send_invoice_mail($ride_id = '', $email = '', $langcode = '') {
        if ($ride_id != '') {
            $ride_info = $this->user_model->get_all_details(RIDES, array('ride_id' => $ride_id));						 
            if ($ride_info->num_rows() == 1) {
                if ($email == '') {
                    $email = $ride_info->row()->booking_information['booking_email'];
                }
								if ($ride_info->row()->summary['ride_distance'] > $ride_info->row()->fare_breakup['min_km']) {
										$after_min_distance = $ride_info->row()->summary['ride_distance'] - $ride_info->row()->fare_breakup['min_km'];
								} else {
										$after_min_distance = 0;
								}
								if ($ride_info->row()->summary['ride_duration'] > $ride_info->row()->fare_breakup['min_time']) {
										$after_min_duration = $ride_info->row()->summary['ride_duration'] - $ride_info->row()->fare_breakup['min_time'];
								} else {
										$after_min_duration = 0;
								}	
								                        
								if (isset($ride_info->row()->total['tips_amount'])) {
										if ($ride_info->row()->total['tips_amount'] > 0) {                               
												$tips_amount = number_format($ride_info->row()->total['tips_amount'],2);     
										}
								} else {
										$tips_amount = 0.00;
								}
								if(isset($ride_info->row()->fare_breakup['peak_time_charge'])){
										if ($ride_info->row()->fare_breakup['peak_time_charge'] != '') { 
												$peak_time_charge_def = number_format($ride_info->row()->fare_breakup['peak_time_charge'],2);
												$peak_time_charge = number_format($ride_info->row()->total['peak_time_charge'],2);
										} else {
												$peak_time_charge_def = 0;
												$peak_time_charge = 0.00;
										}
								} else {
										$peak_time_charge_def = 0;
										$peak_time_charge = 0.00;
								}
								
                if(isset($ride_info->row()->fare_breakup['night_charge'])){
										if ($ride_info->row()->fare_breakup['night_charge'] != '') { 
												$night_charge_def = number_format($ride_info->row()->fare_breakup['night_charge'],2);
												$night_charge =  number_format($ride_info->row()->total['night_time_charge'],2);
										} else {
												$night_charge_def = 0 ;
												$night_charge =0.00;
										}
								} else {
										$night_charge_def = 0 ;
										$night_charge =0.00;
								}
								
								if(isset($ride_info->row()->fare_breakup['wait_per_minute'])){
										if ($ride_info->row()->fare_breakup['wait_per_minute'] != '') { 
												$wait_time_def = number_format($ride_info->row()->fare_breakup['wait_per_minute'],2);
												$wait_time =  number_format($ride_info->row()->total['wait_time'],2);
										} else {
												$wait_time_def = 0 ;
												$wait_time =0.00;
										}
								} else {
										$wait_time_def = 0 ;
										$wait_time =0.00;
								}
								
								if(isset($ride_info->row()->total['coupon_discount'])){
										if ($ride_info->row()->total['coupon_discount'] > 0) { 
												$coupon_discount = number_format($ride_info->row()->total['coupon_discount'],2);
										} else {
												$coupon_discount = 0;
										}						
								} else {
									  $coupon_discount = 0.00;
								}
								$template_values = $this->get_invoice_template($langcode);
                $subject = $this->config->item('email_title') . ' invoice for ride : ' . $ride_id;
								/* $invoiceHTML = file_get_contents(base_url() . 'invoice/' . $ride_id);
                $message = $invoiceHTML; */
						$currency_symbol=$this->data['dcurrencyCode'];
						if(isset($ride_info->row()->currency)) {
							$currency_symbol=$ride_info->row()->currency;
						}
					$distance_unit = $this->data['d_distance_unit'];
					if(isset($ride_info->row()->fare_breakup['distance_unit'])){
						$distance_unit = $ride_info->row()->fare_breakup['distance_unit'];
					}
					
                $mailtemplateValues = array('email_title' => $this->config->item('email_title'),
                    'logo_image' => $this->config->item('logo_image'),
										'ride_id' => $ride_info->row()->ride_id,
										'pickup_date' => date("d M, Y", $ride_info->row()->booking_information['pickup_date']->sec),
										'booking_date' => date("d M, Y, h:i A", $ride_info->row()->booking_information['booking_date']->sec),
										'user_name' => $ride_info->row()->user['name'],
										'grand_fare' => number_format($ride_info->row()->total['grand_fare'],2),
										'tips_amount' => $tips_amount,
										'ride_distance' => $ride_info->row()->summary['ride_distance'],
										'ride_duration' => $ride_info->row()->summary['ride_duration'],
										'wallet_usage' => number_format($ride_info->row()->total['wallet_usage'],2),
										'paid_amount' => number_format($ride_info->row()->total['paid_amount'],2),
										'fare_breakup_km' => $ride_info->row()->fare_breakup['min_km'],
										'fare_breakup_time' => $ride_info->row()->fare_breakup['min_time'],
										'fare_breakup_per_km' => $ride_info->row()->fare_breakup['per_km'],
										'fare_breakup_per_min' => $ride_info->row()->fare_breakup['per_minute'],
										'fare_breakup_fare' => number_format($ride_info->row()->fare_breakup['min_fare'],2),
										'base_fare' => number_format($ride_info->row()->total['base_fare'],2),
										'service_tax' => number_format($ride_info->row()->total['service_tax'],2),
										'distance' => number_format($ride_info->row()->total['distance'],2),
										'ride_time' => number_format($ride_info->row()->total['ride_time'],2),
										'location' => $ride_info->row()->location['name'],
										'service_type' => $ride_info->row()->booking_information['service_type'],
										'booking_email' => $ride_info->row()->booking_information['booking_email'],
										'ride_id' => $ride_info->row()->ride_id,
										'rcurrencySymbol' => $currency_symbol,
										'ride_distance_unit' => $distance_unit,
										'footer_content' => $this->config->item('footer_content'),
										'meta_title' => $this->config->item('meta_title'),
										'site_contact_mail' => $this->config->item('site_contact_mail'),
										'site_name_capital' => $this->config->item('site_name_capital'),
										'after_min_duration' => $after_min_duration,
										'after_min_distance' => $after_min_distance,
										'coupon_discount' => $coupon_discount,
										'night_charge' => $night_charge,
										'night_charge_def' => $night_charge_def,
										'peak_time_charge_def' => $peak_time_charge_def,
										'peak_time_charge' => $peak_time_charge,
										'wait_time_def' => $wait_time_def,
										'wait_time' => $wait_time
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
								<html>
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
								<meta name="viewport" content="width=device-width"/>
								<title>' . $subject . '</title>
								<body>';
                include($template_values['templateurl']);
                $message .= '</body>
								</html>';
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $this->config->item('site_contact_mail'),
                    'mail_name' => $this->config->item('email_title'),
                    'to_mail_id' => $email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
								
								  $invoicename = $ride_id . '.pdf';
                  $file_to_save = 'trip_invoice/' . $invoicename;
                  file_put_contents($file_to_save, stripcslashes($message));
                //$this->generate_invoice($ride_id, $message);
                $email_send_to_common = $this->common_email_send($email_values);
               
            }
        }
    }

    /**
     * 
     * This function send the email invoice
     * 	@Param String $ride_id
     * 	@Param String $email
     * */
    public function send_invoice($ride_id = '', $email = '') {
        if ($ride_id != '') {
            $ride_info = $this->user_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($ride_info->num_rows() == 1) {
                $invoicename = $ride_id . '.pdf';
                $file_save_path = 'trip_invoice/' . $invoicename;
                $attachments = $file_save_path;

                $subject = $this->config->item('email_title') . ' invoice for ride : ' . $ride_id;
                $invoiceHTML = file_get_contents(base_url() . 'invoice/' . $ride_id);
                $message = $invoiceHTML;
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $this->config->item('site_contact_mail'),
                    'mail_name' => $this->config->item('email_title'),
                    'to_mail_id' => $email,
                    'subject_message' => $subject,
                    'body_messages' => $message,
                    'attachments' => array($attachments)
                );
                #$this->generate_invoice($ride_id, $message);
                $email_send_to_common = $this->common_email_send($email_values);
                
            }
        }
    }

    /**
     * 
     * This function send the email invoice
     * 	@Param String $ride_id
     * 	@Param String $email
     * */
    public function generate_invoice($ride_id = '', $message = '') {
        if ($message != '' && $ride_id != '') {
            error_reporting(0);
            ini_set('display_errors', 'off');
            require_once("pdfdownload/dompdf_config.inc.php");
            $invoicename = $ride_id . '.pdf';

            $dompdf = new DOMPDF();
            $dompdf->load_html($message);
            $dompdf->render();
            $finalOut = $dompdf->output();

            $file_to_save = 'trip_invoice/' . $invoicename;

            file_put_contents($file_to_save, $finalOut);
        }
    }

    /**
     * 
     * This function send the email invoice
     * 	@Param String $ride_id
     * 	@Param String $email
     * */
    public function generate_invoice_mail($ride_id = '', $email = '', $send_email = 'Yes') {
        if ($ride_id != '') {
            error_reporting(0);
            $ride_info = $this->user_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($ride_info->num_rows() == 1) {
                if ($email == '') {
                    $email = $ride_info->booking_information['booking_email'];
                }
                $subject = $this->config->item('email_title') . ' invoice for ride : ' . $ride_id;
                $invoiceHTML = file_get_contents(base_url() . 'invoice/' . $ride_id);
                $message = $invoiceHTML;

                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $this->config->item('site_contact_mail'),
                    'mail_name' => $this->config->item('email_title'),
                    'to_mail_id' => $email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
                if ($send_email == 'Yes') {
                    $email_send_to_common = $this->common_email_send($email_values);
                }

                ini_set('display_errors', 'off');
                require_once("pdfdownload/dompdf_config.inc.php");
                $invoicename = $ride_id . '.pdf';

                $dompdf = new DOMPDF();
                $dompdf->load_html($message);
                $dompdf->render();
                $finalOut = $dompdf->output();

                $file_to_save = 'trip_invoice/' . $invoicename;

                file_put_contents($file_to_save, $finalOut);
            }
        }
    }

    function wallet_recharge_successfull_notification($pay_details, $rider_info, $txn_time, $recharge_id) {
        $newsid = '9';
        $template_values = $this->get_email_template($newsid);
       
        $user_name = $rider_info->row()->user_name;
		$dcurrencySymbol = $this->data['dcurrencySymbol'];
		if(isset($rider_info->row()->currency_symbol)) 
        $dcurrencySymbol = $rider_info->row()->currency_symbol;
        $amount = $dcurrencySymbol . $pay_details['trans_amount'];
        $txn_id = $pay_details['trans_id'];
        $txn_date = date('M d-Y h:i a', $txn_time);
        $txn_method = $pay_details['ref_id'];
        $wallet_amount = $dcurrencySymbol . $pay_details['avail_amount'];

        if ($txn_id == '') {
            $txn_id = $recharge_id;
        }


        $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
        $mailtemplateValues = array('mail_emailTitle' => $this->config->item('email_title'),
            'mail_logo' => $this->config->item('logo_image'),
            'mail_footerContent' => $this->config->item('footer_content'),
            'mail_metaTitle' => $this->config->item('meta_title'),
            'mail_contactMail' => $this->config->item('site_contact_mail')
        );
        extract($mailtemplateValues);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $subject . '</title>
			<body>';
        include($template_values['templateurl']);
        $message .= '</body>
			</html>';
		 $sender_email = $this->config->item('site_contact_mail');
         $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $rider_info->row()->email,
            'subject_message' => $subject,
            'body_messages' => $message
        );
        $email_send_to_common = $this->common_email_send($email_values);
    }

    /* public function send_driver_welcome_mail($driver_id = '') {

        if ($driver_id != '') {
            $checkDriver = $this->get_selected_fields(DRIVERS, array('_id' => new \MongoId($driver_id)), array('email', 'driver_name'));

            if ($checkDriver->num_rows() > 0) {
                $newsid = '12';
                $template_values = $this->get_newsletter_template_details($newsid);
                $subject = $template_values->message['subject'];
                $mailtemplateValues = array(
                    'user_name' => $checkDriver->row()->driver_name,
                    'mail_emailTitle' => $this->config->item('email_title'),
                    'mail_logo' => $this->config->item('logo_image'),
                    'mail_footerContent' => $this->config->item('footer_content'),
                    'mail_metaTitle' => $this->config->item('meta_title'),
                    'mail_contactMail' => $this->config->item('site_contact_mail')
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta name="viewport" content="width=device-width"/>
					<title>' . $subject . '</title>
					<body>';
                include('./newsletter/template' . $newsid . '.php');
                $message .= '</body>
					</html>';

               $sender_email = $this->config->item('site_contact_mail');
               $sender_name = $this->config->item('email_title');
               
                $email_values = array('mail_type' => 'html',
                    'from_mail_id' => $sender_email,
                    'mail_name' => $sender_name,
                    'to_mail_id' => $checkDriver->row()->email,
                    'subject_message' => $subject,
                    'body_messages' => $message
                );
                $email_send_to_common = $this->common_email_send($email_values);
            }
        }
    }
 */
 
 /**
     *
     * Get Notification / EMAIL templates details
     * @param Interger $news_id
     *
     * */
    public function notification_email_template_info($news_id = '') {
        $this->cimongo->select();
        if ($news_id != '') {
            $this->cimongo->where(array('news_id' => (int) $news_id));
        }
        $res = $this->cimongo->get(NOTIFICATION_TEMPLATES);
        return $res->row();
    }
		
		/**
     * 
     * This function send the email invoice
     * 	@Param String $ride_id
     * 	@Param String $email
     * */
    public function view_invoice($ride_id = '', $email = '', $langcode = '') {
			$message ='';
        if ($ride_id != '') {
            $ride_info = $this->user_model->get_all_details(RIDES, array('ride_id' => $ride_id));
            if ($ride_info->num_rows() == 1) {
                if ($email == '') {
                    $email = $ride_info->row()->booking_information['booking_email'];
                }
								if ($ride_info->row()->summary['ride_distance'] > $ride_info->row()->fare_breakup['min_km']) {
									$after_min_distance = $ride_info->row()->summary['ride_distance'] - $ride_info->row()->fare_breakup['min_km'];
								} else {
									$after_min_distance = 0;
								}
								if ($ride_info->row()->summary['ride_duration'] > $ride_info->row()->fare_breakup['min_time']) {
									$after_min_duration = $ride_info->row()->summary['ride_duration'] - $ride_info->row()->fare_breakup['min_time'];
								} else {
									$after_min_duration = 0;
								}	
								                        
								if (isset($ride_info->row()->total['tips_amount'])) {
										if ($ride_info->row()->total['tips_amount'] > 0) {                               
												$tips_amount = number_format($ride_info->row()->total['tips_amount'],2);     
										}
								} else {
										$tips_amount = 0.00;
								}
								if(isset($ride_info->row()->fare_breakup['peak_time_charge'])){
										if ($ride_info->row()->fare_breakup['peak_time_charge'] != '') { 
												$peak_time_charge_def = number_format($ride_info->row()->fare_breakup['peak_time_charge'],2);
												$peak_time_charge = number_format($ride_info->row()->total['peak_time_charge'],2);
										} else {
												$peak_time_charge_def = 0;
												$peak_time_charge = 0.00;
										}
								} else {
										$peak_time_charge_def = 0;
										$peak_time_charge = 0.00;
								}
								
                if(isset($ride_info->row()->fare_breakup['night_charge'])){
										if ($ride_info->row()->fare_breakup['night_charge'] != '') { 
												$night_charge_def = number_format($ride_info->row()->fare_breakup['night_charge'],2);
												$night_charge =  number_format($ride_info->row()->total['night_time_charge'],2);
										} else {
												$night_charge_def = 0 ;
												$night_charge =0.00;
										}
								} else {
										$night_charge_def = 0 ;
										$night_charge =0.00;
								}
								
								if(isset($ride_info->row()->fare_breakup['wait_per_minute'])){
										if ($ride_info->row()->fare_breakup['wait_per_minute'] != '') { 
												$wait_time_def = number_format($ride_info->row()->fare_breakup['wait_per_minute'],2);
												$wait_time =  number_format($ride_info->row()->total['wait_time'],2);
										} else {
												$wait_time_def = 0 ;
												$wait_time =0.00;
										}
								} else {
										$wait_time_def = 0 ;
										$wait_time =0.00;
								}
								
								if(isset($ride_info->row()->total['coupon_discount'])){
										if ($ride_info->row()->total['coupon_discount'] > 0) { 
												$coupon_discount = number_format($ride_info->row()->total['coupon_discount'],2);
										} else {
												$coupon_discount = 0;
										}						
								} else {
									  $coupon_discount = 0.00;
								}
								$template_values = $this->get_invoice_template($langcode);
								$distance_unit = $this->data['d_distance_unit'];
								if(isset($ride_info->row()->fare_breakup['distance_unit'])){
									$distance_unit = $ride_info->row()->fare_breakup['distance_unit'];
								}
								
                $subject = $this->config->item('email_title') . ' invoice for ride : ' . $ride_id;
								$mailtemplateValues = array('email_title' => $this->config->item('email_title'),
                    'logo_image' => $this->config->item('logo_image'),
										'ride_id' => $ride_info->row()->ride_id,
										'pickup_date' => date("d M, Y", $ride_info->row()->booking_information['pickup_date']->sec),
										'booking_date' => date("d M, Y, h:i A", $ride_info->row()->booking_information['booking_date']->sec),
										'user_name' => $ride_info->row()->user['name'],
										'grand_fare' => number_format($ride_info->row()->total['grand_fare'],2),
										'tips_amount' => $tips_amount,
										'ride_distance' => $ride_info->row()->summary['ride_distance'],
										'ride_duration' => $ride_info->row()->summary['ride_duration'],
										'wallet_usage' => number_format($ride_info->row()->total['wallet_usage'],2),
										'paid_amount' => number_format($ride_info->row()->total['paid_amount'],2),
										'fare_breakup_km' => $ride_info->row()->fare_breakup['min_km'],
										'fare_breakup_time' => $ride_info->row()->fare_breakup['min_time'],
										'fare_breakup_per_km' => $ride_info->row()->fare_breakup['per_km'],
										'fare_breakup_per_min' => $ride_info->row()->fare_breakup['per_minute'],
										'fare_breakup_fare' => number_format($ride_info->row()->fare_breakup['min_fare'],2),
										'base_fare' => number_format($ride_info->row()->total['base_fare'],2),
										'service_tax' => number_format($ride_info->row()->total['service_tax'],2),
										'distance' => number_format($ride_info->row()->total['distance'],2),
										'ride_time' => number_format($ride_info->row()->total['ride_time'],2),
										'location' => $ride_info->row()->location['name'],
										'service_type' => $ride_info->row()->booking_information['service_type'],
										'booking_email' => $ride_info->row()->booking_information['booking_email'],
										'ride_id' => $ride_info->row()->ride_id,
										'rcurrencySymbol' => $ride_info->row()->currency,
										'ride_distance_unit' => $distance_unit,
                    'footer_content' => $this->config->item('footer_content'),
                    'meta_title' => $this->config->item('meta_title'),
                    'site_contact_mail' => $this->config->item('site_contact_mail'),
										'site_name_capital' => $this->config->item('site_name_capital'),
										'after_min_duration' => $after_min_duration,
										'after_min_distance' => $after_min_distance,
										'coupon_discount' => $coupon_discount,
										'night_charge' => $night_charge,
										'night_charge_def' => $night_charge_def,
										'peak_time_charge_def' => $peak_time_charge_def,
										'peak_time_charge' => $peak_time_charge,
										'wait_time_def' => $wait_time_def,
										'wait_time' => $wait_time
                );
                extract($mailtemplateValues);
                $message = '<!DOCTYPE HTML>
								<html>
								<head>
								<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
								<meta name="viewport" content="width=device-width"/>
								<title>' . $subject . '</title>
								<body>';
                include($template_values['templateurl']);
                $message .= '</body>
								</html>';
               
            }
        }
				return stripcslashes($message);
    }

    /**
     * 
     * This function send the email to users when ride is booked
     *  @Param String $user_id
     *  @Param String $ride_id
     * */
    public function user_ride_booked($user_id = '', $ride_id)
    {
        $checkUser = $this->get_selected_fields(USERS, array('_id' => new \MongoId($user_id)), array('email', 'user_name', 'unique_code'));
        if ($checkUser->num_rows() > 0) {
            $template_values = $this->newsletter_model->get_template('ride_booked');
            $adminnewstemplateArr = array('site_title' => $this->config->item('email_title'), 
                                        'mail_logo' => $this->config->item('logo_image'), 
                                        'footer_content' => $this->config->item('footer_content'), 
                                        'meta_title' => $this->config->item('meta_title'), 
                                        'site_contact_mail' => $this->config->item('site_contact_mail')
                                        );
            extract($adminnewstemplateArr);
            $subject = str_replace(
                array('{$site_title}'), 
                array($this->config->item('email_title')), 
                $template_values['subject']
            );
            $message = '<!DOCTYPE HTML>
                <html>
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <meta name="viewport" content="width=device-width"/>
                <title>' . $subject . '</title>
                <body>';
                include($template_values['templateurl']);

            $message .= '</body></html>';
             
             $sender_name = $this->config->item('email_title');
             $sender_email = $this->config->item('site_contact_mail');
          
              $email_values = array('mail_type' => 'html',
                'from_mail_id' => $sender_email,
                'mail_name' => $sender_name,
                'to_mail_id' => $checkUser->row()->email,
                'subject_message' => $subject,
                'body_messages' => $message
            );
            $email_send_to_common = $this->common_email_send($email_values);
        }
    }

    /**
     * 
     * This function send the email to users when ride is accepted
     *  @Param String $ride
     *  @Param String $user
     *  @Param String $driver
     * */
    public function user_ride_accepted($ride, $user, $driver) {
        $template_values = $this->newsletter_model->get_template('mail_user_ride_accepted');
        $booking_time = date("h:i A", $ride->booking_information['est_pickup_date']->sec).' on '. date("jS M, Y", $ride->booking_information['est_pickup_date']->sec);        
        $subsArr = array(
            'site_title' => $this->config->item('email_title'), 
            'mail_logo' => $this->config->item('logo_image'), 
            'footer_content' => $this->config->item('footer_content'), 
            'meta_title' => $this->config->item('meta_title'), 
            'site_contact_mail' => $this->config->item('site_contact_mail'),
            'ride_id' => $ride->ride_id,
            'booking_date' => $booking_time,
            'driver_name' => $driver['name'],
            'car_type' => $driver['vehicle_model'],
            'license_plate' => $driver['vehicle_no']
        );
        extract($subsArr);
        $subject = str_replace(
            array('{$site_title}'), 
            array($this->config->item('email_title')), 
            $template_values['subject']
        );
        $message = '<!DOCTYPE HTML>
            <html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width"/>
            <title>' . $subject . '</title>
            <body>';
        include($template_values['templateurl']);
        $message .= '</body></html>';
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $this->config->item('site_contact_mail'),
            'mail_name' => $this->config->item('email_title'),
            'to_mail_id' => $user->email,
            'subject_message' => $subject,
            'body_messages' => $message
        );
        $email_send_to_common = $this->common_email_send($email_values);
    }

    public function mpesa_status($transaction, $user, $wallet_amount) {
        $newsid = '18';
        $template_values = $this->get_email_template($newsid);       
        $name = $user['name'];
		$currency_symbol = $this->data['dcurrencySymbol'];
		if (isset($user['currency_symbol'])) {
            $currency_symbol = $user['currency_symbol'];
        }
        $amount = $currency_symbol . $transaction['amount'];
        $transaction_date = date('M d-Y h:i a', $transaction['date']);
        $wallet_amount = $currency_symbol . $wallet_amount;
        $subject = $this->config->item('email_title') . ' - ' . $template_values['subject'];
        $mailtemplateValues = array('mail_emailTitle' => $this->config->item('email_title'),
            'mail_logo' => $this->config->item('logo_image'),
            'mail_footerContent' => $this->config->item('footer_content'),
            'mail_metaTitle' => $this->config->item('meta_title'),
            'mail_contactMail' => $this->config->item('site_contact_mail')
        );
        extract($mailtemplateValues);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $subject . '</title>
			<body>';
        include($template_values['templateurl']);
        $message .= '</body>
			</html>';
		$sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
        
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $user['email'],
            'subject_message' => $subject,
            'body_messages' => $message
        );
        $email_send_to_common = $this->common_email_send($email_values);
    }
}
