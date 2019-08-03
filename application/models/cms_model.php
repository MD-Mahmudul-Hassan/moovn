<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to cms management
 * @author Teamtweaks
 *
 */
class Cms_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    public function check_page_exist($condition, $cms_id) {
        $this->cimongo->select(array('_id'));
        $this->cimongo->where($condition);
        $this->cimongo->where_ne('_id', new \MongoId($cms_id));
        $res = $this->cimongo->get(CMS);
        return $res;
    }

    

}
