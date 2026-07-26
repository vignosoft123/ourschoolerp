<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa iniicon-idcardreport"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"> <?=$this->lang->line('menu_idcardreport')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="rpt-filter-card">
            <div class="rpt-filter-title"><i class="fa fa-filter"></i>&nbsp; Filter Options</div>
            <div class="row">
                <div class="form-group col-sm-4" id="usertypeIDDiv">
                    <label for="usertypeID"><?=$this->lang->line("idcardreport_idcard_for")?> <span class="text-red">*</span></label>
                    <?php
                        $usertypesArray['0'] = $this->lang->line("idcardreport_please_select");
                        if(customCompute($usertypes)) {
                            foreach($usertypes as $usertype) {
                                if($usertype->usertypeID != 4) {
                                    $usertypesArray[$usertype->usertypeID] = $usertype->usertype;
                                }
                            }
                        }
                        echo form_dropdown("usertypeID", $usertypesArray, set_value("usertypeID"), "id='usertypeID' class='form-control select2'");
                     ?>
                </div>


                <div class="form-group col-sm-4" id="classesDiv">
                    <label for="classesID"><?=$this->lang->line("idcardreport_class")?> <span class="text-red">*</span></label>
                    <?php
                        $classesArray['0'] = $this->lang->line("idcardreport_please_select");
                        if(customCompute($classes)) {
                            foreach ($classes as $classaKey => $classa) {
                                $classesArray[$classa->classesID] = $classa->classes;
                            }
                        }
                        echo form_dropdown("classesID", $classesArray, set_value("classesID"), "id='classesID' class='form-control select2'");
                     ?>
                </div>

                <div class="form-group col-sm-4" id="sectionDiv">
                    <label for="sectionID" ><?=$this->lang->line("idcardreport_section")?></label>
                    <?php
                        $sectionArray = array(
                            "0" => $this->lang->line("idcardreport_please_select"),
                        );
                        echo form_dropdown("sectionID", $sectionArray, set_value("sectionID"), "id='sectionID' class='form-control select2'");
                     ?>
                </div>

                <div class="form-group col-sm-4" id="userDiv">
                    <label id="userDivlabel" for="userID"><?=$this->lang->line("idcardreport_user")?></label>
                    <?php
                        $userArray['0'] = $this->lang->line("idcardreport_please_select");
                        // make this a multi select so users can choose multiple students
                        echo form_dropdown("userID[]", $userArray, set_value("userID"), "id='userID' class='form-control select2' multiple='multiple'");
                     ?>
                </div>

                 <div class="form-group col-sm-4" id="typeDiv">
                    <label for="photo_type">With or without photo <span class="text-red">*</span></label>
                    <?php
                        $typeArray = array(
                          
                            '0' => "Select",
                            '1' => "With Photo ID Cards",
                            '2' => "Without Photo ID Cards"
                        );
                        echo form_dropdown("photo_type", $typeArray, set_value("photo_type"), "id='photo_type' class='form-control select2'");
                     ?>
                </div>


