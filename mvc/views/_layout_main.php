<?php $this->load->view("components/page_header"); ?>
<?php $this->load->view("components/page_topbar"); ?>
<?php $this->load->view("components/page_menu"); ?>

        <aside class="right-side">
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <?php $this->load->view($subview); ?>
                    </div>
                </div>
            </section>
        </aside>
        <footer class="main-footer">
          	<div class="pull-right hidden-xs">
            	<b>v</b> <?php echo "6.6.3";//config_item('ini_version');?>
          	</div>
          	 <strong><?=$siteinfos->footer?>
          	<!-- &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
          	<b></b>Download:-</b><a href="https://download.anydesk.com/AnyDesk.exe?_ga=2.19595825.802529734.1629965987-92454584.1629965987"><img src="<?=base_url('uploads/anydesk.png')?>"style="width: 113px;height: 20px;">
        
        </a> -->
          	</strong> 
            
<!-- 
    <ul class="nav navbar-nav">
    <li class="dropdown notifications-menu">
            <p style="margin-top: 13px;color: #fff;"><b><?php if(customCompute($siteinfos)) { echo namesorting($siteinfos->sname, 14); } ?></b>  </p>
        </li>

        <li class="dropdown notifications-menu">
            <p style="margin-top: 13px;color: #fff;"><b>Office Timings :-</b> 10:00 A.M. to 7:00 P.M.</p>
        </li>
        <li class="dropdown notifications-menu">
            <p style="margin-top: 13px;color: #fff; margin-left: 10px;"><b>Support Number :- </b> <img src="<?=base_url('uploads/whatsapp.png')?>" style="width: 20px;height: 20px;color: #fff;"> +91 8639963641</p>
        </li>
        <li class="dropdown notifications-menu">
            <p style="margin-top: 13px;color: #fff; margin-left: 10px;"><b>Mail Id :- </b> <img src="<?=base_url('uploads/mail.png')?>" style="width: 20px;height: 20px;color: #fff;"> <a href="mailto:ourschoolerp123@gmail.com">ourschoolerp123@gmail.com</a></p>
        </li>
</ul> -->
        </footer>
<?php $this->load->view("components/page_footer"); ?>


