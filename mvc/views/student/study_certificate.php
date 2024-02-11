<style>
 
.mainborder{
    border: 5px solid #2632a4;
    padding: 3px;
}
.innder-border{
    border: 2px solid #2632a4;
}
.header{
    text-align: center;
    line-height: .4;
}
.header h1{
    font-family:'Times New Roman', Times, serif;
    font-size: 52px;
    font-style: italic;
    text-transform: capitalize;
    font-weight: bold;
   
}
.institute_name{
    font-style: italic;
    text-transform: capitalize;
    font-weight: bold;
    font-size: 18px;
}
 
.bordered-text {
  display: flex;
  align-items: center; /* Center text vertically */
  
  justify-content: center;
  font-weight: 1000;
}
 
.left-line,
.right-line {
  height: 2px; /* Adjust line height */
  width: 23.3%; /* Adjust line width */
  background-color: #2632a4; /* Line color */
}
 
.text {
    font-size: 24px;

    padding: 10px;  
    border: 2px solid #2632a4;
}
.maincontent{
    font-style: italic;
    padding: 5px;
    font-family:'Times New Roman', Times, serif;
}

.input {
  display: flex; 
  padding: 5px;
  
} 
.input::after {
  border-bottom: 3px dotted #2632a4;
  content: '';
  flex: 1;
}

.dateinput {
  display: flex; 
  padding: 5px;
  
} 
.dateinput::after {
  border-bottom: 3px dotted #2632a4;
  content: '';
  width: 30%;
}

.text-container {
    display: flex;
    padding: 5px;
}
.border-after-text {
    border-bottom: 3px dotted #2632a4;
    flex: 1;
}
 
.footer{
    margin-top:50px;
    padding: 5px;
    width: 100%;
    display: flex;
}
.footer .left{
    width: 75%;
}
.footeraddress{
    padding-bottom: 1%;
}
</style>


<?php 
//echo btn_printReport('classesreport', $this->lang->line('report_print'), 'main_block');
?>
<!-- <div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa icon-student"></i> <?= $this->lang->line('menu_student') ?></h3>


        <ol class="breadcrumb">
            <li><a href="<?= base_url("dashboard/index") ?>"><i class="fa fa-laptop"></i> <?= $this->lang->line('menu_dashboard') ?></a></li>
            <li class="active"><?= $this->lang->line('menu_student') ?></li>
        </ol>
    </div> -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

               

    
                        <div class="tab-content">
                            <div id="all" class="tab-pane active">
                                <div id="hide-table">
                                <main style="color: #2632a4;">

          
<div class="mainborder" id="main_block">
    <div class = "innder-border" style="height: 550px;">
        <div class="header" contenteditable="true">
            <h1>Sri potti sriramulu degree College</h1>
            <span align ="center">(Affiliated to Acharya Nagarjuna University)</span>
            <p style="font-size:24px;;"><span style="font-weight: bolder;">DARSI</span><span>&nbsp;- 523 247,</span>  <span>Prakasam Dist. A.P</span></p>

          

            <div class="bordered-text">
                <div class="left-line"></div>
                <div class="text">STUDY & CONDUCT CERTIFICATE</div>
                <div class="right-line"></div>
            </div>
        </div>
        <div class="maincontent">
            <p class="input">This is to certify that Mr / Miss <span class="border-after-text" contenteditable="true"></span> </p>
            <p class="text-container"><span class="input_text">S/o. D/o</span><span class="border-after-text" contenteditable="true"></span><span>is / was a student</span></p>

            <p class="input" contenteditable="true">of &nbsp;<span class="institute_name">Sri Potti sriramulu Degree College,</span> &nbsp;<span class="address">Darsi</span>, During the years</span></p>
            <p class="text-container"><span>with group</span><span class="border-after-text" contenteditable="true"></span>Medium His/Her Character and Conduct is <span class="border-after-text" contenteditable="true"></span></p>
        </div>

        <div></div>
        <div class="footer">
            <div class="left">
                <div class="footeraddress">DARSI</div>
                
                <div class="dateinput" contenteditable="true">Date</div>
            </div>
            <div class="right">
                <div contenteditable="true">
                    <span>Principal</span>
                </div>
                <div >
                    <span>Signature</span>
                    <div contenteditable="true"></div>
                </div>
            </div>
           
        </div>
    </div>
</div>

</main>
                                </div>
                            </div>
                        </div>
                    </div> <!-- nav-tabs-custom --> 
            </div> <!-- col-sm-12 -->
        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

<script type="text/javascript">
    $(".select2").select2();

    $('#classesID').change(function() {
        var classesID = $(this).val();
        if (classesID == 0) {
            $('#hide-table').hide();
            $('.nav-tabs-custom').hide();
        } else {
            $.ajax({
                type: 'POST',
                url: "<?= base_url('student/student_list') ?>",
                data: "id=" + classesID,
                dataType: "html",
                success: function(data) {
                    window.location.href = data;
                }
            });
        }
    });


    var status = '';
    var id = 0;
    $('.onoffswitch-small-checkbox').click(function() {
        if ($(this).prop('checked')) {
            status = 'chacked';
            id = $(this).parent().attr("id");
        } else {
            status = 'unchacked';
            id = $(this).parent().attr("id");
        }

        if ((status != '' || status != null) && (id != '')) {
            $.ajax({
                type: 'POST',
                url: "<?= base_url('student/active') ?>",
                data: "id=" + id + "&status=" + status,
                dataType: "html",
                success: function(data) {
                    if (data == 'Success') {
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

<script language="javascript" type="text/javascript">
		function printDiv(divID) {
			var divElements = document.getElementById(divID).innerHTML;
			var oldPage = document.body.innerHTML;
			document.body.innerHTML =
				"<html><head><title></title></head><body>" +
				divElements + "</body>";
			window.print();
			document.body.innerHTML = oldPage;
			window.location.reload();
		}

</script>