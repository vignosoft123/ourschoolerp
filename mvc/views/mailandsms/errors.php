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

                 <div class="pull-right">
                    <button id="deleteSelectedErrors" class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> Delete Selected
                    </button>
                </div>

                <div id="hide-table">
                 <table id="example1" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllErrors"></th>
                    <th style="width: 5%;"><?=$this->lang->line('slno')?></th>
                    <th style="width: 15%;">Response</th>
                    <th style="width: 15%;">Type</th>
                    <th style="width: 25%;">Message</th>
                    <th style="width: 10%;">Created On</th>
                    <th style="width: 20%;">Request</th>
                    <th style="width: 5%;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(customCompute($errors)) { $i = 1; foreach($errors as $error) { ?>
                    <tr id="errorRow_<?=$error->id?>">
                        <td><input type="checkbox" class="errorCheckbox" value="<?=$error->id?>"></td>
                        <td><?=$i?></td>
                        <td><?=htmlspecialchars($error->api_response)?></td>
                        <td><?=htmlspecialchars($error->type)?></td>
                        <td><?=substr(strip_tags($error->message), 0, 60)?></td>
                        <td><?=date("d M Y h:i a", strtotime($error->created_on))?></td>
                        <td><?=substr(strip_tags($error->request_url), 0, 60)?></td>
                        <td>
                            <button class="btn btn-danger btn-xs deleteError" data-id="<?=$error->id?>">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
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
$(document).ready(function() {
    // Select/Deselect all
    $('#selectAllErrors').on('change', function() {
        $('.errorCheckbox').prop('checked', this.checked);
    });

    // ✅ Single Delete
    $('.deleteError').on('click', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this log?')) {
            $.ajax({
                url: '<?=base_url('mailandsms/delete_error_log/')?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        $('#errorRow_' + id).fadeOut(500, function() { $(this).remove(); });
                    } else {
                        alert(res.message);
                    }
                }
            });
        }
    });

    // ✅ Multiple Delete
    $('#deleteSelectedErrors').on('click', function() {
        const selected = $('.errorCheckbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selected.length === 0) {
            alert('Please select at least one record to delete.');
            return;
        }

        if (confirm('Delete selected logs?')) {
            $.ajax({
                url: '<?=base_url('mailandsms/delete_multiple_error_logs')?>',
                type: 'POST',
                data: { ids: selected },
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        selected.forEach(function(id) {
                            $('#errorRow_' + id).fadeOut(500, function() { $(this).remove(); });
                        });
                    } else {
                        alert(res.message);
                    }
                }
            });
        }
    });
});
</script>
