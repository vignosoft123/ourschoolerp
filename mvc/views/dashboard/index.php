

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

<div class="row">
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
        $arrayIconColor = array(
            'dark-cream-clr',
            'dark-peach-clr',
            'dark-purple-clr',
            'dark-purple-clr',
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
                        'iconColor' => $arrayIconColor[$counter],
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
                                'iconColor' => $arrayIconColor[$counter],
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
            <div class="col-lg-2 col-xs-4">
                <div class="small-box <?=$generateBoxValue['color']?>">
                    <a class="small-box-footer" href="<?=base_url($generateBoxValue['link'])?>">
                        <div class="icon icon-bg <?=$generateBoxValue['iconColor']?>">
                            <i class="fa <?=$generateBoxValue['icon']?>" style="font-size:24px; line-height:46px; color:#fff;"></i>
                        </div>
                        <div class="inner">
                            <p class="para-txt">
                                <?=$this->lang->line('menu_'.$generateBoxValue['menu'])?>
                            </p>
                            <h3 class="h3-title">
                                <?=$generateBoxValue['count']?>
                            </h3>
                        </div>
                    </a>
                </div>
            </div>
    <?php } } ?>

 

            <?php 
            $uids = array(1,2,5,7,8,11,12); 
                if(in_array( $this->session->userdata('usertypeID'),$uids )){
                ?>
            <div class="col-lg-2 col-xs-4">
                <div class="small-box bluebird-clr">
                    <a class="small-box-footer" href="<?= base_url('mailandsms');?>">
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
                    <a class="small-box-footer" href="<?= base_url('mailandsms');?>">
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
            <?php if(in_array($this->session->userdata('usertypeID'), array(1,5))): ?>
            <div class="col-lg-2 col-xs-4">
                <div class="small-box light-blue-clr">
                    <a class="small-box-footer" href="<?= base_url('income');?>">
                        <div class="icon icon-bg dark-blue-clr">
                            <i class="fa fa-inr" style="font-size:24px; line-height:46px; color:#fff;"></i>
                        </div>
                        <div class="inner">
                            <p class="para-txt">Today's Finance</p>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                                <div style="text-align:left;">
                                    <div style="font-size:9px; color:#27ae60; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Income</div>
                                    <div style="font-size:12px; font-weight:700; color:#1a7a1a; line-height:1.2;">
                                        <?= number_format($today_income, 0) ?>
                                    </div>
                                </div>
                                <div style="width:1px; height:28px; background:#cde; margin:0 4px;"></div>
                                <div style="text-align:right;">
                                    <div style="font-size:9px; color:#e74c3c; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Expense</div>
                                    <div style="font-size:12px; font-weight:700; color:#a00; line-height:1.2;">
                                        <?= number_format($today_expense, 0) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php endif; ?>
            <?php } ?>
</div>

