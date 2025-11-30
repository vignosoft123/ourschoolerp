

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

    <div class="table-responsive">
        <table class="table table-bordered table-striped" style="table-layout: fixed; width: 100%;">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 40px;"><input type="checkbox" id="selectAll"></th>
                    <th style="width: 60px;">ID</th>
                    <th style="width: 120px;">Template</th>
                    <th style="width: 200px;">Message</th>
                    <th style="width: 250px;">Request URL</th>
                    <th style="width: 120px;">Response</th>
                    <th style="width: 80px;">Status</th>
                    <th style="width: 120px;">Date</th>
                    <th style="width: 80px;">Action</th>
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
                            <td style="word-wrap: break-word; overflow-wrap: break-word;"><?= htmlentities($log->template_name) ?></td>
                            <td style="word-wrap: break-word; overflow-wrap: break-word; max-height: 100px; overflow-y: auto;" 
                                title="Click to view full message">
                                <span class="text-clickable" data-type="message" data-content="<?= htmlentities($log->message) ?>" style="cursor: pointer; color: #007bff; text-decoration: underline;">
                                    <?= strlen($log->message) > 60 ? htmlentities(substr($log->message, 0, 60)) . '...' : htmlentities($log->message) ?>
                                </span>
                            </td>
                            <td style="word-wrap: break-word; overflow-wrap: break-word; font-size: 11px;" 
                                title="Click to view full URL">
                                <span class="text-clickable" data-type="url" data-content="<?= htmlentities($log->request_url) ?>" style="cursor: pointer; color: #007bff; text-decoration: underline;">
                                    <?= strlen($log->request_url) > 50 ? htmlentities(substr($log->request_url, 0, 50)) . '...' : htmlentities($log->request_url) ?>
                                </span>
                            </td>
                            <td style="word-wrap: break-word; overflow-wrap: break-word; font-size: 11px;" 
                                title="Click to view full response">
                                <span class="text-clickable" data-type="response" data-content="<?= htmlentities($log->api_response) ?>" style="cursor: pointer; color: #007bff; text-decoration: underline;">
                                    <?= strlen($log->api_response) > 30 ? htmlentities(substr($log->api_response, 0, 30)) . '...' : htmlentities($log->api_response) ?>
                                </span>
                            </td>
                            <td><?= $statusLabel ?></td>
                            <td style="font-size: 12px;"><?= date('d-m-Y H:i', strtotime($log->created_on)) ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm deleteBtn" data-id="<?= $log->id ?>">Delete</button>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="9" class="text-center text-muted">No logs found</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for displaying full text -->
<div class="modal fade" id="textModal" tabindex="-1" role="dialog" aria-labelledby="textModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="textModalLabel">Full Content</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label id="modalContentLabel">Content:</label>
                    <textarea id="modalContent" class="form-control" rows="10" readonly style="white-space: pre-wrap; word-wrap: break-word;"></textarea>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="copyToClipboard()">📋 Copy</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
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

    // Click to view full content
    $('.text-clickable').on('click', function() {
        const type = $(this).data('type');
        const content = $(this).data('content');
        
        let title = '';
        switch(type) {
            case 'message':
                title = 'Full Message';
                break;
            case 'url':
                title = 'Full Request URL';
                break;
            case 'response':
                title = 'Full API Response';
                break;
        }
        
        $('#textModalLabel').text(title);
        $('#modalContentLabel').text(title + ':');
        $('#modalContent').val(content);
        $('#textModal').modal('show');
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

function copyToClipboard() {
    const content = document.getElementById('modalContent');
    content.select();
    content.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show feedback
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '✅ Copied!';
    btn.classList.add('btn-success');
    btn.classList.remove('btn-secondary');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.add('btn-secondary');
        btn.classList.remove('btn-success');
    }, 2000);
}
</script>
