<?php
 
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * Site landing page related functions
 * @author Casperon
 *
 * */
class Landing extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('user_model');
        $returnArr = array();
        
    }

    public function index() {  
      
		$heading = 'Welcome - ' . $this->config->item('email_title');
        $this->data['banner']=$this->user_model->get_all_details(BANNER, array('status' => 'Publish'));
        $this->data['heading'] = $heading;
		$this->data['landing_details']=$this->user_model->get_all_details(LANDING_CONTENT);
		/* Dynamic Header and Footer Menu */
		$lang = $this->data['dLangCode'];
		if($this->data['langCode'] != $this->data['dLangCode']){
			$lang = $this->data['langCode'];
		}
        
		$header_menu = $this->user_model->get_selected_fields(MENU,array('name'=>'top_menu'),array('added_pages','add_home_navigation'));
		if($header_menu->num_rows()>0){
			$this->data['header_home'] = 'no';
			if(isset($header_menu->row()->add_home_navigation)){
				if($header_menu->row()->add_home_navigation == 'yes'){
					$this->data['header_home'] = 'yes';
			}
				
			}
            #echo "<pre>";
			$hmenu_details = array();
			if(isset($header_menu->row()->added_pages)){
				if(!empty($header_menu->row()->added_pages)){
					$i=0;
					foreach($header_menu->row()->added_pages as $header_page){
						if($header_page!=''){
							$detail = $this->user_model->get_all_details(CMS,array('_id'=>new MongoId($header_page)));
                            #print_r($detail->row());
							if($detail->num_rows() > 0){
								$seourl = $detail->row()->seourl;
								$hmenu_details[$i] = array('name'=>$detail->row()->page_name,'url'=>$seourl);
								if(isset($detail->row()->$lang)){
									if(!empty($detail->row()->$lang)){
										$detail = $detail->row()->$lang;
										$hmenu_details[$i] = array('name'=>$detail['page_name'],'url'=>$seourl);
									}
								}
								$i++;
							}
						}
					}
				}
				
			}
            
            #print_r($hmenu_details);
            #exit;
			$this->data['header_menu'] = $hmenu_details;
		}
		
		$footer_menu = $this->user_model->get_selected_fields(MENU,array('name'=>'footer_menu'),array('added_pages','add_home_navigation'));
		
		if($footer_menu->num_rows()>0){
			$this->data['footer_home'] = 'no';
			if(isset($header_menu->row()->add_home_navigation)){
				if($header_menu->row()->add_home_navigation == 'yes'){
					$this->data['footer_home'] = 'yes';
			}
				
			}
			$fmenu_details = array();
			if(isset($footer_menu->row()->added_pages)){
				if(!empty($footer_menu->row()->added_pages)){
					$i=0;
					foreach($footer_menu->row()->added_pages as $footer_page){
						if($footer_page!=''){
							$detail = $this->user_model->get_all_details(CMS,array('_id'=>new MongoId($footer_page)));
							
							$seourl = "";
							if(isset($detail->row()->seourl)){
								$seourl = $detail->row()->seourl;	
							}
							if($seourl!=""){
								$fmenu_details[$i] = array('name'=>$detail->row()->page_name,'url'=>$seourl);
								if(isset($detail->row()->$lang)){
									if(!empty($detail->row()->$lang)){
										$detail = $detail->row()->$lang;
										$fmenu_details[$i] = array('name'=>$detail['page_name'],'url'=>$seourl);
									}
								}
								$i++;
							}
						}
					}
				}
				
			}
			$this->data['footer_menu'] = $fmenu_details;
		}
		/* Dynamic Header and Footer Menu --------- Ends Here */
        $this->load->view('site/landing/landing', $this->data);
    }

    public function changeLangage() {
        $choosenlonCode = $this->input->get('q');
        if ($choosenlonCode != '') {

            /* Load selected lang library */

            $defaultLanguage = $this->config->item('default_lang_code');
            if ($defaultLanguage == '') {
                $defaultLanguage = 'en';
            }

            $selectedLanguage = $choosenlonCode;
            $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $selectedLanguage . "_lang.php";
            if ($selectedLanguage != '') {
                if (!(is_file($filePath))) {
                    $this->lang->load($defaultLanguage, $defaultLanguage);
                } else {
                    $this->lang->load($selectedLanguage, $selectedLanguage);
                }
            } else {
                $this->lang->load($defaultLanguage, $defaultLanguage);
            }


            $selectedLang = $this->user_model->get_all_details(LANGUAGES, array('lang_code' => $choosenlonCode));
            if ($selectedLang->num_rows() > 0) {
                $languageArr = array(APP_NAME.'langCode' => $selectedLang->row()->lang_code, APP_NAME.'langName' => $selectedLang->row()->name);
                $this->session->set_userdata($languageArr);
                $this->setErrorMessage('success', 'Language changed successfully', 'driver_lang_changed_success');
            } else {
                $this->setErrorMessage('error', 'Language not changed. Please try again', 'driver_lang_not_changed_success');
            }
        } else {
            $this->setErrorMessage('error', 'Language not changed. Please try again', 'driver_lang_not_changed_success');
        }
        redirect('');
    }

}

/* End of file landing.php */
/* Location: ./application/controllers/site/landing.php */