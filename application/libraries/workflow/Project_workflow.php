<?php

use function Clue\StreamFilter\fun;

class Project_workflow extends Workflow_app
{
    private static $module =array(
        'name'=>'project',
        'title'=>'Deal',
        'description'=>'Deal module',
        'icon'=>'<i class="fa fa-handshake-o"></i>',
        'triggers'=>['project_created','project_updated','project_deleted','project_note_added']
    );

    protected $project_id ='';
    protected $project_change_log_id =0;
    protected $project_change_log ;
    protected $project_note_id=0 ;
    protected $project;
    protected $merge_fields =[];

    private static $triggers =array(
        'project_created'=>[
            'title'=>'Deal Created',
            'description'=>'Workflow starts when new deal created.',
            'icon'=>'<i class="fa fa-plus text-success" aria-hidden="true"></i>',
            'triggers'=>['condition','project_assign_staff','add_activity','send_email','send_whatsapp','send_sms'],
        ],
        'project_updated'=>[
            'title'=>'Deal Updated',
            'description'=>'Workflow starts when deal updated.',
            'icon'=>'<i class="fa fa-pencil-square-o text-primary" aria-hidden="true"></i>',
            'triggers'=>['project_update_event','condition','project_assign_staff','add_activity','send_email','send_whatsapp','send_sms'],
        ],
        'project_deleted'=>[
            'title'=>'Deal Deleted',
            'description'=>'Workflow starts when deal deleted.',
            'icon'=>'<i class="fa fa-trash text-danger" aria-hidden="true"></i>',
            'triggers'=>['send_email','send_whatsapp','send_sms'],
        ],
        'project_note_added'=>[
            'title'=>'Note added',
            'description'=>'Workflow starts when new note added.',
            'icon'=>'<i class="fa fa-sticky-note text-warning" aria-hidden="true" style="color:#fad75a"></i>',
            'triggers'=>['send_email','send_whatsapp','send_sms'],
        ],
        'project_cronjob'=>[
            'title'=>'Lead Scheduler',
            'description'=>'Workflow starts when deal deleted.',
            'icon'=>'<i class="fa fa-clock-o text-warning" aria-hidden="true"></i>',
            'triggers'=>['condition','send_email','send_whatsapp','send_sms'],
        ],
        'project_update_event'=>[
            'title'=>'Deal update event',
            'description'=>'Configure deal update event',
            'icon'=>'<i class="fa fa-question text-warning" aria-hidden="true"></i>',
            'triggers'=>['condition','project_assign_staff','add_activity','send_email','send_whatsapp','send_sms'],
        ],
        'condition'=>[
            'title'=>'Conditions',
            'description'=>'Define deal conditions.',
            'icon'=>'<i class="fa fa-question text-warning" aria-hidden="true"></i>',
            'triggers'=>['true','false'],
        ],
        'converted_from_lead'=>[
            'title'=>'Converted from lead',
            'description'=>'When deal is converted from lead',
            'icon'=>'<i class="fa fa-question text-warning" aria-hidden="true"></i>',
            'triggers'=>['true','false'],
        ],
        'project_assign_staff'=>[
            'title'=>'Assign owner',
            'description'=>'Assign owner to deal.',
            'icon'=>'<i class="fa fa-user-plus text-success" aria-hidden="true"></i>',
            'triggers'=>['send_email','send_whatsapp','send_sms'],
        ],
        'add_activity'=>[
            'title'=>'Add Activity',
            'description'=>'Add new activity',
            'icon'=>'<i class="fa fa-tasks text-success" aria-hidden="true"></i>',
            'triggers'=>[],
        ],
        'send_email'=>[
            'title'=>'Send Email',
            'type'=>'notification',
            'medium'=>'email',
            'description'=>'Send email notification.',
            'icon'=>'<i class="fa fa-envelope" style="color:#BB001B" aria-hidden="true"></i>',
            'triggers'=>[],
        ],
        'send_whatsapp'=>[
            'title'=>'Send Whatsapp',
            'type'=>'notification',
            'medium'=>'whatsapp',
            'description'=>'Send Whatsapp notification.',
            'icon'=>'<i class="fa fa-whatsapp " style="color:#25D366" aria-hidden="true"></i>
            ',
            'triggers'=>[],
        ],
        'send_sms'=>[
            'title'=>'Send SMS',
            'type'=>'notification',
            'medium'=>'sms',
            'description'=>'Send SMS notification.',
            'icon'=>'<i class="fa fa-commenting text-primary" aria-hidden="true"></i>
            ',
            'triggers'=>[],
        ],
        'true'=>[
            'title'=>'True',
            'description'=>'If condition true.',
            'icon'=>'<i class="fa fa-check text-success" aria-hidden="true"></i>',
            'triggers'=>['condition','converted_from_lead','project_assign_staff','add_activity','send_email','send_whatsapp','send_sms'],
        ],
        'false'=>[
            'title'=>'False',
            'description'=>'If condition false.',
            'icon'=>'<i class="fa fa-times text-danger" aria-hidden="true"></i>',
            'triggers'=>['condition','converted_from_lead','project_assign_staff','add_activity','send_email','send_whatsapp','send_sms'],
        ],
    );