<?php if($getActiveUserID == 1 || $getActiveUserID == 5) { ?>
    <div class="row">
        <div class="col-sm-8">
            <div class="box">
                <div class="box-header with-border" style="padding:8px 15px; background:#EAF6FF;">
                    <h3 class="box-title" style="font-size:13px; font-weight:700;">
                        <i class="fa fa-area-chart text-blue"></i> Accounts Summary
                        <small style="font-size:10px; color:#999; margin-left:4px;">Click month to view day-wise details</small>
                    </h3>
                </div>
                <div class="box-body" style="padding: 10px 0px;">
                    <div id="earningGraph"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="box">
                <div class="box-header with-border" style="padding:8px 15px; background:#E6F4FE;">
                    <h3 class="box-title" style="font-size:13px; font-weight:700;">
                        <i class="fa fa-pie-chart text-blue"></i> Fee Collection Status
                    </h3>
                </div>
                <div class="box-body" style="padding:10px 0;">
                    <div id="feeStatusDonut" style="min-height:200px;"></div>
                    <div style="display:flex; justify-content:space-around; padding:8px 10px 4px; border-top:1px solid #f0f0f0; margin-top:4px;">
                        <div style="text-align:center;">
                            <div style="font-size:16px; font-weight:700; color:#2ecc71;">
                                &#8377;<?= number_format($feeStatus['collected'] ?? 0, 0) ?>
                            </div>
                            <div style="font-size:10px; color:#777;">Collected</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="font-size:16px; font-weight:700; color:#f39c12;">
                                &#8377;<?= number_format($feeStatus['discount'] ?? 0, 0) ?>
                            </div>
                            <div style="font-size:10px; color:#777;">Discount/Waiver</div>
                        </div>
                        <div style="text-align:center;">
                            <div style="font-size:16px; font-weight:700; color:#e74c3c;">
                                &#8377;<?= number_format($feeStatus['due'] ?? 0, 0) ?>
                            </div>
                            <div style="font-size:10px; color:#777;">Outstanding</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Class-wise Fee Collection + Fee Type Breakdown -->
    <div class="row">
        <div class="col-sm-8">
            <div class="box">
                <div class="box-header with-border" style="padding:8px 15px; background:#EBF5FF;">
                    <h3 class="box-title" style="font-size:13px; font-weight:700;">
                        <i class="fa fa-bar-chart text-blue"></i> Class-wise Fee Collection
                    </h3>
                </div>
                <div class="box-body" style="padding:10px 0;">
                    <div id="feeClassBarChart" style="min-height:260px;"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="box">
                <div class="box-header with-border" style="padding:8px 15px; background:#F5EEFF;">
                    <h3 class="box-title" style="font-size:13px; font-weight:700;">
                        <i class="fa fa-pie-chart text-blue"></i> Fee by Type
                    </h3>
                </div>
                <div class="box-body" style="padding:10px 0;">
                    <div id="feeTypeDonut" style="min-height:260px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments + Collection Summary -->
    <div class="row">
        <div class="col-sm-8">
            <div class="box">
                <div class="box-header with-border" style="padding:8px 15px; background:#EDFBF2;">
                    <h3 class="box-title" style="font-size:13px; font-weight:700;">
                        <i class="fa fa-list text-blue"></i> Recent Fee Payments
                    </h3>
                </div>
                <div class="box-body" style="padding:0;">
                    <table class="table table-condensed table-hover" style="margin:0; font-size:12px;">
                        <thead>
                            <tr style="background:#f9f9f9;">
                                <th style="padding:8px 12px;">Student</th>
                                <th style="padding:8px 12px;">Date</th>
                                <th style="padding:8px 12px;">Mode</th>
                                <th style="padding:8px 12px; text-align:right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(customCompute($recentPayments)): foreach($recentPayments as $rp): ?>
                            <tr>
                                <td style="padding:7px 12px;"><?= htmlspecialchars($rp->student_name ?? '&mdash;') ?></td>
                                <td style="padding:7px 12px; white-space:nowrap;"><?= date('d M Y', strtotime($rp->paymentdate)) ?></td>
                                <td style="padding:7px 12px;"><span class="label label-default" style="font-size:10px;"><?= htmlspecialchars($rp->paymenttype ?? '') ?></span></td>
                                <td style="padding:7px 12px; text-align:right; font-weight:600; color:#27ae60;">
                                    &#8377;<?= number_format($rp->paymentamount, 0) ?>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" style="text-align:center; color:#aaa; padding:24px;">No payments recorded yet</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="box">
                <div class="box-header with-border" style="padding:8px 15px; background:#FFFBEB;">
                    <h3 class="box-title" style="font-size:13px; font-weight:700;">
                        <i class="fa fa-check-circle text-blue"></i> Collection Summary
                    </h3>
                </div>
                <div class="box-body" style="padding:16px;">
                    <?php
                        $totalInvoiced  = $feeStatus['invoiced']  ?? 0;
                        $totalCollected = $feeStatus['collected'] ?? 0;
                        $totalDiscount  = $feeStatus['discount']  ?? 0;
                        $totalDue       = $feeStatus['due']       ?? 0;
                        $collectionPct  = $totalInvoiced > 0 ? round(($totalCollected / $totalInvoiced) * 100) : 0;
                    ?>
                    <div style="margin-bottom:10px;">
                        <div style="font-size:11px; color:#888; margin-bottom:2px;">Total Invoiced</div>
                        <div style="font-size:20px; font-weight:700; color:#333;">&#8377;<?= number_format($totalInvoiced, 0) ?></div>
                    </div>
                    <div style="display:flex; gap:8px; margin-bottom:8px;">
                        <div style="flex:1; background:#e8fdf1; border-radius:6px; padding:7px 8px; text-align:center;">
                            <div style="font-size:12px; font-weight:700; color:#27ae60;">&#8377;<?= number_format($totalCollected, 0) ?></div>
                            <div style="font-size:10px; color:#777; margin-top:2px;">Collected</div>
                        </div>
                        <div style="flex:1; background:#fef6e8; border-radius:6px; padding:7px 8px; text-align:center;">
                            <div style="font-size:12px; font-weight:700; color:#e67e22;">&#8377;<?= number_format($totalDiscount, 0) ?></div>
                            <div style="font-size:10px; color:#777; margin-top:2px;">Discount/Waiver</div>
                        </div>
                        <div style="flex:1; background:#fef0f0; border-radius:6px; padding:7px 8px; text-align:center;">
                            <div style="font-size:12px; font-weight:700; color:#e74c3c;">&#8377;<?= number_format($totalDue, 0) ?></div>
                            <div style="font-size:10px; color:#777; margin-top:2px;">Outstanding</div>
                        </div>
                    </div>
                    <div style="font-size:11px; color:#888; margin-bottom:5px;">Collection Rate</div>
                    <div class="progress" style="height:16px; border-radius:8px; margin-bottom:4px;">
                        <div class="progress-bar progress-bar-success" style="width:<?= $collectionPct ?>%; border-radius:8px; line-height:16px; font-size:11px;">
                            <?php if($collectionPct >= 10): ?><?= $collectionPct ?>%<?php endif; ?>
                        </div>
                    </div>
                    <div style="font-size:11px; color:#aaa; text-align:right;"><?= $collectionPct ?>% invoices fully paid</div>
                </div>
            </div>
        </div>
    </div>

<?php } ?>

