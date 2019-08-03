<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to documents management 
 * @author Casperon
 *
 * */
class Documents extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model('documents_model');
        if ($this->checkPrivileges('documents', $this->privStatus) == FALSE) {
            redirect('admin');
        }
    }

    /**
     *
     * This function loads the documents list page
     *
     * */
    public function index() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            redirect('admin/documents/display_documents_list');
        }
    }

    /**
     *
     * This function Displays the documents List
     *
     * */
    public function display_documents_list() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
		    if ($this->lang->line('admin_menu_documents_list') != '') 
		    $this->data['heading']= stripslashes($this->lang->line('admin_menu_documents_list')); 
		    else  $this->data['heading'] = 'Documents List';
            $documentsType = $this->documents_model->get_documents_type();
            foreach ($documentsType['result'] as $result) {
                if ($result['category'] != '') {
                    $this->data['documentsType'] = $documentsType['result'];
                    $condition = array('category' => $result['category']);
                    $this->data['documentList'][$result['category']] = $this->documents_model->get_all_details(DOCUMENTS, $condition)->result();
                }
            }
            $this->load->view('admin/documents/display_documents', $this->data);
        }
    }

    /**
     *
     * This function loads the add/Edit documents form
     *
     * */
    public function add_edit_document_form() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $document_id = $this->uri->segment(4, 0);
            $form_mode = FALSE;
			if ($this->lang->line('admin_menu_add_new_documents') != '') 
		    $heading = stripslashes($this->lang->line('admin_menu_add_new_documents')); 
		    else  $heading = 'Add New Documents';
            if ($document_id != '') {
                $condition = array('_id' => new \MongoId($document_id));
                $this->data['documentdetails'] = $this->documents_model->get_all_details(DOCUMENTS, $condition);
                if ($this->data['documentdetails']->num_rows() != 1) {
                    redirect('admin/documents/display_documents_list');
                }
                $form_mode = TRUE;
                //$heading = 'Edit Documents';
				if ($this->lang->line('admin_menu_add_new_documents') != '') 
		        $heading = stripslashes($this->lang->line('admin_menu_add_new_documents')); 
		        else  $heading = 'Add New Documents';
            }
            $this->data['form_mode'] = $form_mode;
            $this->data['heading'] = $heading;
            $this->load->view('admin/documents/add_edit_document', $this->data);
        }
    }

    /**
     *
     * This function insert document informations into databse
     *
     * */
    public function insertEditDocument() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            #echo '<pre>'; print_r($_POST); 
            $document_id = $this->input->post('document_id');
            $document_category = $this->input->post('category');
            $name = $this->input->post('name');

            if ($document_id == '') {
                $condition = array('name' => $name, 'category' => $document_category);
            } else {
                $condition = array('name' => $name, 'category' => $document_category, '_id !=' => new \MongoId($document_id));
            }

            $duplicate_name = $this->documents_model->get_all_details(DOCUMENTS, $condition);
            if ($duplicate_name->num_rows() > 0) {
                $this->setErrorMessage('error', 'This Document already exists','admin_document_already_exists');
                redirect('admin/documents/add_edit_document_form/' . $document_id);
            }

            if ($this->input->post('status') == 'on') {
                $status = 'Active';
            } else {
                $status = 'Inactive';
            }
            if ($this->input->post('hasExp') == 'on') {
                $hasExp = 'Yes';
            } else {
                $hasExp = 'No';
            }
            if ($this->input->post('hasReq') == 'on') {
                $hasReq = 'Yes';
            } else {
                $hasReq = 'No';
            }

            $dataArr = array('name' => $name,
                'category' => $document_category,
                'status' => $status,
                'hasExp' => $hasExp,
                'hasReq' => $hasReq,
                'created' => date('Y-m-d H:i:s')
            );
            if ($document_id == '') {
                $this->documents_model->simple_insert(DOCUMENTS, $dataArr);
                $this->setErrorMessage('success', 'Document added successfully','admin_document_added_success');
            } else {
                $condition = array('_id' => new \MongoId($document_id));
                unset($dataArr['created']);
                $this->documents_model->update_details(DOCUMENTS, $dataArr, $condition);
                $this->setErrorMessage('success', 'Document updated successfully','admin_document_updated_success');
            }
            redirect('admin/documents/display_documents_list');
        }
    }

    /**
     *
     * This function delete the Document record from db
     *
     * */
    public function delete_documents() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $document_id = $this->uri->segment(4, 0);
            $condition = array('_id' => new \MongoId($document_id));
            $this->documents_model->commonDelete(DOCUMENTS, $condition);
            $this->setErrorMessage('success', 'Document deleted successfully','admin_document_deleted_success');
            redirect('admin/documents/display_documents_list');
        }
    }

    /**
     *
     * This function change the status of the Document
     *
     * */
    public function change_document_status() {
        if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $mode = $this->uri->segment(4, 0);
            $document_id = $this->uri->segment(5, 0);
            $status = ($mode == '0') ? 'Inactive' : 'Active';
            $newdata = array('status' => $status);
            $condition = array('_id' => new \MongoId($document_id));
            $this->documents_model->update_details(DOCUMENTS, $newdata, $condition);
            $this->setErrorMessage('success', 'Document Status Changed Successfully','admin_document_status_change');
            redirect('admin/documents/display_documents_list');
        }
    }

    /**
     *
     * This function change the status of the Document globally
     *
     * */
    public function change_document_status_global() {
        #var_dump($_POST['checkbox_id']); die;
        if (count($_POST['checkbox_id']) > 0 && $_POST['statusMode'] != '') {
            $this->documents_model->activeInactiveCommon(DOCUMENTS, '_id');
            if (strtolower($_POST['statusMode']) == 'delete') {
                $this->setErrorMessage('success', 'Document deleted successfully','admin_document_delete_success');
            } else {
                $this->setErrorMessage('success', 'Document status changed successfully','admin_document_status_change');
            }
            redirect('admin/documents/display_documents_list');
        }
    }

    /**
     *
     * This function check the icon propotion
     *
     * */
    public function ajax_check_icon() {
        list($w, $h) = getimagesize($_FILES["icon"]["tmp_name"]);
        if ($w >= 70 && $h >= 40) {
            echo 'Success';
        } else {
            echo 'Error';
        }
    }
	
	
	public function edit_language_document(){
		if ($this->checkLogin('A') == '') {
            redirect('admin');
        } else {
            $document_id = $this->uri->segment(4, 0);
            if ($document_id != '') {
                $condition = array('_id' => new \MongoId($document_id));
                $this->data['documentdetails'] = $documentdetails = $this->documents_model->get_all_details(DOCUMENTS, $condition);
                $this->data['languagesList'] = $this->documents_model->get_all_details(LANGUAGES, array('status' => 'Active'));
				#echo '<pre>'; print_r( $this->data['documentdetails']->result()); die;
                if ($this->data['documentdetails']->num_rows() != 1) {
                    redirect('admin/documents/display_documents_list');
                }
            }
			
			if ($this->lang->line('edit_document_language') != '') 
			$heading = stripslashes($this->lang->line('edit_document_language')); 
			else  $heading = 'Edit document language';
			 
			 $this->data['heading'] = $heading;
            $this->load->view('admin/documents/edit_document_language_form', $this->data);
        }
	}
	
	public function update_language_content(){
		if ($this->checkLogin('A') == '') {
            redirect('admin');
        }  
		$language_content = $this->input->post('name_languages');  
		$document_id = $this->input->post('document_id');
		$updCond = array('_id' => new MongoId($document_id ));
		$dataArr = array('name_languages' => $language_content);  #echo '<pre>'; print_r($dataArr ); die;
		$this->documents_model->update_details(DOCUMENTS,$dataArr ,$updCond);
		$this->setErrorMessage('success', 'Language content updated successfully','language_content_updated_successfully');
        redirect('admin/documents/display_documents_list');
	}

}

/* End of file documents.php */
/* Location: ./application/controllers/admin/documents.php */