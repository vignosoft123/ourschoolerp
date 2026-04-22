<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-book"></i> <?=$this->lang->line('online_exam_answer_key')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url('take_exam/index')?>"><i class="fa fa-list"></i> <?=$this->lang->line('menu_take_exam')?></a></li>
            <li class="active"><?=$this->lang->line('online_exam_answer_key')?></li>
        </ol>
    </div>

    <div id="printablediv">
        <style type="text/css">
            .selected_div {
                background: #27c24c;
                border: thin white solid;
                border-radius: 50%;
                padding: 5px;
            }
            .question-body {
                font-size: 15px;
                font-weight: bold;
                margin-bottom: 4px;
            }
            .question-body p { display: inline; }
            .question-answer { margin-top: 0; }
            .table tr td { width: 50%; }
            .questionimg {
                width: 40% !important;
                padding-left: 10px;
                padding-top: 5px;
                height: 120px;
            }
            .booklet-summary {
                background: #f5f5f5;
                border-radius: 4px;
                padding: 12px 20px;
                margin-bottom: 18px;
            }
            .booklet-summary td { padding: 4px 16px 4px 0; font-size: 14px; }
            .explanation-box {
                background: #fffbe6;
                border-left: 3px solid #f0ad4e;
                padding: 6px 10px;
                margin-top: 6px;
                font-size: 13px;
            }
            @media print {
                .no-print { display: none !important; }
            }
        </style>

        <div class="box-body">
            <!-- Exam header -->
            <div class="row">
                <div class="col-sm-12" style="margin-bottom: 10px;">
                    <h4 class="text-bold"><?=$exam->name?></h4>
                </div>
            </div>

            <!-- Score summary -->
            <div class="booklet-summary">
                <table>
                    <tr>
                        <td><b><?=$this->lang->line('take_exam_total_question')?> :</b> <?=$statusRecord->totalQuestion?></td>
                        <td><b><?=$this->lang->line('take_exam_total_answer')?> :</b> <?=$statusRecord->totalAnswer?></td>
                        <td><b><?=$this->lang->line('take_exam_total_current_answer')?> :</b> <?=$statusRecord->totalCurrectAnswer?></td>
                    </tr>
                    <tr>
                        <td><b><?=$this->lang->line('take_exam_total_mark')?> :</b> <?=$statusRecord->totalMark?></td>
                        <td><b><?=$this->lang->line('take_exam_total_obtained_mark')?> :</b> <?=$statusRecord->totalObtainedMark?></td>
                        <td><b><?=$this->lang->line('take_exam_total_percentage')?> :</b> <?=number_format($statusRecord->totalPercentage, 2)?>%</td>
                    </tr>
                </table>
            </div>

            <!-- Questions -->
            <div class="col-sm-12">
                <?php if(customCompute($questions)) {
                    $i = 0;
                    foreach($questions as $question) {
                        $optionCount = $question->totalOption;
                        $i++;
                ?>
                <div style="margin-bottom: 18px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    <div class="question-body">
                        <label><b><?=$i?>.</b> <?=$question->question?></label>
                        <span class="pull-right text-muted" style="font-size:13px;">[<?=$question->mark?> <?=$this->lang->line('online_exam_question_mark')?>]</span>
                    </div>

                    <?php if($question->upload != '') { ?>
                    <div style="margin-bottom: 6px;">
                        <img style="width:250px;height:150px;padding-left:20px;"
                             src="<?=base_url('uploads/images/'.$question->upload)?>" alt="">
                    </div>
                    <?php } ?>

                    <div class="question-answer">
                        <table class="table">
                            <tr>
                            <?php
                                $oc = 1;
                                $tdCount = 0;
                                $questionoptions = isset($question_options[$question->questionBankID]) ? $question_options[$question->questionBankID] : [];
                                if(customCompute($questionoptions)) {
                                    $optionLabel = 'A';
                                    foreach($questionoptions as $option) {
                                        if($optionCount >= $oc) {
                                            $oc++;
                                            $userChose   = isset($examquestionsuseranswer[$question->questionBankID]) && $option->optionID == $examquestionsuseranswer[$question->questionBankID]->optionID;
                                            $isCorrect   = isset($examquestionsanswer[$question->questionBankID]) && $option->optionID == $examquestionsanswer[$question->questionBankID]->optionID;

                                            if($userChose && $isCorrect) {
                                                // Student chose correctly — green
                                                echo '<td style="background:#27c24c;color:#fff;">';
                                                echo '<span>'.$optionLabel.'.</span> <span>'.$option->name.'</span>';
                                            } elseif($userChose && !$isCorrect) {
                                                // Student chose wrongly — red
                                                echo '<td style="background:#f05050;color:#fff;">';
                                                echo '<span>'.$optionLabel.'.</span> <span>'.$option->name.'</span>';
                                            } elseif(!$userChose && $isCorrect) {
                                                // Correct answer student missed — checkmark
                                                echo '<td>';
                                                echo '<span>'.$optionLabel.'.</span> <span>'.$option->name.'</span>';
                                                echo ' <span class="selected_div"><i class="fa fa-check text-white"></i></span>';
                                            } else {
                                                echo '<td>';
                                                echo '<span>'.$optionLabel.'.</span> <span>'.$option->name.'</span>';
                                            }

                                            if(!is_null($option->img) && $option->img != '') {
                                                echo '<label><img class="questionimg" src="'.base_url('uploads/images/'.$option->img).'"/></label>';
                                            }
                                            echo '</td>';
                                            $optionLabel++;
                                            $tdCount++;
                                            if($tdCount == 2) {
                                                $tdCount = 0;
                                                echo '</tr><tr>';
                                            }
                                        }
                                    }
                                }
                            ?>
                            </tr>
                        </table>
                    </div>

                    <?php if(!empty($question->explanation)) { ?>
                    <div class="explanation-box">
                        <i class="fa fa-lightbulb-o"></i> <b>Explanation:</b> <?=$question->explanation?>
                    </div>
                    <?php } ?>
                </div>
                <?php } } else { ?>
                <div class="callout callout-warning">
                    <p>No questions found for this exam.</p>
                </div>
                <?php } ?>
            </div>

            <!-- Legend -->
            <div class="col-sm-12 no-print" style="margin: 10px 0 15px;">
                <span style="background:#27c24c;color:#fff;padding:3px 10px;border-radius:3px;margin-right:8px;">Your Correct Answer</span>
                <span style="background:#f05050;color:#fff;padding:3px 10px;border-radius:3px;margin-right:8px;">Your Wrong Answer</span>
                <span style="border:1px solid #ccc;padding:3px 10px;border-radius:3px;margin-right:8px;">
                    <span class="selected_div" style="padding:3px 5px;"><i class="fa fa-check text-white"></i></span> Correct Answer (Not Chosen)
                </span>
            </div>
        </div><!-- box-body -->
    </div><!-- printablediv -->

    <div class="box-footer no-print">
        <button onclick="printDiv('printablediv')" class="btn btn-default">
            <i class="fa fa-print"></i> <?=$this->lang->line('report_print')?>
        </button>
        <a href="<?=base_url('take_exam/index')?>" class="btn btn-primary" style="margin-left:8px;">
            <i class="fa fa-arrow-left"></i> Back to Exam List
        </a>
    </div>
</div>

<script type="text/javascript">
    function printDiv(divID) {
        var oldPage = document.body.innerHTML;
        var divElements = document.getElementById(divID).innerHTML;
        document.body.innerHTML = '<html><head><title></title></head><body>' + divElements + '</body>';
        window.print();
        document.body.innerHTML = oldPage;
        window.location.reload();
    }
</script>
