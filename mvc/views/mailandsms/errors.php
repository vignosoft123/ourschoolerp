<style>
    .red-text{color:red !important;}
   </style> 

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-mailandsms"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_mailandsms')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php if(permissionChecker('mailandsms_add')) { ?>
                    <h5 class="page-header">
                        <a href="<?php echo base_url('mailandsms/add') ?>">
                            <i class="fa fa-plus"></i> 
                            <?=$this->lang->line('add_title')?>
                        </a>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a class="red-text" href="<?php echo base_url('mailandsms/errors') ?>">
                            <i class="glyphicon glyphicon-ban-circle"></i> 
                            Error logs
                        </a>
                    </h5>
                <?php } ?>

                <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-lg-1"><?=$this->lang->line('slno')?></th>
                                <th class="col-lg-3">Response</th>
                                <th class="col-lg-3">Type</th>
                                <th class="col-lg-3">Created On</th>
                                <th class="col-lg-6">Request</th>

                               
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($errors)) {$i = 1; foreach($errors as $error) { ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td ><?= $error->api_response?>
                                         </td>
                                         <td ><?= $error->type?>
                                         </td>
                                         <td >
                                        <?= $error->created_on?> </td>
                                        <td >
                                        <?=  $error->request_url?> </td>
                                       
  
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>
   

            </div> <!-- col-sm-12 -->
            
        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->
