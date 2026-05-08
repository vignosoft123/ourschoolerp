<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-university"></i> Add Bank</h3>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('dashboard/index') ?>"><i class="fa fa-laptop"></i> Dashboard</a></li>
            <li><a href="<?= base_url('banks/index') ?>">Banks</a></li>
            <li class="active">Add Bank</li>
        </ol>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-6">

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('banks/add') ?>">
                    <div class="form-group">
                        <label>Bank Name <span class="text-red">*</span></label>
                        <input type="text" name="bank_name" class="form-control" placeholder="e.g. HDFC, ICICI, Axis..." maxlength="255" required>
                    </div>
                    <div class="form-group">
                        <a href="<?= base_url('banks/index') ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-success">Save Bank</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
