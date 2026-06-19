
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-template"></i> Edit Whatsapp Template</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url("mailandsmstemplate/whatsapp_index")?>">Whatsapp Templates</a></li>
            <li class="active">Edit Whatsapp Template</li>
        </ol>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <form class="form-horizontal" role="form" method="post">

                    <div class="form-group">
                        <label for="whatsapp_name" class="col-sm-2 control-label">
                            <?=$this->lang->line("mailandsmstemplate_name")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="whatsapp_name" name="whatsapp_name"
                                value="<?=set_value('whatsapp_name', $whatsapp_template->template_name)?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp_user" class="col-sm-2 control-label">
                            <?=$this->lang->line("mailandsmstemplate_user")?> <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-4">
                            <?php
                                $array = array('select' => $this->lang->line('mailandsmstemplate_select_user'));
                                if(customCompute($usertypes)) {
                                    foreach ($usertypes as $key => $usertype) {
                                        $array[$usertype->usertypeID] = $usertype->usertype;
                                    }
                                }
                                echo form_dropdown("whatsapp_user", $array, set_value("whatsapp_user", $whatsapp_template->usertypeID), "id='whatsapp_user' class='form-control select2'");
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?=$this->lang->line("mailandsmstemplate_tags")?></label>
                        <div class="col-sm-8">
                            <div class="col-sm-12 border" id="whatsapp_tags">
                                <?php
                                if(customCompute($usertypes)) {
                                    foreach ($usertypes as $key => $usertype) {
                                        if($usertype->usertypeID == 2) {
                                            echo '<div class="whatsapptagdiv" id="whatsapp_'.$usertype->usertype.'">';
                                                echo $teachers;
                                            echo '</div>';
                                        } elseif($usertype->usertypeID == 3) {
                                            echo '<div class="whatsapptagdiv" id="whatsapp_'.$usertype->usertype.'">';
                                                echo $students;
                                            echo '</div>';
                                        } elseif($usertype->usertypeID == 4) {
                                            echo '<div class="whatsapptagdiv" id="whatsapp_'.$usertype->usertype.'">';
                                                echo $parents;
                                            echo '</div>';
                                        } else {
                                            echo '<div class="whatsapptagdiv" id="whatsapp_'.str_replace(' ', '', $usertype->usertype).'">';
                                                echo $users;
                                            echo '</div>';
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp_template_field" class="col-sm-2 control-label">
                            Params <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-8">
                            <textarea class="form-control" style="resize:vertical;" id="whatsapp_template_field" name="params"><?=set_value('params', $whatsapp_template->params)?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="whatsapp_temp_name" class="col-sm-2 control-label">
                            Whatsapp Template <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-8">
                            <textarea class="form-control" style="resize:vertical;" id="whatsapp_temp_name" name="whatsapp_temp_name"><?=set_value('whatsapp_temp_name', $whatsapp_template->template)?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="template_id" class="col-sm-2 control-label">
                            Template ID <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="template_id" name="template_id"
                                value="<?=set_value('template_id', $whatsapp_template->templ_id)?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="short_name" class="col-sm-2 control-label">
                            Short Name <span class="text-red">*</span>
                        </label>
                        <div class="col-sm-4">
                            <?php
                                $shorts = array(
                                    'select'             => $this->lang->line('mailandsmstemplate_select_user'),
                                    'ATTENDANCE'         => 'ATTENDANCE',
                                    'FEE_PAID'           => 'FEE_PAID',
                                    'EXAM_MARKS'         => 'EXAM_MARKS',
                                    'HOLIDAY_INTIMATION' => 'HOLIDAY_INTIMATION',
                                    'FEE_REMINDER'       => 'FEE_REMINDER',
                                    'PROGRESS_CARD'      => 'PROGRESS_CARD',
                                );
                                echo form_dropdown("short_name", $shorts, set_value("short_name", $whatsapp_template->short_name), "id='short_name' class='form-control select2'");
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-8">
                            <input type="submit" class="btn btn-success" value="<?=$this->lang->line("update_template")?>">
                            <a href="<?=base_url('mailandsmstemplate/whatsapp_index')?>" class="btn btn-default">Cancel</a>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.select2').select2();

    $(document).ready(function() {
        var whatsapp_setuser = "<?=$whatsapp_user?>";
        if(whatsapp_setuser != 'select') {
            <?php
                if(customCompute($usertypes)) {
                    foreach ($usertypes as $key => $usertype) {
                        echo 'if('.$usertype->usertypeID." == whatsapp_setuser) {"."\n";
                            echo '$("#whatsapp_'.str_replace(' ', '', $usertype->usertype).'").show();'."\n";
                        echo '} else {'."\n";
                            echo '$("#whatsapp_'.str_replace(' ', '', $usertype->usertype).'").hide();'."\n";
                        echo '}'."\n";
                    }
                }
            ?>
        } else {
            <?php
                if(customCompute($usertypes)) {
                    foreach ($usertypes as $key => $usertype) {
                        echo '$("#whatsapp_'.str_replace(' ', '', $usertype->usertype).'").hide();'."\n";
                    }
                }
            ?>
        }

        $('#whatsapp_user').change(function() {
            var whatsapp_user = $(this).val();
            if(whatsapp_user != 'select') {
                <?php
                    if(customCompute($usertypes)) {
                        foreach ($usertypes as $key => $usertype) {
                            echo 'if('.$usertype->usertypeID." == whatsapp_user) {"."\n";
                                echo '$("#whatsapp_'.str_replace(' ', '', $usertype->usertype).'").show();'."\n";
                            echo '} else {'."\n";
                                echo '$("#whatsapp_'.str_replace(' ', '', $usertype->usertype).'").hide();'."\n";
                            echo '}'."\n";
                        }
                    }
                ?>
            } else {
                <?php
                    if(customCompute($usertypes)) {
                        foreach ($usertypes as $key => $usertype) {
                            echo '$("#whatsapp_'.str_replace(' ', '', $usertype->usertype).'").hide();'."\n";
                        }
                    }
                ?>
            }
        });
    });

    $('.whatsapptagdiv > .sms_alltag').click(function() {
        var value = $(this).val();
        insertAtCaret('whatsapp_temp_name', value);
    });

    function insertAtCaret(areaId, text) {
        var txtarea = document.getElementById(areaId);
        if(!txtarea) { return; }
        var scrollPos = txtarea.scrollTop;
        var strPos = 0;
        var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? "ff" : (document.selection ? "ie" : false));
        if(br == "ie") {
            txtarea.focus();
            var range = document.selection.createRange();
            range.moveStart('character', -txtarea.value.length);
            strPos = range.text.length;
        } else if(br == "ff") {
            strPos = txtarea.selectionStart;
        }
        var front = (txtarea.value).substring(0, strPos);
        var back  = (txtarea.value).substring(strPos, txtarea.value.length);
        txtarea.value = front + text + back;
        strPos = strPos + text.length;
        if(br == "ie") {
            txtarea.focus();
            var ieRange = document.selection.createRange();
            ieRange.moveStart('character', -txtarea.value.length);
            ieRange.moveStart('character', strPos);
            ieRange.moveEnd('character', 0);
            ieRange.select();
        } else if(br == "ff") {
            txtarea.selectionStart = strPos;
            txtarea.selectionEnd   = strPos;
            txtarea.focus();
        }
        txtarea.scrollTop = scrollPos;
    }
</script>
