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

                        <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </li>


                         <li>
                        <b> <a href="<?php echo base_url('dashboard/index'); ?>" class="logo" style="width:100% !important">
                        <?php if(customCompute($siteinfos)) { 
                                //echo namesorting($siteinfos->sname, 14); 
                                echo $siteinfos->sname;
                            } ?>
                        </a></b>
                        </li>
                      
                         </ul>
                </div>


                <div class="navbar-right">

                <div class="search-sec">
                                                  
                        </div>

                    <ul class="nav navbar-nav top-navigation-icons">
                         <!-- <li class="dropdown notifications-menu">
                           
                        </li>  -->
                        <!-- <li class="dropdown notifications-menu">
                            <p style="margin-top: 13px;color: #fff; margin-left: 10px;"><b>Support Number :- </b> <img src="<?=base_url('uploads/whatsapp.png')?>" style="width: 20px;height: 20px;color: #fff;"> +91 8639963641</p>
                        </li>
                        <li class="dropdown notifications-menu">
                            <p style="margin-top: 13px;color: #fff; margin-left: 10px;"><b>Mail Id :- </b> <img src="<?=base_url('uploads/mail.png')?>" style="width: 20px;height: 20px;color: #fff;"> <a href="mailto:ourschoolerp123@gmail.com">ourschoolerp123@gmail.com</a></p>
                        </li> -->
                        <li>
                           
                        </li>
                        <li>
                            <form name="g_search_form" action="<?php echo base_url('Student/global_student_search');?>" method="post">
                                <!-- <input type="text" name="global_search" placeholder="Global Student Search..." value="">
                                <input type="submit" class="btn btn-success" value="Search"> -->
                                <div class="search">
                                    <input type="text" name="global_search" class="search__input form-control" placeholder="Global student search...">
                                    <button class="search__button">
                                    <i class="fa fa-search"></i>
                                    </button>
                                </div>                                
                            </form>  
                        </li>
                        <li class="dropdown notifications-menu">
                            <a target="_blank" href="<?=base_url('frontend/index')?>" class="dropdown-toggle" data-toggle="tooltip" title="<?=$this->lang->line('menu_visit_site')?>" data-placement="bottom">
                                <i class="fa fa-globe"></i>
                            </a>
                        </li>

                        <?php if(permissionChecker('schoolyear')) { funtopbarschoolyear($siteinfos, $topbarschoolyears); } ?>

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