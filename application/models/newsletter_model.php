<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
*
* This model contains all db functions related to Review management
* @author Casperon
*
**/
 
class Newsletter_model extends My_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_template($slug, $langcode = '')
    {
        if ($langcode == '') {
            $langcode = $this->mailLang;
        }
    
        $email_data = $this->get_template_details($slug);
        if ($email_data === false) {
            return false;
        } else {
            $data = array();
            if ($langcode != 'en') {
                $templateurl = FCPATH.DIRECTORY_SEPARATOR . 'newsletter' . DIRECTORY_SEPARATOR . 'template' . $email_data->news_id->value . '_' . $langcode . '.php';
                if (!file_exists($templateurl)) {
                    $subject = $email_data->message['subject'];
                    $sender_name = $email_data->sender['name'];
                    $sender_email = $email_data->sender['email'];
                    $templateurl = FCPATH.DIRECTORY_SEPARATOR . 'newsletter' . DIRECTORY_SEPARATOR . 'template' . $newsid . '.php';
                
                    $data = array(
                        "subject" => $subject,
                        "sender_name" => $sender_name,
                        "sender_email" => $sender_email,
                        "templateurl" => $templateurl
                    );
                } else {
                    $lang_details = $email_data->$langcode;
                    $subject = $lang_details['email_subject'];
                    $sender_name = $lang_details['sender_name'];
                    $sender_email = $lang_details['sender_email'];
                    $templateurl = FCPATH . DIRECTORY_SEPARATOR . 'newsletter' . DIRECTORY_SEPARATOR . 'template' . $newsid . '_' . $langcode . '.php';
                    $data = array(
                        "subject" => $subject,
                        "sender_name" => $sender_name,
                        "sender_email" => $sender_email,
                        "templateurl" => $templateurl
                    );
                }
            } else {
                $subject = $email_data->message['subject'];
                $sender_name = (isset($email_data->sender['name']) ? $email_data->sender['name']: '');
                $sender_email = (isset($email_data->sender['email']) ? $email_data->sender['email']: '');
                $templateurl = FCPATH . DIRECTORY_SEPARATOR . 'newsletter' . DIRECTORY_SEPARATOR . 'template' . $email_data->news_id->value . '.php';
                $data = array(
                    "subject" => $subject,
                    "sender_name" => $sender_name,
                    "sender_email" => $sender_email,
                    "templateurl" => $templateurl
                );
            }
        }
        return $data;
    }

    public function get_template_details($slug)
    {
        $this->cimongo->select();
        $this->cimongo->where(array('slug' => $slug));
        $res = $this->cimongo->get(NEWSLETTER);
        if ($res->num_rows() === 1) {
            return $res->row();
        } else {
            return false;
        }
    }
}