    private static $mergefields =array('project_merge_fields','staff_merge_fields','project_note_merge_fields','others_merge_fields');

    public function __construct()
    {
        parent::__construct();
        $this->setModule(self::$module['name'],self::$module['title'],self::$module['description'],self::$module['icon'],self::$module['triggers']);

        foreach(self::$triggers as $name => $trigger){
            $this->setTrigger(self::$module['name'],$name,$trigger['title'],$trigger['description'],$trigger['icon'],$trigger['triggers'],isset($trigger['type'])?$trigger['type']:'',isset($trigger['medium'])?$trigger['medium']:'');
        }
        $this->ci->load->model('staff_model');
        $this->ci->load->model('pipeline_model');
        $this->ci->load->model('projects_model');

        $this->ci->load->library('merge_fields/projects_merge_fields');
        $this->ci->load->library('merge_fields/other_merge_fields');
        $this->ci->load->library('merge_fields/staff_merge_fields');
        $this->ci->load->library('merge_fields/project_note_merge_fields');


        $this->setMergeFields(self::$module['name'], self::$mergefields);
        $this->setProjectQueryFields();
        
    }

    protected function setProjectQueryFields(){

        $text_operators =array('equal',
        'not_equal',
        'begins_with',
        'not_begins_with',
        'contains',
        'not_contains',
        'ends_with',
        'not_ends_with',
        'is_empty',
        'is_not_empty');

        $this->setQueryFields(
            self::$module['name'],
            array(
                'id'=>'name',
                'label'=>'Deal name',
                'type'=>'string',
                'input'=>'text',
                'operators'=>$text_operators,
            )
        );
        $this->setQueryFields(
            self::$module['name'],
            array(
                'id'=>'project_cost',
                'label'=>'Deal value',
                'type'=>'string',
                'input'=>'text',
                'operators'=>$text_operators,
            )
        );
        $allstaffs =$this->ci->staff_model->get();
        if($allstaffs){
            $staffs =array();
            foreach($allstaffs as $staff){
                if($staff['full_name'] && strlen(trim($staff['full_name'])) >0){
                    $staffs [$staff['staffid']] =$staff['full_name'];
                }
            }
            $this->setQueryFields(
                self::$module['name'],
                array(
                    'id'=>'teamleader',
                    'label'=>'Deal owner',
                    'type'=>'string',
                    'input'=>'select',
                    'values'=>$staffs,
                    'operators'=> array('equal', 'not_equal')
                )
            );
        }
        $allpipelines =$this->ci->pipeline_model->getPipeline();
        if($allpipelines){
            $pipelines =array();
            foreach($allpipelines as $pipeline){
                if($pipeline['name'] && strlen(trim($pipeline['name'])) >0){
                    $pipelines [$pipeline['id']] =$pipeline['name'];
                }
            }
            $this->setQueryFields(
                self::$module['name'],
                array(
                    'id'=>'pipeline_id',
                    'label'=>'Deal pipeline',
                    'type'=>'string',
                    'input'=>'select',
                    'values'=>$pipelines,
                    'operators'=> array('equal', 'not_equal')
                )
            );
        }

        $allstatus =$this->ci->projects_model->get_project_statuses();
        if($allstatus){
            $statuses =array();
            foreach($allstatus as $status){
                if($status['name'] && strlen(trim($status['name'])) >0){
                    $statuses [$status['id']] =$status['name'];
                }
            }
            $this->setQueryFields(
                self::$module['name'],
                array(
                    'id'=>'status',
                    'label'=>'Deal stage',
                    'type'=>'string',
                    'input'=>'select',
                    'values'=>$statuses,
                    'operators'=> array('equal', 'not_equal')
                )
            );
        }
    }

