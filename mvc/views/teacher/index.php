<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-teacher"></i> <?=$this->lang->line('panel_title')?></h3>

       
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_teacher')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <?php 
                    if(permissionChecker('teacher_add')){
                ?>
                <h5 class="page-header btn-center">
                    <a class="ose-btn create-btn" href="<?php echo base_url('teacher/add') ?>"><i class="fa fa-plus"></i> 
                    <?=$this->lang->line('add_title')?></a>
                </h5>

                <?php } ?>

                <div id="hide-table">
                    <table id="example1" class="table table-bordered tableBorder dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-sm-1"><?=$this->lang->line('slno')?></th>
                                <!-- <th class="col-sm-1"><?=$this->lang->line('teacher_photo')?></th> -->
                                <th class="col-sm-1">Signature</th>
                                <th class="col-sm-2"><?=$this->lang->line('teacher_name')?></th>
                                <th class="col-sm-2"><?="Designation"?></th>
                                <th class="col-sm-2"><?=$this->lang->line('teacher_email')?></th>
                                <th class="col-sm-2"><?=$this->lang->line('teacher_phone')?></th>
                                <th class="col-sm-2">Default Login Time</th>
                                <th class="col-sm-2">Default Logout Time</th>
                                <?php if(permissionChecker('teacher_edit')){ ?>
                                <th class="col-sm-1"><?=$this->lang->line('teacher_status')?></th>
                                <?php } ?>
                                <?php if(permissionChecker('teacher_edit') || permissionChecker('teacher_delete') || permissionChecker('teacher_view')) { ?>
                                <th class="col-sm-2"><?=$this->lang->line('action')?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($teachers)) {$i = 1; foreach($teachers as $teacher) { ?>
                                <tr>
                                    <td data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td class="teacher-photo-cell" style="cursor:pointer;" onclick="setTeacherID(<?=$teacher->teacherID?>);" data-toggle="modal" data-target="#teacherPhotoUploadModal" data-title="<?=$this->lang->line('teacher_photo')?>">
                                        <?=profileimage($teacher->photo)?>
                                        <span class="photo-zoom-icon" data-img="<?=base_url('uploads/images/').($teacher->photo ? $teacher->photo : 'default.png')?>" title="Preview" onclick="event.stopPropagation();document.getElementById('teacherZoomImg').src=this.getAttribute('data-img');$('#teacherPhotoZoomModal').modal('show');">
                                            <i class="fa fa-search-plus" aria-hidden="true"></i>
                                        </span>
                                    </td>
                                    <!-- <td data-title="<?=$this->lang->line('teacher_photo')?>">
                                          <?=signatureimage($teacher->signature)?>
                                    </td> -->
                                    <td data-title="<?=$this->lang->line('teacher_name')?>">
                                        <?php echo $teacher->name; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('teacher_name')?>">
                                        <?php echo $teacher->designation; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('teacher_email')?>">
                                        <?php echo $teacher->email; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('teacher_phone')?>">
                                        <?php echo $teacher->phone; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('teacher_phone')?>">
                                        <?php echo $teacher->default_login_time; ?>
                                    </td>
                                      <td data-title="<?=$this->lang->line('teacher_phone')?>">
                                        <?php echo $teacher->default_logout_time; ?>
                                    </td>
                                    <?php if(permissionChecker('teacher_edit')){ ?>
                                    <td data-title="<?=$this->lang->line('teacher_status')?>">
                                      <div class="onoffswitch-small" id="<?=$teacher->teacherID?>">
                                          <input type="checkbox" id="myonoffswitch<?=$teacher->teacherID?>" class="onoffswitch-small-checkbox" name="paypal_demo" <?php if($teacher->active === '1') echo "checked='checked'"; ?>>
                                          <label for="myonoffswitch<?=$teacher->teacherID?>" class="onoffswitch-small-label">
                                              <span class="onoffswitch-small-inner"></span>
                                              <span class="onoffswitch-small-switch"></span>
                                          </label>
                                      </div>           
                                    </td>
                                    <?php } ?>
                                    <?php if(permissionChecker('teacher_edit') || permissionChecker('teacher_delete') || permissionChecker('teacher_view')) { ?>
                                    <td class="action-btns" data-title="<?=$this->lang->line('action')?>">
                                        <?php
                                            echo btn_view('teacher/view/'.$teacher->teacherID, $this->lang->line('view'));
                                            echo btn_edit('teacher/edit/'.$teacher->teacherID, $this->lang->line('edit'));
                                            echo btn_delete('teacher/delete/'.$teacher->teacherID, $this->lang->line('delete'));
                                        ?>
                                    </td>
                                    <?php } ?>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>

            </div> <!-- col-sm-12 -->
        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

<!-- Teacher Photo Upload Modal -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
<div class="modal fade" id="teacherPhotoUploadModal" tabindex="-1" aria-labelledby="teacherPhotoUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;border:none;box-shadow:0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;border-radius:15px 15px 0 0;border-bottom:none;padding:20px 25px;">
                <h5 class="modal-title" style="font-weight:600;font-size:20px;"><i class="fa fa-upload"></i> Upload Teacher Photo</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:0.9;font-size:28px;font-weight:300;text-shadow:none;"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="padding:30px 25px;background:#f8f9fa;">
                <form id="teacherPhotoUploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Choose a photo to upload</label>
                        <input class="form-control" type="file" id="teacherFormFile" name="file" accept="image/*">
                        <input type="hidden" id="teacher_id_upload" name="teacherID" value="">
                    </div>
                    <div class="mb-3" id="teacherImagePreviewContainer" style="display:none;text-align:center;">
                        <img id="teacherImagePreview" style="max-width:100%;max-height:300px;" />
                    </div>
                    <div class="mb-3" id="teacherCropBtnContainer" style="display:none;text-align:center;">
                        <button type="button" class="btn btn-secondary" id="teacherCropImageBtn">Crop &amp; Compress</button>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Teacher Photo Zoom Modal -->
<div class="modal fade" id="teacherPhotoZoomModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius:20px;border:none;box-shadow:0 15px 50px rgba(0,0,0,0.3);background:transparent;">
            <div class="modal-body text-center" style="padding:0;border-radius:20px;background:#000;position:relative;">
                <button type="button" class="close" data-dismiss="modal" style="position:absolute;top:10px;right:15px;z-index:10;color:white;opacity:1;font-size:32px;font-weight:300;"><span>&times;</span></button>
                <img id="teacherZoomImg" src="" style="max-width:100%;max-height:85vh;border-radius:20px;object-fit:contain;" />
            </div>
        </div>
    </div>
</div>

<style>
    .teacher-photo-cell img { vertical-align:middle; }
    .photo-zoom-icon { margin-left:6px;color:#337ab7;cursor:pointer;display:inline-block;vertical-align:middle; }
    .photo-zoom-icon i { font-size:14px; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
(function(){
    var tCropper, tCroppedBlob, tCompressedBlob = null;
    var T_MAX_MB = 0.05, T_MAX_W = 400, T_MAX_H = 400;

    function compressTeacherImage(file, maxBytes) {
        return new Promise(function(resolve, reject) {
            var img = new Image(), reader = new FileReader();
            reader.onload = function(e) {
                img.onload = function() {
                    var w = img.width, h = img.height;
                    if (w > T_MAX_W || h > T_MAX_H) {
                        var r = Math.min(T_MAX_W/w, T_MAX_H/h);
                        w = Math.round(w*r); h = Math.round(h*r);
                    }
                    var canvas = document.createElement('canvas');
                    canvas.width = w; canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                    (function tryQ(q) {
                        canvas.toBlob(function(blob) {
                            if (!blob) { reject(new Error('failed')); return; }
                            if (blob.size <= maxBytes || q <= 0.1) resolve(blob);
                            else tryQ(q - 0.1);
                        }, 'image/jpeg', q);
                    })(0.9);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    document.getElementById('teacherFormFile').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (!file || !file.type.startsWith('image/')) return;
        tCroppedBlob = null; tCompressedBlob = null;
        var maxBytes = Math.round(T_MAX_MB * 1024 * 1024);
        function showPreview(src) {
            var img = document.getElementById('teacherImagePreview');
            img.src = src;
            document.getElementById('teacherImagePreviewContainer').style.display = 'block';
            document.getElementById('teacherCropBtnContainer').style.display = 'block';
            if (tCropper) tCropper.destroy();
            tCropper = new Cropper(img, { aspectRatio:1, viewMode:1, autoCropArea:1 });
        }
        if (file.size > maxBytes) {
            compressTeacherImage(file, maxBytes).then(function(blob) {
                tCompressedBlob = blob;
                showPreview(URL.createObjectURL(blob));
            }).catch(function() {
                var r = new FileReader();
                r.onload = function(ev) { showPreview(ev.target.result); };
                r.readAsDataURL(file);
            });
        } else {
            var r = new FileReader();
            r.onload = function(ev) { showPreview(ev.target.result); };
            r.readAsDataURL(file);
        }
    });

    document.getElementById('teacherCropImageBtn').addEventListener('click', function() {
        if (!tCropper) return;
        tCropper.getCroppedCanvas({ width:T_MAX_W, height:T_MAX_H, imageSmoothingQuality:'high' })
            .toBlob(function(blob) {
                tCroppedBlob = blob;
                document.getElementById('teacherImagePreview').src = URL.createObjectURL(blob);
                alert('Cropped. Click Submit to upload.');
            }, 'image/jpeg', 0.7);
    });

    document.getElementById('teacherPhotoUploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('teacher_id_upload').value;
        if (!id) { alert('No teacher selected.'); return; }
        var fd = new FormData();
        fd.append('teacherID', id);
        if (tCroppedBlob) fd.append('file', tCroppedBlob, 'cropped.jpg');
        else if (tCompressedBlob) fd.append('file', tCompressedBlob, 'compressed.jpg');
        else {
            var fi = document.getElementById('teacherFormFile');
            if (!fi.files[0]) { alert('Please select an image.'); return; }
            fd.append('file', fi.files[0]);
        }
        fetch('<?=base_url('teacher/uploadPhoto')?>', { method:'POST', body:fd })
            .then(function(r){ return r.text(); })
            .then(function(res) {
                if (res === 'success') { alert('Photo uploaded!'); location.reload(); }
                else alert('Upload failed: ' + res);
            });
    });

    window.setTeacherID = function(id) {
        document.getElementById('teacher_id_upload').value = id;
        tCroppedBlob = null; tCompressedBlob = null;
        document.getElementById('teacherFormFile').value = '';
        document.getElementById('teacherImagePreviewContainer').style.display = 'none';
        document.getElementById('teacherCropBtnContainer').style.display = 'none';
        if (tCropper) { tCropper.destroy(); tCropper = null; }
    };
})();
</script>

<script>
  var status = '';
  var id = 0;
  $('.onoffswitch-small-checkbox').click(function() {
      if($(this).prop('checked')) {
          status = 'chacked';
          id = $(this).parent().attr("id");
      } else {
          status = 'unchacked';
          id = $(this).parent().attr("id");
      }

      if((status != '' || status != null) && (id !='')) {
          $.ajax({
              type: 'POST',
              url: "<?=base_url('teacher/active')?>",
              data: "id=" + id + "&status=" + status,
              dataType: "html",
              success: function(data) {
                  if(data == 'Success') {
                      toastr["success"]("Success")
                      toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "500",
                        "hideDuration": "500",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                      }
                  } else {
                      toastr["error"]("Error")
                      toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "500",
                        "hideDuration": "500",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                      }
                  }
              }
          });
      }
  }); 
</script>