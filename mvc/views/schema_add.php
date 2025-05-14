<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa iniicon-balancefeesreport"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"> <?=$this->lang->line('menu_balancefeesreport')?></li>
        </ol>
    </div><!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row">

            <div class="col-sm-12">
                
            <h2>Schema Update Interface</h2>
    <form method="post" action="<?= base_url('schema_update/save_query') ?>">
        <textarea name="sql_query" rows="5" cols="100" placeholder="Paste ALTER TABLE query here"></textarea><br><br>
        <button type="submit">Add to JSON</button>
    </form>

    <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

               

            </div>

        </div><!-- row -->
    </div><!-- Body -->
</div><!-- /.box -->

 

<script type="text/javascript">

 </script>


