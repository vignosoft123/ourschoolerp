<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-university"></i> <?=$this->lang->line('panel_title')?></h3>

        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('panel_title')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php if(permissionChecker('college_group_add')) { ?>
                <h5 class="page-header">
                    <a href="<?php echo base_url('college_group/add') ?>">
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
                                <th class="col-sm-4"><?=$this->lang->line('college_group_name')?></th>
                                <th class="col-sm-5"><?=$this->lang->line('college_group_url')?></th>
                                <?php if(permissionChecker('college_group_edit') || permissionChecker('college_group_delete')) { ?>
                                    <th class="col-sm-2"><?=$this->lang->line('action')?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($college_groups)) {$i = 1; foreach($college_groups as $college_group) { ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('college_group_name')?>">
                                        <?=$college_group->college_name?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('college_group_url')?>">
                                        <a href="<?=$college_group->college_url?>" target="_blank"><?=$college_group->college_url?></a>
                                    </td>
                                    <?php if(permissionChecker('college_group_edit') || permissionChecker('college_group_delete')) { ?>
                                    <td data-title="<?=$this->lang->line('action')?>">
                                        <?php if(permissionChecker('college_group_edit')) { echo btn_edit('college_group/edit/'.$college_group->collegegroupID, $this->lang->line('edit')); } ?>
                                        <?php if(permissionChecker('college_group_delete')) { echo btn_delete('college_group/delete/'.$college_group->collegegroupID, $this->lang->line('delete')); } ?>
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
