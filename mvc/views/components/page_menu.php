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
                        <br/>
                        <br/>
                        <?php 
                         $sql = "select login from loginlog order by loginlogID desc limit 0,1";
                           $timestamp =  $this->db->query($sql)->result_array();
                         
                           $time =  date("Y-m-d H:i:s",($timestamp[0]['login']));
                        ?>
                        <!-- <a >Last Login Time : <?= $time;?> </a> -->
                    </div>
                </div>
                
                <ul class="sidebar-menu">
                    <?php
                        if(customCompute($dbMenus)) {
                            $menuDesign = '';
                            display_menu($dbMenus, $menuDesign);
                            echo $menuDesign;
                        }
                    ?>
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>