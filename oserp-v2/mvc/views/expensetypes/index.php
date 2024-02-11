
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-expensetypes"></i> Expense Categories</h3>

       
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_expensetypes')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php //if(permissionChecker('expensetypes_add')) { ?>
                    <h5 class="page-header">
                        <a href="<?php echo base_url('expensetypes/add') ?>">
                            <i class="fa fa-plus"></i> 
                            Add Expense Categories
                        </a>
                    </h5>
                <?php //} ?>

                <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-2">S.No</th>
                                <th class="col-sm-2">Name</th>
                                <th class="col-sm-2">Note</th>
                                <?php //if(permissionChecker('expensetypes_edit') || permissionChecker('expensetypes_delete')) { ?>
                                <th class="col-sm-2">Action</th>
                                <?php //} ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($expensetypes)) {$i = 1; foreach($expensetypes as $feetype) { ?>
                                <tr>
                                    <td data-title="S.No">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="name">
                                        <?php echo $feetype->expensetypes; ?>
                                    </td>
                  
                                    <td data-title="note">
                                        <?php echo $feetype->note; ?>
                                    </td>
                                    <?php //if(permissionChecker('expensetypes_edit') || permissionChecker('expensetypes_delete')) { ?>
                                        <td data-title="<?=$this->lang->line('action')?>">
                                            <?php //echo btn_edit('types/edit/'.$feetype->expensetypesID, $this->lang->line('edit')) ?>
                                            <a href="<?php echo base_url('expensetypes/edit/'.$feetype->expensetypesID);?>">Edit</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                                           <a href="<?php echo base_url('expensetypes/delete_exp_category/'.$feetype->expensetypesID);?>">Delete</a>
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