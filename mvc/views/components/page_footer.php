 <style>
       /* Length dropdown */
.dataTables_length label {
    font-weight: 600;
    margin: 0;
}
.dataTables_length select {
    border-radius: 6px !important;
    padding: 4px 8px !important;
    margin-left: 6px;
}

/* Search box */
.dataTables_filter label {
    font-weight: 600;
    margin: 0;
}
.dataTables_filter input {
    border-radius: 6px !important;
    padding: 4px 8px !important;
    margin-left: 6px;
    border: 1px solid #ccc;
}
 

          </style>

</div>

       
        <script type="text/javascript" src="<?php echo base_url('assets/bootstrap/bootstrap.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/inilabs/style.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/jquery.dataTables.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/dataTables.buttons.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/jszip.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/pdfmake.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/vfs_fonts.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/tools/buttons.html5.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/datatables/dataTables.bootstrap.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/inilabs/inilabs.js'); ?>"></script>
        <script type="text/javascript">
          $(document).ready(function () {
            $(document).ajaxStart(function () {
              $("#loading").show();
            }).ajaxStop(function () {
              $("#loading").hide();
            });
          });

          $(document).ready(function () {
            // $('#example3, #example1, #example2').DataTable({
            $('#example3, #example2').DataTable({
              dom : 'Bfrtip',
              buttons : [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
              ],
              search : false
            });
          });
        </script>

        <script type="text/javascript">
          $(function () {
            $("#withoutBtn").dataTable();
          });
        </script>

        <?php if ($this->session->flashdata('success')): ?>
            <script type="text/javascript">
              toastr[ "success" ]("<?=$this->session->flashdata('success');?>");
              toastr.options = {
                "closeButton" : true,
                "debug" : false,
                "newestOnTop" : false,
                "progressBar" : false,
                "positionClass" : "toast-top-right",
                "preventDuplicates" : false,
                "onclick" : null,
                "showDuration" : "500",
                "hideDuration" : "500",
                "timeOut" : "5000",
                "extendedTimeOut" : "1000",
                "showEasing" : "swing",
                "hideEasing" : "linear",
                "showMethod" : "fadeIn",
                "hideMethod" : "fadeOut"
              }
            </script>
        <?php endif ?>
        <?php if ($this->session->flashdata('error')): ?>
            <script type="text/javascript">
              toastr[ "error" ]("<?=$this->session->flashdata('error');?>");
              toastr.options = {
                "closeButton" : true,
                "debug" : false,
                "newestOnTop" : false,
                "progressBar" : false,
                "positionClass" : "toast-top-right",
                "preventDuplicates" : false,
                "onclick" : null,
                "showDuration" : "500",
                "hideDuration" : "500",
                "timeOut" : "5000",
                "extendedTimeOut" : "1000",
                "showEasing" : "swing",
                "hideEasing" : "linear",
                "showMethod" : "fadeIn",
                "hideMethod" : "fadeOut"
              }
            </script>
        <?php endif ?>

        <?php
            if ( isset($footerassets) ) {
                foreach ( $footerassets as $assetstype => $footerasset ) {
                    if ( $assetstype == 'css' ) {
                        if ( customCompute($footerasset) ) {
                            foreach ( $footerasset as $keycss => $css ) {
                                echo '<link rel="stylesheet" href="' . base_url($css) . '">' . "\n";
                            }
                        }
                    } elseif ( $assetstype == 'js' ) {
                        if ( customCompute($footerasset) ) {
                            foreach ( $footerasset as $keyjs => $js ) {
                                echo '<script type="text/javascript" src="' . base_url($js) . '"></script>' . "\n";
                            }
                        }
                    }
                }
            }
        ?>
        
        <script type="text/javascript">
            $("ul.sidebar-menu li").each(function() {
                if($(this).attr('class') === 'active') {
                    $(this).parents('li').addClass('active');
                }
            });

            $(document).ready(function () {
              setTimeout(function () {
                $.ajax({
                  type : 'GET',
                  dataType : "html",
                  async : false,
                  url : "<?=base_url('alert/alert')?>",
                  success : function (data) {
                    $(".my-push-message-list").html(data);
                    var alertNumber = 0;
                    $('.my-push-message-list li').each(function () {
                      alertNumber++;
                    });
                    if (alertNumber > 0) {
                      $('.my-push-message-ul').removeAttr('style');
                      $('.my-push-message-a').append('<span class="label label-danger"><lable class="alert-image">' + alertNumber + '</lable> </span>');
                      $('.my-push-message-number').html('<?=$this->lang->line("la_fs") . " "?>' + alertNumber + '<?=" " . $this->lang->line("la_ls")?>');
                    } else {
                      $('.my-push-message-ul').remove();
                    }
                  }
                });
              }, 5000);
            });

           

 

$('.dataTable').DataTable({
    pageLength: 50,
    lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
    language: { lengthMenu: '_MENU_' },
    dom: '<"dt-top-bar"<"dt-top-left"lf><"dt-top-right"B>>rtip',
    buttons: [
        { extend: 'copyHtml5',  text: '<i class="fa fa-clone"></i> Copy',  className: 'dt-btn dt-btn-copy'  },
        { extend: 'excelHtml5', text: '<i class="fa fa-file-excel-o"></i> Excel', className: 'dt-btn dt-btn-excel' },
        { extend: 'csvHtml5',   text: '<i class="fa fa-file-text-o"></i> CSV',   className: 'dt-btn dt-btn-csv'   },
        { extend: 'pdfHtml5',   text: '<i class="fa fa-file-pdf-o"></i> PDF',    className: 'dt-btn dt-btn-pdf'   }
    ],
    initComplete: function() {
        var $table   = $(this.api().table().node());
        var $wrapper = $table.closest('.dataTables_wrapper');
        var $dtLeft  = $wrapper.find('.dt-top-left');
        var $dtRight = $wrapper.find('.dt-top-right');

        // 1) Student section tabs — move Download Excel button into left bar
        var $tabPane = $wrapper.closest('.tab-pane');
        if ($tabPane.length) {
            var $dlBtn = $tabPane.find('.section-download-btn').first();
            if ($dlBtn.length) {
                var $dlWrap = $dlBtn.parent();
                $dlBtn.detach().appendTo($dtLeft);
                if ($dlWrap.children().length === 0) $dlWrap.remove();
            }
            return;
        }

        // 2) All other modules — move .create-btn to the right side
        // $wrapper is inside #hide-table (or similar); .page-header is a sibling of that parent
        var $createBtn = $wrapper.parent().siblings().find('.create-btn').first();
        if (!$createBtn.length) {
            // fallback: search any ancestor col- container
            $createBtn = $wrapper.closest('[class*="col-"]').find('.create-btn').first();
        }
        if ($createBtn.length && $dtRight.length) {
            var $header = $createBtn.closest('.page-header, h5, h4, h3');
            $createBtn.detach().appendTo($dtRight);
            if ($header.length && $header.children(':not(br)').length === 0) $header.hide();
        }
    }
});


        </script>

        
    </body>
</html>
