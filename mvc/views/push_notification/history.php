<section class="content-header">
    <h1>Push Notification History</h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= base_url('Push_notification') ?>">Push Notification</a></li>
        <li class="active">History</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-history"></i> Sent Notifications (Last 100)</h3>
                    <div class="box-tools pull-right">
                        <a href="<?= base_url('Push_notification') ?>" class="btn btn-sm btn-primary">
                            <i class="fa fa-paper-plane"></i> Send New
                        </a>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <?php if (empty($logs)): ?>
                    <div class="text-center" style="padding: 40px 0; color: #999;">
                        <i class="fa fa-bell-slash-o fa-3x" style="margin-bottom:12px;"></i>
                        <p>No notifications have been sent yet.</p>
                        <a href="<?= base_url('Push_notification') ?>" class="btn btn-primary">Send First Notification</a>
                    </div>
                    <?php else: ?>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sent At</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Recipients</th>
                                <th>Sent To</th>
                                <th style="color:green;">Success</th>
                                <th style="color:red;">Failed</th>
                                <th>Sent By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $i => $log): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td style="white-space:nowrap;"><?= date('d M Y H:i', strtotime($log->sent_at)) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($log->title) ?></strong>
                                    <div class="text-muted small" style="margin-top:3px; max-width:260px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= htmlspecialchars($log->message) ?>">
                                        <?= htmlspecialchars($log->message) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $typeLabels = [
                                        'general'      => ['General',       'default'],
                                        'exam_alert'   => ['Exam Alert',    'warning'],
                                        'fee_reminder' => ['Fee Reminder',  'danger'],
                                        'holiday'      => ['Holiday',       'info'],
                                        'custom'       => ['Custom',        'primary'],
                                    ];
                                    $tl = $typeLabels[$log->notification_type] ?? [$log->notification_type, 'default'];
                                    ?>
                                    <span class="label label-<?= $tl[1] ?>"><?= $tl[0] ?></span>
                                </td>
                                <td>
                                    <?php if ($log->recipient_type === 'all'): ?>
                                        <span class="label label-success">All Students</span>
                                    <?php elseif ($log->recipient_type === 'class'): ?>
                                        <span class="label label-info"><?= htmlspecialchars($log->class_name ?: 'Class') ?></span>
                                    <?php else: ?>
                                        <span class="label label-primary"><?= htmlspecialchars($log->class_name ?: '') ?> — <?= htmlspecialchars($log->section_name ?: '') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><strong><?= $log->total_recipients ?></strong></td>
                                <td class="text-center"><span class="text-green"><strong><?= $log->success_count ?></strong></span></td>
                                <td class="text-center">
                                    <?php if ($log->failure_count > 0): ?>
                                    <span class="text-red"><strong><?= $log->failure_count ?></strong></span>
                                    <?php else: ?>
                                    <span class="text-muted">0</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($log->sent_by_name ?: '—') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
