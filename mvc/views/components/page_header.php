<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">

        <title><?=$this->lang->line('panel_title')?></title>

        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <link rel="SHORTCUT ICON" href="<?=base_url("uploads/images/$siteinfos->photo")?>" />

        <link rel="stylesheet" href="<?=base_url('assets/pace/pace.css')?>">

        <!-- <link href="<?php echo base_url('assets/scss/table.css'); ?>" rel="stylesheet"> -->

        <script type="text/javascript" src="<?php echo base_url('assets/inilabs/jquery.min.js'); ?>"></script>
        
        <!-- <script type="text/javascript" src="<?php echo base_url('assets/slimScroll/jquery.slimscroll.min.js'); ?>"></script> -->

        <script type="text/javascript" src="<?php echo base_url('assets/toastr/toastr.min.js'); ?>"></script>


        <link href="<?php echo base_url('assets/bootstrap/bootstrap.min.css'); ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/fonts/font-awesome.css?1.0'); ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/fonts/icomoon.css'); ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/fonts/ini-icon.css'); ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/datatables/dataTables.bootstrap.css'); ?>" rel="stylesheet">

        <link id="headStyleCSSLink" href="<?php echo base_url($backendThemePath.'/style.css?v=1.0'); ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/inilabs/hidetable.css'); ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/inilabs/dashboard.css'); ?>" rel="stylesheet">
        

        <link href="<?php echo base_url('assets/inilabs/inilabs.css'); ?>" rel="stylesheet">

        <link id="headInilabsCSSLink" href="<?php echo base_url($backendThemePath.'/inilabs.css'); ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/inilabs/responsive.css'); ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/toastr/toastr.min.css'); ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/inilabs/mailandmedia.css'); ?>" rel="stylesheet">

        <link rel="stylesheet" href="<?php echo base_url('assets/datatables/buttons.dataTables.min.css'); ?>" >

        <link rel="stylesheet" href="<?php echo base_url('assets/inilabs/combined.css'); ?>" >
        <link rel="stylesheet" href="<?php echo base_url('assets/ajaxloder/ajaxloder.css'); ?>" >

        <style>
      
.mega-menu-auto ul {
  width: 95%;
  margin: 5px auto;
    margin-top: 5px;
  padding-left: 20px;
  max-height: 200px;
  overflow: auto;
  margin-top: 40px;
}
          .mega-menu-auto ul li {
            list-style: none;
            line-height: 28px;
            border-bottom: 1px solid #71c130;
          }
          .mega-menu-auto ul.a {
  list-style-type: circle !important;
}
        </style>

        <?php
            if(isset($headerassets)) {
                foreach ($headerassets as $assetstype => $headerasset) {
                    if($assetstype == 'css') {
                      if(customCompute($headerasset)) {
                        foreach ($headerasset as $keycss => $css) {
                          echo '<link rel="stylesheet" href="'.base_url($css).'">'."\n";
                        }
                      }
                    } elseif($assetstype == 'js') {
                      if(customCompute($headerasset)) {
                        foreach ($headerasset as $keyjs => $js) {
                          echo '<script type="text/javascript" src="'.base_url($js).'"></script>'."\n";
                        }
                      }
                    }
                }
            }
        ?>

        <script type="text/javascript">
          $(window).load(function() {
            $(".se-pre-con").fadeOut("slow");;
          });


           
         $(document).bind('keydown', function (e){
           //Alt+N
           if (e.altKey && (e.which === 78)) {
             
             $('.select2-container--default').addClass('modal-custom');
             
            //  $('#tree2_header_green').hide();
             

             $('#new_search').on('shown.bs.modal', function() {
                $("#searchFilterGreen").focus();
             });
             
             $("#new_search").modal('show');
             $('#new_search .modal-backdrop').remove();
            
             $.ajax({            
                   type: 'post',
                   url: '<?php echo site_url('Dashboard/menu_search')?>',
                   dataType: "html",
                   success: function (response) {                  
                     $('#tree2_header_green').show();
                     $("#tree2_header_green").html(response);
                   }
               });
           }
         
           // alt+a //
           if (e.altKey && (e.which === 65)) {
               var assignment_id = "<?php echo $pid;?>";
               if( assignment_id == 0){
                $('#select_assignment').modal('show');
                $('#new_search').modal('hide');
                $('.select2-container--default').addClass('modal-custom');
              }else{
                alert("It will work in outside Assignment");
              }
              
           }
         });

         $(document).on('keyup', '#searchFilterGreen', function() {
                 // Retrieve the input field text and reset the count to zero
                 var filter = $(this).val(),
                 count = 0;
                 var filterlenth = filter.length;
                 if(filterlenth > 1){
                   $(".mmenus_header_green").show();
                 }else{
                 $(".mmenus_header_green").hide();
                 }
                 // Loop through the comment list
                 $('.mmenus_header_green li').each(function() {
                   // If the list item does not contain the text phrase fade it out
                   if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                     $(this).hide();  // MY CHANGE
         
                     // Show the list item if the phrase matches and increase the count by 1
                   } else {
                     $(this).show(); // MY CHANGE
                     count++;
                   }
         
                 });
         
         });

         
        </script>
    </head>
    <body class="skin-blue fuelux">
        <div class="se-pre-con"></div>
        <div id="loading">
            <img src="<?=base_url('assets/ajaxloder/loader.gif')?>" width="150" height="150"/>
        </div>


        <!-----------Start Menu Search modal----->
       <div id="new_search" class="modal fade home_search " role="dialog"  >
         <div class="modal-dialog ">
            <!-- Modal content-->
            <div class="modal-content">
               <div class="modal-header border-b">
                  <button type="button" class="close reload-c" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title"> Menu Search </h4>
               </div>
               <div class="modal-body">
                  <div class="form-horizontal">
                     <div class="row">
                  <div class=" col-md-12 ">
                     <div class="navigation-search d-flex ">
                        <input id="searchFilterGreen" class="form-control" name="menu_search_1" placeholder="What are you searching for?">
                        <!-- <label> <i class="fa fa-search"></i> </label> -->
                     </div>
                  </div>
                  <div class="mega-menu-auto mmenus_header_green" style="display:none;">
                     <ul id="tree2_header_green">
                     </ul>
                  </div>
               </div>
               </div>
                  </div>
               <div class="clearfix"> </div>
            </div>
         </div>
      </div>
       <!-----------End Menu Search modal----->
  