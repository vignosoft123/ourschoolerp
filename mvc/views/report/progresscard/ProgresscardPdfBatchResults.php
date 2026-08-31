<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-file-pdf-o"></i> Progress Card PDFs</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li><a href="<?=base_url('progresscardreport/index')?>">Progress Report</a></li>
            <li class="active">PDFs</li>
        </ol>
    </div>
    <div class="box-body">
        <?php if(customCompute($generatedPdfs) > 0) { ?>
            <p>
                <b><?=customCompute($generatedPdfs)?></b> student(s) found for
                <b><?=isset($classes[$classesID]) ? $classes[$classesID] : ''?></b> -
                <b><?=isset($sections[$sectionID]) ? $sections[$sectionID] : ''?></b>
                <?php if(!empty($exams[$examID])) { ?> — <b><?=$exams[$examID]?></b><?php } ?>.
                Each PDF is generated on the fly when you click View/Download — nothing is pre-built or stored on the server.
            </p>

            <div style="margin-bottom:14px;">
                <?php if(!empty($zipUrl)) { ?>
                    <a class="btn btn-success" href="<?=$zipUrl?>"><i class="fa fa-file-archive-o"></i> Download All as ZIP</a>
                <?php } ?>
                <?php if(!empty($consolidatedUrl)) { ?>
                    <a class="btn btn-info" target="_blank" href="<?=$consolidatedUrl?>"><i class="fa fa-eye"></i> View Consolidated PDF</a>
                    <a class="btn btn-primary" href="<?=$consolidatedUrl?>?dl=1"><i class="fa fa-download"></i> Download Consolidated PDF</a>
                <?php } ?>
                <a class="btn btn-default" href="<?=base_url('progresscardreport/index')?>"><i class="fa fa-arrow-left"></i> Back to Report</a>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Roll No.</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach($generatedPdfs as $pdf) { ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td><?=$pdf->name?></td>
                            <td><?=$pdf->roll?></td>
                            <td>
                                <a class="btn btn-info btn-xs" target="_blank" href="<?=$pdf->viewUrl?>"><i class="fa fa-eye"></i> View</a>
                                <a class="btn btn-primary btn-xs" href="<?=$pdf->downloadUrl?>"><i class="fa fa-download"></i> Download</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="callout callout-danger">
                <p><b class="text-info">No students found for this class/section/exam.</b></p>
            </div>
            <a class="btn btn-default" href="<?=base_url('progresscardreport/index')?>"><i class="fa fa-arrow-left"></i> Back to Report</a>
        <?php } ?>
    </div>
</div>
