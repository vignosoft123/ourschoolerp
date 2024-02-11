<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-home"></i> <?= $this->lang->line('panel_title') ?></h3>
        <ol class="breadcrumb">
            <li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
            <li class="active"><?= $this->lang->line('menu_villages') ?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php
                if (permissionChecker('village_add')) {
                ?>
                    <h5 class="page-header">
                        <a href="<?php echo base_url('village/add') ?>">
                            <i class="fa fa-plus"></i>
                            <?= $this->lang->line('add_title') ?>
                        </a>
                    </h5>
                <?php } ?>


                <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-1"><?= $this->lang->line('slno') ?></th>
                                <th class="col-sm-2"><?= $this->lang->line('villageName') ?></th>
                                <th class="col-sm-1"><?= $this->lang->line('village_status') ?></th>
                                <?php if (permissionChecker('village_edit') || permissionChecker('village_delete')) { ?>
                                    <th class="col-sm-1"><?= $this->lang->line('action') ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (customCompute($villages)) {
                                $i = 1;
                                foreach ($villages as $village) { ?>
                                    <tr>
                                        <td data-title="<?= $this->lang->line('slno') ?>">
                                            <?php echo $i; ?>
                                        </td>

                                        <td data-title="<?= $this->lang->line('village_name') ?>">
                                            <?php echo $village->villageName; ?>
                                        </td>



                                        <td data-title="<?= $this->lang->line('village_status') ?>">
                                            <?php
                                            if ($village->status == 0) {
                                                echo "<button class='btn btn-danger btn-xs'>" . $this->lang->line('village_in_active_status') . "</button>";
                                            } else {
                                                echo "<button class='btn btn-success btn-xs'>" . $this->lang->line('village_active_status') . "</button>";
                                            }
                                            ?>
                                        </td>

                                        <?php if (permissionChecker('village_edit') || permissionChecker('village_delete')) { ?>
                                            <td data-title="<?= $this->lang->line('action') ?>">
                                                <?php echo btn_edit('village/edit/' . $village->villageID, $this->lang->line('edit')); ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                            <?php $i++;
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>