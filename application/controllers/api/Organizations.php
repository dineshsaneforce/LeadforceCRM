<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Organizations extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        load_admin_language();
        $this->load->model('Authenticationapi_model');
        $this->load->model('api_model');
        $postdata = file_get_contents("php://input");
        $_POST = (array) json_decode($postdata,true);
    }

    // get staffs
    public function getbydeal($id)
    {
        $this->db->where('id',$id);
        $deal =$this->db->get(db_prefix() .'projects')->row();
        if($deal){
            if($deal->clientid){
                $this->db->where(['userid' => $deal->clientid,'active' => 1]);
                $organization =$this->db->get(db_prefix() .'clients')->row();
                if($organization){
                    $this->api_model->response_ok(true,$organization,'');
                }else{
                    $this->api_model->response_ok(true,new stdClass(),'No records found');
                }
            }else{
                $this->api_model->response_ok(true,new stdClass(),'No records found');
            }
            $this->api_model->response_ok(true,$new_staffs,'');
        }else{
            $this->api_model->response_ok(true,new stdClass(),'No records found');
        }
               
    }
}
