
<div class="box">
    <div class="box-header">

    <a href="<?php echo base_url('mailandsmstemplate/index');?>"> <h3 class="box-title"><i class="fa icon-template"></i> <?=$this->lang->line('panel_title')?></h3></a>
    
        <a href="<?php echo base_url('mailandsmstemplate/whatsapp_index');?>"> <h3 class="box-title"><i class="fa icon-template"></i> Whatsapp Templates</h3></a>

       
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active">Whatsapp Templates</li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php 
                    if(permissionChecker('mailandsmstemplate_add')) {
                ?>
                    <h5 class="page-header">
                        <a href="<?php echo base_url('mailandsmstemplate/add') ?>">
                            <i class="fa fa-plus"></i> 
                            <?=$this->lang->line('add_title')?>
                        </a>
                    </h5>
                <?php } ?>

                <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-1"><?=$this->lang->line('slno')?></th>
                                <th class="col-sm-2">Temaplate Name</th>
                                <th class="col-sm-2">Params</th>
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
                            <?php if(customCompute($mailandsmstemplates)) {$i = 1; foreach($mailandsmstemplates as $mailandsmstemplate) { ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('template_name')?>">
                                        <?php 
                                            if(strlen($mailandsmstemplate->template_name) > 25)
                                                echo substr($mailandsmstemplate->template_name, 0, 25)."...";
                                            else 
                                                echo substr($mailandsmstemplate->template_name, 0, 25);
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
                                    <?php //if(permissionChecker('mailandsmstemplate_edit') || permissionChecker('mailandsmstemplate_delete') || permissionChecker('mailandsmstemplate_view')) {
                                    ?>
                                    <td data-title="<?=$this->lang->line('action')?>">
                                        <?php //echo btn_view('mailandsmstemplate/view/'.$mailandsmstemplate->mailandsmstemplateID, $this->lang->line('view')) ?>
                                        <?php //echo btn_edit('mailandsmstemplate/edit/'.$mailandsmstemplate->mailandsmstemplateID, $this->lang->line('edit')) ?>
                                        <?php echo btn_delete('mailandsmstemplate/delete/'.$mailandsmstemplate->mailandsmstemplateID.'/whatsapp', $this->lang->line('delete')) ?>
                                    </td>
                                    <?php //} ?>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>
                

            </div>
        </div>
    </div>
</div>