    protected function initialise_project($project_created_flow,$project_id,$project_change_log_id=0,$project_note_id=0)
    {
        $this->project_id =$project_id;
        $this->project_change_log_id =$project_change_log_id;
        $this->project_note_id =$project_note_id;
        $this->project = $this->ci->projects_model->get($project_id);
        if($project_change_log_id){
            $this->ci->db->where('id',$project_change_log_id);
            $this->project_change_log =$this->ci->db->get(db_prefix().'project_changelogs')->row();
        }else{
            $this->project_change_log =new stdClass();
        }
        $this->setup();
        if($this->project_note_id){
            $note =$this->ci->projects_model->get_notes_byid($this->project_note_id);
            if(trim(strlen($note->mentions))>0){
                $mentions =explode(',',$note->mentions);
                foreach($mentions as $mention){
                    $this->run_mergefields($mention);
                    $subject =$this->mergefieldsContent($this->merge_fields,"{project_note_added_by} mentions you in a deal note");
                    $fromname =$this->mergefieldsContent($this->merge_fields,"{companyname}");
                    $message =$this->mergefieldsContent($this->merge_fields,"<span style=\"font-size: 14pt;\"><strong>Hello {staff_firstname} {staff_lastname}</strong></span><br /><br />{project_note_added_by} mentions you in a deal note.<br /><br />{project_note_description}<br /><br />Click <a href=\"{project_link}?group=project_notes\">here</a> to view the deal.");
                    $this->ci->db->where('staffid', $mention);
                    $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
                    if(!$staff){
                        return;
                    }
                    $sendto =$staff->email;
                    $this->sendEmail($fromname,$sendto,$subject,$message,'workflow deal created');
                }
                

                return true;
            }
        }
       

        $project_created_flow =$project_created_flow[0];
        $this->run(self::$module['name'],$project_created_flow->id);
    }
    public function project_created($project_id){
        
        $project_created_flow =$this->ci->workflow_model->getmoduleflows(self::$module['name'],['action'=>'project_created']);
        
        if($project_created_flow){
            $this->initialise_project($project_created_flow,$project_id);
        }
    }

    public function project_deleted($project_id){
        $project_created_flow =$this->ci->workflow_model->getmoduleflows(self::$module['name'],['action'=>'project_deleted']);
        
        if($project_created_flow){
            $this->initialise_project($project_created_flow,$project_id);
        }
    }

    public function project_updated($project_id,$project_change_log_id =0){
        $project_created_flow =$this->ci->workflow_model->getmoduleflows(self::$module['name'],['action'=>'project_updated']);
        
        if($project_created_flow){
            
            $this->initialise_project($project_created_flow,$project_id,$project_change_log_id);
        }
    }

