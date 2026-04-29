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

        <?php if ($this->session->flashdata('success')) { ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
        <?php } ?>
        <?php if ($this->session->flashdata('error')) { ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php } ?>

        <table class="table table-bordered table-hover" id="youtube_table">
            <thead>
                <tr>
                    <th width="40">#</th>
                    <th width="100"><?php echo $this->lang->line('youtube_thumbnail'); ?></th>
                    <th><?php echo $this->lang->line('youtube_title_label'); ?></th>
                    <th><?php echo $this->lang->line('youtube_class'); ?></th>
                    <th><?php echo $this->lang->line('youtube_section'); ?></th>
                    <th><?php echo $this->lang->line('youtube_subject'); ?></th>
                    <th width="70"><?php echo $this->lang->line('youtube_views'); ?></th>
                    <th width="80"><?php echo $this->lang->line('youtube_status'); ?></th>
                    <th width="120"><?php echo $this->lang->line('youtube_action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (customCompute($youtube_links)) { $sl = 1; foreach ($youtube_links as $row) { ?>
                <tr>
                    <td><?php echo $sl++; ?></td>
                    <td>
                        <?php if ($row->thumbnail) { ?>
                            <a href="<?php echo $row->link; ?>" target="_blank">
                                <img src="<?php echo $row->thumbnail; ?>" alt="thumbnail"
                                     style="width:90px;height:52px;object-fit:cover;border-radius:4px;">
                            </a>
                        <?php } else { ?>
                            <span class="text-muted">—</span>
                        <?php } ?>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($row->title ?: '—'); ?></strong>
                        <?php if ($row->description) { ?>
                            <br><small class="text-muted"><?php echo htmlspecialchars(substr(strip_tags($row->description), 0, 60)); ?>...</small>
                        <?php } ?>
                    </td>
                    <td><?php echo $row->class_name ?: '—'; ?></td>
                    <td><?php echo $row->section_name ?: '—'; ?></td>
                    <td><?php echo $row->subject_name ?: '—'; ?></td>
                    <td class="text-center"><?php echo $row->view_count; ?></td>
                    <td class="text-center">
                        <?php if ($row->status == 1) { ?>
                            <span class="label label-success">Active</span>
                        <?php } else { ?>
                            <span class="label label-default">Inactive</span>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo btn_edit(base_url('youtube/edit/' . $row->id), $this->lang->line('youtube_edit_btn')); ?>
                        <?php echo btn_delete(base_url('youtube/delete/' . $row->id), $this->lang->line('youtube_delete_btn')); ?>
                    </td>
                </tr>
                <?php } } else { ?>
                <tr>
                    <td colspan="9" class="text-center text-muted">No YouTube links found.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>

<script>
$(function() {
    $('#youtube_table').DataTable({ "order": [] });
});
</script>
