<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Integrations extends AdminController
{
    public $moudle_permission_name = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('integration_manager');
    }

    public function index()
    {
        if(!is_admin()){
            access_denied();
        }
        $data =[];
        $data['title'] ='Integrations';
        $data['categoryintegrations'] = $this->integration_manager->getIntegrations();
        $this->load->view('admin/integrations/integrations',$data);
    }

    public function search() {
        $searchTerm = $this->input->post('searchTerm');
        if(strlen(trim($searchTerm)) ==0){
            $data['categoryintegrations'] = $this->integration_manager->getIntegrations();
        }else{
            $data['categoryintegrations'] = $this->integration_manager->searchIntegrations($searchTerm);
        }        
        $data['searchTerm'] = $searchTerm;
        $this->load->view('admin/plugins/pluginslist', $data);
    }

}
