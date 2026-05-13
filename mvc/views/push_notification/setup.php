<section class="content-header">
    <h1>Push Notification Setup</h1>
    <ol class="breadcrumb">
        <li><a href="<?= base_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= base_url('Push_notification') ?>">Push Notification</a></li>
        <li class="active">Setup</li>
    </ol>
</section>

<section class="content">
    <div class="row">

        <!-- Left: Status + Verification -->
        <div class="col-md-5">

            <!-- Current Service Account Status -->
            <div class="box <?= isset($service_account_info) && $service_account_info ? ($service_account_info['is_correct'] ? 'box-success' : 'box-danger') : 'box-warning' ?>">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-shield"></i> Service Account Status</h3>
                </div>
                <div class="box-body">
                    <?php if (!empty($service_account_info)): ?>
                    <table class="table table-condensed" style="margin-bottom:0;">
                        <tr>
                            <td><strong>Project ID</strong></td>
                            <td>
                                <?php if ($service_account_info['is_correct']): ?>
                                <span class="text-green"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($service_account_info['project_id']) ?></span>
                                <?php else: ?>
                                <span class="text-red"><i class="fa fa-times-circle"></i> <?= htmlspecialchars($service_account_info['project_id']) ?></span>
                                <div class="small text-red">Expected: our-school-erp-cbf37</div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Client Email</strong></td>
                            <td><small><?= htmlspecialchars($service_account_info['client_email']) ?></small></td>
                        </tr>
                        <tr>
                            <td><strong>File Size</strong></td>
                            <td><?= $service_account_info['file_size'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>File Path</strong></td>
                            <td><small class="text-muted"><?= htmlspecialchars($service_account_path) ?></small></td>
                        </tr>
                    </table>
                    <?php else: ?>
                    <div class="text-center text-red" style="padding:20px 0;">
                        <i class="fa fa-exclamation-triangle fa-2x" style="margin-bottom:8px;"></i>
                        <p><strong>Service account file not found!</strong></p>
                        <p class="text-muted small"><?= htmlspecialchars($service_account_path) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="box-footer">
                    <a href="<?= base_url('Push_notification/verify') ?>" class="btn btn-default">
                        <i class="fa fa-check-square-o"></i> Run Verification
                    </a>
                    <a href="<?= base_url('Push_notification') ?>" class="btn btn-primary pull-right">
                        <i class="fa fa-paper-plane"></i> Send Notification
                    </a>
                </div>
            </div>

            <!-- Verification Results (shown after /verify) -->
            <?php if (isset($verify_checks) && !empty($verify_checks)): ?>
            <div class="box <?= $verify_passed ? 'box-success' : 'box-danger' ?>">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <?php if ($verify_passed): ?>
                        <i class="fa fa-check-circle text-green"></i> All Checks Passed
                        <?php else: ?>
                        <i class="fa fa-times-circle text-red"></i> Verification Failed
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="box-body" style="padding:0;">
                    <table class="table table-condensed" style="margin-bottom:0;">
                        <?php foreach ($verify_checks as $check): ?>
                        <tr>
                            <td style="width:24px; padding-left:15px;">
                                <?php if ($check['ok']): ?>
                                <i class="fa fa-check-circle text-green"></i>
                                <?php else: ?>
                                <i class="fa fa-times-circle text-red"></i>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($check['label']) ?></strong></td>
                            <td class="text-muted small"><?= htmlspecialchars($check['detail']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Instructions -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-info-circle"></i> How to Get the Correct File</h3>
                </div>
                <div class="box-body">
                    <ol style="padding-left:18px; line-height:2.2;">
                        <li>Open <a href="https://console.firebase.google.com/project/our-school-erp-cbf37/settings/serviceaccounts/adminsdk" target="_blank">Firebase Console → Service Accounts (our-school-erp-cbf37) <i class="fa fa-external-link"></i></a></li>
                        <li>Click <strong>"Generate new private key"</strong></li>
                        <li>Open the downloaded JSON in a text editor</li>
                        <li>Copy <strong>all</strong> the content (Ctrl+A, Ctrl+C)</li>
                        <li>Paste it into the form on the right and submit</li>
                    </ol>
                </div>
            </div>

        </div>

        <!-- Right: Upload Form -->
        <div class="col-md-7">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-upload"></i> Update Service Account</h3>
                </div>
                <form method="POST" action="<?= base_url('Push_notification/setup') ?>">
                    <div class="box-body">

                        <?php $error = $this->session->flashdata('error'); if ($error): ?>
                        <div class="alert alert-danger"><i class="fa fa-times-circle"></i> <?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php $success = $this->session->flashdata('success'); if ($success): ?>
                        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Paste Firebase Service Account JSON <span class="text-danger">*</span></label>
                            <textarea name="service_account_json" class="form-control" rows="18" style="font-family:monospace; font-size:12px; resize:vertical;"
                                placeholder='{
  "type": "service_account",
  "project_id": "our-school-erp-cbf37",
  "private_key_id": "abc123...",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
  "client_email": "firebase-adminsdk-xxxxx@our-school-erp-cbf37.iam.gserviceaccount.com",
  "client_id": "123456789...",
  ...
}'></textarea>
                            <span class="help-block">Make sure the <strong>project_id</strong> is <code>our-school-erp-cbf37</code>. A backup of the current file is created automatically before replacing it.</span>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-save"></i> Update Service Account
                        </button>
                        <a href="<?= base_url('Push_notification/verify') ?>" class="btn btn-default btn-lg" style="margin-left:8px;">
                            <i class="fa fa-check-square-o"></i> Verify After Update
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>
