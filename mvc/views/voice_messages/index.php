<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-microphone text-red"></i> <?php echo $this->lang->line('voice_messages_title'); ?></h3>
        <div class="box-tools pull-right">
            <a href="<?php echo base_url('voice_messages/add'); ?>" class="btn btn-success btn-sm">
                <i class="fa fa-plus"></i> <?php echo $this->lang->line('voice_add'); ?>
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-hover" id="vm_table">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th>Voice Name</th>
                    <th width="320">Audio Preview</th>
                    <th width="150">Created Date</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (customCompute($voices)): $sl = 1; foreach ($voices as $row): ?>
                <tr>
                    <td><?php echo $sl++; ?></td>
                    <td><?php echo htmlspecialchars($row->voice_name); ?></td>
                    <td>
                        <audio controls style="width:300px;height:36px;">
                            <source src="<?php echo base_url('uploads/voice_messages/' . $row->file_name); ?>">
                            Your browser does not support audio.
                        </audio>
                    </td>
                    <td><?php echo $row->created_at ? date('d M Y', strtotime($row->created_at)) : '&mdash;'; ?></td>
                    <td>
                        <?php echo btn_edit_show(base_url('voice_messages/edit/' . $row->id), $this->lang->line('voice_edit_btn')); ?>
                        <?php echo btn_delete_show(base_url('voice_messages/delete/' . $row->id), $this->lang->line('voice_delete_btn')); ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="5" class="text-center text-muted">No voice messages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$(function() {
    $('#vm_table').DataTable({ order: [[3, 'desc']] });
});
</script>
