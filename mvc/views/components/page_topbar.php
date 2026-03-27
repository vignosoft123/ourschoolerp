<style>
    /* Optional enhancements */
.input-group .form-control {
    border-radius: 20px 0 0 20px;
}

.input-group .btn {
    border-radius: 0 20px 20px 0;
}

/* Topbar Enhancements */
.header .logo {
    width: auto !important;
    white-space: nowrap;
    font-size: 16px !important;
    line-height: 50px;
    text-transform: uppercase;
    font-weight: 700;
    color: #333 !important;
}

@media (max-width: 991px) {
    .header .logo {
        font-size: 14px !important;
    }
}

@media (max-width: 767px) {
    .header .navbar {
        margin-left: 0;
    }
    .header .logo {
        padding: 0 10px;
        font-size: 12px !important;
    }
    .navbar-left {
        display: none !important;
    }
    .navbar-right {
        float: right;
        padding-right: 15px;
    }
    #g_search_form {
        display: none;
    }
}

.search-box {
    background-color: #f1f3f4 !important;
    border: 1px solid #e0e0e0 !important;
    border-radius: 4px !important;
    transition: all 0.3s ease;
}
.search-box:focus {
    background-color: #fff !important;
    border-color: #1a73e8 !important;
    box-shadow: 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(26,115,232,.6) !important;
}
#search_icon {
    background: transparent;
    border: none;
    color: #666;
    font-size: 16px;
    padding: 8px 10px;
    margin-top: 4px;
}
#schoolDropdown {
    height: 34px;
    margin-top: 8px;
    border-radius: 4px;
    border-color: #e0e0e0;
}

.search-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    pointer-events: none; /* So clicks go to the input */
}


    </style>
