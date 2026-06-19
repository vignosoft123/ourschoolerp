
<div class="box">
    <div class="box-header">
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_mailandsmstemplate')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <div class="nav-tabs-custom" style="margin-bottom:15px;">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="<?=base_url('mailandsmstemplate/index')?>">
                                <i class="fa icon-template"></i> <?=$this->lang->line('panel_title')?>
                            </a>
                        </li>
                        <li>
                            <a href="<?=base_url('mailandsmstemplate/whatsapp_index')?>">
                                <i class="fa fa-whatsapp"></i> Whatsapp Templates
                            </a>
                        </li>
                    </ul>
                </div>

                <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-1"><?=$this->lang->line('slno')?></th>
                                <th class="col-sm-1">DB ID</th>
                                <th class="col-sm-2"><?=$this->lang->line('mailandsmstemplate_name')?></th>
                                <th class="col-sm-2"><?=$this->lang->line('mailandsmstemplate_type')?></th>
                                <th class="col-sm-2"><?=$this->lang->line('mailandsmstemplate_user')?></th>
                                <th class="col-sm-2"><?=$this->lang->line('mailandsmstemplate_template')?></th>
                                <th class="col-sm-1">Template ID</th>
                                <?php if(permissionChecker('mailandsmstemplate_edit') || permissionChecker('mailandsmstemplate_delete') || permissionChecker('mailandsmstemplate_view')) {
                                ?>
                                <th class="col-sm-2"><?=$this->lang->line('action')?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($mailandsmstemplates)) {$i = 1; foreach($mailandsmstemplates as $mailandsmstemplate) { 
                            //   echo "<pre>";  var_dump($mailandsmstemplate);die;
                                ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="DB ID">
                                        <?php echo $mailandsmstemplate->mailandsmstemplateID; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('mailandsmstemplate_name')?>">
                                        <?php 
                                            if(strlen($mailandsmstemplate->name) > 25)
                                                echo substr($mailandsmstemplate->name, 0, 25)."...";
                                            else 
                                                echo substr($mailandsmstemplate->name, 0, 25);
                                        ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('mailandsmstemplate_type')?>">
                                        <?php echo ucfirst($mailandsmstemplate->type); ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('mailandsmstemplate_user')?>">
                                        <?php
                                            echo ucfirst($mailandsmstemplate->usertype);
                                        ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('mailandsmstemplate_template')?>">
                                        <?php 
                                            if(strlen($mailandsmstemplate->template) > 25)
                                                echo substr($mailandsmstemplate->template, 0, 25)."...";
                                            else 
                                                echo substr($mailandsmstemplate->template, 0, 25);
                                        ?>
                                    </td>
                                    <td data-title="Template ID">
                                        <?php echo $mailandsmstemplate->templ_id; ?>
                                    </td>
                                    <?php if(permissionChecker('mailandsmstemplate_edit') || permissionChecker('mailandsmstemplate_delete') || permissionChecker('mailandsmstemplate_view')) {
                                    ?>
                                    <td data-title="<?=$this->lang->line('action')?>">
                                        <?php echo btn_view('mailandsmstemplate/view/'.$mailandsmstemplate->mailandsmstemplateID, $this->lang->line('view')) ?>
                                        <?php echo btn_edit('mailandsmstemplate/edit/'.$mailandsmstemplate->mailandsmstemplateID, $this->lang->line('edit')) ?>
                                        <?php echo btn_delete('mailandsmstemplate/delete/'.$mailandsmstemplate->mailandsmstemplateID, $this->lang->line('delete')) ?>
                                    </td>
                                    <?php } ?>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>
                

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    <?php if(permissionChecker('mailandsmstemplate_add')) { ?>
    var $addBtn = $('<a href="<?=base_url('mailandsmstemplate/add')?>" class="btn btn-success btn-sm" style="margin-left:5px"><i class="fa fa-plus"></i> <?=$this->lang->line('add_title')?></a>');
    $('.buttons-pdf').after($addBtn);
    <?php } ?>
});
</script>