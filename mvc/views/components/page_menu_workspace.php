    <div class="wrapper row-offcanvas row-offcanvas-left">
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="left-side sidebar-offcanvas">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img style="display:block" src="<?=imagelink($this->session->userdata('photo'))?>" class="img-circle" alt="" />
                    </div>

                    <div class="pull-left info">
                        <?php
                            $name = $this->session->userdata("name");
                            if(strlen($name) > 18) {
                               $name = substr($name, 0,18);
                            }
                            echo "<p>".$name."</p>";
                        ?>
                        <a href="<?=base_url("profile/index")?>">
                            <i class="fa fa-hand-o-right color-green"></i>
                            <?=$this->session->userdata("usertype")?>
                        </a>
                    </div>
                </div>

                <ul class="sidebar-menu">
                    <li class="<?=(isset($activemenu) && $activemenu == 'bookmarks') ? 'active' : ''?>">
                        <a href="<?=base_url('workspace/index')?>">
                            <i class="fa fa-bookmark"></i>
                            <span>Bookmarks</span>
                        </a>
                    </li>
                    <li class="<?=(isset($activemenu) && $activemenu == 'mybusiness') ? 'active' : ''?>">
                        <a href="<?=base_url('workspace/mybusiness')?>">
                            <i class="fa fa-briefcase"></i>
                            <span>My Business</span>
                        </a>
                    </li>
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>