<header class="header">
            <!-- <a href="<?php echo base_url('dashboard/index'); ?>" class="logo">
                <?php if(customCompute($siteinfos)) { echo namesorting($siteinfos->sname, 14); } ?>
            </a> -->
            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <div class="navbar-left">
                <ul class="nav navbar-nav top-navigation-icons">
                         <li>
                            <a href="<?php echo base_url('dashboard/index'); ?>" class="logo">
                                <?php if(customCompute($siteinfos)) { echo $siteinfos->sname; } ?>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- School Selection Dropdown -->
                

                <div class="navbar-right">

                <div class="search-sec">
                                                  
                        </div>

                    <ul class="nav navbar-nav top-navigation-icons">
                        <!-- School Selection Dropdown moved here -->
                        <?php if(customCompute($topbar_college_groups)) { ?>
                        <li class="dropdown hidden-xs">
                            <select id="schoolDropdown" class="form-control" style="margin-top: 8px; margin-right: 10px; min-width: 150px;">
                                <option value="">Select College</option>
                                <?php foreach($topbar_college_groups as $college) { ?>
                                    <option value="<?=rtrim($college->college_url, '/') . '/signin/index'?>"><?=$college->college_name?></option>
                                <?php } ?>
                            </select>
                        </li>
                        <?php } ?>

                        <?php 
                        if($this->session->userdata('usertypeID') != 3){?>
                        <li class="hidden-xs hidden-sm">
                                <form id="g_search_form"  name="g_search_form" action="<?= base_url('Student/global_student_search'); ?>" method="post">
        <div class="position-relative" style="max-width: 250px; margin-top: 8px;">
            <input type="text" name="global_search" class="form-control search-box" placeholder="Global student search...">
        </div>
    </form>
                        </li>

                        <li>
                            <button id="search_icon"><i class="fa fa-search"></i> </button>
                        </li> 


                        <li>
                            <a href="#" id="qr-icon">
                                <img src="<?php echo base_url('Qr/show');?>" 
                                    alt="QR Icon" width="24" height="24" style="cursor:pointer;">
                            </a>
                        </li>


                        <?php }?>
                        <li class="dropdown notifications-menu">
                            <a target="_blank" href="<?=base_url('frontend/index')?>" class="dropdown-toggle" data-toggle="tooltip" title="<?=$this->lang->line('menu_visit_site')?>" data-placement="bottom">
                                <i class="fa fa-globe"></i>
                            </a>
                        </li>

                        <?php if(permissionChecker('schoolyear')) { funtopbarschoolyear($siteinfos, $topbarschoolyears); } ?>

                        <?php //if(permissionChecker('admissionenquiry')) { ?>
                            <li class="dropdown messages-menu">
                                <a href="<?=base_url('admissionenquiry/index')?>" class="dropdown-toggle" data-toggle="tooltip" title="<?=$this->lang->line('menu_admissionenquiry')?>" data-placement="bottom">
                                    <i class="fa fa-user-plus"></i>
                                </a>
                            </li>
                        <?php //} ?>

                        <li class="dropdown messages-menu my-push-message">
                            <a href="#" class="dropdown-toggle my-push-message-a" data-toggle="dropdown" >
                                <i class="fa fa-bell-o" ></i>
                            </a>
                            <ul class="dropdown-menu my-push-message-ul" style="display:none">
                                <li class='header my-push-message-number'>
                                </li>
                                <li>
                                    <ul class="menu my-push-message-list">
                                    </ul>
                                </li>
                            </ul>
                        </li>


                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?=imagelink($this->session->userdata('photo')) 
                                ?>" class="user-logo" alt="" />
                                <span>
                                    <?=(strlen($this->session->userdata('name')) > 10) ? substr($this->session->userdata('name'), 0, 10) : $this->session->userdata('name')?>
                                    <i class="caret"></i>
                                </span>   
                            </a>

                            <ul class="dropdown-menu">
                                <li class="user-body">
                                    <div class="col-xs-6 text-center">
                                        <a href="<?=base_url("profile/index")?>">
                                            <div><i class="fa fa-briefcase"></i></div>
                                            <?=$this->lang->line("profile")?> 
                                        </a>
                                    </div>
                                    <div class="col-xs-6 text-center">
                                        <a href="<?=base_url("signin/cpassword")?>">
                                            <div><i class="fa fa-lock"></i></div>
                                            <?=$this->lang->line("change_password")?> 
                                        </a>
                                    </div>
                                </li>
                                <li class="user-footer">
                                    <div class="text-center">
                                        <a href="<?=base_url("signin/signout")?>">
                                            <div><i class="fa fa-power-off"></i></div>
                                            <?=$this->lang->line("logout")?> 
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <!-- Close Button -->
      <div class="modal-header p-2 border-0">
        <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close" style="font-size: 1.4rem;">
          <span aria-hidden="true">Close&times;</span>
        </button>
      </div>

      <!-- QR Image -->
      <div class="modal-body text-center pt-0">
        <img id="qrImage" src="" alt="QR Code" class="img-fluid">
      </div>
    </div>
  </div>
</div>



<script>
$(document).ready(function(){
    // Auto-select current school based on URL
    var currentUrl = window.location.hostname;
    $('#schoolDropdown option').each(function() {
        var optUrl = $(this).val();
        if(optUrl != "" && optUrl.indexOf(currentUrl) !== -1) {
            $(this).prop('selected', true);
        }
    });

    $('#qr-icon').on('click', function(e){
        e.preventDefault();

        // Replace with your actual QR image path
        var qrPath = '<?php echo base_url('Qr/pqr');?>';

        $('#qrImage').attr('src', qrPath);
        $('#qrModal').modal('show');
    });


});

$(document).on("click",'#search_icon',function(){
        document.getElementById('g_search_form').submit();
});

// Handle school dropdown selection
$(document).on("change",'#schoolDropdown',function(){
    var selectedUrl = $(this).val();
    var currentUrl = window.location.href;
    
    if(selectedUrl && selectedUrl !== '') {
        // Check if the selected URL is different from current site
        var domainArr = selectedUrl.split('/');
        var domain = domainArr[2] ? domainArr[2] : '';
        
        if (domain != '' && window.location.hostname != domain) {
            window.open(selectedUrl, '_blank');
        }
    }
});
</script>
