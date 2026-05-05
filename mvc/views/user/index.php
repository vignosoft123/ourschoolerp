<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-users"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_user')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <?php 
                    if(permissionChecker('user_add')) {
                ?>
                    <h5 class="page-header btn-center">
                        <a class="ose-btn create-btn" href="<?php echo base_url('user/add') ?>">
                            <i class="fa fa-plus"></i> 
                            <?=$this->lang->line('add_title')?>
                        </a>
                    </h5>
                <?php } ?>

                <div id="hide-table">
                    <table id="example1" class="table table-bordered tableBorder dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="col-lg-1 text-center"><?=$this->lang->line('slno')?></th>
                                <th class="col-lg-2"><?=$this->lang->line('user_photo')?></th>
                                <th class="col-lg-2"><?=$this->lang->line('user_name')?></th>
                                <!-- <th class="col-lg-2"><?=$this->lang->line('user_email')?></th> -->
                                <th class="col-lg-2"><?=$this->lang->line('user_phone')?></th>
                                <th class="col-lg-2"><?=$this->lang->line('user_usertype')?></th>
                                <?php if(permissionChecker('user_edit')) { ?>
                                <th class="col-lg-2">Discount Permission</th>
                                <?php } ?>
                                <?php if(permissionChecker('user_edit')) { ?>
                                <th class="col-lg-1"><?=$this->lang->line('user_status')?></th>
                                <?php } ?>
                                <?php if(permissionChecker('user_edit') || permissionChecker('user_delete') || permissionChecker('user_view')) { ?>
                                <th class="col-lg-2 text-center"><?=$this->lang->line('action')?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($users)) {$i = 1; foreach($users as $user) { ?>
                                <tr>
                                    <td class=" text-center" data-title="<?=$this->lang->line('slno')?>">
                                        <?php echo $i; ?>
                                    </td>
                                    <td class="user-photo-cell" style="cursor:pointer;" onclick="setUserID(<?=$user->userID?>);" data-toggle="modal" data-target="#userPhotoUploadModal" data-title="<?=$this->lang->line('user_photo')?>">
                                        <?=profileimage($user->photo)?>
                                        <span class="photo-zoom-icon" data-img="<?=base_url('uploads/images/').($user->photo ? $user->photo : 'default.png')?>" title="Preview" onclick="event.stopPropagation();document.getElementById('userZoomImg').src=this.getAttribute('data-img');$('#userPhotoZoomModal').modal('show');">
                                            <i class="fa fa-search-plus" aria-hidden="true"></i>
                                        </span>
                                    </td>
                                    <td data-title="<?=$this->lang->line('user_name')?>">
                                        <?php echo $user->name; ?>
                                    </td>
                                    <!-- <td data-title="<?=$this->lang->line('user_email')?>">
                                        <?php echo $user->email; ?>
                                    </td> -->
                                    <td data-title="<?=$this->lang->line('user_phone')?>">
                                        <?php echo $user->phone; ?>
                                    </td>
                                    <td data-title="<?=$this->lang->line('user_usertype')?>">
                                        <?=$user->usertype;?>
                                    </td>
                                    <?php if(permissionChecker('user_edit')) { ?>
                                    <td data-title="Discount Permission">
                                        <div class="onoffswitch-small" id="discount-<?=$user->userID?>">
                                            <input type="checkbox" id="discount_switch<?=$user->userID?>" class="onoffswitch-small-checkbox discount-permission-checkbox" name="discount_permission" <?php if($user->is_able_payment_discount == '1') echo "checked='checked'"; ?>>
                                            <label for="discount_switch<?=$user->userID?>" class="onoffswitch-small-label">
                                                <span class="onoffswitch-small-inner"></span>
                                                <span class="onoffswitch-small-switch"></span>
                                            </label>
                                        </div>
                                    </td>
                                    <?php } ?>
                                    <?php if(permissionChecker('user_edit')) { ?>
                                    <td data-title="<?=$this->lang->line('user_status')?>">
                                      <div class="onoffswitch-small" id="<?=$user->userID?>">
                                          <input type="checkbox" id="myonoffswitch<?=$user->userID?>" class="onoffswitch-small-checkbox" name="paypal_demo" <?php if($user->active === '1') echo "checked='checked'"; ?>>
                                          <label for="myonoffswitch<?=$user->userID?>" class="onoffswitch-small-label">
                                              <span class="onoffswitch-small-inner"></span>
                                              <span class="onoffswitch-small-switch"></span>
                                          </label>
                                      </div>           
                                    </td>
                                    <?php } ?>
                                    <?php if(permissionChecker('user_edit') || permissionChecker('user_delete') || permissionChecker('user_view')) { ?>
                                    <td class="text-center" data-title="<?=$this->lang->line('action')?>">
                                        <?php echo btn_view('user/view/'.$user->userID, $this->lang->line('view')) ?>
                                        <?php echo btn_edit('user/edit/'.$user->userID, $this->lang->line('edit')) ?>
                                        <?php echo btn_delete('user/delete/'.$user->userID, $this->lang->line('delete')) ?>
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

<!-- User Photo Upload Modal -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet" />
<div class="modal fade" id="userPhotoUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:15px;border:none;box-shadow:0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;border-radius:15px 15px 0 0;border-bottom:none;padding:20px 25px;">
                <h5 class="modal-title" style="font-weight:600;font-size:20px;"><i class="fa fa-upload"></i> Upload User Photo</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:white;opacity:0.9;font-size:28px;font-weight:300;text-shadow:none;"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="padding:30px 25px;background:#f8f9fa;">
                <form id="userPhotoUploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Choose a photo to upload</label>
                        <input class="form-control" type="file" id="userFormFile" name="file" accept="image/*">
                        <input type="hidden" id="user_id_upload" name="userID" value="">
                    </div>
                    <div class="mb-3" id="userImagePreviewContainer" style="display:none;text-align:center;">
                        <img id="userImagePreview" style="max-width:100%;max-height:300px;" />
                    </div>
                    <div class="mb-3" id="userCropBtnContainer" style="display:none;text-align:center;">
                        <button type="button" class="btn btn-secondary" id="userCropImageBtn">Crop &amp; Compress</button>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- User Photo Zoom Modal -->
<div class="modal fade" id="userPhotoZoomModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius:20px;border:none;box-shadow:0 15px 50px rgba(0,0,0,0.3);background:transparent;">
            <div class="modal-body text-center" style="padding:0;border-radius:20px;background:#000;position:relative;">
                <button type="button" class="close" data-dismiss="modal" style="position:absolute;top:10px;right:15px;z-index:10;color:white;opacity:1;font-size:32px;font-weight:300;"><span>&times;</span></button>
                <img id="userZoomImg" src="" style="max-width:100%;max-height:85vh;border-radius:20px;object-fit:contain;" />
            </div>
        </div>
    </div>
</div>

<style>
    .user-photo-cell img { vertical-align:middle; }
    .photo-zoom-icon { margin-left:6px;color:#337ab7;cursor:pointer;display:inline-block;vertical-align:middle; }
    .photo-zoom-icon i { font-size:14px; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
(function(){
    var uCropper, uCroppedBlob, uCompressedBlob = null;
    var U_MAX_MB = 0.05, U_MAX_W = 400, U_MAX_H = 400;

    function compressUserImage(file, maxBytes) {
        return new Promise(function(resolve, reject) {
            var img = new Image(), reader = new FileReader();
            reader.onload = function(e) {
                img.onload = function() {
                    var w = img.width, h = img.height;
                    if (w > U_MAX_W || h > U_MAX_H) {
                        var r = Math.min(U_MAX_W/w, U_MAX_H/h);
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

    document.getElementById('userFormFile').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (!file || !file.type.startsWith('image/')) return;
        uCroppedBlob = null; uCompressedBlob = null;
        var maxBytes = Math.round(U_MAX_MB * 1024 * 1024);
        function showPreview(src) {
            var img = document.getElementById('userImagePreview');
            img.src = src;
            document.getElementById('userImagePreviewContainer').style.display = 'block';
            document.getElementById('userCropBtnContainer').style.display = 'block';
            if (uCropper) uCropper.destroy();
            uCropper = new Cropper(img, { aspectRatio:1, viewMode:1, autoCropArea:1 });
        }
        if (file.size > maxBytes) {
            compressUserImage(file, maxBytes).then(function(blob) {
                uCompressedBlob = blob;
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

    document.getElementById('userCropImageBtn').addEventListener('click', function() {
        if (!uCropper) return;
        uCropper.getCroppedCanvas({ width:U_MAX_W, height:U_MAX_H, imageSmoothingQuality:'high' })
            .toBlob(function(blob) {
                uCroppedBlob = blob;
                document.getElementById('userImagePreview').src = URL.createObjectURL(blob);
                alert('Cropped. Click Submit to upload.');
            }, 'image/jpeg', 0.7);
    });

    document.getElementById('userPhotoUploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var id = document.getElementById('user_id_upload').value;
        if (!id) { alert('No user selected.'); return; }
        var fd = new FormData();
        fd.append('userID', id);
        if (uCroppedBlob) fd.append('file', uCroppedBlob, 'cropped.jpg');
        else if (uCompressedBlob) fd.append('file', uCompressedBlob, 'compressed.jpg');
        else {
            var fi = document.getElementById('userFormFile');
            if (!fi.files[0]) { alert('Please select an image.'); return; }
            fd.append('file', fi.files[0]);
        }
        fetch('<?=base_url('user/uploadPhoto')?>', { method:'POST', body:fd })
            .then(function(r){ return r.text(); })
            .then(function(res) {
                if (res === 'success') { alert('Photo uploaded!'); location.reload(); }
                else alert('Upload failed: ' + res);
            });
    });

    window.setUserID = function(id) {
        document.getElementById('user_id_upload').value = id;
        uCroppedBlob = null; uCompressedBlob = null;
        document.getElementById('userFormFile').value = '';
        document.getElementById('userImagePreviewContainer').style.display = 'none';
        document.getElementById('userCropBtnContainer').style.display = 'none';
        if (uCropper) { uCropper.destroy(); uCropper = null; }
    };
})();
</script>

<script>
  var status = '';
  var id = 0;
  $('.onoffswitch-small-checkbox:not(.discount-permission-checkbox)').click(function() {
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
              url: "<?=base_url('user/active')?>",
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

  $('.discount-permission-checkbox').click(function() {
      var disc_status = $(this).prop('checked') ? 'chacked' : 'unchacked';
      var disc_id = $(this).parent().attr('id').replace('discount-', '');

      $.ajax({
          type: 'POST',
          url: "<?=base_url('user/discount_permission')?>",
          data: "id=" + disc_id + "&status=" + disc_status,
          dataType: "html",
          success: function(data) {
              if(data == 'Success') {
                  toastr["success"]("Success");
              } else {
                  toastr["error"]("Error");
              }
          }
      });
  });
</script>
