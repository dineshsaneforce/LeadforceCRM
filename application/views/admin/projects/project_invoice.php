<?php if (has_permission('invoice', '', 'create')) { ?>
    <a href="<?php echo admin_url('invoices/invoice?rel_type=project&rel_id=' . $project->id); ?>" class="btn btn-info mbot25">New Invoice</a>
<?php } ?>
<?php
$table_data = array(
    _l('invoice_dt_table_heading_number'),
    _l('invoice_dt_table_heading_amount'),
    _l('invoice_total_tax'),
    array(
      'name'=>_l('invoice_estimate_year'),
      'th_attrs'=>array('class'=>'not_visible')
    ),
    _l('invoice_dt_table_heading_date'),
    array(
      'name'=>_l('invoice_dt_table_heading_client'),
      'th_attrs'=>array('class'=>(isset($client) ? 'not_visible' : ''))
    ),
    _l('project'),
    _l('tags'),
    _l('invoice_dt_table_heading_duedate'),
    _l('invoice_dt_table_heading_status'));
  $custom_fields = get_custom_fields('invoice',array('show_on_table'=>1));
  foreach($custom_fields as $field){
    array_push($table_data,$field['name']);
  }
  $table_data = hooks()->apply_filters('invoices_table_columns', $table_data);
  render_datatable($table_data, 'invoices-project');
?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) { 
        initDataTable('.table-invoices-project', admin_url + 'invoices/invoice_relations/' + <?php echo $project->id; ?> + '/project','undefined', 'undefined','undefined',[6,'desc']);
    });
</script>