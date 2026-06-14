<style>
    /* ── Top action bar (mirrors student page) ── */
    .sms-top-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        padding: 14px 18px;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .sbar-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none !important;
        transition: all 0.2s ease;
        line-height: 1.3;
        letter-spacing: 0.2px;
    }
    .sbar-btn:hover {
        text-decoration: none !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 14px rgba(0,0,0,0.18);
        color: #fff !important;
    }
    .sbar-btn i { font-size: 14px; }
    .sbar-btn-add    { background: linear-gradient(135deg,#0cc035 0%,#0a9d2b 100%); color:#fff !important; }
    .sbar-btn-errors { background: linear-gradient(135deg,#e53935 0%,#b71c1c 100%); color:#fff !important; }
    .sbar-btn-wa     { background: linear-gradient(135deg,#25d366 0%,#128c7e 100%); color:#fff !important; }
    .sbar-btn-delete { background: linear-gradient(135deg,#e53935 0%,#b71c1c 100%); color:#fff !important; }
    .sbar-btn.sbar-disabled {
        background: #ccc !important; color: #888 !important;
        cursor: not-allowed !important; transform: none !important;
        box-shadow: none !important; opacity: 0.7;
    }

    /* ── Filter btn group ── */
    .status-filters .btn { border-radius: 0; font-weight: 600; }
    .status-filters .btn:first-child { border-radius: 6px 0 0 6px; }
    .status-filters .btn:last-child  { border-radius: 0 6px 6px 0; }
    .status-filters .filter-btn.active  { box-shadow: inset 0 3px 5px rgba(0,0,0,.25); opacity: 1; }
    .status-filters .filter-btn:not(.active) { opacity: 0.6; }

    /* ── Table styling (mirrors student page #example1) ── */
    #mailSmsTable {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #fff !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
        border-radius: 8px !important;
        overflow: hidden !important;
        width: 100% !important;
        border: 1px solid #e0e0e0 !important;
    }
    #mailSmsTable thead {
        background: linear-gradient(135deg,#1a73e8 0%,#1045a8 100%) !important;
        color: #fff !important;
    }
    #mailSmsTable thead th {
        text-align: center !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        letter-spacing: 0.5px !important;
        border: none !important;
        border-right: 1px solid rgba(255,255,255,0.2) !important;
        color: #fff !important;
    }
    #mailSmsTable thead th:last-child { border-right: none !important; }
    #mailSmsTable tbody tr {
        transition: all 0.3s ease !important;
        border-bottom: 1px solid #f0f0f0 !important;
    }
    #mailSmsTable tbody tr:hover {
        background: linear-gradient(90deg,#fff3e0 0%,#ffe0b2 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 8px rgba(255,107,107,0.2) !important;
    }
    #mailSmsTable tbody td {
        padding: 10px 10px !important;
        vertical-align: middle !important;
        border: 1px solid #eee !important;
        font-size: 13px !important;
        text-align: center !important;
    }
    #mailSmsTable tbody tr:nth-child(even) { background: rgba(255,235,238,0.3) !important; }
    #mailSmsTable tbody tr:nth-child(odd)  { background: #fff !important; }

    /* ── Action buttons (mirrors student page) ── */
    .action-btns { white-space: nowrap !important; text-align: center; }
    .action-btns .btn {
        margin: 2px !important;
        border-radius: 4px !important;
        font-size: 11px !important;
        padding: 4px 8px !important;
        transition: all 0.3s ease !important;
    }
    .action-btns .btn:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
    }

    /* ── Status badges ── */
    .label { font-size: 11px; padding: 4px 7px; border-radius: 3px; }

    /* ── DataTables top bar ── */
    .dt-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:6px; }
    .dt-top-left { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .dt-top-right .dt-buttons { display:inline-flex; gap:4px; }
    .dt-top-right .dt-buttons .btn { font-size:12px; }

    input[type="checkbox"] { width:16px !important; height:16px !important; accent-color:#0cc035 !important; cursor:pointer !important; }
</style>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-mailandsms"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_mailandsms')?></li>
        </ol>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php if(permissionChecker('mailandsms_add')) { ?>
                <div class="sms-top-bar">
                    <a href="<?=base_url('mailandsms/add')?>" class="sbar-btn sbar-btn-add">
                        <i class="fa fa-plus"></i> Add SMS / Whatsapp
                    </a>
                    <a href="<?=base_url('mailandsms/errors')?>" class="sbar-btn sbar-btn-errors">
                        <i class="fa fa-exclamation-circle"></i> SMS Error Logs
                    </a>
                    <a href="<?=base_url('mailandsms/whatsapp_logs_list')?>" class="sbar-btn sbar-btn-wa">
                        <i class="fa fa-whatsapp"></i> Whatsapp Logs
                    </a>

                    <button id="deleteSelected" class="sbar-btn sbar-btn-delete sbar-disabled" disabled style="margin-left:auto;">
                        <i class="fa fa-trash"></i> Delete Selected
                    </button>
                </div>
                <?php } ?>

                <div id="hide-table">

                    <!-- Status filter buttons -->
                    <div style="margin-bottom:12px;">
                        <div class="status-filters btn-group">
                            <button class="btn btn-default btn-sm filter-btn active" data-filter="all">All</button>
                            <button class="btn btn-success btn-sm filter-btn" data-filter="delivered">
                                <i class="fa fa-check-circle"></i> Delivered
                            </button>
                            <button class="btn btn-danger btn-sm filter-btn" data-filter="failed">
                                <i class="fa fa-times-circle"></i> Failed
                            </button>
                            <button class="btn btn-warning btn-sm filter-btn" data-filter="pending">
                                <i class="fa fa-clock-o"></i> Pending
                            </button>
                            <button class="btn btn-default btn-sm filter-btn" data-filter="n/a">N/A</button>
                        </div>
                    </div>

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
                                <th>Status</th>
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
                                    <td class="sms-status-cell" data-campid="<?=htmlspecialchars($mailandsms->campid)?>">
                                        <?php if($mailandsms->campid): ?>
                                            <span class="label label-default"><i class="fa fa-spinner fa-spin"></i></span>
                                        <?php else: ?>
                                            <span class="label label-default">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if(permissionChecker('mailandsms_view')) { ?>
                                    <td class="action-btns">
                                        <?php echo btn_view('mailandsms/view/'.$mailandsms->mailandsmsID.'/'.$mailandsms->campid, $this->lang->line('view')) ?>
                                        <button class="btn btn-danger btn-xs mrg deleteSingle"
                                                data-id="<?=$mailandsms->mailandsmsID?>"
                                                data-toggle="tooltip" data-placement="top" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                    <?php } ?>
                                </tr>
                            <?php $i++; } } ?>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {

    var activeFilter = 'all';
    var drawTimeout;

    function getStatusBadge(s) {
        var lower = s.toLowerCase();
        var cls, icon;
        if (lower === 'delivered') {
            cls = 'label-success'; icon = 'fa-check-circle';
        } else if (lower.indexOf('fail') !== -1 || lower.indexOf('reject') !== -1 || lower.indexOf('undeliv') !== -1) {
            cls = 'label-danger';  icon = 'fa-times-circle';
        } else if (lower.indexOf('pend') !== -1 || lower.indexOf('queue') !== -1 || lower.indexOf('send') !== -1) {
            cls = 'label-warning'; icon = 'fa-clock-o';
        } else if (lower === 'n/a' || lower === '' || lower === '-') {
            cls = 'label-default'; icon = 'fa-minus';
        } else {
            cls = 'label-info';    icon = 'fa-info-circle';
        }
        return '<span class="label ' + cls + '"><i class="fa ' + icon + '"></i> ' + s + '</span>';
    }

    function matchesFilter(status) {
        var s = (status || '').toLowerCase();
        if (activeFilter === 'all')     return true;
        if (activeFilter === 'delivered') return s === 'delivered';
        if (activeFilter === 'failed')    return s.indexOf('fail') !== -1 || s.indexOf('reject') !== -1 || s.indexOf('undeliv') !== -1;
        if (activeFilter === 'pending')   return s.indexOf('pend') !== -1 || s.indexOf('queue') !== -1 || s.indexOf('send') !== -1;
        if (activeFilter === 'n/a')       return s === 'n/a' || s === '' || s === '-';
        return true;
    }

    // DataTables custom search for status filter
    $.fn.dataTable.ext.search.push(function (settings, data, rowIndex) {
        if (settings.nTable.id !== 'mailSmsTable') return true;
        if (activeFilter === 'all') return true;
        var rowData = settings.aoData[rowIndex];
        if (!rowData || !rowData.nTr) return true;
        var status = $(rowData.nTr).data('status') || '';
        return matchesFilter(status);
    });

    // Initialize DataTable
    var table = $('#mailSmsTable').DataTable({
        pageLength : 50,
        lengthMenu : [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        dom        : '<"dt-top"<"dt-top-left"lf><"dt-top-right"B>>rtip',
        buttons    : [
            { extend: 'copyHtml5',  text: '<i class="fa fa-clone"></i> Copy',            className: 'btn btn-default btn-xs' },
            { extend: 'excelHtml5', text: '<i class="fa fa-file-excel-o"></i> Excel',    className: 'btn btn-default btn-xs' },
            { extend: 'csvHtml5',   text: '<i class="fa fa-file-text-o"></i> CSV',       className: 'btn btn-default btn-xs' },
            { extend: 'pdfHtml5',   text: '<i class="fa fa-file-pdf-o"></i> PDF',        className: 'btn btn-default btn-xs' }
        ],
        columnDefs: [{ orderable: false, targets: [0] }]
    });

    // Mark rows with no campid as n/a
    $('#mailSmsTable tbody tr').each(function () {
        var campid = $(this).find('.sms-status-cell').data('campid');
        if (!campid) $(this).data('status', 'n/a');
    });

    // Filter button clicks
    $('.filter-btn').on('click', function () {
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        table.draw();
    });

    // AJAX status load with debounced redraw
    $('.sms-status-cell[data-campid!=""]').each(function () {
        var $cell  = $(this);
        var $row   = $cell.closest('tr');
        var campid = $cell.data('campid');
        if (!campid) return;
        $.ajax({
            url      : '<?=base_url('mailandsms/get_campid_status')?>',
            type     : 'POST',
            data     : { campid: campid },
            dataType : 'json',
            success  : function (res) {
                var s = res.status || 'N/A';
                $row.data('status', s.toLowerCase());
                $cell.html(getStatusBadge(s));
                clearTimeout(drawTimeout);
                drawTimeout = setTimeout(function () { table.draw(false); }, 150);
            },
            error : function () {
                $row.data('status', 'n/a');
                $cell.html('<span class="label label-default"><i class="fa fa-minus"></i> N/A</span>');
            }
        });
    });

    // Delete Selected: enable/disable based on checkbox
    function syncDeleteBtn() {
        var any = $('.recordCheckbox:checked').length > 0;
        if (any) {
            $('#deleteSelected').prop('disabled', false).removeClass('sbar-disabled');
        } else {
            $('#deleteSelected').prop('disabled', true).addClass('sbar-disabled');
        }
    }

    $('#selectAll').on('change', function () {
        $('.recordCheckbox').prop('checked', $(this).prop('checked'));
        syncDeleteBtn();
    });

    $(document).on('change', '.recordCheckbox', function () {
        if (!$(this).prop('checked')) $('#selectAll').prop('checked', false);
        syncDeleteBtn();
    });

    // Single delete
    $(document).on('click', '.deleteSingle', function () {
        var id = $(this).data('id');
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url      : '<?=base_url('mailandsms/delete_mailandsms')?>',
                type     : 'POST',
                data     : { id: id },
                dataType : 'json',
                success  : function (res) {
                    if (res.status) table.row('#row_' + id).remove().draw(false);
                    alert(res.message);
                }
            });
        }
    });

    // Bulk delete
    $('#deleteSelected').on('click', function () {
        var selected = [];
        $('.recordCheckbox:checked').each(function () { selected.push($(this).val()); });
        if (!selected.length) { alert('Please select at least one record.'); return; }
        if (confirm('Are you sure you want to delete selected records?')) {
            $.ajax({
                url      : '<?=base_url('mailandsms/delete_mailandsms_bulk')?>',
                type     : 'POST',
                data     : { ids: selected },
                dataType : 'json',
                success  : function (res) {
                    if (res.status) {
                        selected.forEach(function (id) { table.row('#row_' + id).remove(); });
                        table.draw(false);
                    }
                    alert(res.message);
                    syncDeleteBtn();
                }
            });
        }
    });

    // Tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
