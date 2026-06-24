<?php
// Group voices by class_id for tabs
$grouped = [];
if (customCompute($voices)) {
    foreach ($voices as $v) {
        $key = $v->class_id ? $v->class_id : 0;
        $grouped[$key]['label']    = $v->class_name ? $v->class_name : 'All Classes';
        $grouped[$key]['rows'][]   = $v;
    }
}
?>
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

        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#vm_tab_all">All</a>
                </li>
                <?php foreach ($grouped as $classId => $group): ?>
                <li>
                    <a data-toggle="tab" href="#vm_tab_<?php echo $classId; ?>">
                        <?php echo htmlspecialchars($group['label']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content">

                <!-- All Tab -->
                <div id="vm_tab_all" class="tab-pane active">
                    <table class="table table-bordered table-hover vm-datatable">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>Voice Name</th>
                                <th width="120">Class</th>
                                <th width="120">Section</th>
                                <th width="300">Audio Preview</th>
                                <th width="130">Created Date</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (customCompute($voices)): $sl = 1; foreach ($voices as $row): ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo htmlspecialchars($row->voice_name); ?></td>
                                <td><?php echo $row->class_name ? htmlspecialchars($row->class_name) : '<span class="text-muted">All</span>'; ?></td>
                                <td><?php echo $row->section_name ? htmlspecialchars($row->section_name) : '<span class="text-muted">All</span>'; ?></td>
                                <td>
                                    <audio controls style="width:280px;height:36px;">
                                        <source src="<?php echo base_url('uploads/voice_messages/' . $row->file_name); ?>">
                                    </audio>
                                </td>
                                <td><?php echo $row->created_at ? date('d M Y', strtotime($row->created_at)) : '&mdash;'; ?></td>
                                <td>
                                    <?php echo btn_edit('voice_messages/edit/' . $row->id, $this->lang->line('voice_edit_btn')); ?>
                                    <?php echo btn_delete('voice_messages/delete/' . $row->id, $this->lang->line('voice_delete_btn')); ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="7" class="text-center text-muted">No voice messages found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Per-Class Tabs -->
                <?php foreach ($grouped as $classId => $group): ?>
                <div id="vm_tab_<?php echo $classId; ?>" class="tab-pane">
                    <table class="table table-bordered table-hover vm-datatable">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>Voice Name</th>
                                <th width="120">Section</th>
                                <th width="300">Audio Preview</th>
                                <th width="130">Created Date</th>
                                <th width="100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sl = 1; foreach ($group['rows'] as $row): ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo htmlspecialchars($row->voice_name); ?></td>
                                <td><?php echo $row->section_name ? htmlspecialchars($row->section_name) : '<span class="text-muted">All</span>'; ?></td>
                                <td>
                                    <audio controls style="width:280px;height:36px;">
                                        <source src="<?php echo base_url('uploads/voice_messages/' . $row->file_name); ?>">
                                    </audio>
                                </td>
                                <td><?php echo $row->created_at ? date('d M Y', strtotime($row->created_at)) : '&mdash;'; ?></td>
                                <td>
                                    <?php echo btn_edit('voice_messages/edit/' . $row->id, $this->lang->line('voice_edit_btn')); ?>
                                    <?php echo btn_delete('voice_messages/delete/' . $row->id, $this->lang->line('voice_delete_btn')); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endforeach; ?>

            </div><!-- /.tab-content -->
        </div><!-- /.nav-tabs-custom -->

    </div>
</div>

<script>
$(function() {
    var initialised = {};

    function initTable(pane) {
        var $table = $(pane).find('.vm-datatable');
        if ($table.length && !initialised[pane]) {
            initialised[pane] = true;
            $table.DataTable({ order: [[3, 'desc']], pageLength: 10, autoWidth: false });
        } else if ($table.length) {
            $table.DataTable().columns.adjust().draw();
        }
    }

    // Init active tab on load
    initTable('#vm_tab_all');

    // Init each tab on first open, redraw on subsequent opens
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        initTable($(e.target).attr('href'));
    });
});
</script>
