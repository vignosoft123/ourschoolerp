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
                            Sms Error Logs
                        </a>

                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a class="red-text" href="<?php echo base_url('mailandsms/whatsapp_logs_list') ?>">
                            <i class="glyphicon glyphicon-ban-circle"></i> 
                            Whatsapp Logs
                        </a>

                    </h5>
                <?php } ?>

                <div id="hide-table">

                        <button id="deleteSelected" class="btn btn-danger btn-sm pull-right">Delete Selected</button>


                    <table id="mailSmsTable" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th><?=$this->lang->line('slno')?></th>
            <th><?=$this->lang->line('mailandsms_usertype')?></th>
            <th><?=$this->lang->line('mailandsms_users')?></th>
            <th>Campaign ID</th>
            <th><?=$this->lang->line('mailandsms_dateandtime')?></th>
            <th><?=$this->lang->line('mailandsms_message')?></th>
            <?php if(permissionChecker('mailandsms_view')) { ?>
                <th><?=$this->lang->line('action')?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php if(customCompute($mailandsmss)) { $i = 1; foreach($mailandsmss as $mailandsms) { ?>
            <tr id="row_<?=$mailandsms->mailandsmsID?>">
                <td><input type="checkbox" class="recordCheckbox" value="<?=$mailandsms->mailandsmsID?>"></td>
                <td><?=$i?></td>
                <td><?=($mailandsms->usertypeID !== NULL) ? $mailandsms->usertype : $this->lang->line('mailandsms_guest_user')?></td>
                <td>
                    <?php
                        if(strlen($mailandsms->users) > 36) {
                            echo substr($mailandsms->users, 0, 36). "..";
                        } else {
                            echo $mailandsms->users;
                        }
                    ?>
                </td>
                <td><?=$mailandsms->campid?></td>
                <td><?=date("d M Y h:i:s a", strtotime($mailandsms->create_date))?></td>
                <td><?=substr(strip_tags($mailandsms->message), 0, 36).'..'?></td>

                <?php if(permissionChecker('mailandsms_view')) { ?>
                <td>

                  <?php echo btn_view('mailandsms/view/'.$mailandsms->mailandsmsID.'/'.$mailandsms->campid, $this->lang->line('view')) ?>
                  
                    <button class="btn btn-danger btn-xs deleteSingle" data-id="<?=$mailandsms->mailandsmsID?>">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
                <?php } ?>
            </tr>
        <?php $i++; } } ?>
    </tbody>
</table>

                </div>
   

            </div> <!-- col-sm-12 -->
            
        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

<script>
$(document).ready(function () {
    // ✅ Select all checkbox
    $('#selectAll').on('change', function() {
        $('.recordCheckbox').prop('checked', $(this).prop('checked'));
    });

    // ✅ Single delete
    $('.deleteSingle').click(function () {
        let id = $(this).data('id');
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: "<?=base_url('mailandsms/delete_mailandsms')?>",
                type: "POST",
                data: { id: id },
                dataType: "json",
                success: function (res) {
                    if (res.status) {
                        $('#row_' + id).remove();
                    }
                    alert(res.message);
                }
            });
        }
    });

    // ✅ Bulk delete
    $('#deleteSelected').click(function () {
        let selected = [];
        $('.recordCheckbox:checked').each(function () {
            selected.push($(this).val());
        });

        if (selected.length === 0) {
            alert('Please select at least one record.');
            return;
        }

        if (confirm('Are you sure you want to delete selected records?')) {
            $.ajax({
                url: "<?=base_url('mailandsms/delete_mailandsms_bulk')?>",
                type: "POST",
                data: { ids: selected },
                dataType: "json",
                success: function (res) {
                    if (res.status) {
                        selected.forEach(id => $('#row_' + id).remove());
                    }
                    alert(res.message);
                }
            });
        }
    });
});
</script>