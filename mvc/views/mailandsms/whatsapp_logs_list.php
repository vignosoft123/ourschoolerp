

<div class="box">
    <div class="box-header">
        <h3 class="box-title"> 📱 WhatsApp Logs</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("mailandsms/index")?>"> <?=$this->lang->line('menu_mailandsms')?></a></li>
            <li class="active"> <?=$this->lang->line('menu_add')?> <?=$this->lang->line('menu_mailandsms')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">

             

<div class="  mt-4">
  

    <form method="get" class="form-inline mb-3">
        <select name="status" class="form-control mr-2" onchange="this.form.submit()">
            <option value="">All</option>
            <option value="success" <?= $status_filter == 'success' ? 'selected' : '' ?>>Success</option>
            <option value="failure" <?= $status_filter == 'failure' ? 'selected' : '' ?>>Failure</option>
        </select>
    </form>

    <div class="mb-2">
        <button id="deleteSelected" class="btn btn-danger btn-sm">🗑️ Delete Selected</button>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>ID</th>
                <th>Template</th>
                <th>Message</th>
                <th>Response</th>
                <th>Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)) { ?>
                <?php foreach ($logs as $log) {
                    $isSuccess = (strpos($log->api_response, 'S.') !== false);
                    $statusLabel = $isSuccess
                        ? '<span class="badge badge-success">Success</span>'
                        : '<span class="badge badge-danger">Failure</span>';
                ?>
                    <tr>
                        <td><input type="checkbox" class="logCheckbox" value="<?= $log->id ?>"></td>
                        <td><?= $log->id ?></td>
                        <td><?= $log->template_name ?></td>
                        <td><?= htmlentities($log->message) ?></td>
                        <td><?= htmlentities($log->api_response) ?></td>
                        <td><?= $statusLabel ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($log->created_on)) ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm deleteBtn" data-id="<?= $log->id ?>">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="8" class="text-center text-muted">No logs found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>




        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

  
<script>
$(document).ready(function() {
    // Select all
    $('#selectAll').on('click', function() {
        $('.logCheckbox').prop('checked', this.checked);
    });

    // Delete single
    $('.deleteBtn').on('click', function() {
        const id = $(this).data('id');
        if (confirm('Delete this log?')) {
            deleteLogs([id]);
        }
    });

    // Delete selected
    $('#deleteSelected').on('click', function() {
        const ids = $('.logCheckbox:checked').map(function() { return $(this).val(); }).get();
        if (ids.length === 0) {
            alert('Please select at least one log.');
            return;
        }
        if (confirm('Delete selected logs?')) {
            deleteLogs(ids);
        }
    });

    function deleteLogs(ids) {
        $.ajax({
            url: '<?= base_url("mailandsms/delete_whatsapp_logs") ?>',
            method: 'POST',
            data: { ids: ids },
            dataType: 'json',
            success: function(res) {
                alert(res.message);
                if (res.status == 1) location.reload();
            }
        });
    }
});
</script>
