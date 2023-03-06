<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = ['name', 'icon', 'status','option'];
$sIndexColumn = 'id';
$sTable = db_prefix() . 'tasktype';
$additionalSelect = ['id'];
$join = [];
$where = [];
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow)
{
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++)
    {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name')
        {
            $_data = '<b><a href="#" class="edit-task task-type-link" data-taskypeid="' . $aRow['id'] . '">' . $_data . '</a></b>';
            $_data .= '<div class="row-options">';
            $_data .= '</div>';
        }
         elseif ($aColumns[$i] == 'status') {
            $checked = '';
            if ($aRow['status'] == 1) {
                $checked = 'checked';
            }
            if( $aRow['id'] != 1){
                $_data = '<div class="onoffswitch">
                <input type="checkbox" data-switch-url="' . admin_url() . 'tasktype/change_tasktype_status" name="onoffswitch" class="onoffswitch-checkbox task-type-toggle" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
                <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
            </div>';        
            $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';  
            }else{
                $_data = "";
            }
        }
        elseif ($aColumns[$i] == 'icon') {
            $_data = '<i class="fa ' . $aRow['icon'] . '"></i>';
        }elseif ($aColumns[$i] == 'option'&&$aRow['id'] != 1){
            $_data = '<a href="' . admin_url('tasktype/delete_tasktype/' . $aRow['id']) . '" class="_delete text-danger"><i class="fa fa-trash"></i></a>';
        }        
        $row[] = $_data;
        $row['DT_RowClass'] = 'has-row-options';
    }
    $output['aaData'][] = $row;
}