<!-- 
                <div class="form-group col-sm-4" id="typeDiv">
                    <label for="type"><?=$this->lang->line("idcardreport_type")?> <span class="text-red">*</span></label>
                    <?php
                        $typeArray = array(
                            '0' => $this->lang->line("idcardreport_please_select"),
                            '1' => $this->lang->line("idcardreport_frontpart"),
                            '2' => $this->lang->line("idcardreport_backpart"),
                        );
                        echo form_dropdown("type", $typeArray, set_value("type"), "id='type' class='form-control select2'");
                     ?>
                </div>

                <div class="form-group col-sm-4" id="backgroundDiv">
                    <label for="background"><?=$this->lang->line("idcardreport_background")?> <span class="text-red">*</span></label>
                    <?php
                        $backgroundArray = array(
                            '0' => $this->lang->line("idcardreport_please_select"),
                            '1' => $this->lang->line("idcardreport_yes"),
                            '2' => $this->lang->line("idcardreport_no"),
                        );
                        echo form_dropdown("background", $backgroundArray, set_value("background"), "id='background' class='form-control select2'");
                     ?>
                </div> -->

            </div><!-- row -->

            <hr class="rpt-filter-divider">

            <div class="row">
                <div class="form-group col-sm-12" id="fieldsDiv">
                    <label>Fields to Display on ID Card</label>
                    <div class="rpt-idcard-fields">
                        <?php
                            $idcardFieldOptions = array(
                                'medium'        => 'Medium',
                                'class_section' => 'Class/Sec',
                                'father_name'   => "F'Name",
                                'contact_no'    => 'Contact No.',
                                'village'       => 'Village',
                                'blood_group'   => 'Blood Group',
                            );
                            foreach ($idcardFieldOptions as $fieldValue => $fieldLabel) {
                        ?>
                            <label class="rpt-idcard-field-item">
                                <input type="checkbox" class="idcard-field-checkbox" name="fields[]" value="<?=$fieldValue?>" checked>
                                <span><?=$fieldLabel?></span>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </div><!-- row -->

            <div class="row">
                <div class="form-group col-sm-12" id="fontStyleDiv">
                    <label>Font Style</label>
                    <div class="rpt-idcard-font-toolbar">
                        <div class="rpt-idcard-font-group">
                            <span class="rpt-idcard-font-caption">Font</span>
                            <select id="idcardFontFamily" class="form-control rpt-idcard-font-select">
                                <option value="Arial, sans-serif">Arial</option>
                                <option value="'Times New Roman', serif">Times New Roman</option>
                                <option value="Verdana, sans-serif">Verdana</option>
                                <option value="Georgia, serif">Georgia</option>
                                <option value="'Courier New', monospace">Courier New</option>
                                <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
                            </select>
                        </div>
                        <div class="rpt-idcard-font-group">
                            <span class="rpt-idcard-font-caption">Size</span>
                            <select id="idcardFontSize" class="form-control rpt-idcard-font-select">
                                <option value="12">12px</option>
                                <option value="13">13px</option>
                                <option value="14">14px</option>
                                <option value="15" selected>15px</option>
                                <option value="16">16px</option>
                                <option value="18">18px</option>
                                <option value="20">20px</option>
                                <option value="22">22px</option>
                                <option value="24">24px</option>
                            </select>
                        </div>
                        <div class="rpt-idcard-font-group">
                            <span class="rpt-idcard-font-caption">Style</span>
                            <div class="rpt-idcard-style-group">
                                <button type="button" id="idcardFontBold" class="rpt-idcard-style-btn" title="Bold"><b>B</b></button>
                                <button type="button" id="idcardFontItalic" class="rpt-idcard-style-btn" title="Italic"><i>I</i></button>
                            </div>
                        </div>
                        <div class="rpt-idcard-font-group">
                            <span class="rpt-idcard-font-caption">Label Color</span>
                            <input type="color" id="idcardLabelColor" class="rpt-idcard-color-input" value="#000000" title="Label Color">
                        </div>
                        <div class="rpt-idcard-font-group">
                            <span class="rpt-idcard-font-caption">Value Color</span>
                            <input type="color" id="idcardValueColor" class="rpt-idcard-color-input" value="#000000" title="Value Color">
                        </div>
                        <div class="rpt-idcard-font-group">
                            <span class="rpt-idcard-font-caption">Photo Border</span>
                            <input type="color" id="idcardPhotoBorderColor" class="rpt-idcard-color-input" value="#000000" title="Photo Border Color">
                        </div>
                    </div>
                </div>
            </div><!-- row -->

            <div class="rpt-filter-actions">
                <button id="get_idcardreport" class="btn btn-success rpt-filter-btn"><i class="fa fa-search"></i> <?=$this->lang->line("idcardreport_submit")?></button>
            </div>

        </div><!-- /.rpt-filter-card -->
    </div><!-- Body -->
</div><!-- /.box -->

<div id="load_idcardreport"></div>


<script type="text/javascript">
    
    function printDiv(divID) {
        var oldPage = document.body.innerHTML;
        var divElements = document.getElementById(divID).innerHTML;
        document.body.innerHTML = "<html><head><title></title></head><body>" + divElements + "</body>";

        window.print();
        document.body.innerHTML = oldPage;
        window.location.reload();
    }

    $(function(){
        $("#routinefor").val(0);
        $("#classesID").val(0);
        $("#sectionID").val(0);
        $("#userID").val(null).trigger('change');
        $("#type").val(0);
        $("#background").val(0);
        $('#classesDiv').hide('slow');
        $('#sectionDiv').hide('slow');
        $('#userDiv').hide('slow');
        $(".select2").select2();
    });

    $(document).on('change', '.idcard-field-checkbox', function() {
        $('#load_idcardreport').html('');
    });

    $(document).on('click', '#idcardFontBold, #idcardFontItalic', function() {
        $(this).toggleClass('active');
        $('#load_idcardreport').html('');
    });

    $(document).on('change', '#idcardFontFamily, #idcardFontSize, #idcardLabelColor, #idcardValueColor, #idcardPhotoBorderColor', function() {
        $('#load_idcardreport').html('');
    });

    $(document).on('change', "#usertypeID", function() {
        $('#load_idcardreport').html("");
        var usertypeID = $(this).val();
        var classesID = $("#classesID").val();
        var sectionID = $("#sectionID").val();
        var idcardtext = $('#usertypeID option:selected').text();
        var error = 0;

        $('#userDivlabel').text(idcardtext);
        if(usertypeID == '0'){
            $('#classesDiv').hide('slow');
            $('#sectionDiv').hide('slow');
            $('#userDiv').hide('slow');
        } else if(usertypeID == '3') {
            $("#classesID").val(0);
            $("#sectionID").val(0);
            $("#userID").val(null).trigger('change');
            $('#classesDiv').show('slow');
            $('#sectionDiv').show('slow');
            $('#userDiv').show('slow');
        } else if(usertypeID != '0' && usertypeID !='3') {
            $("#classesID").val(0);
            $("#sectionID").val(0);
            $("#userID").val(null).trigger('change');
            $('#classesDiv').hide('slow');
            $('#sectionDiv').hide('slow');
            $('#userDiv').show('slow');
        }

        var passData = {
            'usertypeID':usertypeID,
            'classesID':classesID,
            'sectionID':sectionID,
        }

        if(usertypeID > 0)  {
            $.ajax({
                type : 'POST',
                url  : '<?=base_url('idcardreport/getUser')?>',
                data : passData,
                success : function(data) {
                    $('#userID').html(data);
                }
            });
        }
    });

    $(document).on('change', "#classesID", function() {
        $('#load_idcardreport').html('');
        var usertypeID = $('#usertypeID').val();
        var classesID = $(this).val();
        if(classesID == 0) {
            $('#sectionID').html('<option value="0">'+"<?=$this->lang->line("idcardreport_please_select")?>"+'</option>');
            $('#userID').html('<option value="0">'+"<?=$this->lang->line("idcardreport_please_select")?>"+'</option>');
        } else {
            $.ajax({
                type:'POST',
                url:'<?=base_url('idcardreport/getSection')?>',
                data:{'id':classesID},
                success:function(data) {
                    $('#sectionID').html(data);
                }
            });
        }

        if(classesID > 0 && usertypeID == 3) {
            $.ajax({
                type:'POST',
                url:'<?=base_url('idcardreport/getStudentByClass')?>',
                data:{'usertypeID':usertypeID,'classesID':classesID},
                success:function(data) {
                    $('#userID').html(data);
                }
            });
        }
    });

    $(document).on('change', "#sectionID", function() {
        $('#load_idcardreport').html('');
        var usertypeID = $('#usertypeID').val();
        var classesID = $('#classesID').val();
        var sectionID = $('#sectionID').val();

        if(classesID > 0 && usertypeID == 3) {
            $.ajax({
                type:'POST',
                url:'<?=base_url('idcardreport/getStudentBySection')?>',
                data:{'usertypeID':usertypeID,'classesID':classesID,'sectionID':sectionID},
                success:function(data) {
                    $('#userID').html('0');
                    $('#userID').html(data);
                }
            });
        }
    });

    $(document).on('change', "#userID", function() {
        $('#load_idcardreport').html("");
    });


    $(document).on('change', "#type", function() {
        $('#load_idcardreport').html("");
    });

    $(document).on('change', "#background", function() {
        $('#load_idcardreport').html("");
    });

    $(document).on('click','#get_idcardreport', function() {
        var usertypeID = $('#usertypeID').val();
        var classesID = $('#classesID').val();
        var sectionID = $('#sectionID').val();
        var userID    = $('#userID').val();
        var type      = $('#type').val();
        var background= $('#background').val();
        var photo_type= $('#photo_type').val();
        var fields = [];
        $('.idcard-field-checkbox:checked').each(function() {
            fields.push($(this).val());
        });
        var fontFamily = $('#idcardFontFamily').val();
        var fontSize   = $('#idcardFontSize').val();
        var fontBold   = $('#idcardFontBold').hasClass('active') ? 1 : 0;
        var fontItalic = $('#idcardFontItalic').hasClass('active') ? 1 : 0;
        var labelColor = $('#idcardLabelColor').val();
        var valueColor = $('#idcardValueColor').val();
        var photoBorderColor = $('#idcardPhotoBorderColor').val();
        var error = 0;
        var field = {
            'usertypeID'       : usertypeID,
            'classesID'        : classesID,
            'sectionID'        : sectionID,
            'userID'           : userID,
            'type'             : type,
            'background'       : background,
            'photo_type'       : photo_type,
            'fields'           : fields,
            'fontFamily'       : fontFamily,
            'fontSize'         : fontSize,
            'fontBold'         : fontBold,
            'fontItalic'       : fontItalic,
            'labelColor'       : labelColor,
            'valueColor'       : valueColor,
            'photoBorderColor' : photoBorderColor,
        }

        if(usertypeID == 0 ) {
            $('#usertypeIDDiv').addClass('has-error');
            error++;
        } else {
            $('#usertypeIDDiv').removeClass('has-error');
        }

        if(usertypeID == 3 && classesID == 0 ) {
            $('#classesDiv').addClass('has-error');
            error++;
        } else {
            $('#classesDiv').removeClass('has-error');
        }

        if(type == 0 ) {
            $('#typeDiv').addClass('has-error');
            error++;
        } else {
            $('#typeDiv').removeClass('has-error');
        }

        if(background == 0 ) {
            $('#backgroundDiv').addClass('has-error');
            error++;
        } else {
            $('#backgroundDiv').removeClass('has-error');
        } 

        if(error == 0 ) {
            makingPostDataPreviousofAjaxCall(field);
        }
    });

    function makingPostDataPreviousofAjaxCall(field) {
        var passData = field;
        ajaxCall(passData);
    }

    function ajaxCall(passData) {
        $.ajax({
            type:'POST',
            url:'<?=base_url('idcardreport/getIdcardReport')?>',
            data:passData,
            dataType:'html',
            success:function(data) {
                var response = JSON.parse(data);
                renderLoder(response, passData);
            }
        });
    }

    function renderLoder(response, passData) {
        if(response.status) {
            $('#load_idcardreport').html(response.render);
            for (var key in passData) {
                if (passData.hasOwnProperty(key)) {
                    $('#'+key).parent().removeClass('has-error');
                }
            }
        } else {
            for (var key in passData) {
                if (passData.hasOwnProperty(key)) {
                    $('#'+key).parent().removeClass('has-error');
                }
            }

            for (var key in response) {
                if (response.hasOwnProperty(key)) {
                    $('#'+key).parent().addClass('has-error');
                }
            }
        }
    }

</script>


