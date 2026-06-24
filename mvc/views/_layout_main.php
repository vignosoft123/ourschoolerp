<?php $this->load->view("components/page_header"); ?>
<link rel="stylesheet" href="/assets/css/report-buttons.css">
<link rel="stylesheet" href="/assets/css/reports.css">
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
        <footer class="main-footer" style="text-align:center;">
          	<strong>
                <?=$siteinfos->footer?> &nbsp;|&nbsp;
                Office Timings:- 10.00 AM to 6.00 PM &nbsp;|&nbsp;
                Support Number:- +91 8639963641 &nbsp;|&nbsp;
                Mail Id:- ourschoolerp123@gmail.com
          	</strong>
            <span class="pull-right hidden-xs"><b>v</b> <?php echo VERSION;?></span>
        </footer>
<?php $this->load->view("components/page_footer"); ?>


