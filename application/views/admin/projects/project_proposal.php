<?php if (has_permission('proposals', '', 'create')) { ?>
    <a href="<?php echo admin_url('proposals/proposal?rel_type=project&rel_id=' . $project->id); ?>" class="btn btn-info mbot25"><?php echo _l('new_proposal'); ?></a>
<?php } ?>
<?php
$table_data = array(
    _l('proposal') . ' #',
    _l('proposal_subject'),
    _l('proposal_total'),
    _l('proposal_date'),
    _l('proposal_open_till'),
    _l('tags'),
    _l('proposal_date_created'),
    _l('proposal_status')
);
$custom_fields = get_custom_fields('proposal', array('show_on_table' => 1));
foreach ($custom_fields as $field) {
    array_push($table_data, $field['name']);
}
$table_data = hooks()->apply_filters('proposals_relation_table_columns', $table_data);
render_datatable($table_data, 'proposals-project', [], [
    'data-last-order-identifier' => 'proposals-relation',
    'data-default-order'         => get_table_last_order('proposals-relation'),
]);
?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) { 
        initDataTable('.table-proposals-project', admin_url + 'proposals/proposal_relations/' + <?php echo $project->id; ?> + '/project','undefined', 'undefined','undefined',[6,'desc']);
    });
</script>