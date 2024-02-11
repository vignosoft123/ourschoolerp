

<style>
    /* .blue{ background: #97085d !important}
    .violet{ background: #a60404 !important}
    .orange{ background: #811a84 !important}
    .yellow{ background: #ffa600 !important} 
    .pink{background: #b91776 !important}
    .green{background: #005c48 !important}
    .darkblue{background: #04488d !important} */
    .blue{ background: #FFE2E5 !important}
    .violet{ background: #FFF4DE !important}
    .orange{ background: #F3E8FF !important}
    .yellow{ background: #F3E8FF !important} 
    .cream-clr{
        background:#FFE2E5 !important;
    }
    .peach-clr{
        background:#FFF4DE !important;
    }
    .light-purple-clr{
        background:#F3E8FF !important;
    }
    .light-green-clr{
        background:#DCFCE7 !important;
    }   
    .bluebird-clr{
        background:#E2FAFE !important;
    }    
    .light-blue-clr{
        background:#E3EBFE !important;
    }
    .dark-cream-clr{
        background:#FA5A7D !important;
    }
    .dark-peach-clr{
        background:#FF947A !important;
    }
    .dark-purple-clr{
        background:#BF83FF !important;
    }
    .dark-green-clr{
        background:#40d674 !important;
    }   
    .dark-bluebird-clr{
        background:#57cde0 !important;
    }    
    .dark-blue-clr{
        background:#7b9ff8 !important;
    }
</style>

<div class="main-wrapper">
    <?php if(config_item('demo')) { ?>
        <div class="col-sm-12" id="resetDummyData">
            <div class="callout callout-danger">
                <h4>Reminder!</h4>
                <p>Dummy data will be reset in every <code>30</code> minutes</p>
            </div>
        </div>

        <script type="text/javascript"> 
            $(document).ready(function() {
                var count = 7;
                var countdown = setInterval(function(){
                    $("p.countdown").html(count + " seconds remaining!");
                    if (count == 0) {
                        clearInterval(countdown);
                        $('#resetDummyData').hide();
                    }
                    count--;
                }, 1000);
            });
        </script>
    <?php } ?>

    <?php //if((config_item('demo') === FALSE) && ($siteinfos->auto_update_notification == 1) && ($versionChecking != 'none')) { ?>
        <?php //if($this->session->userdata('updatestatus') === null) { ?>
            <!-- <div class="col-sm-12" id="updatenotify">
                <div class="callout callout-success">
                    <h4>Dear Admin</h4>
                    <p>INIlabs school management system has released a new update.</p>
                    <p>Do you want to update it now <?=config_item('ini_version')?> to <?=$versionChecking?> ?</p>
                    <a href="<?=base_url('dashboard/remind')?>" class="btn btn-danger">Remind me</a>
                    <a href="<?=base_url('dashboard/update')?>" class="btn btn-success">Update</a>
                </div>
            </div> -->
        <?php //} ?> 
    <?php //} ?>

    <?php
        // $arrayColor = array(
        //     'bg-orange-dark',
        //     'bg-teal-light',
        //     'bg-pink-light',
        //     'bg-purple-light'
        // );

        $arrayColor = array(
            'blue',
            'violet',
            'yellow',
             'orange',
        );

        function allModuleArray($usertypeID='1', $dashboardWidget) {
          $userAllModuleArray = array(
            $usertypeID => array(
                'student'   => $dashboardWidget['students'],
                'classes'   => $dashboardWidget['classes'],
                'teacher'   => $dashboardWidget['teachers'],
                'user'   => $dashboardWidget['users'],
                //'parents'   => $dashboardWidget['parents'],
                'subject'   => $dashboardWidget['subjects'],
                'book'     => $dashboardWidget['books'],
                'feetypes'  => $dashboardWidget['feetypes'],
                'lmember'   => $dashboardWidget['lmembers'],
                'event'     => $dashboardWidget['events'],
                'issue'     => $dashboardWidget['issues'],
                'holiday'   => $dashboardWidget['holidays'],
                'invoice'   => $dashboardWidget['invoices'],
            )
          );
          return $userAllModuleArray;
        }

        $userArray = array(
            '1' => array(
                'student'   => $dashboardWidget['students'],
                'teacher'   => $dashboardWidget['teachers'],
                'user'   => $dashboardWidget['users'],
                //'parents'   => $dashboardWidget['parents'],
                'subject'   => $dashboardWidget['subjects']
            ),
            '2' => array(
                'student'   => $dashboardWidget['students'],
                'teacher'   => $dashboardWidget['teachers'],
                'classes'   => $dashboardWidget['classes'],
                'subject'   => $dashboardWidget['subjects'],
            ),
            '3' => array(
                'teacher'   => $dashboardWidget['teachers'],
                'subject'   => $dashboardWidget['subjects'],
                'issue'     => $dashboardWidget['issues'],
                'invoice'   => $dashboardWidget['invoices'],
            ),
            '4' => array(
                'teacher'   => $dashboardWidget['teachers'],
                'book'     => $dashboardWidget['books'],
                'event'     => $dashboardWidget['events'],
                'holiday'   => $dashboardWidget['holidays'],
            ),
            '5' => array(
                'student'   => $dashboardWidget['students'],
                'teacher'   => $dashboardWidget['teachers'],
                // 'parents'   => $dashboardWidget['parents'],
               
                'feetypes'  => $dashboardWidget['feetypes'],
                'invoice'   => $dashboardWidget['invoices'],
            ),
            '6' => array(
                'teacher'   => $dashboardWidget['teachers'],
                'lmember'   => $dashboardWidget['lmembers'],
                'book'      => $dashboardWidget['books'],
                'issue'     => $dashboardWidget['issues'],
            ),
            '7' => array(
                'teacher'       => $dashboardWidget['teachers'],
                'event'         => $dashboardWidget['events'],
                'holiday'       => $dashboardWidget['holidays'],
                'visitorinfo'  => $dashboardWidget['visitors'],
            ),
        );

        $generateBoxArray = array();
        $counter = 0;
        $getActiveUserID = $this->session->userdata('usertypeID');
        $getAllSessionDatas = $this->session->userdata('master_permission_set');
        foreach ($getAllSessionDatas as $getAllSessionDataKey => $getAllSessionData) {
            if($getAllSessionData == 'yes') {
                if(isset($userArray[$getActiveUserID][$getAllSessionDataKey])) {
 

                    if($counter == 4) {
                      break;
                    }
                    $generateBoxArray[$getAllSessionDataKey] = array(
                        'icon' => $dashboardWidget['allmenu'][$getAllSessionDataKey],
                        'color' => $arrayColor[$counter],
                        'link' => $getAllSessionDataKey,
                        'count' => $userArray[$getActiveUserID][$getAllSessionDataKey],
                        'menu' => $dashboardWidget['allmenulang'][$getAllSessionDataKey],
                    );
                    $counter++;
                }

            }
        }

        $icon = '';
        $menu = '';
        if($counter < 2) {
            $userArray = allModuleArray($getActiveUserID, $dashboardWidget);
            foreach ($getAllSessionDatas as $getAllSessionDataKey => $getAllSessionData) {
                if($getAllSessionData == 'yes') {
                    if(isset($userArray[$getActiveUserID][$getAllSessionDataKey])) {
                        if($counter == 2) {
                            break;
                        }

                        if(!isset($generateBoxArray[$getAllSessionDataKey])) {
                            $generateBoxArray[$getAllSessionDataKey] = array(
                                'icon' => $dashboardWidget['allmenu'][$getAllSessionDataKey],
                                'color' => $arrayColor[$counter],
                                'link' => $getAllSessionDataKey,
                                'count' => $userArray[$getActiveUserID][$getAllSessionDataKey],
                                'menu' => $dashboardWidget['allmenulang'][$getAllSessionDataKey]
                            );
                            $counter++;
                        }
                    }
                }
            }
        }

    //    echo "<pre>";print_r($generateBoxArray);die;
        if(customCompute($generateBoxArray)) { foreach ($generateBoxArray as $generateBoxArrayKey => $generateBoxValue) { ?>
            <!-- <div class="col-lg-2 col-xs-4">
                <div class="small-box <?=$generateBoxValue['color']?>">
                    <a class="small-box-footer" href="<?=base_url($generateBoxValue['link'])?>">
                        <div class="icon <?=$generateBoxValue['color']?>" style="padding: 9.5px 18px 6px 18px;">
                            <i class="fa <?=$generateBoxValue['icon']?>"></i>
                        </div>
                        <div class="inner">
                            <h3 class="h3-title">
                                <?=$generateBoxValue['count']?>
                            </h3>
                            <p class="para-txt">
                                <?=$this->lang->line('menu_'.$generateBoxValue['menu'])?>
                            </p>
                        </div>
                    </a>
                </div>
            </div> -->
    <?php } } ?>

 

            <?php 
            $uids = array(1,2,5,7,8,11,12); 
                if(in_array( $this->session->userdata('usertypeID'),$uids )){
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="stat-cards">
                            <div class="stat-cards-item">
                                <div class="stat-cards-top-cnt">
                                    <div class="stat-cards-info">
                                        <p class="stat-cards-info__title">Total Students</p>
                                        <p class="stat-cards-info__num">1478</p>
                                    </div>
                                    <div class="stat-cards-icon">
                                    <img src="../uploads/images/stat_students.svg" alt="Students"/>
                                    </div>
                                </div>
                                <div class="stat-cards-bottom-cnt">
                                <div class="progress">
                                    <div data-percentage="0%" style="width: 60%;" class="progress-bar" 
                                    role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                    <p class="stat-cards-info__progress">
                                        <span class="stat-cards-info__profit success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trending-up" aria-hidden="true"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                        </span>
                                            Last week present 75%
                                    </p>
                                </div>
                            </div>
                            <div class="stat-cards-item">
                                <div class="stat-cards-top-cnt">
                                    <div class="stat-cards-info">
                                        <p class="stat-cards-info__title">Total Teachers</p>
                                        <p class="stat-cards-info__num">86</p>
                                    </div>
                                    <div class="stat-cards-icon">
                                    <img src="../uploads/images/stat_teachers.svg" alt="Teachers"/>
                                    </div>
                                </div>
                                <div class="stat-cards-bottom-cnt">
                                <div class="progress">
                                    <div data-percentage="0%" style="width: 60%;" class="progress-bar" 
                                    role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                    <p class="stat-cards-info__progress">
                                        <span class="stat-cards-info__profit success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trending-up" aria-hidden="true"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                        </span>
                                            Last week present 95%
                                    </p>
                                </div>
                            </div>
                            <div class="stat-cards-item">
                                <div class="stat-cards-top-cnt">
                                    <div class="stat-cards-info">
                                        <p class="stat-cards-info__title">Total Staff</p>
                                        <p class="stat-cards-info__num">148</p>
                                    </div>
                                    <div class="stat-cards-icon">
                                    <img src="../uploads/images/stat_staff.svg" alt="Staff"/>
                                    </div>
                                </div>
                                <div class="stat-cards-bottom-cnt">
                                <div class="progress">
                                    <div data-percentage="0%" style="width: 60%;" class="progress-bar" 
                                    role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                    <p class="stat-cards-info__progress">
                                        <span class="stat-cards-info__profit success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trending-up" aria-hidden="true"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                        </span>
                                            Last week present 75%
                                    </p>
                                </div>
                            </div>
                            <div class="stat-cards-item">
                                <div class="stat-cards-top-cnt">
                                    <div class="stat-cards-info">
                                        <p class="stat-cards-info__title">Fees Collection</p>
                                        <p class="stat-cards-info__num">8000Rs</p>
                                    </div>
                                    <div class="stat-cards-icon">
                                    <img src="../uploads/images/stat_fees.svg" alt="Fees"/>
                                    </div>
                                </div>
                                <div class="stat-cards-bottom-cnt">
                                <div class="progress">
                                    <div data-percentage="0%" style="width: 60%;" class="progress-bar" 
                                    role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                    <p class="stat-cards-info__progress">
                                        <span class="stat-cards-info__profit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trending-up" aria-hidden="true"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                                        </span>
                                        This month collection 75%
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">                    
                        <div class="box">
                            <div class="box-body" style="padding: 10px 0px;">
                                <div id="attendanceGraph"></div>
                            </div>            
                        </div>
                    </div>
                </div>
            <!-- <div class="col-lg-2 col-xs-4">
                <div class="small-box bluebird-clr">
                    <a class="small-box-footer" href="#">
                        <div class="icon icon-bg dark-bluebird-clr">
                            <img src="../uploads/images/whatsapp-count.svg" alt="Whatsapp Count"/>
                        </div>
                        <div class="inner">
                        <p class="para-txt">
                                Whatsapp Count
                            </p>
                            <h3 class="h3-title">                                 
                                <?=$whatsapp_count?>
                            </h3>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-2 col-xs-4">
                <div class="small-box light-green-clr">
                    <a class="small-box-footer" href="#">
                        <div class="icon icon-bg dark-green-clr">
                        <img src="../uploads/images/sms-count.svg" alt="SMS Count"/> 
                        </div>
                        <div class="inner">
                        <p class="para-txt">
                                SMS Count
                            </p>
                            <h3 class="h3-title">
                                <?php echo $sms_count;?>
                            </h3>                            
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-2 col-xs-4">
                <div class="small-box light-blue-clr">
                    <a class="small-box-footer" href="#">
                        <div class="icon icon-bg dark-blue-clr">
                        <img src="../uploads/images/voice-count.svg" alt="Voice Count"/> 
                        </div>
                        <div class="inner ">
                        <p class="para-txt">
                                Voice Count
                            </p>
                            <h3 class="h3-title">
                                <?php echo $voice_count;?>
                            </h3>                           
                        </div>
                    </a>
                </div>
            </div> -->
            <?php } ?>
</div>

<?php if($getActiveUserID == 1 || $getActiveUserID == 5) { ?>
    <div class="row">
        <div class="col-sm-8">
            <div class="box">
                <div class="box-body" style="padding: 10px 0px;">
                    <div id="earningGraph"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
        <div class="box">
                <div class="box-body" style="padding: 10px 0px;">
                   
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if($getActiveUserID == 1 || $getActiveUserID == 5) { ?>
    <div class="row">
        <div class="col-sm-4">
            <?php $this->load->view('dashboard/ProfileBox'); ?>
        </div>
        <div class="col-sm-8">
            <div class="box">
                <div class="box-body" style="padding: 10px 0px;">
                    <div id="attendanceGraph"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php if(permissionChecker('notice')) { ?>
        <div class="col-sm-6">
            <?php $this->load->view('dashboard/NoticeBoard', array('val' => 5, 'length' => 15, 'maxlength' => 45)); ?>
        </div>
        <?php } ?>
        <div class="col-sm-6">
            <div class="box">
                <div class="box-body" style="padding: 10px 0px;">
                    <div id="visitor"></div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="row">
        <div class="col-sm-4">
            <?php $this->load->view('dashboard/ProfileBox'); ?>
        </div>
        <?php if(permissionChecker('notice')) { ?>
        <div class="col-sm-8">
            <div class="box">
                <div class="box-body" style="padding: 10px 0px;height: 320px">
                    <?php $this->load->view('dashboard/NoticeBoard', array('val' => 5, 'length' => 20, 'maxlength' => 70)); ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
<?php } ?>

<div class="row">
  <div class="col-sm-6">
      <div class="box">
          <div class="box-body">
              <!-- THE CALENDAR -->
              <div id="calendar"></div>
          </div>
      </div>
  </div>
</div><!-- /.row -->

<?php
    if($attendanceSystem != 'subject') {
        $this->load->view("dashboard/AttendanceHighChartJavascript");
    } else {
        $this->load->view("dashboard/SubjectWiseAttendanceHighChartJavascript");
    }
?>
<?php $this->load->view("dashboard/EarningHighChartJavascript.php"); ?>
<?php $this->load->view("dashboard/CalenderJavascript"); ?>
<?php $this->load->view("dashboard/VisitorHighChartJavascript"); ?>

