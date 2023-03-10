<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
.horizontal-tabs {
    width:100%;
}
.project-tabs {
    float:left;
}
.pipechange {
    float:right;
    padding-top:8px;
}
.formnewpipeline .dropdown-menu {
    width:100%;
}
</style>
<?php //pre($ownerHierarchy); 
$hasHIstory = $this->approval_model->hasHistory('projects', $project->id) ? true : false;
?>
<div class="horizontal-scrollable-tabs">
  <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
  <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
  <div class="horizontal-tabs">
    <ul class="nav nav-tabs no-margin project-tabs nav-tabs-horizontal" role="tablist">
        <?php
        foreach(filter_project_visible_tabs($tabs, $project->settings->available_features) as $key => $tab){
            $dropdown = isset($tab['collapse']) ? true : false;
            if($key =='project_approval' && $project->approved ==1 && !$hasHIstory){
                continue;
            }
            ?>
            <li class="<?php if($key == 'project_tasks' && !$this->input->get('group')){echo 'active ';} ?>project_tab_<?php echo $key; ?><?php if($dropdown){echo ' nav-tabs-submenu-parent';} ?>">
                <a
                data-group="<?php echo $key; ?>"
                role="tab"
                <?php if($dropdown){ ?>
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="true"
                    class="dropdown-toggle"
                    href="#"
                    id="dropdown_<?php echo $key; ?>"<?php } else { ?>
                    href="<?php echo admin_url('projects/view/'.$project->id.'?group='.$key); ?>"
                    <?php } ?>>
                    <!-- <i class="<?php echo $tab['icon']; ?>" aria-hidden="true"></i> -->
                    <?php echo $tab['name']; ?>
                    <?php $count_name =$key."_count"; ?>
                    <?php if(isset($$count_name) && $$count_name >0): ?>
                    <span class="badge badge-light ml-3" id="<?php echo $count_name; ?>"><?php echo $$count_name?></span>
                    <?php endif; ?>
                    <?php if($dropdown){ ?> <span class="caret"></span> <?php } ?>
                </a>
                <?php if($dropdown){ ?>
                    <?php if(!is_rtl()){ ?>
                        <div class="tabs-submenu-wrapper">
                        <?php } ?>
                        <ul class="dropdown-menu" aria-labelledby="dropdown_<?php echo $key; ?>">
                            <?php
                            foreach($tab['children'] as $d){
                                echo '<li class="nav-tabs-submenu-child"><a href="'.admin_url('projects/view/'.$project->id.'?group='.$d['slug']).'" data-group="'.$d['slug'].'">'.$d['name'].'</a></li>';
                            }
                            ?>
                        </ul>
                        <?php if(!is_rtl()){ ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</div>
</div>