<?php if($getActiveUserID == 1 || $getActiveUserID == 5) { ?>
    <div class="row">
        <div class="col-sm-8">
            <div class="box">
                <div class="box-header with-border" style="padding:8px 15px; background:#EDFAF4;">
                    <h3 class="box-title" style="font-size:13px; font-weight:700;">
                        <i class="fa fa-bar-chart text-blue"></i> Students Today's Attendance
                        <small style="font-size:10px; color:#999; margin-left:4px;">Click columns to view monthly data</small>
                    </h3>
                </div>
                <div class="box-body" style="padding: 10px 0px;">
                    <div id="attendanceGraph"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="box">
                <div class="box-header with-border" style="padding:8px 15px; background:#FFF0F2;">
                    <h3 class="box-title" style="font-size:13px; font-weight:700;">
                        <i class="fa fa-users text-blue"></i> Today's Attendance
                        <small style="font-size:10px; color:#999; margin-left:4px;"><?= date('d M Y') ?></small>
                    </h3>
                </div>
                <div class="box-body" style="padding:0; height:280px; overflow-y:auto;">
                    <?php if(customCompute($todaysAttendance)): ?>
                    <table class="table table-condensed" style="margin:0; font-size:12px;">
                        <thead>
                            <tr style="background:#f9f9f9;">
                                <th style="padding:7px 12px;">Class</th>
                                <th style="padding:7px 8px; text-align:center; color:#27ae60;">Present</th>
                                <th style="padding:7px 8px; text-align:center; color:#e74c3c;">Absent</th>
                                <th style="padding:7px 8px; text-align:center; color:#888;">%</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($todaysAttendance as $classID => $att):
                            $p = (int)($att['P'] ?? 0);
                            $a = (int)($att['A'] ?? 0);
                            $total = $p + $a;
                            $pct   = $total > 0 ? round(($p / $total) * 100) : 0;
                            $pctColor = $pct >= 80 ? '#27ae60' : ($pct >= 60 ? '#f39c12' : '#e74c3c');
                        ?>
                        <tr style="border-bottom:1px solid #f5f5f5;">
                            <td style="padding:6px 12px; font-size:11px;">
                                <?= isset($classes[$classID]) ? htmlspecialchars($classes[$classID]->classes) : 'Class '.$classID ?>
                            </td>
                            <td style="padding:6px 8px; text-align:center; font-weight:600; color:#27ae60;"><?= $p ?></td>
                            <td style="padding:6px 8px; text-align:center; font-weight:600; color:#e74c3c;"><?= $a ?></td>
                            <td style="padding:6px 8px; text-align:center; font-weight:700; color:<?= $pctColor ?>; font-size:11px;"><?= $pct ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div style="text-align:center; color:#bbb; padding:60px 0; font-size:13px;">
                        <i class="fa fa-clock-o" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                        Attendance not marked yet today
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="display:flex; flex-wrap:wrap; align-items:stretch;">
        <?php if(permissionChecker('notice')) { ?>
        <div class="col-sm-6" style="display:flex; flex-direction:column;">
            <?php $this->load->view('dashboard/NoticeBoard', array('val' => 5, 'length' => 15, 'maxlength' => 45)); ?>
        </div>
        <?php } ?>
        <div class="col-sm-6" style="display:flex; flex-direction:column;">
            <div class="box" style="flex:1; display:flex; flex-direction:column;">
                <div class="box-header with-border" style="padding:8px 15px; background:#E8F4FD;">
                    <h3 class="box-title" style="font-size:13px; font-weight:700;">
                        <i class="fa fa-line-chart text-blue"></i> Site Visits
                        <small style="font-size:10px; color:#999; margin-left:4px;">Last 7 days</small>
                    </h3>
                </div>
                <div class="box-body" style="flex:1; padding:10px 0; min-height:180px;">
                    <div id="visitor" style="height:100%;"></div>
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

<div class="row" style="display:flex; flex-wrap:wrap; align-items:stretch;">
  <div class="col-sm-6" style="display:flex; flex-direction:column;">
      <div class="box" style="flex:1;">
          <div class="box-header with-border" style="padding:8px 15px; background:#F0F4FF;">
              <h3 class="box-title" style="font-size:13px; font-weight:700;">
                  <i class="fa fa-calendar text-blue"></i> School Calendar
              </h3>
          </div>
          <div class="box-body">
              <!-- THE CALENDAR -->
              <div id="calendar"></div>
          </div>
      </div>
  </div>
  <div class="col-sm-6" style="display:flex; flex-direction:column;">
      <div class="box" style="flex:1; display:flex; flex-direction:column;">
          <div class="box-header with-border" style="padding:10px 15px; background:#FFF5EE;">
              <h3 class="box-title" style="font-size:13px; font-weight:700;">
                  <i class="fa fa-calendar-check-o text-blue"></i> Upcoming Events &amp; Holidays
              </h3>
          </div>
          <div class="box-body" style="padding:0; flex:1; overflow-y:auto; min-height:200px;">
              <?php
                  // Merge events and holidays into one sorted list
                  $upcomingItems = [];
                  $today = date('Y-m-d');
                  if(customCompute($events)) {
                      foreach($events as $ev) {
                          if(isset($ev->fdate) && $ev->fdate >= $today) {
                              $upcomingItems[] = ['date' => $ev->fdate, 'title' => $ev->title, 'type' => 'event'];
                          }
                      }
                  }
                  if(customCompute($holidays)) {
                      foreach($holidays as $hd) {
                          if(isset($hd->fdate) && $hd->fdate >= $today) {
                              $upcomingItems[] = ['date' => $hd->fdate, 'title' => $hd->title, 'type' => 'holiday'];
                          }
                      }
                  }
                  usort($upcomingItems, function($a, $b){ return strcmp($a['date'], $b['date']); });
                  $upcomingItems = array_slice($upcomingItems, 0, 12);
              ?>
              <?php if(count($upcomingItems) > 0): ?>
              <table class="table table-condensed" style="margin:0; font-size:12px;">
                  <tbody>
                  <?php foreach($upcomingItems as $item): ?>
                      <tr style="border-bottom:1px solid #f5f5f5;">
                          <td style="padding:8px 12px; width:90px; white-space:nowrap; color:#888;">
                              <?= date('d M', strtotime($item['date'])) ?>
                          </td>
                          <td style="padding:8px 12px;">
                              <?= htmlspecialchars($item['title']) ?>
                          </td>
                          <td style="padding:8px 12px; text-align:right;">
                              <?php if($item['type'] === 'holiday'): ?>
                                  <span class="label" style="background:#e74c3c; font-size:9px;">Holiday</span>
                              <?php else: ?>
                                  <span class="label" style="background:#3498db; font-size:9px;">Event</span>
                              <?php endif; ?>
                          </td>
                      </tr>
                  <?php endforeach; ?>
                  </tbody>
              </table>
              <?php else: ?>
              <div style="text-align:center; color:#bbb; padding:60px 0; font-size:13px;">
                  <i class="fa fa-calendar-o" style="font-size:32px; display:block; margin-bottom:10px;"></i>
                  No upcoming events or holidays
              </div>
              <?php endif; ?>
          </div>
      </div>
  </div>
</div><!-- /.row -->

<!-- ── This Month's Birthdays ──────────────────────────────────────── -->
<?php
    $bTotal    = count($birthday_students) + count($birthday_teachers) + count($birthday_users);
    $today_day = (int)date('j');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-header with-border" style="background:#FFFBEB; padding:10px 15px;">
                <h3 class="box-title" style="font-size:14px; font-weight:700;">
                    &#127874; This Month's Birthdays
                    <small style="font-size:11px; color:#999; margin-left:6px;"><?= date('F Y') ?></small>
                </h3>
                <div class="box-tools pull-right">
                    <span style="font-size:11px; background:#f39c12; color:#fff; border-radius:10px; padding:3px 10px; font-weight:600;">
                        <?= $bTotal ?> this month
                    </span>
                </div>
            </div>
            <div class="box-body" style="padding:0;">
                <?php if($bTotal == 0): ?>
                    <div style="text-align:center; color:#bbb; padding:40px 0; font-size:13px;">
                        <i class="fa fa-smile-o" style="font-size:32px; display:block; margin-bottom:10px;"></i>
                        No birthdays this month
                    </div>
                <?php else: ?>
                <div style="display:flex; border-top:1px solid #f0f0f0;">

                    <!-- ── Students ── -->
                    <div style="flex:5; border-right:1px solid #f0f0f0;">
                        <!-- Header row with filter icon -->
                        <div style="background:#EBF5FB; padding:8px 14px; border-bottom:1px solid #e0eaf3; display:flex; align-items:center; gap:6px;">
                            <i class="fa fa-graduation-cap" style="color:#2471A3;"></i>
                            <strong style="font-size:12px; color:#2471A3;">Students</strong>
                            <span id="bday-student-count" style="margin-left:auto; font-size:10px; background:#2471A3; color:#fff; border-radius:8px; padding:1px 7px;"><?= count($birthday_students) ?></span>
                            <button id="bday-filter-toggle" title="Filter by Class &amp; Section"
                                style="background:none; border:1px solid #2471A3; border-radius:4px; padding:1px 6px; cursor:pointer; color:#2471A3; font-size:11px; line-height:1.6; margin-left:4px;">
                                <i class="fa fa-filter"></i>
                            </button>
                        </div>
                        <!-- Collapsible filter panel -->
                        <div id="bday-filter-panel" style="display:none; background:#f0f7fc; padding:7px 14px; border-bottom:1px solid #cde2f0; gap:6px; align-items:center; flex-wrap:wrap;">
                            <select id="bday-class-filter" style="font-size:11px; padding:3px 6px; border:1px solid #b0cfe0; border-radius:4px; min-width:130px; color:#333;">
                                <option value="">All Classes</option>
                                <?php
                                    $uniqueClasses = [];
                                    foreach($birthday_students as $bs) {
                                        if(!empty($bs->classesID) && !isset($uniqueClasses[$bs->classesID])) {
                                            $uniqueClasses[$bs->classesID] = $bs->classname ?? '—';
                                        }
                                    }
                                    foreach($uniqueClasses as $cid => $cname) {
                                        echo "<option value='".htmlspecialchars($cid)."'>".htmlspecialchars($cname)."</option>";
                                    }
                                ?>
                            </select>
                            <select id="bday-section-filter" disabled style="font-size:11px; padding:3px 6px; border:1px solid #b0cfe0; border-radius:4px; min-width:110px; color:#333;">
                                <option value="">All Sections</option>
                            </select>
                            <button id="bday-filter-clear" style="font-size:11px; padding:3px 8px; background:#e74c3c; color:#fff; border:none; border-radius:4px; cursor:pointer;">
                                <i class="fa fa-times"></i> Clear
                            </button>
                        </div>
                        <!-- Student list -->
                        <div id="bday-student-list" style="max-height:300px; overflow-y:auto;">
                            <?php if(customCompute($birthday_students)): ?>
                                <?php foreach($birthday_students as $bs):
                                    $isToday = ((int)date('j', strtotime($bs->dob)) === $today_day);
                                ?>
                                <div class="bday-student-row"
                                     data-class-id="<?= (int)($bs->classesID ?? 0) ?>"
                                     data-section-id="<?= (int)($bs->sectionID ?? 0) ?>"
                                     style="display:flex; align-items:center; gap:10px; padding:7px 14px; border-bottom:1px solid #f5f5f5; <?= $isToday ? 'background:#FFFDE7;' : '' ?>">
                                    <div style="width:32px; height:32px; border-radius:50%; overflow:hidden; flex-shrink:0; background:#c5d8e8; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#fff;">
                                        <?php if(!empty($bs->photo)): ?>
                                            <img src="<?= imagelink($bs->photo) ?>" style="width:100%; height:100%; object-fit:cover;" alt="">
                                        <?php else: ?>
                                            <?= strtoupper(substr($bs->name, 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?= $isToday ? '&#127874; ' : '' ?><?= htmlspecialchars($bs->name) ?>
                                        </div>
                                        <div style="font-size:10px; color:#888;">
                                            <?= date('d M', strtotime($bs->dob)) ?>
                                            <?php if(!empty($bs->classname)): ?>&nbsp;&middot;&nbsp;<?= htmlspecialchars($bs->classname) ?><?php endif; ?>
                                            <?php if(!empty($bs->sectionname)): ?>&nbsp;&middot;&nbsp;<?= htmlspecialchars($bs->sectionname) ?><?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if($isToday): ?>
                                        <span style="font-size:9px; background:#e67e22; color:#fff; border-radius:8px; padding:2px 6px; white-space:nowrap; flex-shrink:0;">Today!</span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="text-align:center; color:#bbb; padding:30px 0; font-size:12px;">No student birthdays this month</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ── Teachers ── -->
                    <div style="flex:4; border-right:1px solid #f0f0f0;">
                        <!-- Header row with filter icon -->
                        <div style="background:#EAFAF1; padding:8px 14px; border-bottom:1px solid #d5f0e0; display:flex; align-items:center; gap:6px;">
                            <i class="fa fa-user" style="color:#1E8449;"></i>
                            <strong style="font-size:12px; color:#1E8449;">Teachers</strong>
                            <span id="bday-teacher-count" style="margin-left:auto; font-size:10px; background:#1E8449; color:#fff; border-radius:8px; padding:1px 7px;"><?= count($birthday_teachers) ?></span>
                            <button id="bday-teacher-filter-toggle" title="Filter by Designation"
                                style="background:none; border:1px solid #1E8449; border-radius:4px; padding:1px 6px; cursor:pointer; color:#1E8449; font-size:11px; line-height:1.6; margin-left:4px;">
                                <i class="fa fa-filter"></i>
                            </button>
                        </div>
                        <!-- Collapsible filter panel -->
                        <div id="bday-teacher-filter-panel" style="display:none; background:#f0faf5; padding:7px 14px; border-bottom:1px solid #b8e8c8; gap:6px; align-items:center; flex-wrap:wrap;">
                            <select id="bday-designation-filter" style="font-size:11px; padding:3px 6px; border:1px solid #8ecfa8; border-radius:4px; min-width:160px; color:#333;">
                                <option value="">All Designations</option>
                                <?php
                                    $uniqueDesignations = [];
                                    foreach($birthday_teachers as $bt) {
                                        $d = trim($bt->designation ?? '');
                                        if($d !== '' && !in_array($d, $uniqueDesignations)) {
                                            $uniqueDesignations[] = $d;
                                        }
                                    }
                                    sort($uniqueDesignations);
                                    foreach($uniqueDesignations as $d) {
                                        echo "<option value='".htmlspecialchars($d)."'>".htmlspecialchars($d)."</option>";
                                    }
                                ?>
                            </select>
                            <button id="bday-teacher-filter-clear" style="font-size:11px; padding:3px 8px; background:#e74c3c; color:#fff; border:none; border-radius:4px; cursor:pointer;">
                                <i class="fa fa-times"></i> Clear
                            </button>
                        </div>
                        <!-- Teacher list -->
                        <div style="max-height:300px; overflow-y:auto;">
                            <?php if(customCompute($birthday_teachers)): ?>
                                <?php foreach($birthday_teachers as $bt):
                                    $isToday = ((int)date('j', strtotime($bt->dob)) === $today_day);
                                ?>
                                <div class="bday-teacher-row"
                                     data-designation="<?= htmlspecialchars(trim($bt->designation ?? '')) ?>"
                                     style="display:flex; align-items:center; gap:10px; padding:7px 14px; border-bottom:1px solid #f5f5f5; <?= $isToday ? 'background:#FFFDE7;' : '' ?>">
                                    <div style="width:32px; height:32px; border-radius:50%; overflow:hidden; flex-shrink:0; background:#a9d9b7; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#fff;">
                                        <?php if(!empty($bt->photo)): ?>
                                            <img src="<?= imagelink($bt->photo) ?>" style="width:100%; height:100%; object-fit:cover;" alt="">
                                        <?php else: ?>
                                            <?= strtoupper(substr($bt->name, 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?= $isToday ? '&#127874; ' : '' ?><?= htmlspecialchars($bt->name) ?>
                                        </div>
                                        <div style="font-size:10px; color:#888;">
                                            <?= date('d M', strtotime($bt->dob)) ?>
                                            <?php if(!empty($bt->designation)): ?>&nbsp;&middot;&nbsp;<?= htmlspecialchars($bt->designation) ?><?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if($isToday): ?>
                                        <span style="font-size:9px; background:#e67e22; color:#fff; border-radius:8px; padding:2px 6px; white-space:nowrap; flex-shrink:0;">Today!</span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="text-align:center; color:#bbb; padding:30px 0; font-size:12px;">No teacher birthdays this month</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ── Staff / Users ── -->
                    <div style="flex:3;">
                        <div style="background:#F5EEF8; padding:8px 14px; border-bottom:1px solid #e8d8f0; display:flex; align-items:center; gap:6px;">
                            <i class="fa fa-users" style="color:#7D3C98;"></i>
                            <strong style="font-size:12px; color:#7D3C98;">Staff</strong>
                            <span style="margin-left:auto; font-size:10px; background:#7D3C98; color:#fff; border-radius:8px; padding:1px 7px;"><?= count($birthday_users) ?></span>
                        </div>
                        <div style="max-height:300px; overflow-y:auto;">
                            <?php if(customCompute($birthday_users)): ?>
                                <?php foreach($birthday_users as $bu):
                                    $isToday = ((int)date('j', strtotime($bu->dob)) === $today_day);
                                ?>
                                <div style="display:flex; align-items:center; gap:10px; padding:7px 14px; border-bottom:1px solid #f5f5f5; <?= $isToday ? 'background:#FFFDE7;' : '' ?>">
                                    <div style="width:32px; height:32px; border-radius:50%; overflow:hidden; flex-shrink:0; background:#c8a9d9; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#fff;">
                                        <?php if(!empty($bu->photo)): ?>
                                            <img src="<?= imagelink($bu->photo) ?>" style="width:100%; height:100%; object-fit:cover;" alt="">
                                        <?php else: ?>
                                            <?= strtoupper(substr($bu->name, 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?= $isToday ? '&#127874; ' : '' ?><?= htmlspecialchars($bu->name) ?>
                                        </div>
                                        <div style="font-size:10px; color:#888;">
                                            <?= date('d M', strtotime($bu->dob)) ?>
                                        </div>
                                    </div>
                                    <?php if($isToday): ?>
                                        <span style="font-size:9px; background:#e67e22; color:#fff; border-radius:8px; padding:2px 6px; white-space:nowrap; flex-shrink:0;">Today!</span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="text-align:center; color:#bbb; padding:30px 0; font-size:12px;">No staff birthdays this month</div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div><!-- /.flex row -->
                <?php endif; ?>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
</div><!-- /.row birthdays -->

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
<?php if($getActiveUserID == 1 || $getActiveUserID == 5): ?>
<?php $this->load->view("dashboard/FeeCollectionChartJavascript"); ?>
<?php endif; ?>

<script>
/* ── Birthday student filter ─────────────────────────────────────── */
(function () {
    // Build section map from data attributes: { classId: { sectionId: sectionName } }
    var sectionMap = {};
    document.querySelectorAll('.bday-student-row').forEach(function (row) {
        var cid = row.dataset.classId;
        var sid = row.dataset.sectionId;
        if (!cid || cid === '0') return;
        if (!sectionMap[cid]) sectionMap[cid] = {};
        // Read section name from the subtitle text (3rd segment after ·)
        var sub = row.querySelector('div > div:last-child');
        if (sub && sid && sid !== '0' && !sectionMap[cid][sid]) {
            var parts = sub.textContent.split('·');
            if (parts.length >= 3) sectionMap[cid][sid] = parts[2].trim();
        }
    });

    var $toggle      = document.getElementById('bday-filter-toggle');
    var $panel       = document.getElementById('bday-filter-panel');
    var $classFilter = document.getElementById('bday-class-filter');
    var $secFilter   = document.getElementById('bday-section-filter');
    var $clearBtn    = document.getElementById('bday-filter-clear');
    var $count       = document.getElementById('bday-student-count');
    var $rows        = document.querySelectorAll('.bday-student-row');

    if (!$toggle) return;

    // Toggle filter panel visibility
    $toggle.addEventListener('click', function () {
        var shown = $panel.style.display === 'flex';
        $panel.style.display = shown ? 'none' : 'flex';
    });

    // Populate sections when class changes
    $classFilter.addEventListener('change', function () {
        var cid = this.value;
        $secFilter.innerHTML = '<option value="">All Sections</option>';
        if (cid && sectionMap[cid]) {
            Object.keys(sectionMap[cid]).forEach(function (sid) {
                var opt = document.createElement('option');
                opt.value = sid;
                opt.textContent = sectionMap[cid][sid] || ('Section ' + sid);
                $secFilter.appendChild(opt);
            });
            $secFilter.disabled = false;
        } else {
            $secFilter.disabled = true;
        }
        applyFilter();
    });

    $secFilter.addEventListener('change', applyFilter);

    $clearBtn.addEventListener('click', function () {
        $classFilter.value = '';
        $secFilter.innerHTML = '<option value="">All Sections</option>';
        $secFilter.disabled = true;
        applyFilter();
    });

    function applyFilter() {
        var cid = $classFilter.value;
        var sid = $secFilter.value;
        var visible = 0;
        $rows.forEach(function (row) {
            var match = (!cid || row.dataset.classId === cid) &&
                        (!sid || row.dataset.sectionId === sid);
            row.style.display = match ? 'flex' : 'none';
            if (match) visible++;
        });
        $count.textContent = visible;
    }
}());

/* ── Birthday teacher filter ─────────────────────────────────────── */
(function () {
    var $toggle   = document.getElementById('bday-teacher-filter-toggle');
    var $panel    = document.getElementById('bday-teacher-filter-panel');
    var $desgFilt = document.getElementById('bday-designation-filter');
    var $clearBtn = document.getElementById('bday-teacher-filter-clear');
    var $count    = document.getElementById('bday-teacher-count');
    var $rows     = document.querySelectorAll('.bday-teacher-row');

    if (!$toggle) return;

    $toggle.addEventListener('click', function () {
        var shown = $panel.style.display === 'flex';
        $panel.style.display = shown ? 'none' : 'flex';
    });

    $desgFilt.addEventListener('change', applyTeacherFilter);

    $clearBtn.addEventListener('click', function () {
        $desgFilt.value = '';
        applyTeacherFilter();
    });

    function applyTeacherFilter() {
        var desg = $desgFilt.value.trim();
        var visible = 0;
        $rows.forEach(function (row) {
            var match = !desg || row.dataset.designation === desg;
            row.style.display = match ? 'flex' : 'none';
            if (match) visible++;
        });
        $count.textContent = visible;
    }
}());
</script>

