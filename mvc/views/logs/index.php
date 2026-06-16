<style>
.log-filters { background:#f8f9fa; border:1px solid #e0e0e0; border-radius:6px; padding:14px 16px; margin-bottom:18px; }
.log-filters .form-group { margin-bottom:0; }
.log-badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700; text-transform:uppercase; }
.log-badge.create    { background:#e8f5e9; color:#2e7d32; }
.log-badge.update    { background:#e3f2fd; color:#1565c0; }
.log-badge.delete    { background:#ffebee; color:#c62828; }
.log-badge.deactivate{ background:#fff3e0; color:#e65100; }
.log-badge.view      { background:#f3e5f5; color:#6a1b9a; }
.log-badge.default   { background:#eceff1; color:#546e7a; }
.log-val { font-size:11px; font-family:monospace; color:#555; max-width:220px; word-break:break-all; white-space:pre-wrap; }
</style>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-history"></i> Activity Logs</h3>
        <div class="box-tools pull-right">
            <span class="badge badge-primary"><?= number_format($total) ?> total</span>
        </div>
    </div>
    <div class="box-body">

        <!-- Filters -->
        <form method="GET" action="<?= base_url('logs') ?>" class="log-filters">
            <div class="row">
                <div class="col-sm-2">
                    <select name="module" class="form-control input-sm">
                        <option value="">All Modules</option>
                        <?php
                        $modules = ['delete_account_request','student','teacher','user','fee','exam','attendance'];
                        foreach ($modules as $m):
                        ?>
                        <option value="<?= $m ?>" <?= $filters['module']==$m?'selected':'' ?>><?= ucwords(str_replace('_',' ',$m)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select name="action" class="form-control input-sm">
                        <option value="">All Actions</option>
                        <?php foreach (['create','update','delete','deactivate','view'] as $a): ?>
                        <option value="<?= $a ?>" <?= $filters['action']==$a?'selected':'' ?>><?= ucfirst($a) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select name="record_type" class="form-control input-sm">
                        <option value="">All Types</option>
                        <?php foreach (['student','teacher','user'] as $rt): ?>
                        <option value="<?= $rt ?>" <?= $filters['record_type']==$rt?'selected':'' ?>><?= ucfirst($rt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <input type="date" name="date_from" class="form-control input-sm" placeholder="From date" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
                </div>
                <div class="col-sm-2">
                    <input type="date" name="date_to" class="form-control input-sm" placeholder="To date" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
                </div>
                <div class="col-sm-2">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                        <span class="input-group-btn">
                            <button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-search"></i></button>
                            <a href="<?= base_url('logs') ?>" class="btn btn-default btn-sm"><i class="fa fa-times"></i></a>
                        </span>
                    </div>
                </div>
            </div>
        </form>

        <?php if (empty($logs)): ?>
        <div class="text-center" style="padding:50px 0;color:#aaa;">
            <i class="fa fa-history fa-3x" style="display:block;margin-bottom:12px;"></i>
            No activity logs found.
        </div>
        <?php else: ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" style="font-size:13px;">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Record</th>
                        <th>Description</th>
                        <th>Previous</th>
                        <th>New</th>
                        <th>Performed By</th>
                        <th>IP</th>
                        <th>Date &amp; Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php $n = ($page - 1) * $limit; foreach ($logs as $log): $n++; ?>
                <tr>
                    <td><?= $n ?></td>
                    <td><span style="font-size:12px;font-weight:600;"><?= htmlspecialchars(str_replace('_',' ', $log->module)) ?></span></td>
                    <td>
                        <?php $act = strtolower($log->action); ?>
                        <span class="log-badge <?= in_array($act,['create','update','delete','deactivate','view'])?$act:'default' ?>">
                            <?= htmlspecialchars($log->action) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($log->record_type): ?>
                        <div><strong><?= htmlspecialchars(ucfirst($log->record_type)) ?></strong></div>
                        <div style="color:#888;font-size:11px;">ID: <?= (int)$log->record_id ?></div>
                        <?php else: ?>
                        <span style="color:#ccc;">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $log->description ? htmlspecialchars($log->description) : '<span style="color:#ccc;">—</span>' ?></td>
                    <td>
                        <?php if ($log->old_value): ?>
                        <div class="log-val"><?= htmlspecialchars($log->old_value) ?></div>
                        <?php else: ?><span style="color:#ccc;">—</span><?php endif; ?>
                    </td>
                    <td>
                        <?php if ($log->new_value): ?>
                        <div class="log-val"><?= htmlspecialchars($log->new_value) ?></div>
                        <?php else: ?><span style="color:#ccc;">—</span><?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:600;"><?= htmlspecialchars($log->performed_by_name ?? '—') ?></div>
                        <div style="color:#888;font-size:11px;"><?= htmlspecialchars($log->performed_by_usertype_name ?? '') ?></div>
                    </td>
                    <td style="font-size:11px;color:#666;"><?= htmlspecialchars($log->ip_address ?? '—') ?></td>
                    <td style="white-space:nowrap;font-size:12px;">
                        <?= date('d M Y', strtotime($log->created_at)) ?><br>
                        <span style="color:#888;"><?= date('h:i A', strtotime($log->created_at)) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($last_page > 1): ?>
        <div class="text-center" style="margin-top:16px;">
            <ul class="pagination pagination-sm">
                <?php if ($page > 1): ?>
                <li><a href="<?= base_url('logs') ?>?page=<?= $page-1 ?>&<?= http_build_query(array_filter($filters)) ?>">«</a></li>
                <?php endif; ?>
                <?php for ($p = max(1,$page-3); $p <= min($last_page,$page+3); $p++): ?>
                <li class="<?= $p==$page?'active':'' ?>">
                    <a href="<?= base_url('logs') ?>?page=<?= $p ?>&<?= http_build_query(array_filter($filters)) ?>"><?= $p ?></a>
                </li>
                <?php endfor; ?>
                <?php if ($page < $last_page): ?>
                <li><a href="<?= base_url('logs') ?>?page=<?= $page+1 ?>&<?= http_build_query(array_filter($filters)) ?>">»</a></li>
                <?php endif; ?>
            </ul>
            <p style="color:#888;font-size:12px;">Showing page <?= $page ?> of <?= $last_page ?> (<?= number_format($total) ?> records)</p>
        </div>
        <?php endif; ?>

        <?php endif; ?>

    </div>
</div>