    public function project_note_added($timeline_id)
    {
        
        $this->ci->db->where('id',$timeline_id);
        $timeline =$this->ci->db->get(db_prefix().'project_log')->row();
        if($timeline){
            $project_note_added_flow =$this->ci->workflow_model->getmoduleflows(self::$module['name'],['action'=>'project_note_added']);
            $this->initialise_project($project_note_added_flow,$timeline->project_id,0,$timeline->type_id);
        }
    }
    private function setup(){
        $this->run_mergefields();
    }

    private function run_mergefields($staff_id=0)
    {

        $projects_merge_fields = $this->ci->projects_merge_fields->format($this->project_id);
        if($staff_id){
            $staff_merge_fields = $this->ci->staff_merge_fields->format($staff_id);
        }else{
            $staff_merge_fields = $this->ci->staff_merge_fields->format($this->project->teamleader);
        }
        if($this->project_note_id){
            $project_note_merge_fields = $this->ci->project_note_merge_fields->format($this->project_note_id);
        }else{
            $project_note_merge_fields =array();
        }
        $others_merge_fields = $this->ci->other_merge_fields->format();
        $this->merge_fields = array_merge($projects_merge_fields, $staff_merge_fields,$others_merge_fields,$project_note_merge_fields);
    }

    protected function run_condition($flow){
        if($flow->action =='converted_from_lead'){
            return $this->project->lead_id?true:false;
        }elseif($flow->configure){
            $sql = "SELECT * FROM ".db_prefix()."projects WHERE ( ".$flow->configure['sql']." ) and id = ?";
            $params =$flow->configure['params'];
            $params [] =$this->project_id;
            $row =$this->ci->db->query($sql, $params)->row();
            return ($row)?true:false ;
        }
    }

    protected function prepare_and_send_email($flow,$staff_id) {
        if($staff_id){
            $this->run_mergefields($staff_id);
            $subject =$this->mergefieldsContent($this->merge_fields,$flow->configure['subject']);
            $fromname =$this->mergefieldsContent($this->merge_fields,$flow->configure['fromname']);
            $message =$this->mergefieldsContent($this->merge_fields,$flow->configure['message']);
            $this->ci->db->where('staffid', $staff_id);
            $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
            if(!$staff){
                return;
            }
            $sendto =$staff->email;
            $this->sendEmail($fromname,$sendto,$subject,$message,'workflow deal created');
        }   
    }

    protected function run_email($flow)
    {
        if($flow->configure){
            if($flow->configure['sendto'] =='staff' || $flow->configure['sendto'] =='other_staffs'){
                if($flow->configure['sendto'] =='staff'){
                    $staffs =array($this->get_staff_id());
                }else{
                    $staffs =$flow->configure['other_staffs_group'];
                }
                
                if($staffs){
                    foreach ($staffs as $staff_id) {
                        $this->prepare_and_send_email($flow,$staff_id);
                    }
                }
            }elseif($flow->configure['sendto'] =='followers'){
                $members =$this->get_followers();
                if($members){
                    foreach ($members as $member) {
                        $this->prepare_and_send_email($flow,$member['staff_id']);
                    }
                }
            }elseif($flow->configure['sendto'] =='manager'){
                $this->prepare_and_send_email($flow,$this->get_manager_id());
            }
        }
    }

