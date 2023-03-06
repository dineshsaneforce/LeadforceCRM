<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tasktype extends AdminController
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('tasktype_model');
    }

/**
 * Task-types List
**/
    public function index()
    {
		if (!has_permission('tasktype', '', 'view')) {
            access_denied('tasktype');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('tasktype');
        }
        $data['title'] = _l('tasktype');
        $this->load->view('admin/tasktype/list', $data);
    }

/**
 * Add or edit Task-type
**/
    public function save()
    {
        if (!has_permission('tasktype', '', 'view')) {
            access_denied('tasktype');
        }
        if ($this->input->post())
		{
            $data = $this->input->post();
            if (!isset($data['id']) || !$data['id']) {
				$checktasktypeexist = $this->tasktype_model->checkTasktypeexist($data['name']);
				if(!empty($checktasktypeexist)) {

                    $response = array(
                        'warning' => true,
                        'msg' => _l('already_exist', _l('tasktype'))
                    );
                    echo json_encode($response);
                    return;
				}
				else {
					if (!has_permission('tasktype', '', 'create')) {
						access_denied('tasktype');
					}
					$id = $this->tasktype_model->addTasktype($data);
					if ($id) {

                        $response = array(
                            'success' => true,
                            'msg' => _l('added_successfully', _l('tasktype'))
                        );
                        echo json_encode($response);
                        return;
					}
				}
            }
			else{
				$checktasktypeexist = $this->tasktype_model->checkTasktypeexist($data['name']);
				if(!empty($checktasktypeexist) && $checktasktypeexist->id != $data['id']) {
                    $response = array(
                        'warning' => true,
                        'msg' => _l('already_exist', _l('tasktype'))
                    );
                    echo json_encode($response);
                    return;                    
				}
				else {
					if (!has_permission('tasktype', '', 'edit')) {
						access_denied('tasktype');
					}
					$success = $this->tasktype_model->updateTasktype($data, $data['id'] );
					if ($success) {
                        $response = array(
                            'success' => true,
                            'msg' => _l('updated_successfully', _l('tasktype')),
                            'data' => $tasktype
                        );
					}
                    echo json_encode($response);
                    return;				
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('tasktype_lowercase'));
        }
		else {
            $tasktype = $this->tasktype_model->getTasktype($id);
            $data['tasktype'] = $tasktype;
            $title = _l('edit', _l('tasktype')) . ' ' . $tasktype->name;
        }
        $data['bodyclass'] = 'tasktype';
        $data['title']     = $title;
        $this->load->view('admin/tasktype/form', $data);
    }

public function edit($id)
{ 
    if (!has_permission('tasktype', '', 'view')) {
        access_denied('tasktype');
    } 
    $tasktype = $this->tasktype_model->getTasktype($id);
    if (!$tasktype) {
        $response = array(
            'success' => false,
            'msg' => _l( _l('tasktype_not_found'))
        );
        echo json_encode($response);
        return;
    }else {
        $response = array(
            'success'=>true,
            'data'=>$tasktype
        );
        echo json_encode($response);
        return;
    }
    if ($this->input->post()) 
    {  
        $data = $this->input->post();
        $data['id'] = $id;

        $checktasktypeexist = $this->tasktype_model->checkTasktypeexist($data['name']);
        if (!empty($checktasktypeexist) && $checktasktypeexist->id != $id) {
            $response = array(
                'warning' => true,
                'msg' => _l('already_exist', _l('tasktype'))
            );
            echo json_encode($response);
            return;
        }
        else {
          
            $success = $this->tasktype_model->updateTasktype($data, $id);
            if ($success) {
                $response = array(
                    'success' => true,
                    'msg' => _l('updated_successfully', _l('tasktype')),
                    'data' => $tasktype
                );
                echo json_encode($response);
                return;
            }
            $this->load->view('admin/tasktype/form', $data);
        }
    }
}
/**
 * View Task-type
**/
	public function view($id)
    {
        if (!has_permission('tasktype', '', 'view')) {
            access_denied('View tasktype');
        }
        $data['tasktype'] = $this->tasktype_model->getTasktype($id);

        if (!$data['tasktype']) {
            show_404();
        }
        add_views_tracking('tasktype', $id);
        $data['title'] = $data['tasktype']->name;
        $this->load->view('admin/tasktype/view', $data);
    }
	
/**
 * Delete Task-type
**/
    public function delete_tasktype($id)
    {
        if (!has_permission('tasktype', '', 'delete')) {
            access_denied('tasktype');
        }
        if (!$id) {
            redirect(admin_url('tasktype'));
        }
        $num_tasks = $this->tasktype_model->countTasks_by_tasktype($id);
        if ($num_tasks > 0) {
            set_alert('warning', _l('tasktype_has_tasks'));
            redirect(admin_url('tasktype'));
        }
        $response = $this->tasktype_model->deleteTasktype($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('tasktype')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('tasktype_lowercase')));
        }
        redirect(admin_url('tasktype'));
    }
 /* Change tasktype status active or inactive */
    public function change_tasktype_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('tasktype_model');
            $this->tasktype_model->change_tasktype_status($id, $status);
        }
    }
}