<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
* This controller contains the functions related to SMS and Email Templates management 
* @author Casperon
*
**/

class Templates extends MY_Controller {
	public function __construct(){
        parent::__construct();
		$this->load->helper(array('cookie','date','form'));
		$this->load->library(array('encrypt','form_validation'));		
		$this->load->model('templates_model');
		
		if ($this->checkPrivileges('templates',$this->privStatus) == FALSE){
			redirect('admin');
		}
		$c_fun = $this->uri->segment(3);
		$restricted_function = array('insertEditEmailtemplate','delete_email_template');
		if(in_array($c_fun,$restricted_function) && $this->data['isDemo']===TRUE){
			$this->setErrorMessage('error','You are in demo version. you can\'t do this action.','admin_common_demo_version');
			redirect($_SERVER['HTTP_REFERER']); die;
		}
    }
    
    /**
    *
    * This function loads the subscribers list page
	*
    **/
   	public function index(){	
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			redirect('admin/templates/display_email_template');
		}
	}
	
	
	/**
	* 
	* This function loads the Email templates page
	*
	**/
	public function display_email_template(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_menu_email_template_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_email_template_list')); 
		    else  $this->data['heading'] = 'Email Template List';
			$condition = array();
			$this->data['templateList'] = $this->templates_model->get_all_details(NEWSLETTER,$condition,array('news_id'=>'DESC'));
			$this->load->view('admin/newsletter/display_emailtemplates',$this->data);
		}
	}
	
	/**
	* 
	* This function loads the templates page
	*
	**/
	public function add_email_template(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		  if ($this->lang->line('admin_templates_add_email_template') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_templates_add_email_template')); 
		    else  $this->data['heading'] = 'Add Email Template';

		
			$this->load->view('admin/newsletter/add_email_template',$this->data);
		}
	}
	
	/**
	* 
	* This function loads the edit Email Template form
	*
	**/
	public function edit_email_template_form() {
		if ($this->checkLogin('A') == '') {
			redirect('admin');
		} else {
		    if ($this->lang->line('admin_menu_edit_email_template') != '') {
		    	$this->data['heading'] = stripslashes($this->lang->line('admin_menu_edit_email_template'));
		    }
		    else {
		    	$this->data['heading'] = 'Edit Email Template';
		    }
			$email_id = $this->uri->segment(4,0);
            $this->data['template_id'] = $email_id;
            $this->data['language_code'] = $language_code = $this->uri->segment(5, 0);
            $condition = array('_id' => new \MongoId($email_id));
            /* Sending all the languages that has been already translated */
            $added_languages = $this->templates_model->get_selected_fields(NEWSLETTER, $condition, array('translated_languages'))->row();
            if (isset($added_languages->translated_languages)) {
                $this->data['translated_languages'] = $added_languages->translated_languages;
            }
            /* Sending all ---> Ends heree..... */
			$condition = array('_id' =>  new \MongoId($email_id));
         	$this->data['langList'] = $this->templates_model->get_selected_fields(LANGUAGES, array(), array('name', 'lang_code'))->result();
			$this->data['template_details'] = $this->templates_model->get_all_details(NEWSLETTER, $condition);
			if ($this->data['template_details']->num_rows() == 1) {
				$this->load->view('admin/newsletter/edit_email_template',$this->data);
			} else {
				redirect('admin');
			}
		}
	}
	
	/**
	* 
	* This function insert and edit a user
	*
	*/
	public function insertEditEmailtemplate(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$template_id = $this->input->post('_id');
			$lang_code = $this->input->post('lang_code');
        
			$excludeArr = array("_id","status");
			$etemplate_status = 'Active';
			$dataArr = array();
				
			$getTemplates=$this->templates_model->get_selected_fields(NEWSLETTER,array(),array('news_id'),array('news_id'=>'DESC'));
			if ($template_id == ''){
				$nid = $getTemplates->row()->news_id->value;
				$news_id = $nid+1;
				$dataArr = array(
					'news_id'=> new \MongoInt64 ($news_id),
					'status' => $etemplate_status,
					'created_date'=>date('Y-m-d H:i:s')
					);
			}else{
				$condition = array('_id' =>  new \MongoId($template_id));
				$template_contentOld=$this->templates_model->get_selected_fields(NEWSLETTER,$condition,array('message.description','news_id'));
				if($template_contentOld->num_rows()>0){
					$news_id=(string)$template_contentOld->row()->news_id;
				}
				
			}
			
			if($lang_code=='' || $lang_code=='0'){
				$post_message = $this->input->post('message',FALSE);
				$org_description_content = $post_message['message']['description'];
				$temp_description_content = str_replace("'.base_url().'", base_url(), $post_message['message']['description']);
				
				$dataArr['message']['title'] = $post_message['message']['title'];
				$dataArr['message']['subject'] = $post_message['message']['subject'];
				$dataArr['message']['description'] = $org_description_content;
				
				$file = 'newsletter/template'.$news_id.'.php';
				
			}else{
				$post_message = $this->input->post("$lang_code",FALSE);
				$org_description_content = $post_message["$lang_code"]['email_description'];
				$temp_description_content = str_replace("'.base_url().'", base_url(), $post_message["$lang_code"]['email_description']);
				
				$dataArr["$lang_code"]['email_title'] = $post_message["$lang_code"]['email_title'];
				$dataArr["$lang_code"]['email_subject'] = $post_message["$lang_code"]['email_subject'];
				$dataArr["$lang_code"]['email_description'] = $org_description_content;
				
				$file = 'newsletter/template'.$news_id.'_'.$lang_code.'.php';
			}
			
			if ($template_id == ''){
				$condition = array();
				$this->templates_model->commonInsertUpdate(NEWSLETTER,'insert',$excludeArr,$dataArr,$condition);
				
				$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
				$temp_description_string = str_replace("}",".'",$template_content_new);
				
				$config = "<?php \$message .= '";
				$config .= "$temp_description_string";
				$config .= "';  ?>";
				file_put_contents($file, $config);
				$this->setErrorMessage('success','Email template added successfully','admin_template_email_added_success');
			}else {
				if($lang_code!='' && $lang_code!='0'){
					$condition = array('_id' =>  new \MongoId($template_id));
					$added_languages = $this->templates_model->get_selected_fields(NEWSLETTER, $condition, array('translated_languages'))->row();

					if (isset($added_languages->translated_languages)) {
						foreach ($added_languages->translated_languages as $added) {
							$translated[] = $added;
						}
						if (!in_array($lang_code, $translated)){
							$translated[] = $lang_code;
						}
						$translated_langs = array('translated_languages' => $translated);
						$dataArr = array_merge($dataArr, $translated_langs);
					}else {
						$translated[] = $lang_code;
						$translated_langs = array('translated_languages' => $translated);
						$dataArr = array_merge($dataArr, $translated_langs);
					}
					
					$dataArr = array_merge(array('news_id'=> new \MongoInt64 ($news_id)),$dataArr);
					$this->templates_model->commonInsertUpdate(NEWSLETTER,'update',$excludeArr,$dataArr,$condition);
				
					$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
					$temp_description_string = str_replace("}",".'",$template_content_new);
					
					$config = "<?php \$message .= '";
					$config .= "$temp_description_string";
					$config .= "';  ?>";
					file_put_contents($file, $config);
					$this->setErrorMessage('success','Email template updated successfully','admin_tempalte_email_update_success');
				}else{
					$condition = array('_id' =>  new \MongoId($template_id));
					$dataArr = array_merge(array('news_id'=> new \MongoInt64 ($news_id)),$dataArr);
					$this->templates_model->commonInsertUpdate(NEWSLETTER,'update',$excludeArr,$dataArr,$condition);
					
					$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
					$temp_description_string = str_replace("}",".'",$template_content_new);
				
					$config = "<?php \$message .= '";
					$config .= "$temp_description_string";
					$config .= "';  ?>";
					file_put_contents($file, $config);
					$this->setErrorMessage('success','Email template updated successfully','admin_tempalte_email_update_success');
				}
			}
			redirect('admin/templates/display_email_template');
		}
	}
	
	/**
	* 
	* This function loads the email template view page
	*
	**/
	public function view_email_template(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_menu_view_email_template') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_view_email_template')); 
		    else  $this->data['heading'] = 'View Email Template';
			//$this->data['heading'] = 'View Email Template';
			$template_id = $this->uri->segment(4,0);
			$condition = array('_id' =>  new \MongoId($template_id));
			$this->data['template_details'] = $this->templates_model->get_all_details(NEWSLETTER,$condition);
			if ($this->data['template_details']->num_rows() == 1){
				$this->load->view('admin/newsletter/view_email_template',$this->data);
			}else {
				redirect('admin');
			}
		}
	}
	
	/**
	* 
	* This function delete the email templates from db
	*
	**/
	public function delete_email_template(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$email_id = $this->uri->segment(4,0);
			$condition = array('_id' =>  new \MongoId($email_id));
			 $this->templates_model->commonDelete(NEWSLETTER,$condition);
			$this->setErrorMessage('success','Email template deleted successfully','admin_templete_email_delete_success');
			redirect('admin/templates/display_email_template');
		}
	}
	
	/**
	* 
	* This function loads the SMS templates page
	*
	**/
	public function display_sms_templates(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_templates_sms_templates') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_templates_sms_templates')); 
		    else  $this->data['heading'] = 'SMS Templates';
			$condition = array();
			$this->data['templateList'] = $this->templates_model->get_all_details(SMS_TEMPLATE,$condition);
			$this->load->view('admin/newsletter/display_sms_templates',$this->data);
		}
	}
	
	
	/**
	* 
	* This function loads the SMS templates page
	*
	**/
	public function display_subscribers_list(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
		    if ($this->lang->line('admin_templates_subscribers_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_templates_subscribers_list')); 
		    else  $this->data['heading'] = 'Subscribers List';
			$condition = array();
			$this->data['subscribersList'] = $this->templates_model->get_all_details(NEWSLETTER_SUBSCRIBER,$condition);
			$this->data['NewsList'] = $this->templates_model->get_all_details(NEWSLETTER,$condition);
			$this->load->view('admin/newsletter/display_subscribers',$this->data);
		}
	}
	
	/**
	 * 
	 * This function change the subscribers status
	 */
	public function change_subscribers_status(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$mode = $this->uri->segment(4,0);
			$user_id = $this->uri->segment(5,0);
			$status = ($mode == '0')?'InActive':'Active';
			$newdata = array('status' => $status);
			$condition = array('_id' => new \MongoId($user_id));
			$this->templates_model->update_details(NEWSLETTER_SUBSCRIBER,$newdata,$condition);
			$this->setErrorMessage('success','Subscribers Status Changed Successfully','admin_template_subcriber_status');
			redirect('admin/templates/display_subscribers_list');
		}
	}
	

	/**
	 * 
	 * This function delete the subscribers record from db
	 */
	public function delete_subscribers(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			$user_id = $this->uri->segment(4,0);
			$condition = array('_id' => new \MongoId($user_id));
			$this->templates_model->commonDelete(NEWSLETTER_SUBSCRIBER,$condition);
			$this->setErrorMessage('success','Subscribers deleted successfully','admin_template_subcriber_delate');
			redirect('admin/templates/display_subscribers_list');
		}
	}
	
	/**
	 * 
	 * This function change the subscribers status, delete the user record
	 */
	public function change_newsletter_status_global(){
		if($this->input->post('statusMode')=='SendMail' &&  $this->input->post('mail_contents')!=''){
			if(count($_POST['checkbox_id']) > 0){
				$data =  $_POST['checkbox_id'];
				for ($i=0;$i<count($data);$i++){
					if($data[$i] == 'on'){
						unset($data[$i]);
					}
				}
				
				$SubscribEmail=$this->templates_model->send_mail_subcribers($data);
				
				$emailtemplate_id = $this->input->post('mail_contents');
				$condition1 = array('news_id' => new \MongoInt64($emailtemplate_id));
				$NewsTemplate= $this->templates_model->get_all_details(NEWSLETTER,$condition1);
				
				
				$this->templates_model->send_mail_subcribers_list($SubscribEmail, $NewsTemplate);
				$this->setErrorMessage('success'," Send Mail's successfully",'admin_common_mail_send_success');
				redirect('admin/templates/display_subscribers_list');
			}else{
				$this->setErrorMessage('error'," Email Not Send",'admin_common_mail_send_error');
				redirect('admin/templates/display_subscribers_list');
			}
		}else if($this->input->post('statusMode')=='SendMailAll' &&  $this->input->post('mail_contents')!=''){
			$conditionval = array();
			$SubscribEmail=$this->templates_model->get_newsletter_details(NEWSLETTER_SUBSCRIBER,$conditionval);
			$emailtemplate_id = $this->input->post('mail_contents');
			$condition1 = array('news_id' => new \MongoInt64($emailtemplate_id));
			$NewsTemplate= $this->templates_model->get_all_details(NEWSLETTER,$condition1);
			$this->templates_model->send_mail_subcribers_list($SubscribEmail, $NewsTemplate);
			$this->setErrorMessage('success'," Send Mail's successfully",'admin_common_mail_send_success');
			redirect('admin/templates/display_subscribers_list');
		}else{
			if(count($this->input->post('checkbox_id')) > 0 &&  $this->input->post('statusMode') != ''){
				$this->templates_model->activeInactiveCommon(NEWSLETTER_SUBSCRIBER,'_id');
				if (strtolower($this->input->post('statusMode')) == 'delete'){
					$this->setErrorMessage('success','Subscribers records deleted successfully','admin_template_subcrib_record_deleted');
				}else {
					$this->setErrorMessage('success','Subscribers records status changed successfully','admin_template_subcrib_status');
				}
				redirect('admin/templates/display_subscribers_list');
			}
		}
	}
	
	public function invoice_template(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
			 
			$this->data['language_code'] = $language_code = $this->uri->segment(4, 0);
			$condition = array();
			/* Sending all the languages that has been already translated */
			$added_languages = $this->templates_model->get_selected_fields(INVOICE, $condition, array('translated_languages'))->row();
			if (isset($added_languages->translated_languages)) {
				$this->data['translated_languages'] = $added_languages->translated_languages;
			}
			/* Sending all ---> Ends heree..... */
			$condition = array();
			$this->data['langList'] = $this->templates_model->get_selected_fields(LANGUAGES, array(), array('name', 'lang_code'))->result();
			$this->data['template_details'] = $this->templates_model->get_all_details(INVOICE,$condition);
			
			
             if ($this->lang->line('invoice_template_lang') != '') {
				 $heading= stripslashes($this->lang->line('invoice_template_lang')); 
			 }else{
				$heading = 'Invoice Template';
			 }
			 $this->data['heading'] = $heading;
			 
			if ($this->data['template_details']->num_rows() == 1){
				$this->load->view('admin/newsletter/invoice_template',$this->data);
			}else {
				$this->load->view('admin/newsletter/invoice_template',$this->data);
			}				
		}	
	}
	
	/**
	* 
	* This function insert and edit a user
	*
	*/
	public function insertEditInvoicetemplate(){
		if ($this->checkLogin('A') == ''){
			redirect('admin');
		}else {
        
			$template_id = $this->input->post('_id');
			$lang_code = $this->input->post('lang_code');
       
			$excludeArr = array("_id","status","lang_code");
			$etemplate_status = 'Active';
			$dataArr = array();

			if ($template_id == ''){
				$dataArr = array(
					'status' => $etemplate_status,
					'created_date'=>date('Y-m-d H:i:s')
					);
			} 
			
			if($lang_code=='' || $lang_code=='0'){
				$post_message = $this->input->post('message',FALSE);
				$org_description_content = $post_message['message']['description'];
				$temp_description_content = str_replace("'.base_url().'", base_url(), $post_message['message']['description']);
				
				$dataArr['message']['title'] = $post_message['message']['title'];
				$dataArr['message']['subject'] = $post_message['message']['subject'];
				$dataArr['message']['description'] = $org_description_content;
				
				$file = 'invoice/invoice_template.php';
				
			}else{
				$post_message = $this->input->post("$lang_code",FALSE);
				$org_description_content = $post_message["$lang_code"]['email_description'];
				$temp_description_content = str_replace("'.base_url().'", base_url(), $post_message["$lang_code"]['email_description']);
				
				$dataArr["$lang_code"]['email_title'] = $post_message["$lang_code"]['email_title'];
				$dataArr["$lang_code"]['email_subject'] = $post_message["$lang_code"]['email_subject'];
				$dataArr["$lang_code"]['email_description'] = $org_description_content;
				
				$file = 'invoice/invoice_template_'.$lang_code.'.php';
			}
			
			if ($template_id == ''){
				$condition = array();
				$this->templates_model->commonInsertUpdate(INVOICE,'insert',$excludeArr,$dataArr,$condition);
				
				$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
				$temp_description_string = str_replace("}",".'",$template_content_new);
				
				$config = "<?php \$message .= '";
				$config .= "$temp_description_string";
				$config .= "';  ?>";
				
				file_put_contents($file, $config);
				$this->setErrorMessage('success','Email template added successfully','admin_template_email_added_success');
			}else {
				if($lang_code!='' && $lang_code!='0'){
					$condition = array('_id' =>  new \MongoId($template_id));
					$added_languages = $this->templates_model->get_selected_fields(INVOICE, $condition, array('translated_languages'))->row();

					if (isset($added_languages->translated_languages)) {
						foreach ($added_languages->translated_languages as $added) {
							$translated[] = $added;
						}
						if (!in_array($lang_code, $translated)){
							$translated[] = $lang_code;
						}	
						$translated_langs = array('translated_languages' => $translated);
						$dataArr = array_merge($dataArr, $translated_langs);
					}else {
						$translated[] = $lang_code;
						$translated_langs = array('translated_languages' => $translated);
						$dataArr = array_merge($dataArr, $translated_langs);
					}
					
					$this->templates_model->commonInsertUpdate(INVOICE,'update',$excludeArr,$dataArr,	$condition);
					
					$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
					$temp_description_string = str_replace("}",".'",$template_content_new);
					
					$config = "<?php \$message .= '";
					$config .= "$temp_description_string";
					$config .= "';  ?>";
					file_put_contents($file, $config);
					$this->setErrorMessage('success','Email template updated successfully','admin_tempalte_email_update_success');
				}else{
					$condition = array('_id' =>  new \MongoId($template_id));
					
					$this->templates_model->commonInsertUpdate(INVOICE,'update',$excludeArr,$dataArr,$condition);
					
					$template_content_new = str_replace("{","'.",addslashes($temp_description_content));
					$temp_description_string = str_replace("}",".'",$template_content_new);
				
					$config = "<?php \$message .= '";
					$config .= "$temp_description_string";
					$config .= "';  ?>";
					file_put_contents($file, $config);
					$this->setErrorMessage('success','Email template updated successfully','admin_tempalte_email_update_success');
				}
			}
			redirect('admin/templates/invoice_template');
		}
	}
}

?>