    protected function check_flow($flow){
        $staff_id =0;
        switch ($flow->action) {
            case 'project_assign_staff':
                $configure =$flow->configure;
                if($configure['type']=='direct_assign'){
                    if($configure['assignto']){
                        $staff_id =$configure['assignto'];
                    }
                }elseif($configure['type']=='round_robin_method'){
                    $last_run =0;

                    if(isset($configure['round_robin_last_run'])){
                        $last_run =$configure['round_robin_last_run']+1;
                    }
                    

                    if($configure['stafftype'] =='staff'){
                        $staff_ids =$configure['assigntogroup'];
                    }else if($configure['stafftype'] =='roles'){
                        $staff_ids =$this->get_roles_staffs($configure['assigntorole']);
                    }else if($configure['stafftype'] =='designation'){
                        $staff_ids =$this->get_designations_staffs($configure['assigntodesignation']);
                    }

                    if(!$staff_ids[$last_run]){
                        $last_run =0;
                    }

                    if($staff_ids){
                        $staff_id =$staff_ids[$last_run];
                    }
                    if($staff_id){
                        $configure ['round_robin_last_run'] =$last_run;
                        $this->ci->workflow_model->updateFlowConfigure($flow->id,json_encode($configure));
                    }
                    
                }elseif($configure['type']=='having_less_no_of_projects'){
                    $staff_ids =array();
                    if($configure['stafftype'] =='staff'){
                        $staff_ids =$configure['assigntogroup'];
                    }else if($configure['stafftype'] =='roles'){
                        $staff_ids =$this->get_roles_staffs($configure['assigntorole']);
                    }else if($configure['stafftype'] =='designation'){
                        $staff_ids =$this->get_designations_staffs($configure['assigntodesignation']);
                    }
                    if($staff_ids){
                        $this->ci->db->where_in('teamleader', $staff_ids);
                        $this->ci->db->group_by("teamleader");
                        $this->ci->db->order_by('COUNT(id)', 'ASC');
                        $low_project =$this->ci->db->get(db_prefix().'projects')->row();
                        if($low_project){
                            $staff_id =$low_project->teamleader;
                        }
                        
                    }
                }elseif($configure['type']=='having_more_wins'){
                    $staff_ids =array();
                    if($configure['stafftype'] =='staff'){
                        $staff_ids =$configure['assigntogroup'];
                    }else if($configure['stafftype'] =='roles'){
                        $staff_ids =$this->get_roles_staffs($configure['assigntorole']);
                    }else if($configure['stafftype'] =='designation'){
                        $staff_ids =$this->get_designations_staffs($configure['assigntodesignation']);
                    }
                    if($staff_ids){
                        $this->ci->db->where_in('teamleader', $staff_ids);
                        $this->ci->db->where('stage_of !=', 1);
                        $this->ci->db->group_by("teamleader");
                        $this->ci->db->order_by('COUNT(id)', 'DESC');
                        $more_wins =$this->ci->db->get(db_prefix().'projects')->row();
                        if($more_wins){
                            $staff_id =$more_wins->teamleader;
                        }
                    }
                }
                break;
            case 'project_update_event':
                if($flow->configure){
                    $configure =$flow->configure;
                    if($this->project_change_log->field_name ==$configure['event']){
                        return true;
                    }elseif($this->project_change_log->field_name =='stage_of' && $this->project_change_log->field_name.'_'.$this->project_change_log->current_value == $configure['event']){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
                break;
            default:
                break;
        }
        if($staff_id){
            $this->project->teamleader =$staff_id;
            $this->ci->db->where('id',$this->project_id);
            $this->ci->db->update(db_prefix().'projects',['teamleader'=>$staff_id]);
        }
        
    }

    protected function run_whatsapp($flow)
    {
        if($flow->configure){
            $sendto =$flow->configure['sendto'];

            if($flow->configure['sendto'] =='staff' || $flow->configure['sendto'] =='other_staffs'){
                if($flow->configure['sendto'] =='staff'){
                    $staffs =array($this->get_staff_id());
                }else{
                    $staffs =$flow->configure['other_staffs_group'];
                }
                
                if($staffs){
                    foreach ($staffs as $staff_id) {
                        $this->run_mergefields($staff_id);
                        $this->ci->db->where('staffid', $staff_id);
                        $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
                        if($staff){
                            $sendto =$this->getCountryCallingCode($staff->phone_country_code) . $staff->phonenumber;
                            $this->sendWhatsapp($sendto, $flow, $this->merge_fields);
                        }
                    }
                }
            }elseif($flow->configure['sendto'] =='followers'){
                $members =$this->get_followers();
                if($members){
                    foreach ($members as $member) {
                        $this->run_mergefields($member['staff_id']);
                        $this->ci->db->where('staffid', $member['staff_id']);
                        $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
                        if($staff){
                            $sendto =$this->getCountryCallingCode($staff->phone_country_code) . $staff->phonenumber;
                            $this->sendWhatsapp($sendto, $flow, $this->merge_fields);
                        }
                    }
                }
            }elseif($flow->configure['sendto'] =='manager'){
                $manager_id =$this->get_manager_id();
                if($manager_id){
                    $this->run_mergefields($manager_id);
                    $this->ci->db->where('staffid', $manager_id);
                    $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
                    if($staff){
                        $sendto =$this->getCountryCallingCode($staff->phone_country_code) . $staff->phonenumber;
                        $this->sendWhatsapp($sendto, $flow, $this->merge_fields);
                    }
                }
            }
        }
    }

    protected function run_sms($flow)
    {
        
        if($flow->configure){
            $sendto =$flow->configure['sendto'];
            if($flow->configure['sendto'] =='staff' || $flow->configure['sendto'] =='other_staffs'){
                if($flow->configure['sendto'] =='staff'){
                    $staffs =array($this->get_staff_id());
                }else{
                    $staffs =$flow->configure['other_staffs_group'];
                }
                if($staffs){
                    foreach ($staffs as $staff_id) {
                        $this->run_mergefields($staff_id);
                        $this->ci->db->where('staffid', $staff_id);
                        $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
                        if($staff){
                            $sendto =$this->getCountryCallingCode($staff->phone_country_code) . $staff->phonenumber;
                            $this->sendSMS($sendto, $flow, $this->merge_fields);
                        }
                        
                    }
                }
            }elseif($flow->configure['sendto'] =='followers'){
                $members =$this->get_followers();
                if($members){
                    foreach ($members as $member) {
                        $this->run_mergefields($member['staff_id']);
                        $this->ci->db->where('staffid', $member['staff_id']);
                        $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
                        if($staff){
                            $sendto =$this->getCountryCallingCode($staff->phone_country_code) . $staff->phonenumber;
                            $this->sendSMS($sendto, $flow, $this->merge_fields);
                        }
                    }
                }
                $sendto =$this->getCountryCallingCode($this->project->phone_country_code) .$this->project->phonenumber;
            }elseif($flow->configure['sendto'] =='manager'){
                $manager_id =$this->get_manager_id();
                if($manager_id){
                    $this->run_mergefields($manager_id);
                    $this->ci->db->where('staffid', $manager_id);
                    $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
                    if($staff){
                        $sendto =$this->getCountryCallingCode($staff->phone_country_code) . $staff->phonenumber;
                        $this->sendSMS($sendto, $flow, $this->merge_fields);
                    }
                }
                
            }
            
        }
    }

    protected function run_add_activity($flow)
    {
        if($flow->configure){
            $this->ci->load->model('tasks_model');
            $startdate =Date('Y-m-d H:i:s', strtotime($flow->configure['startdate']));
            $data =array(
                'name'=>$flow->configure['subject'],
                'tasktype'=>$flow->configure['type'],
                'description'=>$flow->configure['description'],
                'priority'=>$flow->configure['priority'],
                'startdate'=>$startdate,
                'rel_type'=>'project',
                'rel_id'=>$this->project_id,
                'addedfrom'=>$this->project->assigned,
            );
            // pr($data);
            // echo 'Creating new activity';
            // pre('die');
            $data['taskid']  = $this->ci->tasks_model->addcrontask($data);
            $data['assignee'] = $this->project->teamleader;
            $this->ci->tasks_model->add_crontask_assignees($data);
        }
    }

    public function get_staff_id()
    {
        return $this->project->teamleader;
    }

    public function get_followers()
    {
        return $this->ci->projects_model->get_project_members($this->project_id);
    }

    public function get_manager_id()
    {
        $this->ci->db->where('staffid', $this->project->teamleader);
        $staff = $this->ci->db->get(db_prefix() . 'staff')->row();
        if(!$staff || !$staff->reporting_to){
            return 0;
        }

        return $staff->reporting_to;
    }
}