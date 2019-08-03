<?php

/**
 *
 * This controller contains the functions related to Multi-language Management
 * @author Casperon
 *
 */
class Multilanguage extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');
        $this->load->helper('language');
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('admin_model');
        $this->load->model('multilanguage_model');
        $this->load->helper('directory');
        if ($this->checkPrivileges('multilang', $this->privStatus) == FALSE) {
            redirect('admin');
        }
    }

    /**
     *
     * This function loads the language list
     */
    function index() {
        $this->display_language_list();
    }

    /**
     *
     * This function loads the language list
     */
    function display_language_list() {

        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
		    if ($this->lang->line('admin_multilanguage_multi_language_management') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_multilanguage_multi_language_management')); 
		    else  $this->data['heading'] = 'Multi Language Management';
            $this->data['admin_settings'] = $result = $this->admin_model->getAdminSettings();
            $this->data['language_list'] = $result = $this->multilanguage_model->get_language_list();
            $this->load->view('admin/multilanguage/language_list', $this->data);
        }
    }

    /**
     *
     * This function loads the new language list form
     *
     * */
    public function add_new_lg() {
        if ($this->checkLogin('A') == '') {
            show_404();
        } else {
		    if ($this->lang->line('admin_multilanguage_add_new_language') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_multilanguage_add_new_language')); 
		    else  $this->data['heading'] = 'Add New Language';
            $this->load->view('admin/multilanguage/add_new_lg', $this->data);
        }
    }

    /**
     *
     * This function load the edit language list form
     *
     * */
    function edit_language() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {

            $file_name_prefix = 'file';
            $file_number = $this->uri->segment(5);

            $selectedLanguage = $this->uri->segment('4');
            $languagDirectory = APPPATH . 'language/' . $selectedLanguage;
            //echo $languagDirectory;


            $get_english_lang_count = directory_map(APPPATH . "language/en/");
            //echo "<pre>";print_r($get_english_lang_count);die;
            $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $file_name_prefix . $file_number . "_lang.php";
            if (!is_dir($languagDirectory)) {

                mkdir($languagDirectory, 0777);

                if (!is_file($filePath)) {

                    mkdir($languagDirectory, 0777);
                    file_put_contents($filePath, '');
                }
            }
            //echo $filePath;die;
            // $this->lang->load('file1', $selectedLanguage);
            if (is_file($filePath)) {
                $this->lang->load($file_name_prefix . $file_number, $selectedLanguage);
            }

            //$filePath = APPPATH."language/en/".$file_name_prefix.$file_number."_lang.php";		
            $filePath = APPPATH . "language/en/" . $file_name_prefix . $file_number . "_lang.php";
            $fileValues = file_get_contents($filePath);
            #echo "<pre>";print_r($fileValues);die;
            /*             * ******************************** Key value explode start ************************************ */
            $fileKeyValues_explode1 = @explode("\$lang['", $fileValues);
            $language_file_keys = array();
            foreach ($fileKeyValues_explode1 as $fileKeyValues2) {
                $fileKeyValues_explode2 = @explode("']", $fileKeyValues2);
                $language_file_keys[] = $fileKeyValues_explode2[0];
            }
            /*             * ******************************** Key value explode end ************************************ */

            /*             * ********************************  value explode start ************************************ */
            $fileValues_explode1 = @explode("']='", $fileValues);
            $language_file_values = array();

            #echo "<pre>";print_r($fileKeyValues_explode1);die;
            foreach ($fileValues_explode1 as $fileValues2) {
                $fileValues_explode2 = @explode("';", $fileValues2);
                $language_file_values[] = $fileValues_explode2[0];
            }
            /*             * ********************************  value explode end ************************************ */

            #echo count($get_english_lang_count);die;
            #echo "<pre>";print_r($language_file_keys); echo "<pre>";print_r($language_file_values);die;	
            $this->data['file_key_values'] = $language_file_keys;
            $this->data['file_lang_values'] = $language_file_values;
            $this->data['selectedLanguage'] = $selectedLanguage;
		           if ($this->lang->line('admin_multilanguage_edit_language') != '') 
		          $this->data['heading']= stripslashes($this->lang->line('admin_multilanguage_edit_language')); 
		         else  $this->data['heading'] = 'Edit Language';
            $this->data['file_name_prefix'] = $file_name_prefix;
            $this->data['get_total_files'] = count($get_english_lang_count);
            $this->data['current_file_no'] = $file_number;
            $this->data['admin_settings'] = $result = $this->admin_model->getAdminSettings();
            $this->load->view('admin/multilanguage/language_edit', $this->data);
        }
    }

    /**
     *
     * This function add/edit the language list in lang file
     */
    function languageAddEditValues() {

        $getLanguageKeyDetails = $this->input->post('languageKeys');
        $getLanguageContentDetails = $this->input->post('language_vals');
        $selectedLanguage = $this->input->post('selectedLanguage');
        $file_name_prefix = $this->input->post('file_name_prefix');
        $current_file_no = $this->input->post('current_file_no');
        // echo "<pre>";print_r($getLanguageContentDetails);die;
        /* file write start */
        $loopItem = 0;
        $config = '<?php';
        foreach ($getLanguageKeyDetails as $key_val) {
            $language_file_values = addslashes($getLanguageContentDetails[$loopItem]);
            $config .= "\n\$lang['$key_val'] = '$language_file_values'; ";
            $loopItem = $loopItem + 1;
        }

        $config .= ' ?>';

        $languagDirectory = APPPATH . "language/" . $selectedLanguage;
        if (!is_dir($languagDirectory)) {
            mkdir($languagDirectory, 0777);
        }

        //$filePath = APPPATH."language/".$selectedLanguage."/".$selectedLanguage."_lang.php";
        $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $file_name_prefix . $current_file_no . "_lang.php";
        file_put_contents($filePath, $config);
        //redirect('admin/multilanguage/display_language_list');
        $get_folder_files = directory_map(APPPATH . "language/" . $selectedLanguage);



        /*         * ****** Merge all sub files into language single language file every time update start *************** */


        $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $selectedLanguage . "_lang.php";

        if (!is_file($filePath)) {
            mkdir($languagDirectory, 0777);
            file_put_contents($filePath, '');
        }
        file_put_contents($filePath, '');

        foreach ($get_folder_files as $file_name_dtls) {
            if ($file_name_dtls != $selectedLanguage . "_lang.php") {
                $open_file_to_append = APPPATH . "language/" . $selectedLanguage . "/" . $file_name_dtls;
                $handle = fopen($filePath, 'a');
                $data = file_get_contents($open_file_to_append);
                fwrite($handle, $data);
            }
        }
        /*         * ****** Merge all sub files into language single language file eveerytime update end *************** */


        /*         * ****** Merge all english sub files into english language single language file eveerytime update start *************** */
        $get_en_folder_files = directory_map(APPPATH . "language/en");

        $filePath = APPPATH . "language/en/en_lang.php";

        if (!is_file($filePath)) {
            mkdir($languagDirectory, 0777);
            file_put_contents($filePath, '');
        }
        file_put_contents($filePath, '');
        //echo "<pre>";print_r($get_en_folder_files);die;
        foreach ($get_en_folder_files as $file_name_dtls) {
            if ($file_name_dtls != "en_lang.php") {
                $open_file_to_append = APPPATH . "language/en/" . $file_name_dtls;
                $handle = fopen($filePath, 'a');
                $data = file_get_contents($open_file_to_append);
                fwrite($handle, $data);
            }
        }
        /*         * ****** Merge all sub files into language single language file eveerytime update end *************** */

        redirect('admin/multilanguage/edit_language/' . $selectedLanguage . "/" . $current_file_no);
    }

    /**
     *
     * This function delete the language 
     *
     * */
    function delete_language() {
        $languageId = $this->uri->segment('4');
        if ($languageId != '') {
            $languageDetails = $this->multilanguage_model->get_all_details(LANGUAGES, array('_id' => new \MongoId($languageId)));
            if ($languageDetails->num_rows() > 0) {
                if ($languageDetails->row()->default_language == 'Yes') {
                    $this->setErrorMessage('error', " You cannot remove the default language.",'admin_multilanguage_remove_default');
                } else {
                    $delete_language = $this->multilanguage_model->delete_language($languageId);
                    $this->setErrorMessage('success', " Language deleted successfully",'admin_multilanguage_delete_success');
                }
            }
            redirect('admin/multilanguage/display_language_list');
        } else {
            redirect('admin/multilanguage/display_language_list');
        }
    }

    /**
     *
     * This function change the language status
     *
     * */
    function change_multi_language_details() {
        $statusMode = $this->input->post('statusMode');
        $checkbox_id = $this->input->post('checkbox_id');
        $checkboxId = array();
        foreach ($checkbox_id as $cid) {
            if ($cid != 'on' && $cid != 'off') {
                $checkboxId[] = new \MongoId($cid);
            }
        }
        if ($statusMode != '' && !empty($checkboxId)) {
            $change_language_status = $this->multilanguage_model->change_language_status($statusMode, $checkboxId);
            $this->setErrorMessage('success', " Language settings changed successfully",'admin_multilanguage_setting_change_success');
            redirect('admin/multilanguage/display_language_list');
        } else {
            redirect('admin');
        }
    }

    /**
     *
     * This function change the language status
     */
    function change_language_status() {
        $current_status = $this->uri->segment('4');
        $languageId = $this->uri->segment('5');
        if ($current_status != '' && $languageId != '') {
            $languageDetails = $this->multilanguage_model->get_all_details(LANGUAGES, array('_id' => new \MongoId($languageId)));
            if ($languageDetails->num_rows() > 0) {
                if ($languageDetails->row()->default_language == 'Yes') {
                    $this->setErrorMessage('error', " You cannot change the default language status.",'admin_multilanguage_defalut_language');
                } else {
                    $change_language_details = $this->multilanguage_model->change_language_details($current_status, $languageId);
                    $this->setErrorMessage('success', " Language settings changed successfully",'admin_multilanguage_language_setting_change');
                }
            }
            redirect('admin/multilanguage/display_language_list');
        } else {
            redirect('admin/multilanguage/display_language_list');
        }
    }

    /**
     *
     * This function change the default language
     *
     * */
    public function change_language_default() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $mode = $this->uri->segment(4, 0);
            $language_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'No' : 'Yes';

            $languageDetails = $this->multilanguage_model->get_all_details(LANGUAGES, array('_id' => new \MongoId($language_id)));
            if ($languageDetails->num_rows() > 0) {
                if ($languageDetails->row()->default_language == 'Yes' && $status == 'No') {
                    $this->setErrorMessage('error', 'There should be atleast one default language.','admin_multilanguage_atleast_default_language');
                } else {
                    if ($languageDetails->row()->status == 'Inactive') {
                        $this->setErrorMessage('error', 'Default language should be in active status.','admin_multilanguage_default_language_active_status');
                    } else {
                        $filePath = APPPATH . "language/" . $languageDetails->row()->lang_code . "/" . $languageDetails->row()->lang_code . "_lang.php";
                        if (!is_file($filePath)) {
                            $this->setErrorMessage('error', 'This language is cannot make as defaul currently.','admin_multilanguage_cannot_default_currently');
                        } else {
                            $this->multilanguage_model->update_details(LANGUAGES, array('default_language' => 'No'), array('default_language' => 'Yes'));
                            $newdata = array('default_language' => $status);
                            $condition = array('_id' => new \MongoId($language_id));
                            $this->multilanguage_model->update_details(LANGUAGES, $newdata, $condition);
                            $getLanguage = $this->multilanguage_model->get_all_details(LANGUAGES, $condition);
                            /*  Write default language in config setting  */
                            if (isset($getLanguage->row()->default_language)) {
                                if ($getLanguage->row()->default_language == 'Yes') {
                                    $file = 'commonsettings/dectar_lang_settings.php';
                                    if (!is_file($file)) {
                                        mkdir($languagDirectory, 0777);
                                        file_put_contents($file, '');
                                    }
                                    $lonCode = $getLanguage->row()->lang_code;
                                    $lonName = $getLanguage->row()->name;
                                    $config = '<?php ';
                                    $config .= "\n\$config['default_lang_code'] = '$lonCode'; ";
                                    $config .= "\n\$config['default_lang_name'] = '$lonName'; ";
                                    $config .= "\n ?>";
                                    file_put_contents($file, $config);
                                }
                            }
                            $this->setErrorMessage('success', 'This language changed As default','admin_multilanguage_language_default');
                        }
                    }
                }
            }
            redirect('admin/multilanguage/display_language_list');
        }
    }

    /**
     *
     * This function check the language list
     *
     * */
    public function add_lg_process() {
        if ($this->checkLogin('A') == '') {
            show_404();
        } else {
            $lname = $this->input->post('name');
            $lcode = $this->input->post('lang_code');
            $duplicateName = $this->multilanguage_model->get_all_details(LANGUAGES, array('name' => $lname));
            if ($duplicateName->num_rows() > 0) {
                $this->setErrorMessage('error', 'Language name already exists','admin_multilanguage_language_name_already_exist');
                echo "<script>window.history.go(-1);</script>";
                exit();
            } else {
                $duplicateCode = $this->multilanguage_model->get_all_details(LANGUAGES, array('lang_code' => $lcode));
                if ($duplicateCode->num_rows() > 0) {
                    $this->setErrorMessage('error', 'Language code already exists','admin_multilanguage_language_code_already_exist');
                    echo "<script>window.history.go(-1);</script>";
                    exit();
                } else {
                    $dataArr = array('default_language' => 'No');
                    if ($lcode == 'en') {
                        $dataArr = array('default_language' => 'Yes');
                        $file = 'commonsettings/dectar_lang_settings.php';
                        if (!is_file($file)) {
                            mkdir($languagDirectory, 0777);
                            file_put_contents($file, '');
                        }
                        $config = '<?php ';
                        $config .= "\n\$config['default_lang_code'] = '$lcode'; ";
                        $config .= "\n\$config['default_lang_name'] = '$lname'; ";
                        $config .= "\n ?>";
                        file_put_contents($file, $config);
                    }
                    $this->multilanguage_model->commonInsertUpdate(LANGUAGES, 'insert', array(), $dataArr);
                    $this->setErrorMessage('success', 'Language added successfully');
                    redirect('admin/multilanguage/display_language_list');
                }
            }
        }
    }

	/**
	*
	* Loads the language form for the mobile app
	*
	**/
    public function mobile_edit_language() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $this->data['selectedLang'] = $selectedLang = $this->uri->segment('4');
            $this->data['admin_settings'] = $this->admin_model->getAdminSettings();
            $this->data['language_list'] = $this->multilanguage_model->get_language_list();
            $this->data['language_list_db'] = $this->multilanguage_model->get_all_details(MOBILE_LANGUAGES, array('language_code' => $selectedLang));
            $condition = array('language_code' => 'en');
            $langData = $this->multilanguage_model->get_selected_fields(MOBILE_LANGUAGES, $condition);
            $this->data['language_key_values'] = $this->loadLanguageFromJSON();
			
		    if ($this->lang->line('admin_edit_language_for_mobiles') != '') {
				$this->data['heading']= stripslashes($this->lang->line('admin_edit_language_for_mobiles')); 
			}else{
				$this->data['heading'] = 'Edit Languages for Mobiles';
			}
			
            $this->load->view('admin/multilanguage/mobile_language_list', $this->data);
        }
    }

    public function add_edit_mobile_language() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $getLanguageKeyDetails = $this->input->post('languageKeys');
            $getLanguageContentDetails = $this->input->post('language_vals');
            $selectedLang = $this->input->post('selectedLang');
            $langArr = array();
            $excludeArray = array('languageKeys', 'language_vals', 'selectedLang');
            $loopItem = 0;
            foreach ($getLanguageKeyDetails as $key_val) {
                $langArr[$key_val] = $getLanguageContentDetails[$loopItem];
                $loopItem = $loopItem + 1;
            }
            $finalArray = array('key_values' => $langArr);
            $condition = array('language_code' => $selectedLang);
            $checkLangExists = $this->multilanguage_model->get_selected_fields(MOBILE_LANGUAGES, $condition);

            if ($checkLangExists->num_rows() == 0) {
                $finalArray['language_code'] = $selectedLang;
                $this->multilanguage_model->commonInsertUpdate(MOBILE_LANGUAGES, 'insert', $excludeArray, $finalArray);
            } else {
                $this->multilanguage_model->commonInsertUpdate(MOBILE_LANGUAGES, 'update', $excludeArray, $finalArray, $condition);
            }
			
            redirect('admin/multilanguage/mobile_edit_language/');
        }
    }


	/**
	*
	* Loads the language form for the keywords
	*
	**/
    public function keyword_edit_language() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $this->data['selectedLang'] = $selectedLang = $this->uri->segment('4');
            $this->data['language_list'] = $this->multilanguage_model->get_language_list();
            $this->data['language_list_db'] = $this->multilanguage_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $selectedLang,'type'=>"keyword"));
            $this->data['language_key_values'] = get_language_files_for_keywords();
			
		    if ($this->lang->line('admin_edit_language_for_keywords') != '') {
				$this->data['heading']= stripslashes($this->lang->line('admin_edit_language_for_keywords')); 
			}else{
				$this->data['heading'] = 'Edit Languages for Keywords';
			}
			
            $this->load->view('admin/multilanguage/keyword_language_list', $this->data);
        }
    }
	
	/**
	*
	* Loads the language form for the validation
	*
	**/
    public function validation_edit_language() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
			$this->data['selectedLang'] = $selectedLang = $this->uri->segment('4');
			$this->data['language_list'] = $this->multilanguage_model->get_language_list();
			$this->data['language_list_db'] = $this->multilanguage_model->get_all_details(MULTI_LANGUAGES, array('language_code' => $selectedLang,'type'=>"validation"));
			$this->data['language_key_values'] = get_language_files_for_validation();
			
		    if ($this->lang->line('admin_edit_language_for_validation') != '') {
				$this->data['heading']= stripslashes($this->lang->line('admin_edit_language_for_validation')); 
			}else{
				$this->data['heading'] = 'Edit Languages for Validation Messages';
			}
			
            $this->load->view('admin/multilanguage/validation_language_list', $this->data);
        }
    }

    public function add_edit_keyval_language() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $getLanguageKeyDetails = $this->input->post('languageKeys');
            $getLanguageContentDetails = $this->input->post('language_vals');
            $selectedLang = $this->input->post('selectedLang');
            $type = $this->input->post('type');
            $langArr = array();
            $excludeArray = array('languageKeys', 'language_vals', 'selectedLang');
            $loopItem = 0;
            foreach ($getLanguageKeyDetails as $key_val) {
                $langArr[$key_val] = $getLanguageContentDetails[$loopItem];
                $loopItem = $loopItem + 1;
            }
            $finalArray = array('key_values' => $langArr);
            $condition = array('language_code' => $selectedLang,'type' => (string)$type);
            $checkLangExists = $this->multilanguage_model->get_selected_fields(MULTI_LANGUAGES, $condition);

            if ($checkLangExists->num_rows() == 0) {
                $finalArray['language_code'] = $selectedLang;
                $this->multilanguage_model->commonInsertUpdate(MULTI_LANGUAGES, 'insert', $excludeArray, $finalArray);
            } else {
                $this->multilanguage_model->commonInsertUpdate(MULTI_LANGUAGES, 'update', $excludeArray, $finalArray, $condition);
            }
			if($type=="keyword"){
				redirect('admin/multilanguage/keyword_edit_language/');
			}else{
				redirect('admin/multilanguage/validation_edit_language/');
			}
            
        }
    }

}

?>