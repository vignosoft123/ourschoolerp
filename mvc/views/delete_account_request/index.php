<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-user-times"></i> Delete Account Requests
        </h3>
    </div>

    <div class="box-body">

        <!-- Type filter tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs" id="dar-tabs">
                <li class="active" data-type="all">
                    <a href="#">All <span class="badge" id="cnt-all"><?= $counts['all'] ?></span></a>
                </li>
                <li data-type="student">
                    <a href="#">Student <span class="badge" id="cnt-student"><?= $counts['student'] ?></span></a>
                </li>
                <li data-type="teacher">
                    <a href="#">Teacher <span class="badge" id="cnt-teacher"><?= $counts['teacher'] ?></span></a>
                </li>
                <li data-type="user">
                    <a href="#">User <span class="badge" id="cnt-user"><?= $counts['user'] ?></span></a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Table container — filled via AJAX POST -->
                <div id="dar-table-wrap">
                    <div class="text-center" style="padding:30px 0;color:#aaa;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /.box-body -->
</div><!-- /.box -->

<!-- Row template (hidden) -->
<script type="text/html" id="dar-row-tpl">
    <tr id="row-{{id}}">
        <td>{{no}}</td>
        <td><span class="label label-{{typeBadge}}" style="font-size:12px;text-transform:capitalize;">{{type}}</span></td>
        <td>{{user_id}}</td>
        <td>{{user_name}}</td>
        <td>{{roll}}</td>
        <td>{{phone}}</td>
        <td>{{class_section}}</td>
        <td>{{reason}}</td>
        <td><span class="label label-{{statusBadge}}" style="font-size:12px;text-transform:capitalize;" id="status-{{id}}">{{status}}</span></td>
        <td>{{requested_at}}</td>
        <td>{{actions}}</td>
    </tr>
</script>

<script>
(function () {
    var BASE   = '<?= base_url() ?>';
    var active = 'all';

    var typeBadge   = { student: 'primary', teacher: 'success', user: 'warning' };
    var statusBadge = { pending: 'danger', processed: 'success' };

    function escHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function loadList(type) {
        $('#dar-table-wrap').html(
            '<div class="text-center" style="padding:30px 0;color:#aaa;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>'
        );

        $.post(BASE + 'delete_account_request/get_list', { type: type }, function (res) {
            if (!res.status) {
                $('#dar-table-wrap').html('<p class="text-danger">' + escHtml(res.message) + '</p>');
                return;
            }

            if (!res.data.length) {
                $('#dar-table-wrap').html(
                    '<div class="text-center" style="padding:40px 0;color:#aaa;">' +
                    '<i class="fa fa-inbox fa-3x" style="margin-bottom:12px;display:block;"></i>' +
                    'No delete account requests found.</div>'
                );
                return;
            }

            var tpl  = $('#dar-row-tpl').html();
            var rows = '';
            $.each(res.data, function (i, r) {
                var actions = '';
                if (r.status === 'pending') {
                    actions += '<button class="btn btn-xs btn-success btn-mark-processed" data-id="' + r.id + '" title="Mark as Processed" style="margin-right:4px;"><i class="fa fa-check"></i></button>';
                }
                actions += '<button class="btn btn-xs btn-danger btn-delete-request" data-id="' + r.id + '" title="Delete Request"><i class="fa fa-trash"></i></button>';

                var classSection = '';
                if (r.class || r.section) {
                    classSection = escHtml((r.class || '') + (r.section ? ' - ' + r.section : ''));
                } else {
                    classSection = '<em style="color:#aaa;">—</em>';
                }

                var row = tpl
                    .replace(/{{no}}/g,           i + 1)
                    .replace(/{{id}}/g,            r.id)
                    .replace(/{{type}}/g,          escHtml(r.type))
                    .replace(/{{typeBadge}}/g,     typeBadge[r.type] || 'default')
                    .replace(/{{user_id}}/g,       r.user_id)
                    .replace(/{{user_name}}/g,     escHtml(r.user_name))
                    .replace(/{{roll}}/g,          r.roll ? escHtml(r.roll) : '<em style="color:#aaa;">—</em>')
                    .replace(/{{phone}}/g,         r.phone ? escHtml(r.phone) : '<em style="color:#aaa;">—</em>')
                    .replace(/{{class_section}}/g, classSection)
                    .replace(/{{reason}}/g,        r.reason ? escHtml(r.reason) : '<em style="color:#aaa;">—</em>')
                    .replace(/{{status}}/g,        escHtml(r.status))
                    .replace(/{{statusBadge}}/g,   statusBadge[r.status] || 'default')
                    .replace(/{{requested_at}}/g,  escHtml(r.requested_at))
                    .replace(/{{actions}}/g,       actions);

                rows += row;
            });

            var html =
                '<div class="table-responsive">' +
                '<table class="table table-bordered table-striped table-hover">' +
                '<thead><tr>' +
                '<th style="width:40px;">#</th><th>Type</th><th>ID</th><th>Name</th>' +
                '<th>Roll No</th><th>Phone</th><th>Class / Section</th>' +
                '<th>Reason</th><th>Status</th><th>Requested At</th>' +
                '<th style="width:100px;">Action</th>' +
                '</tr></thead><tbody>' + rows + '</tbody></table></div>';

            $('#dar-table-wrap').html(html);

        }, 'json').fail(function () {
            $('#dar-table-wrap').html('<p class="text-danger">Server error. Please try again.</p>');
        });
    }

    // Tab click — POST by type
    $(document).on('click', '#dar-tabs li', function (e) {
        e.preventDefault();
        active = $(this).data('type');
        $('#dar-tabs li').removeClass('active');
        $(this).addClass('active');
        loadList(active);
    });

    // Mark as processed
    $(document).on('click', '.btn-mark-processed', function () {
        var id  = $(this).data('id');
        var btn = $(this);
        if (!confirm('Mark this request as processed?')) return;

        $.post(BASE + 'delete_account_request/mark_processed', { id: id }, function (res) {
            if (res.status) {
                $('#status-' + id).removeClass('label-danger').addClass('label-success').text('processed');
                btn.remove();
            } else {
                alert(res.message || 'Failed to update.');
            }
        }, 'json').fail(function () { alert('Server error.'); });
    });

    // Delete request row
    $(document).on('click', '.btn-delete-request', function () {
        var id = $(this).data('id');
        if (!confirm('Delete this request permanently?')) return;

        $.post(BASE + 'delete_account_request/remove', { id: id }, function (res) {
            if (res.status) {
                $('#row-' + id).fadeOut(300, function () { $(this).remove(); });
            } else {
                alert(res.message || 'Failed to delete.');
            }
        }, 'json').fail(function () { alert('Server error.'); });
    });

    // Load default tab on page ready
    loadList('all');

}());
</script>
