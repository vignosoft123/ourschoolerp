<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-bookmark"></i> Bookmarks</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="page-header">
                    <a href="javascript:void(0);" class="ose-btn create-btn" onclick="openBookmarkModal()">
                        <i class="fa fa-plus"></i> Add Bookmark
                    </a>
                </h5>

                <div id="hide-table">
                    <table id="bookmarksTable" class="table table-striped table-bordered table-hover dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-1">#</th>
                                <th class="col-sm-3">Category</th>
                                <th class="col-sm-3">Name</th>
                                <th class="col-sm-3">URL</th>
                                <th class="col-sm-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (customCompute($bookmarks)) { $i = 1; foreach ($bookmarks as $bookmark) { ?>
                                <tr>
                                    <td data-title="#"><?php echo $i; ?></td>
                                    <td data-title="Category"><?php echo htmlspecialchars($bookmark->category); ?></td>
                                    <td data-title="Name"><?php echo htmlspecialchars($bookmark->name); ?></td>
                                    <td data-title="URL">
                                        <a href="<?php echo htmlspecialchars($bookmark->url); ?>" target="_blank" rel="noopener">
                                            <?php echo htmlspecialchars($bookmark->url); ?>
                                        </a>
                                    </td>
                                    <td data-title="Action">
                                        <a href="javascript:void(0);" class="edit-bookmark-btn btn btn-warning btn-xs"
                                            data-id="<?php echo $bookmark->id; ?>"
                                            data-category="<?php echo htmlspecialchars($bookmark->category); ?>"
                                            data-name="<?php echo htmlspecialchars($bookmark->name); ?>"
                                            data-url="<?php echo htmlspecialchars($bookmark->url); ?>"
                                            title="Edit"><i class="fa fa-edit"></i></a>
                                        <a href="javascript:void(0);" class="delete-bookmark-btn btn btn-danger btn-xs"
                                            data-id="<?php echo $bookmark->id; ?>"
                                            title="Delete"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php $i++; } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function() {
    $('body').append($('#bookmarkModal').detach());
});

function openBookmarkModal() {
    $('#bookmarkForm')[0].reset();
    $('#bookmarkID').val('');
    $('#bookmarkModalTitle').html('<i class="fa fa-plus"></i> Add Bookmark');
    $('.form-group').removeClass('has-error');
    $('.text-red').text('');
    $('#bookmarkModal').modal('show');
}

$(document).on('click', '.edit-bookmark-btn', function() {
    $('#bookmarkForm')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.text-red').text('');
    $('#bookmarkID').val($(this).data('id'));
    $('#bm_category').val($(this).data('category'));
    $('#bm_name').val($(this).data('name'));
    $('#bm_url').val($(this).data('url'));
    $('#bookmarkModalTitle').html('<i class="fa fa-pencil"></i> Edit Bookmark');
    $('#bookmarkModal').modal('show');
});

$(document).on('click', '#saveBookmarkBtn', function() {
    var id = $('#bookmarkID').val();
    var url = id ? "<?=base_url('workspace/ajax_update')?>" : "<?=base_url('workspace/ajax_add')?>";
    var $btn = $(this);
    $btn.prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: url,
        data: $('#bookmarkForm').serialize(),
        dataType: 'json',
        success: function(data) {
            if (data.status) {
                $('#bookmarkModal').modal('hide');
                toastr.success(data.message || 'Success');
                setTimeout(function() { location.reload(); }, 600);
            } else {
                $('.form-group').removeClass('has-error');
                $('.text-red').text('');
                if (data.category) { $('#categoryDiv').addClass('has-error'); $('#category_error').text(data.category); }
                if (data.name) { $('#nameDiv').addClass('has-error'); $('#name_error').text(data.name); }
                if (data.url) { $('#urlDiv').addClass('has-error'); $('#url_error').text(data.url); }
                if (data.message) { $('#url_error').text(data.message); }
            }
        },
        error: function() {
            toastr.error('Request failed. Please try again.');
        },
        complete: function() {
            $btn.prop('disabled', false);
        }
    });
});

$(document).on('click', '.delete-bookmark-btn', function() {
    if (!confirm('Delete this bookmark?')) return;
    var id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: "<?=base_url('workspace/ajax_delete')?>",
        data: { id: id },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                toastr.success('Bookmark deleted.');
                setTimeout(function() { location.reload(); }, 600);
            } else {
                toastr.error(data.message || 'Failed to delete bookmark.');
            }
        },
        error: function() {
            toastr.error('Request failed. Please try again.');
        }
    });
});
</script>

<!-- Modal at the END of the view, outside all tables/containers (AdminLTE gotcha) -->
<div class="modal fade" id="bookmarkModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="max-width:380px;margin-top:120px;">
    <div class="modal-content" style="border-radius:10px;overflow:hidden;">
      <div class="modal-header" style="background:#17a2b8;color:#fff;padding:14px 20px;">
        <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;font-size:20px;"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="bookmarkModalTitle" style="font-size:15px;font-weight:700;"><i class="fa fa-plus"></i> Add Bookmark</h4>
      </div>
      <form id="bookmarkForm">
        <div class="modal-body" style="padding:20px 24px;">
            <input type="hidden" name="id" id="bookmarkID">
            <div class="form-group" id="categoryDiv" style="margin-bottom:14px;">
                <label style="font-size:13px;font-weight:600;color:#333;">Category <span class="text-red">*</span></label>
                <input type="text" class="form-control" style="border-radius:6px;" id="bm_category" name="category" placeholder="e.g. Reports, Tools">
                <span class="text-red" id="category_error" style="font-size:12px;"></span>
            </div>
            <div class="form-group" id="nameDiv" style="margin-bottom:14px;">
                <label style="font-size:13px;font-weight:600;color:#333;">Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" style="border-radius:6px;" id="bm_name" name="name" placeholder="Bookmark name">
                <span class="text-red" id="name_error" style="font-size:12px;"></span>
            </div>
            <div class="form-group" id="urlDiv" style="margin-bottom:6px;">
                <label style="font-size:13px;font-weight:600;color:#333;">URL <span class="text-red">*</span></label>
                <input type="text" class="form-control" style="border-radius:6px;" id="bm_url" name="url" placeholder="https://example.com">
                <span class="text-red" id="url_error" style="font-size:12px;"></span>
            </div>
        </div>
        <div class="modal-footer" style="background:#f8f9fa;padding:12px 20px;">
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-info btn-sm" id="saveBookmarkBtn"><i class="fa fa-save"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
