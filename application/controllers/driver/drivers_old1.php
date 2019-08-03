<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to Drivers at the driver end
 * @author Casperon
 *
 * */
class Drivers extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('driver_model'));

        if ($this->checkLogin('D') == '') {
            redirect('driver');
        }
    }

    public function index() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            redirect('driver/drivers/display_driver_dashboard');
        }
    }

    /**
     * 
     * This function loads the drivers dashboard
     *
     * */
    public function display_driver_dashboard() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            if ($this->lang->line('driver_driver_dashboard') != '')
                $header_name = stripslashes($this->lang->line('driver_driver_dashboard'));
            else
                $header_name = 'Drivers Dashboard';
            $this->data['heading'] = $header_name;
            $condition = 'order by `created` desc';
            $this->data['totalUsersList'] = $this->driver_model->get_selected_fields(DRIVERS, array(), array('email'), array('_id' => 'DESC'))->num_rows();
            $this->data['totalActiveUser'] = $this->driver_model->get_selected_fields(DRIVERS, array('status' => 'Active'), array('email'), array('_id' => 'DESC'))->num_rows();
            $this->data['totalInactiveUser'] = $this->driver_model->get_selected_fields(DRIVERS, array('status' => 'Inactive'), array('email'), array('_id' => 'DESC'))->num_rows();
            $selectedFileds = array('driver_name', 'email', 'image', 'status');
            $this->data['recentdriversList'] = $this->driver_model->get_selected_fields(DRIVERS, array(), $selectedFileds, array('_id' => 'DESC'), 3, 0);
            $this->load->view('driver/drivers/display_drivers_dashboard', $this->data);
        }
    }

    /**
     *
     * This function loads the drivers list page
     *
     * */
    public function display_drivers_list() {
        if ($this->checkLogin('D') == '') {
            if ($this->lang->line('driver_login_first') != '')
                $disp_message = stripslashes($this->lang->line('driver_login_first'));
            else
                $disp_message = 'You must login first';
            $this->setErrorMessage('error', $disp_message);
            redirect('driver');
        }
        if ($this->lang->line('driver_disp_driver_list') != '')
            $disp_message = stripslashes($this->lang->line('driver_disp_driver_list'));
        else
            $disp_message = 'Display Drivers List';
        $this->data['heading'] = $disp_message;

        $driversCount = $this->user_model->get_all_counts(DRIVERS, array());

        if ($driversCount > 1000) {
            $searchPerPage = 500;
            $paginationNo = $this->uri->segment(4);
            if ($paginationNo == '') {
                $paginationNo = 0;
            } else {
                $paginationNo = $paginationNo;
            }

            $this->data['driversList'] = $this->user_model->get_all_details(DRIVERS, array(), array('created' => 'DESC'), $searchPerPage, $paginationNo);

            $searchbaseUrl = 'driver/drivers/display_drivers_list/';
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
            if ($this->lang->line('pagination_first_lbl') != '') $config['first_link'] =stripslashes($this->lang->line('pagination_first_lbl'));  else  $config['first_link'] ='First';
            if ($this->lang->line('pagination_last_lbl') != '') $config['last_link'] = stripslashes($this->lang->line('pagination_last_lbl'));  else  $config['last_link'] ='Last';
            $this->pagination->initialize($config);
            $paginationLink = $this->pagination->create_links();
            $this->data['paginationLink'] = $paginationLink;
        } else {
            $this->data['paginationLink'] = '';
            $this->data['driversList'] = $this->driver_model->get_all_details(DRIVERS, array());
        }
        $this->load->view('driver/drivers/display_drivers_list', $this->data);
    }

    /**
     *
     * This function loads the drivers add form
     *
     * */
    public function add_driver_form() {
        if ($this->checkLogin('D') == '') {
            if ($this->lang->line('driver_login_first') != '')
                $disp_message = stripslashes($this->lang->line('driver_login_first'));
            else
                $disp_message = 'You must login first';
            $this->setErrorMessage('error', $disp_message);
            redirect('driver');
        }
        if ($this->lang->line('driver_add_new_driver') != '')
            $disp_message = stripslashes($this->lang->line('driver_add_new_driver'));
        else
            $disp_message = 'Add New Driver';
        $this->data['heading'] = $disp_message;
        $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, array('status' => 'Active'));
        $this->data['docx_list'] = $this->driver_model->get_all_details(DOCUMENTS, array('status' => 'Active'));
        $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, array('status' => 'Active'), array('brand_name' => 'ASC'));
        $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, array('status' => 'Active'), array('name' => 'ASC'));
        $this->load->view('driver/drivers/add_driver', $this->data);
    }

    /**
     *
     * This function loads the drivers edit form
     *
     * */
    public function edit_driver_form() {
        if ($this->checkLogin('D') == '') {
            if ($this->lang->line('driver_login_first') != '')
                $disp_message = stripslashes($this->lang->line('driver_login_first'));
            else
                $disp_message = 'You must login first';
            $this->setErrorMessage('error', $disp_message);
            redirect('driver');
        }
        $driver_id = $this->uri->segment(4);
        if ($this->lang->line('driver_edit_driver') != '')
            $disp_message = stripslashes($this->lang->line('driver_edit_driver'));
        else
            $disp_message = 'Edit Driver';
        $this->data['heading'] = $disp_message;
        $condition = array('_id' => new \MongoId($driver_id));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
        if ($driver_details->num_rows() == 0) {
            if ($this->lang->line('driver_no_record') != '')
                $disp_message = stripslashes($this->lang->line('driver_no_record'));
            else
                $disp_message = 'No record found for this driver';
            $this->setErrorMessage('error', $disp_message);
            redirect('driver/drivers/display_drivers_list');
        }
        $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, array('status' => 'Active'));
        $this->data['docx_list'] = $this->driver_model->get_all_details(DOCUMENTS, array('status' => 'Active'));
        $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, array('status' => 'Active'), array('name' => 'ASC'));
        $this->data['brandList'] = $this->driver_model->get_all_details(BRAND, array('status' => 'Active'), array('brand_name' => 'ASC'));
        $this->data['modelList'] = $this->driver_model->get_all_details(MODELS, array('status' => 'Active'), array('name' => 'ASC'));
        $this->load->view('driver/drivers/edit_driver', $this->data);
    }

    /**
     *
     * This function Inserts & Edits the drivers
     *
     * */
    public function insertEdit_driver() {
        if ($this->checkLogin('D') == '') {
            if ($this->lang->line('driver_login_first') != '')
                $disp_message = stripslashes($this->lang->line('driver_login_first'));
            else
                $disp_message = 'You must login first';
            $this->setErrorMessage('error', $disp_message);
            redirect('driver');
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

        if ($driver_id == '') {
            $checkEmail = $this->driver_model->check_driver_exist(array('email' => $this->input->post('email')));
            if ($checkEmail->num_rows() >= 1) {
                if ($this->lang->line('driver_email_already_exist') != '')
                    $disp_message = stripslashes($this->lang->line('driver_email_already_exist'));
                else
                    $disp_message = 'This email already exist, please register with different email address.';
                $this->setErrorMessage('error', $disp_message);
                redirect('driver/drivers/add_driver_form');
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

        $excludeArr = array("driver_id", "confirm_password", "new_password", "address", "county", "state", "city", "postal_code", "driver_docx", "driver_docx_expiry", "vehicle_docx", "vehicle_docx_expiry", "vehicle_type", "mobile_number", "dail_code", 'ac');

        $addressArr['address'] = array('address' => $this->input->post('address'),
            'county' => $this->input->post('county'),
            'state' => $this->input->post('state'),
            'city' => $this->input->post('city'),
            'postal_code' => $this->input->post('postal_code')
        );

        $image_data = array();

        $config['overwrite'] = FALSE;
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
                if (count($fileArr) > 0) {
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

            if ($driver_id == '') {
                $documents['vehicle'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate, 'verify_status' => 'No');
            } else {
                $documents['vehicle'][(string) $fileTypeId] = array('typeName' => $docxName, 'fileName' => $fileName, 'expiryDate' => $expiryDate);
            }
        }

        $driver_data = array('created' => date('Y-m-d H:i:s'),
            'password' => md5($this->input->post('new_password')),
            'vehicle_type' => new \MongoId($this->input->post('vehicle_type')),
            'status' => $status,
            'ac' => $ac,
            'no_of_rides' => '0',
            'availability' => 'No',
            'mode' => 'Available',
            'dail_code' => (string) $this->input->post('dail_code'),
            'mobile_number' => (string) $this->input->post('mobile_number'),
            'category' => new \MongoId($this->input->post('category'))
        );

        if ($driver_id != '') {
            unset($driver_data['no_of_rides']);
            unset($driver_data['availability']);
            unset($driver_data['password']);
            unset($driver_data['mode']);
        }

        $dataArr = array_merge($driver_data, $image_data, $addressArr, array('documents' => $documents));
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

            /* Update Stats Starts */
            $current_date = new \MongoDate(strtotime(date("Y-m-d 00:00:00")));
            $field = array('driver.hour_' . date('H') => 1, 'driver.count' => 1);
            $this->driver_model->update_stats(array('day_hour' => $current_date), $field, 1);
            /* Update Stats End */
            if ($this->lang->line('driver_driver_added_success') != '')
                $disp_message = stripslashes($this->lang->line('driver_driver_added_success'));
            else
                $disp_message = 'Driver added successfully';

            $this->setErrorMessage('success', $disp_message);
        } else {
            $excludeArr[] = 'promo_code';
            $condition = array('_id' => new \MongoId($driver_id));
            $this->driver_model->commonInsertUpdate(DRIVERS, 'update', $excludeArr, $dataArr, $condition);
            if ($this->lang->line('driver_driver_added_success') != '')
                $disp_message = stripslashes($this->lang->line('driver_driver_added_success'));
            else
                $disp_message = 'Driver details updated successfully';
            $this->setErrorMessage('success', $disp_message);
        }
        redirect('driver/drivers/display_drivers_list');
    }

    /**
     *
     * This function uploads the documents via ajax for driver add & edit 
     *
     * */
    public function ajax_document_upload() {
        if ($this->checkLogin('D') == '') {
            if ($this->lang->line('driver_login_first') != '')
                $disp_message = stripslashes($this->lang->line('driver_login_first'));
            else
                $disp_message = 'You must login first';
            $this->setErrorMessage('error', $disp_message);
            redirect('driver');
        }
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
                        if ($this->lang->line('wallet_success') != '')
                            $disp_message = stripslashes($this->lang->line('wallet_success'));
                        else
                            $disp_message = 'Success';
                        $docResult['err_msg'] = $disp_message;
                    } else {
                        if ($this->lang->line('driver_couldnt_upload') != '')
                            $disp_message = stripslashes($this->lang->line('driver_couldnt_upload'));
                        else
                            $disp_message = 'File could not be uploaded';
                        $docResult['err_msg'] = $disp_message;
                    }
                } else {
                    if ($this->lang->line('driver_file_too_large') != '')
                        $disp_message = stripslashes($this->lang->line('driver_file_too_large'));
                    else
                        $disp_message = 'File too large. File must be less than 2 megabytes.';
                    $docResult['err_msg'] = $disp_message;
                }
            } else {
                if ($this->lang->line('driver_invalid_file_type') != '')
                    $disp_message = stripslashes($this->lang->line('driver_invalid_file_type'));
                else
                    $disp_message = 'Invalid file type. Only PDF, JPG, GIF and PNG types are accepted.';
                $docResult['err_msg'] = $disp_message;
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
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $mode = $this->uri->segment(4, 0);
            $driver_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'No' : 'Yes';
            $newdata = array('verify_status' => $status);
            $condition = array('_id' => new \MongoId($driver_id));
            $this->driver_model->update_details(DRIVERS, $newdata, $condition);
            if ($this->lang->line('driver_status_changed') != '')
                $disp_message = stripslashes($this->lang->line('driver_status_changed'));
            else
                $disp_message = 'Driver Verification Status Changed Successfully';
            $this->setErrorMessage('success', $disp_message);
            redirect('driver/drivers/display_drivers_list');
        }
    }

    /**
     *
     * This function change the driver status
     *
     * */
    public function change_driver_status() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $mode = $this->uri->segment(4, 0);
            $driver_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => new \MongoId($driver_id));
            $this->driver_model->update_details(DRIVERS, $newdata, $condition);
            if ($this->lang->line('driver_status_change_success') != '')
                $disp_message = stripslashes($this->lang->line('driver_status_change_success'));
            else
                $disp_message = 'Driver Status Changed Successfully';
            $this->setErrorMessage('success', $disp_message);
            redirect('driver/drivers/display_drivers_list');
        }
    }

    /**
     * 
     * This function delete the driver from db
     *
     * */
    public function delete_driver() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $promo_id = $this->uri->segment(4, 0);
            $condition = array('_id' => new \MongoId($promo_id));
            $this->driver_model->commonDelete(DRIVERS, $condition);
            if ($this->lang->line('driver_deleted') != '')
                $disp_message = stripslashes($this->lang->line('driver_deleted'));
            else
                $disp_message = 'Driver deleted successfully';
            $this->setErrorMessage('success', $disp_message);
            redirect('driver/drivers/display_drivers_list');
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
                if ($this->lang->line('driver_deleted') != '')
                    $disp_message = stripslashes($this->lang->line('driver_deleted'));
                else
                    $disp_message = 'Driver deleted successfully';
                $this->setErrorMessage('success', $disp_message);
            } else {
                if ($this->lang->line('driver_status_change_success') != '')
                    $disp_message = stripslashes($this->lang->line('driver_status_change_success'));
                else
                    $disp_message = 'Driver Status Changed Successfully';
                $this->setErrorMessage('success', $disp_message);
            }
            redirect('driver/drivers/display_drivers_list');
        }
    }

    /**
     * 
     * This function loads the driver password
     *
     * */
    public function change_password_form() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        }
        $driverId = $this->uri->segment(4);
        if ($this->lang->line('driver_change_password') != '')
            $disp_message = stripslashes($this->lang->line('driver_change_password'));
        else
            $disp_message = 'Change Driver Password';
        $this->data['heading'] = $disp_message;
        $condition = array('_id' => new \MongoId($driverId));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
        $this->load->view('driver/drivers/change_password', $this->data);
    }

    /**
     * 
     * This function loads the driver password
     *
     * */
    public function change_password() {
        if ($this->checkLogin('D') == '' || $this->input->post('new_password') == '') {
            redirect('driver');
        }
        $password = $this->input->post('new_password');
        $driverId = $this->input->post('driver_id');
        $dataArr = array('password' => md5($this->input->post('new_password')));
        $condition = array('_id' => new \MongoId($driverId));
        $driver_details = $this->driver_model->update_details(DRIVERS, $dataArr, $condition);

        /*         * **  send password to driver through email **** */
        $driverinfo = $this->driver_model->get_all_details(DRIVERS, $condition);
        $this->send_driver_pwd($password, $driverinfo);
        if ($this->lang->line('driver_password_changed_sent') != '')
            $disp_message = stripslashes($this->lang->line('driver_password_changed_sent'));
        else
            $disp_message = 'Driver password changed and sent to driver successfully';
        $this->setErrorMessage('success', $disp_message);
        redirect('driver/drivers/display_drivers_list');
    }

    /**
     * 
     * This function send the new password to driver email
     *
     * */
    public function send_driver_pwd($pwd = '', $driverinfo) {
        $driver_name = $driverinfo->row()->driver_name;
        $newsid = '2';
        $template_values = $this->driver_model->get_newsletter_template_details($newsid);
        $subject = $template_values->message['subject'];
        $drivernewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->config->item('logo_image'), 'footer_content' => $this->config->item('footer_content'), 'meta_title' => $this->config->item('meta_title'), 'site_contact_mail' => $this->config->item('site_contact_mail'));
        extract($drivernewstemplateArr);
        $message = '<!DOCTYPE HTML>
			<html>
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta name="viewport" content="width=device-width"/>
			<title>' . $template_values->message['subject'] . '</title>
			<body>';
        include('./newsletter/template' . $newsid . '.php');
        $message .= '</body>
			</html>';

        if ($template_values->sender['name'] == '' && $template_values->sender['email'] == '') {
            $sender_email = $this->config->item('site_contact_mail');
            $sender_name = $this->config->item('email_title');
        } else {
            $sender_name = $template_values->sender['name'];
            $sender_email = $template_values->sender['email'];
        }
        $email_values = array('mail_type' => 'html',
            'from_mail_id' => $sender_email,
            'mail_name' => $sender_name,
            'to_mail_id' => $driverinfo->row()->email,
            'subject_message' => $subject,
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
        if ($this->checkLogin('D') == '') {
            if ($this->lang->line('driver_login_first') != '')
                $disp_message = stripslashes($this->lang->line('driver_login_first'));
            else
                $disp_message = 'You must login first';
            $this->setErrorMessage('error', $disp_message);
            redirect('driver');
        }
        $driverId = $this->uri->segment(4);


        if ($this->lang->line('driver_view_details') != '')
            $disp_message = stripslashes($this->lang->line('driver_view_details'));
        else
            $disp_message = 'View Driver Details';
        $this->data['heading'] = $disp_message;

        $condition = array('_id' => new \MongoId($driverId));
        $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);

        if ($driver_details->num_rows() == 0) {
            if ($this->lang->line('driver_no_records_found') != '')
                $disp_message = stripslashes($this->lang->line('driver_no_records_found'));
            else
                $disp_message = 'No records found';
            $this->setErrorMessage('error', $disp_message);
            redirect('driver/drivers/display_drivers_list');
        }

        $veh_condition = array('_id' => new \MongoId($driver_details->row()->vehicle_type));
        $this->data['vehicle_types'] = $vehicle_types = $this->driver_model->get_all_details(VEHICLES, $veh_condition);

        $cat_condition = array('_id' => new \MongoId($driver_details->row()->category));
        $this->data['driver_category'] = $driver_category = $this->driver_model->get_all_details(CATEGORY, $cat_condition);

        $maker_condition = array('_id' => new \MongoId($driver_details->row()->vehicle_maker));
        $this->data['vehicle_maker'] = $vehicle_maker = $this->driver_model->get_all_details(BRAND, $maker_condition);

        $vehicle_model_model = array('_id' => new \MongoId($driver_details->row()->vehicle_model));
        $this->data['vehicle_model'] = $vehicle_model = $this->driver_model->get_all_details(MODELS, $vehicle_model_model);

        $this->load->view('driver/drivers/view_driver', $this->data);
    }

    /**
     * 
     * This function updates the drivers document verification status via ajax
     *
     * */
    public function document_verify_status_ajax() {
        if ($this->checkLogin('D') == '') {
            echo 'Error';
            redirect('driver');
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
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            if ($this->lang->line('driver_drivers_category') != '')
                $disp_message = stripslashes($this->lang->line('driver_drivers_category'));
            else
                $disp_message = 'Drivers Category';
            $this->data['heading'] = $disp_message;
            $condition = array();
            $this->data['categoryList'] = $this->driver_model->get_all_details(CATEGORY, $condition);
            $this->load->view('driver/drivers/display_drivers_category', $this->data);
        }
    }

    /**
     *
     * This function loads the add/Edit Category form
     *
     * */
    public function add_edit_category() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $category_id = $this->uri->segment(4, 0);
            $form_mode = FALSE;
            if ($this->lang->line('driver_add_new_category') != '')
                $disp_message = stripslashes($this->lang->line('driver_add_new_category'));
            else
                $disp_message = 'Add New Category';
            $heading = $disp_message;
            if ($category_id != '') {
                $condition = array('_id' => new \MongoId($category_id));
                $this->data['categorydetails'] = $this->driver_model->get_all_details(CATEGORY, $condition);
                if ($this->data['categorydetails']->num_rows() != 1) {
                    redirect('driver/drivers/display_drivers_category');
                }
                $form_mode = TRUE;
                if ($this->lang->line('driver_edit_category') != '')
                    $disp_message = stripslashes($this->lang->line('driver_edit_category'));
                else
                    $disp_message = 'Edit Category';
                $heading = $disp_message;
            }
            $this->data['form_mode'] = $form_mode;
            $this->data['heading'] = $heading;
            $this->load->view('driver/drivers/add_edit_category', $this->data);
        }
    }

    /**
     *
     * This function insert vehicle informations into databse
     *
     * */
    public function insertEditCategory() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
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
                if ($this->lang->line('driver_cat_exists') != '')
                    $disp_message = stripslashes($this->lang->line('driver_cat_exists'));
                else
                    $disp_message = 'Category already exists';
                $this->setErrorMessage('error', $disp_message);
                redirect('driver/drivers/add_edit_category/' . $category_id);
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
                //$config['encrypt_name'] = TRUE;
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
                    redirect('driver/drivers/add_edit_vehicle_type_form/' . $category_id);
                }
                $vehicle_data = array('image' => $category_image);
            } else {
                $vehicle_data = array();
            }
            $dataArr = array_merge($inputArr, $vehicle_data);

            if ($category_id == '') {
                $this->driver_model->simple_insert(CATEGORY, $dataArr);
                if ($this->lang->line('driver_cat_added') != '')
                    $disp_message = stripslashes($this->lang->line('driver_cat_added'));
                else
                    $disp_message = 'Category added successfully';
                $this->setErrorMessage('success', $disp_message);
            } else {
                $condition = array('_id' => new \MongoId($category_id));
                $this->driver_model->update_details(CATEGORY, $dataArr, $condition);
                if ($this->lang->line('driver_cat_updated') != '')
                    $disp_message = stripslashes($this->lang->line('driver_cat_updated'));
                else
                    $disp_message = 'Category updated successfully';
                $this->setErrorMessage('success', $disp_message);
            }
            redirect('driver/drivers/display_drivers_category');
        }
    }

    /**
     *
     * This function change the status of the Driver Category
     *
     * */
    public function change_category_status() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $mode = $this->uri->segment(4, 0);
            $category_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => new \MongoId($category_id));
            $this->driver_model->update_details(CATEGORY, $newdata, $condition);
            if ($this->lang->line('driver_cat_status_changed') != '')
                $disp_message = stripslashes($this->lang->line('driver_cat_status_changed'));
            else
                $disp_message = 'Category Status Changed Successfully';
            $this->setErrorMessage('success', $disp_message);
            redirect('driver/drivers/display_drivers_category');
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
                $this->setErrorMessage('success', 'Category records deleted successfully');
            } else {
                $this->setErrorMessage('success', 'Category records status changed successfully');
            }
            redirect('driver/drivers/display_drivers_category');
        }
    }

    /**
     *
     * This function loads the add/Edit vehicle types in a category
     *
     * */
    public function add_edit_category_types() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $category_id = $this->uri->segment(4, 0);
            if ($category_id != '') {
                $condition = array('_id' => new \MongoId($category_id));
                $this->data['categorydetails'] = $this->driver_model->get_all_details(CATEGORY, $condition);
                $this->data['vehicle_types'] = $this->driver_model->get_all_details(VEHICLES, array('status' => 'Active'));
                if ($this->data['categorydetails']->num_rows() != 1) {
                    redirect('driver/drivers/display_drivers_category');
                }
            }
            $this->data['heading'] = 'Vehicle types under category';
            $this->load->view('driver/drivers/add_edit_category_types', $this->data);
        }
    }

    /**
     *
     * This function iadd/Edit vehicle types in a category informations into databse
     *
     * */
    public function insertEditTypes() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $category_id = $this->input->post('category_id');
            if ($category_id != '' && $this->input->post('vehicle_type') != NULL) {
                $vehicle_type = $this->input->post('vehicle_type');
                $dataArr = array('vehicle_type' => $vehicle_type);
                $condition = array('_id' => new \MongoId($category_id));
                $this->driver_model->update_details(CATEGORY, $dataArr, $condition);
                $this->setErrorMessage('success', 'Category\'s types updated successfully');
                redirect('driver/drivers/display_drivers_category');
            } else {
                $this->setErrorMessage('error', 'Category\'s types cannot be updated');
                redirect('driver/drivers/add_edit_category_types/' . $category_id);
            }
        }
    }

    /**
     *
     * This function loads the add/Edit Banking Informations form
     *
     * */
    public function banking() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        } else {
            $driver_id = $this->uri->segment(4, 0);
            $form_mode = FALSE;
            $heading = 'Add Banking Details';
            if ($driver_id != '') {
                $condition = array('_id' => new \MongoId($driver_id));
                $this->data['driver_details'] = $driver_details = $this->driver_model->get_all_details(DRIVERS, $condition);
                if ($this->data['driver_details']->num_rows() != 1) {
                    redirect('driver/drivers/display_drivers_list');
                }
                $form_mode = TRUE;
                $heading = 'Edit Banking Details';
            }
            $this->data['form_mode'] = $form_mode;
            $this->data['heading'] = $heading;
            $this->load->view('driver/drivers/add_edit_banking', $this->data);
        }
    }

    /**
     *
     * This function inserts / edit the driver banking informations
     * */
    public function insertEditDriverBanking() {
        if ($this->checkLogin('D') == '') {
            redirect('driver');
        }
        $driver_id = $this->input->post('driver_id');
        $postedValues = $_POST;
        unset($postedValues['driver_id']);

        $dataArr = array('banking' => $postedValues);  #echo '<pre>'; print_r($dataArr);
        $condition = array('_id' => new \MongoId($driver_id));
        $this->driver_model->update_details(DRIVERS, $dataArr, $condition);
        $this->setErrorMessage('success', 'Driver banking details updated successfully');
        redirect('driver/drivers/banking/' . $driver_id);
    }

}

/* End of file drivers.php */
/* Location: ./application/controllers/driver/drivers.php */