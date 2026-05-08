<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-university"></i> Banks</h3>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('dashboard/index') ?>"><i class="fa fa-laptop"></i> Dashboard</a></li>
            <li class="active">Banks</li>
        </ol>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php if (permissionChecker('banks_add')): ?>
                    <h5 class="page-header">
                        <a href="<?= base_url('banks/add') ?>">
                            <i class="fa fa-plus"></i> Add Bank
                        </a>
                    </h5>
                <?php endif; ?>

                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
                <?php endif; ?>

                <table id="example1" class="table table-striped table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bank Name</th>
                            <?php if (permissionChecker('banks_edit') || permissionChecker('banks_delete')): ?>
                                <th>Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (customCompute($banks)): $i = 1; foreach ($banks as $bank): ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><?= htmlspecialchars($bank->bank_name) ?></td>
                                <?php if (permissionChecker('banks_edit') || permissionChecker('banks_delete')): ?>
                                    <td>
                                        <?php if (permissionChecker('banks_edit')): ?>
                                            <?= btn_edit('banks/edit/' . $bank->banksID, 'Edit') ?>
                                        <?php endif; ?>
                                        <?php if (permissionChecker('banks_delete')): ?>
                                            <?= btn_delete('banks/delete/' . $bank->banksID, 'Delete') ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php $i++; endforeach; endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
