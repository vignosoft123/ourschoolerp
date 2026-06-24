<?php
// Group links by class_id for tabs
$grouped = [];
if (customCompute($youtube_links)) {
    foreach ($youtube_links as $row) {
        $key = $row->class_id ? $row->class_id : 0;
        $grouped[$key]['label']  = $row->class_name ? $row->class_name : 'All Classes';
        $grouped[$key]['rows'][] = $row;
    }
}
?>
<div class="box">
    <div class="box-header">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="box-title">
                    <i class="fa fa-youtube-play text-danger"></i>
                    <?php echo $this->lang->line('youtube_title'); ?>
                </h3>
            </div>
            <div class="col-sm-6 text-right">
                <a href="<?php echo base_url('youtube/add'); ?>" class="btn btn-success btn-sm">
                    <i class="fa fa-plus"></i> <?php echo $this->lang->line('youtube_add'); ?>
                </a>
            </div>
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
                    <a data-toggle="tab" href="#yt_tab_all">All</a>
                </li>
                <?php foreach ($grouped as $classId => $group): ?>
                <li>
                    <a data-toggle="tab" href="#yt_tab_<?php echo $classId; ?>">
                        <?php echo htmlspecialchars($group['label']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="tab-content">

                <!-- All Tab -->
                <div id="yt_tab_all" class="tab-pane active">
                    <table class="table table-bordered table-hover yt-datatable">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th width="100"><?php echo $this->lang->line('youtube_thumbnail'); ?></th>
                                <th><?php echo $this->lang->line('youtube_title_label'); ?></th>
                                <th width="120"><?php echo $this->lang->line('youtube_class'); ?></th>
                                <th width="120"><?php echo $this->lang->line('youtube_section'); ?></th>
                                <th width="130"><?php echo $this->lang->line('youtube_subject'); ?></th>
                                <th width="70"><?php echo $this->lang->line('youtube_views'); ?></th>
                                <th width="80"><?php echo $this->lang->line('youtube_status'); ?></th>
                                <th width="100"><?php echo $this->lang->line('youtube_action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (customCompute($youtube_links)): $sl = 1; foreach ($youtube_links as $row): ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo $row->thumbnail ? '<a href="' . $row->link . '" target="_blank"><img src="' . $row->thumbnail . '" style="width:80px;height:46px;object-fit:cover;border-radius:4px;"></a>' : '<span class="text-muted">—</span>'; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row->title ?: '—'); ?></strong>
                                    <?php if ($row->description): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr(strip_tags($row->description), 0, 60)); ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row->class_name ?: '—'; ?></td>
                                <td><?php echo $row->section_name ?: '—'; ?></td>
                                <td><?php echo $row->subject_name ?: '—'; ?></td>
                                <td class="text-center"><?php echo $row->view_count; ?></td>
                                <td class="text-center">
                                    <span class="label <?php echo $row->status == 1 ? 'label-success' : 'label-default'; ?>">
                                        <?php echo $row->status == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo btn_edit('youtube/edit/' . $row->id, $this->lang->line('youtube_edit_btn')); ?>
                                    <?php echo btn_delete('youtube/delete/' . $row->id, $this->lang->line('youtube_delete_btn')); ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="9" class="text-center text-muted">No YouTube links found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Per-Class Tabs -->
                <?php foreach ($grouped as $classId => $group): ?>
                <div id="yt_tab_<?php echo $classId; ?>" class="tab-pane">
                    <table class="table table-bordered table-hover yt-datatable">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th width="100"><?php echo $this->lang->line('youtube_thumbnail'); ?></th>
                                <th><?php echo $this->lang->line('youtube_title_label'); ?></th>
                                <th width="120"><?php echo $this->lang->line('youtube_section'); ?></th>
                                <th width="130"><?php echo $this->lang->line('youtube_subject'); ?></th>
                                <th width="70"><?php echo $this->lang->line('youtube_views'); ?></th>
                                <th width="80"><?php echo $this->lang->line('youtube_status'); ?></th>
                                <th width="100"><?php echo $this->lang->line('youtube_action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sl = 1; foreach ($group['rows'] as $row): ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><?php echo $row->thumbnail ? '<a href="' . $row->link . '" target="_blank"><img src="' . $row->thumbnail . '" style="width:80px;height:46px;object-fit:cover;border-radius:4px;"></a>' : '<span class="text-muted">—</span>'; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row->title ?: '—'); ?></strong>
                                    <?php if ($row->description): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr(strip_tags($row->description), 0, 60)); ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $row->section_name ?: '—'; ?></td>
                                <td><?php echo $row->subject_name ?: '—'; ?></td>
                                <td class="text-center"><?php echo $row->view_count; ?></td>
                                <td class="text-center">
                                    <span class="label <?php echo $row->status == 1 ? 'label-success' : 'label-default'; ?>">
                                        <?php echo $row->status == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo btn_edit('youtube/edit/' . $row->id, $this->lang->line('youtube_edit_btn')); ?>
                                    <?php echo btn_delete('youtube/delete/' . $row->id, $this->lang->line('youtube_delete_btn')); ?>
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
        var $table = $(pane).find('.yt-datatable');
        if ($table.length && !initialised[pane]) {
            initialised[pane] = true;
            $table.DataTable({ order: [], autoWidth: false });
        } else if ($table.length) {
            $table.DataTable().columns.adjust().draw();
        }
    }

    initTable('#yt_tab_all');

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        initTable($(e.target).attr('href'));
    });
});
</script>
