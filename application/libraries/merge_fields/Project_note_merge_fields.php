<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Project_note_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [];
    }

    /**
     * Merge fields for tasks
     * @param  mixed  $task_id         task id
     * @param  boolean $client_template is client template or staff template
     * @return array
     */
    public function format($note_id)
    {
        $fields = [];

        $this->ci->db->where('id', $note_id);
        $note = $this->ci->db->get(db_prefix().'project_notes')->row();

        if (!$note) {
            return $fields;
        }


        $fields['{project_note_description}'] = $note->content;
        if($note && $note->staff_id){
            $this->ci->db->where('staffid',$note->staff_id);
            $this->ci->db->select('CONCAT(firstname," ",lastname) as full_name');
            $note_staff =$this->ci->db->get(db_prefix().'staff')->row();
            if($note_staff){
                $fields['{project_note_added_by}'] =$note_staff->full_name;
            }
        }

        return hooks()->apply_filters('project_note_merge_fields', $fields, [
        'id'              => $note_id,
        'note'            => $note,
     ]);
    }
}
