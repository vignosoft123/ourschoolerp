<?php if(customCompute($student)) { ?>

<style>
.tv-action-bar { display:flex; align-items:center; justify-content:space-between; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 16px; margin-bottom:18px; flex-wrap:wrap; gap:8px; }
.tv-action-bar .breadcrumb { margin:0; background:none; padding:0; font-size:13px; }

.tv-profile-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:18px; }
.tv-profile-header { background:linear-gradient(135deg,#00897b,#00695c); padding:22px 16px 16px; text-align:center; }
.tv-profile-header img { width:90px; height:90px; object-fit:cover; border-radius:50%; border:3px solid rgba(255,255,255,.35); display:block; margin:0 auto 10px; }
.tv-profile-header h4 { color:#fff; font-size:16px; font-weight:700; margin:0 0 3px; }
.tv-profile-header span { color:rgba(255,255,255,.8); font-size:12px; }
.tv-meta-list { list-style:none; margin:0; padding:0; }
.tv-meta-list li { display:flex; justify-content:space-between; align-items:center; padding:9px 16px; font-size:13px; border-bottom:1px solid #f1f5f9; }
.tv-meta-list li:last-child { border-bottom:none; }
.tv-meta-list li b { color:#4a5568; font-weight:600; }
.tv-meta-list li span { color:#1a202c; font-weight:500; text-align:right; }

.tv-section { background:#fff; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:16px; overflow:hidden; }
.tv-section-head { background:linear-gradient(90deg,#00897b,#00695c); color:#fff; padding:9px 16px; font-size:12.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; display:flex; align-items:center; gap:8px; }
.tv-section-head .fa { font-size:13px; opacity:.85; }
.tv-section-body { padding:14px 16px; }

.tv-row { display:grid; grid-template-columns:repeat(3,1fr); gap:12px 20px; }
.tv-row.cols-2 { grid-template-columns:repeat(2,1fr); }
.tv-row.cols-4 { grid-template-columns:repeat(4,1fr); }
.tv-field { }
.tv-field label { display:block; font-size:11px; font-weight:700; color:#718096; text-transform:uppercase; letter-spacing:.4px; margin-bottom:3px; }
.tv-field .tv-val { font-size:13.5px; color:#1a202c; font-weight:500; padding:5px 10px; background:#f8fafc; border-radius:5px; border-left:3px solid #00897b; min-height:32px; display:flex; align-items:center; }
.tv-field .tv-val.tv-highlight { border-left-color:#1a73e8; background:#e8f0fe; color:#1558b0; font-weight:700; }
.tv-field .tv-val.tv-warn { border-left-color:#f57c00; background:#fff8f0; color:#e65100; font-weight:700; }
</style>

<div class="tv-action-bar">
    <div>
        <button class="btn btn-default btn-sm mrg" onclick="javascript:printDiv('printablediv')">
            <i class="fa fa-print"></i> <?=$this->lang->line('print')?>
        </button>
        <?=btn_add_pdf('hmember/print_preview/'.$student->studentID."/".$set, $this->lang->line('pdf_preview'))?>
        <?php if(customCompute($hmember) && (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) && permissionChecker('hmember_edit')): ?>
            <?=btn_sm_edit('hmember/edit/'.$hmember->studentID."/".$set, $this->lang->line('edit'))?>
        <?php endif; ?>
        <button class="btn btn-default btn-sm mrg" data-toggle="modal" data-target="#mail">
            <i class="fa fa-envelope-o"></i> <?=$this->lang->line('mail')?>
        </button>
    </div>
    <ol class="breadcrumb">
        <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
        <li><a href="<?=base_url("hmember/index/$set")?>"><?=$this->lang->line('panel_title')?></a></li>
        <li class="active"><?=$this->lang->line('view')?></li>
    </ol>
</div>

<div id="printablediv">
    <div class="row">

        <!-- Left: Profile Card -->
        <div class="col-sm-3">
            <div class="tv-profile-card">
                <div class="tv-profile-header">
                    <?=profileviewimage($student->photo)?>
                    <h4><?=$student->srname?></h4>
                    <span><?=isset($usertypes[$student->usertypeID]) ? $usertypes[$student->usertypeID] : 'Student'?></span>
                </div>
                <ul class="tv-meta-list">
                    <li><b><?=$this->lang->line('hmember_registerNO')?></b> <span><?=$student->srregisterNO?></span></li>
                    <li><b><?=$this->lang->line('hmember_roll')?></b> <span><?=$student->srroll?></span></li>
                    <li><b><?=$this->lang->line('hmember_classes')?></b> <span><?=customCompute($class) ? $class->classes : '—'?></span></li>
                    <li><b><?=$this->lang->line('menu_section')?></b> <span><?=customCompute($section) ? $section->section : '—'?></span></li>
                </ul>
            </div>
        </div>

        <!-- Right: Detail Sections -->
        <div class="col-sm-9">

            <!-- Hostel Details -->
            <div class="tv-section">
                <div class="tv-section-head"><i class="fa fa-home"></i> Hostel Details</div>
                <div class="tv-section-body">
                    <div class="tv-row cols-4">
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_hname")?></label>
                            <div class="tv-val tv-highlight"><?=customCompute($hostel) ? $hostel->name : 'N/A'?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_tfee")?></label>
                            <div class="tv-val tv-warn">&#8377; <?=customCompute($hmember) ? number_format($hmember->hbalance, 2) : 'N/A'?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_htype")?></label>
                            <div class="tv-val"><?=customCompute($hostel) ? $hostel->htype : 'N/A'?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_class_type")?></label>
                            <div class="tv-val"><?=customCompute($category) ? $category->class_type : 'N/A'?></div>
                        </div>
                    </div>
                    <div style="margin-top:12px;">
                        <div class="tv-row cols-2">
                            <div class="tv-field">
                                <label><?=$this->lang->line("hmember_joindate")?></label>
                                <div class="tv-val"><?php echo (customCompute($hmember) && $hmember->hjoindate) ? date("d M Y", strtotime($hmember->hjoindate)) : 'N/A'; ?></div>
                            </div>
                            <div class="tv-field">
                                <label><?=$this->lang->line("hmember_phone")?></label>
                                <div class="tv-val"><?=$student->phone ?: '—'?></div>
                            </div>
                        </div>
                    </div>
                    <?php if(customCompute($hostel) && $hostel->address): ?>
                    <div style="margin-top:12px;">
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_hostel_address")?></label>
                            <div class="tv-val"><?=$hostel->address?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="tv-section">
                <div class="tv-section-head"><i class="fa fa-user"></i> Personal Information</div>
                <div class="tv-section-body">
                    <div class="tv-row">
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_dob")?></label>
                            <div class="tv-val"><?php echo $student->dob ? date("d M Y", strtotime($student->dob)) : '—'; ?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_sex")?></label>
                            <div class="tv-val"><?=$student->sex ?: '—'?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_bloodgroup")?></label>
                            <div class="tv-val"><?php echo (isset($allbloodgroup[$student->bloodgroup]) && $student->bloodgroup) ? $student->bloodgroup : '—'; ?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_religion")?></label>
                            <div class="tv-val"><?=$student->religion ?: '—'?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_email")?></label>
                            <div class="tv-val"><?=$student->email ?: '—'?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_state")?></label>
                            <div class="tv-val"><?=$student->state ?: '—'?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_country")?></label>
                            <div class="tv-val"><?php echo (isset($allcountry[$student->country])) ? $allcountry[$student->country] : ($student->country ?: '—'); ?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_remarks")?></label>
                            <div class="tv-val"><?=$student->remarks ?: '—'?></div>
                        </div>
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_extracurricularactivities")?></label>
                            <div class="tv-val"><?=$student->extracurricularactivities ?: '—'?></div>
                        </div>
                    </div>
                    <div style="margin-top:12px;">
                        <div class="tv-field">
                            <label><?=$this->lang->line("hmember_address")?></label>
                            <div class="tv-val"><?=$student->address ?: '—'?></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Email Modal -->
<form class="form-horizontal" role="form" action="<?=base_url('hmember/send_mail');?>" method="post">
    <div class="modal fade" id="mail">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background:linear-gradient(135deg,#00897b,#00695c);border-radius:5px 5px 0 0;">
                    <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:.9;">&times;</button>
                    <h4 class="modal-title" style="color:#fff;"><i class="fa fa-envelope-o"></i> <?=$this->lang->line('mail')?></h4>
                </div>
                <div class="modal-body">
                    <?php echo (form_error('to')) ? "<div class='form-group has-error'>" : "<div class='form-group'>"; ?>
                        <label for="to" class="col-sm-2 control-label"><?=$this->lang->line("to")?> <span class="text-red">*</span></label>
                        <div class="col-sm-6"><input type="email" class="form-control" id="to" name="to" value="<?=set_value('to')?>"></div>
                        <span class="col-sm-4 control-label" id="to_error"></span>
                    </div>
                    <?php echo (form_error('subject')) ? "<div class='form-group has-error'>" : "<div class='form-group'>"; ?>
                        <label for="subject" class="col-sm-2 control-label"><?=$this->lang->line("subject")?> <span class="text-red">*</span></label>
                        <div class="col-sm-6"><input type="text" class="form-control" id="subject" name="subject" value="<?=set_value('subject')?>"></div>
                        <span class="col-sm-4 control-label" id="subject_error"></span>
                    </div>
                    <?php echo (form_error('message')) ? "<div class='form-group has-error'>" : "<div class='form-group'>"; ?>
                        <label for="message" class="col-sm-2 control-label"><?=$this->lang->line("message")?></label>
                        <div class="col-sm-6"><textarea class="form-control" id="message" style="resize:vertical;" name="message"><?=set_value('message')?></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?=$this->lang->line('close')?></button>
                    <input type="button" id="send_pdf" class="btn btn-success" value="<?=$this->lang->line("send")?>">
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
function printDiv(divID) {
    var divElements = document.getElementById(divID).innerHTML;
    var oldPage = document.body.innerHTML;
    document.body.innerHTML = "<html><head><title></title></head><body>" + divElements + "</body>";
    window.print();
    document.body.innerHTML = oldPage;
    window.location.reload();
}

$("#send_pdf").click(function(){
    var to = $('#to').val(), subject = $('#subject').val(), message = $('#message').val();
    var id = "<?=$student->studentID;?>", set = "<?=$set;?>", error = 0;
    $("#to_error").html("");
    if(!to) {
        error++;
        $("#to_error").html("<?=$this->lang->line('mail_to')?>").css({"text-align":"left","color":"red"});
    } else {
        var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
        if(to.search(emailRegEx) == -1) { error++; $("#to_error").html("<?=$this->lang->line('mail_valid')?>").css({"text-align":"left","color":"red"}); }
    }
    if(!subject) {
        error++;
        $("#subject_error").html("<?=$this->lang->line('mail_subject')?>").css({"text-align":"left","color":"red"});
    } else { $("#subject_error").html(""); }
    if(error == 0) {
        $('#send_pdf').attr('disabled','disabled');
        $.ajax({
            type:'POST', url:"<?=base_url('hmember/send_mail')?>",
            data:'to='+to+'&subject='+subject+'&studentID='+id+'&message='+message+'&classesID='+set,
            dataType:"html",
            success:function(data){
                var response = JSON.parse(data);
                if(response.status == false) {
                    $('#send_pdf').removeAttr('disabled');
                    $.each(response,function(index,value){ if(index!='status') toastr["error"](value); });
                } else { location.reload(); }
            }
        });
    }
});
</script>

<?php } ?>
