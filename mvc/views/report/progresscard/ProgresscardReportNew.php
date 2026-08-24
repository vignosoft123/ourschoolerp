<link rel="stylesheet" href="/assets/css/report-buttons.css">
<style>
#printablediv_new, #students_div_new { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }

.pcn-card { max-width: 980px; margin: 0 auto 32px; border-radius: 14px; overflow: hidden; background: #fff; box-shadow: 0 4px 22px rgba(20,30,70,0.10); border: 3px solid #1a237e; font-weight: 600; }

/* ---- Header band ---- */
.pcn-header { display: flex; align-items: center; gap: 18px; padding: 20px 24px; background: linear-gradient(135deg,#eef1fb 0%,#e3e8fb 100%); border-bottom: 4px solid #1a237e; }
/* background-image divs, not <img object-fit:cover> — html2canvas (Download JPEG button)
   doesn't reliably support object-fit, same gotcha already worked around in the ID Card report. */
.pcn-header-logo { width: 68px; height: 68px; border-radius: 50%; background-size: cover; background-position: center center; background-repeat: no-repeat; flex-shrink: 0; border: 2px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.12); }
.pcn-header-info { flex: 1; }
.pcn-header-info h2 { margin: 0 0 3px; font-size: 22px; color: #1a237e; font-weight: 800; letter-spacing: 0.2px; }
.pcn-header-info p { margin: 0; font-size: 12px; color: #5c6b8a; line-height: 1.5; }
.pcn-header-photo { width: 68px; height: 68px; border-radius: 50%; background-size: cover; background-position: center center; background-repeat: no-repeat; border: 3px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.18); flex-shrink: 0; }
.pcn-year-box { background: linear-gradient(160deg,#1a237e,#0d1550); color: #fff; border-radius: 10px; padding: 10px 16px; font-size: 11px; min-width: 200px; flex-shrink: 0; box-shadow: 0 3px 10px rgba(26,35,126,0.35); }
.pcn-year-box .pcn-year-label { opacity: 0.75; letter-spacing: 0.6px; font-size: 10px; }
.pcn-year-box .pcn-year-big { font-size: 20px; font-weight: 800; margin: 2px 0 8px; letter-spacing: 0.3px; color: #ffb300 !important; }
.pcn-year-box div { margin: 3px 0; opacity: 0.95; }

.pcn-title-band { background: linear-gradient(90deg,#1a237e,#283593); color: #fff !important; text-align: center; font-weight: 800; font-size: 13px; padding: 10px; letter-spacing: 0.8px; }

.pcn-body { padding: 22px 24px 26px; background: #fbfcff; }
.pcn-row { display: flex; flex-wrap: wrap; gap: 18px; margin-bottom: 18px; }

/* ---- Section cards ---- */
.pcn-box { position: relative; flex: 1; min-width: 260px; border: 1px solid #e6e9f2; border-radius: 12px; padding: 26px 0 16px; background: #fff; box-shadow: 0 2px 8px rgba(20,30,70,0.05); }
.pcn-box-body { padding: 0 18px; }

/* Floating pill-style section header, overlapping the card's top edge */
.pcn-box h4 { position: absolute; top: -14px; left: 16px; z-index: 2; display: inline-flex; align-items: center; gap: 8px; margin: 0; padding: 7px 16px; border-radius: 20px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.6px; color: #fff !important; font-weight: 700; white-space: nowrap; box-shadow: 0 3px 9px rgba(0,0,0,0.22); }
.pcn-box h4 span.pcn-h-text { color: #fff !important; }
.pcn-h-icon { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 50%; background: rgba(255,255,255,0.28); flex-shrink: 0; font-size: 11px; }

.pcn-h--navy   { background: linear-gradient(90deg,#1a237e,#283593); }
.pcn-h--purple { background: linear-gradient(90deg,#4a148c,#6a1b9a); }
.pcn-h--teal   { background: linear-gradient(90deg,#00695c,#00897b); }
.pcn-h--green  { background: linear-gradient(90deg,#1b5e20,#2e7d32); }
.pcn-h--slate  { background: linear-gradient(90deg,#37474f,#546e7a); }
.pcn-h--orange { background: linear-gradient(90deg,#c2410c,#e65100); }

/* ---- Colored top border per box, matching its header accent (the "legend" framing) ---- */
.pcn-box.pcn-b--navy   { border-top: 4px solid #1a237e; }
.pcn-box.pcn-b--purple { border-top: 4px solid #6a1b9a; }
.pcn-box.pcn-b--teal   { border-top: 4px solid #00897b; }
.pcn-box.pcn-b--green  { border-top: 4px solid #2e7d32; }
.pcn-box.pcn-b--slate  { border-top: 4px solid #546e7a; }
.pcn-box.pcn-b--orange { border-top: 4px solid #e65100; }

.pcn-info-line { display: flex; align-items: center; justify-content: space-between; font-size: 13px; padding: 7px 0; border-bottom: 1px dashed #eef0f6; }
.pcn-info-line:last-child { border-bottom: none; }
.pcn-info-line > span:first-child { color: #37474f !important; font-weight: 700; display: flex; align-items: center; }
.pcn-info-line > span:last-child { font-weight: 700; color: #1a237e !important; }
.pcn-info-icon { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #1a237e; color: #fff !important; font-size: 12px; margin-right: 9px; flex-shrink: 0; box-shadow: 0 2px 5px rgba(26,35,126,0.3); }

/* ---- Stat tiles: each is its own elevated mini-card ---- */
.pcn-stats-grid { display: flex; flex-wrap: wrap; gap: 14px; }
.pcn-stat { flex: 1; min-width: 108px; background: #fff; border: 1px solid #e6e9f5; border-radius: 12px; padding: 14px 8px; text-align: center; box-shadow: 0 3px 10px rgba(20,30,70,0.10); }
.pcn-stat .pcn-stat-label { font-size: 10px; text-transform: uppercase; color: #8792a8 !important; letter-spacing: 0.5px; font-weight: 700; }
.pcn-stat .pcn-stat-value { font-size: 19px; font-weight: 800; color: #1a237e !important; margin-top: 4px; }

/* ---- Result Summary: 2x2 stat grid beside the Attendance donut (matches static layout) ---- */
.pcn-result-layout { display: flex; gap: 14px; align-items: stretch; flex-wrap: wrap; }
.pcn-stats-grid-2x2 { flex: 1.5; min-width: 220px; display: flex; flex-wrap: wrap; gap: 14px; }
.pcn-stats-grid-2x2 .pcn-stat { flex: 1 1 calc(50% - 7px); min-width: 110px; }

/* Standalone Attendance panel, sits beside the 2x2 grid */
.pcn-att-panel { flex: 1; min-width: 160px; background: #fff; border: 1px solid #e6e9f5; border-radius: 12px; padding: 16px 8px; text-align: center; box-shadow: 0 3px 10px rgba(20,30,70,0.10); display: flex; flex-direction: column; align-items: center; justify-content: center; }
.pcn-att-panel .pcn-stat-label { font-size: 11px; text-transform: uppercase; color: #8792a8 !important; letter-spacing: 0.6px; font-weight: 700; }

.pcn-ring { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 4px auto 2px; }
.pcn-ring span { background: #fff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #1a237e !important; }
.pcn-ring--lg { width: 108px; height: 108px; margin: 8px auto 2px; box-shadow: 0 2px 10px rgba(20,30,70,0.12); }
.pcn-ring--lg span { width: 82px; height: 82px; font-size: 19px; }

.pcn-stat-icon { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 6px; color: #fff !important; font-size: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.18); }

/* ---- Per-month attendance % mini bar ---- */
.pcn-att-bar-wrap { width: 100%; height: 5px; background: #eef0f6; border-radius: 3px; margin-top: 5px; overflow: hidden; }
.pcn-att-bar { height: 100%; border-radius: 3px; }

/* ---- Grade Scale legend dot ---- */
.pcn-legend-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 6px; vertical-align: middle; }

.pcn-remark-sub { font-size: 12px; color: #5c6b8a !important; margin-top: 6px; line-height: 1.5; }

/* ---- Tables ---- */
.pcn-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.pcn-table th { background: #1a237e; color: #fff !important; padding: 9px 10px; text-align: center; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; }
.pcn-table td { padding: 9px 10px; text-align: center; border-bottom: 1px solid #f0f1f7; color: #37474f !important; }
.pcn-table tbody tr:nth-child(even) { background: #fafbff; }
.pcn-table tbody tr:hover { background: #f1f4ff; }

.pcn-subject-badge { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; color: #fff !important; font-size: 10px; font-weight: 800; margin-right: 8px; vertical-align: middle; box-shadow: 0 2px 5px rgba(0,0,0,0.18); }
.pcn-status-ok { color: #2e7d32 !important; font-weight: 800; font-size: 15px; }
.pcn-status-absent { color: #c62828 !important; font-weight: 800; font-size: 15px; }
.pcn-grade-chip { padding: 4px 13px; border-radius: 20px; font-weight: 800; font-size: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.12); }

.pcn-remarks-qr { display: flex; gap: 16px; flex-wrap: wrap; }
.pcn-qr-box { text-align: center; min-width: 140px; }
.pcn-qr-box img { width: 92px; height: 92px; border: 1px solid #e6e9f2; border-radius: 8px; padding: 4px; background: #fff; }
.pcn-signatures { display: flex; justify-content: space-between; margin-top: 26px; padding: 0 6px; font-size: 12px; color: #444 !important; font-weight: 600; }
.pcn-charts-row { display: flex; flex-wrap: wrap; gap: 10px; }
.pcn-chart-box { flex: 1; min-width: 260px; height: 250px; }

/* ---- Remarks & scan-to-verify content ---- */
.pcn-remark-text { font-size: 16px; font-weight: 800; margin: 4px 0 0; }
.pcn-qr-caption { font-size: 11px; color: #8792a8 !important; margin: 8px 0 0; }

/* ---- WhatsApp / SMS student picker (must survive the theme's global `label{color:#fff}` rule) ---- */
#students_div_new { background: #fff; border: 1px solid #dfe3ee; border-radius: 10px; padding: 14px 16px; box-shadow: 0 2px 8px rgba(20,30,70,0.06); }
#students_div_new .pcn-picker-title { font-weight: 700; font-size: 14px; color: #263238 !important; display: block; margin-bottom: 10px; }
#students_div_new .pcn-picker-list { display: flex; flex-wrap: wrap; gap: 10px; padding: 10px; border: 1px solid #eceef4; border-radius: 8px; background: #f9fafc; max-height: 260px; overflow-y: auto; margin-bottom: 12px; }
#students_div_new .pcn-picker-item { display: flex; align-items: center; background: #fff; border: 1px solid #d7dbe8; border-radius: 6px; padding: 7px 12px; cursor: pointer; color: #263238 !important; font-size: 13px; }
#students_div_new .pcn-picker-item:hover { background: #eef1ff; border-color: #c2cafb; }
#students_div_new .pcn-picker-item input { margin-right: 8px; }
</style>

<?php
    $pdf_preview_uri = base_url('progresscardreport/pdf/'.$classesID.'/'.$sectionID.'/'.$studentID);

    // Display-only helper: pick a representative FontAwesome icon for a subject name,
    // falling back to null (2-letter abbreviation badge) when nothing matches.
    // Purely cosmetic — does not affect subject_badge()'s color or any other logic.
    if (!function_exists('pcn_subject_icon')) {
        function pcn_subject_icon($subjectName) {
            $name = mb_strtolower(trim((string)$subjectName));
            $map  = array(
                'english'  => 'fa-book',
                'hindi'    => 'fa-font',
                'telugu'   => 'fa-font',
                'sanskrit' => 'fa-font',
                'math'     => 'fa-calculator',
                'science'  => 'fa-flask',
                'social'   => 'fa-globe',
                'evs'      => 'fa-leaf',
                'computer' => 'fa-laptop',
                'art'      => 'fa-paint-brush',
                'music'    => 'fa-music',
                'physical' => 'fa-futbol-o',
                'games'    => 'fa-futbol-o',
                'moral'    => 'fa-heart',
                'g.k'      => 'fa-lightbulb-o',
                'gk'       => 'fa-lightbulb-o',
            );
            foreach ($map as $needle => $icon) {
                if (strpos($name, $needle) !== false) return $icon;
            }
            return null;
        }
    }

    // Display-only legend colors for the static Grade Scale ranges — mirrors the exact
    // thresholds/colors already used by progresscard_resolve_grade() for a given percent.
    $pcnGradeLegendColors = array(
        'A+' => '#2e7d32', 'A' => '#388e3c', 'B+' => '#0288d1', 'B' => '#039be5',
        'C+' => '#fbc02d', 'C' => '#f57c00', 'D' => '#d32f2f',
    );
?>
<div class="rpt-action-bar no-print">
    <a class="btn btn-default rpt-action-btn" target="_blank" href="<?=base_url('progresscardreport/pdf_new/'.$classesID.'/'.$sectionID.'/'.(isset($studentID) ? $studentID : 0).'/'.$examID)?>"><i class="fa fa-print"></i> Print</a>
    <button type="button" class="btn btn-default rpt-action-btn sendSms_new"><i class="fa fa-send"></i> Send SMS</button>
    <button type="button" class="btn btn-default rpt-action-btn sendWhatsapp_btn_new"><i class="fa fa-whatsapp"></i> Send Whatsapp New</button>
    <button type="button" id="pcnDownloadJpegBtn" class="btn btn-default rpt-action-btn"><i class="fa fa-image"></i> Download JPEG</button>
    <button type="button" id="pcnDownloadZipBtn" class="btn btn-default rpt-action-btn"><i class="fa fa-file-archive-o"></i> Download All as JPEG ZIP</button>
</div>

<div class="no-print" id="students_div_new" style="display:none; margin-top:14px; margin-bottom:14px;">
    <span class="pcn-picker-title">Select Student(s)</span>
    <div class="pcn-picker-list">
        <?php foreach((array)$students as $stud): ?>
            <span class="pcn-picker-item" onclick="var c=$(this).find('input'); c.prop('checked', !c.prop('checked'));">
                <input type="checkbox" class="stud_id_new" value="<?=$stud->srstudentID?>" onclick="event.stopPropagation();">
                <span><?=$stud->srname?></span>
            </span>
        <?php endforeach; ?>
    </div>
    <span class="error text-danger" id="stud_error_new" style="font-size:13px; color:#c62828 !important;"></span>
    <br>
    <button type="button" class="btn btn-success sendWhatsapp_new" style="background:#25d366; border:none; margin-top:10px;">
        <i class="fa fa-whatsapp"></i> Send Report to WhatsApp
    </button>
</div>

<div class="box">
    <div class="rpt-box-header">
        <h3><i class="fa fa-clipboard"></i> Progress Card (New Design) - <?=isset($exams[$examID]) ? $exams[$examID] : ''?></h3>
    </div>

    <div id="printablediv_new">
        <div class="box-body">
            <form id="marksForm_new">
            <?php if(customCompute($students)) { $cardIndex = 0; foreach($students as $student) { $cardIndex++; ?>

                <?php
                    $totalMaxMarks = 0;
                    $totalObtained = 0;
                    $subjectRows   = [];

                    if(customCompute($mandatorySubjects)) {
                        foreach($mandatorySubjects as $subj) {
                            $totalMaxMarks += $subj->max_mark;
                            $subjTotal = 0;
                            $isAbsent  = false;

                            if(customCompute($settingExam)) {
                                foreach($settingExam as $seExamID) {
                                    $uniquepercentageArr = isset($markpercentagesclassArr[$seExamID][$subj->subjectID]) ? $markpercentagesclassArr[$seExamID][$subj->subjectID] : [];
                                    $markpercentages = [];
                                    if(customCompute($uniquepercentageArr)) {
                                        $markpercentages = $uniquepercentageArr[(($settingmarktypeID==4)||($settingmarktypeID==6)) ? 'unique' : 'own'];
                                    }
                                    if(customCompute($markpercentages)) {
                                        foreach($markpercentages as $markpercentageID) {
                                            $subjTotal += isset($markArray[$seExamID][$student->srstudentID]['markpercentageMark'][$subj->subjectID][$markpercentageID])
                                                ? $markArray[$seExamID][$student->srstudentID]['markpercentageMark'][$subj->subjectID][$markpercentageID] : 0;
                                        }
                                    }
                                    if(isset($eattendanceArray[$seExamID][$student->srstudentID][$subj->subjectID]) && $eattendanceArray[$seExamID][$student->srstudentID][$subj->subjectID] == 'Absent') {
                                        $isAbsent  = true;
                                        $subjTotal = 0;
                                    }
                                }
                            }

                            $subjectRows[] = array('subject' => $subj->subject, 'max' => $subj->max_mark, 'obtained' => $subjTotal, 'absent' => $isAbsent);
                            $totalObtained += $subjTotal;
                        }
                    }

                    // Comma-separated "SUBJ=obtained/max," string — feeds the existing
                    // send_marks_to_sms()/send_marks_to_whatsapp() message templates.
                    $marksTemplateStr = '';
                    foreach($subjectRows as $row) {
                        $marksTemplateStr .= substr($row['subject'], 0, 3).'='.($row['absent'] ? 0 : $row['obtained']).'/'.$row['max'].',';
                    }

                    $percent   = $totalMaxMarks > 0 ? round(($totalObtained / $totalMaxMarks) * 100, 2) : 0;
                    $hasZeroMark = false;
                    foreach($subjectRows as $row) {
                        if ($row['absent'] || $row['obtained'] == 0) { $hasZeroMark = true; break; }
                    }
                    $gradeInfo = progresscard_resolve_grade($percent, $hasZeroMark);
                    $attInfo   = isset($attendanceByStudent[$student->srstudentID]) ? $attendanceByStudent[$student->srstudentID] : array('months'=>[], 'totalWorkingDays'=>0,'totalPresent'=>0,'totalAbsent'=>0,'yearlyPercentage'=>0);

                    // Remarks — same auto-generated message as the old progress card, for the same percentage.
                    if ($percent >= 90) { $remarkText = 'Excellent'; $remarkColor = '#2e7d32'; }
                    elseif ($percent >= 80) { $remarkText = 'Very Good'; $remarkColor = '#28a745'; }
                    elseif ($percent >= 70) { $remarkText = 'Good'; $remarkColor = '#007bff'; }
                    elseif ($percent >= 60) { $remarkText = 'Fair'; $remarkColor = '#ffc107'; }
                    elseif ($percent >= 50) { $remarkText = 'Average'; $remarkColor = '#fb8c00'; }
                    else { $remarkText = 'Need Improvement'; $remarkColor = '#e53935'; }

                    // QR generation moved next to its (disabled) display block below — see the
                    // "Scan to Verify" comment there. It used to run unconditionally here even
                    // though the display was disabled, writing a PNG per student per exam into
                    // uploads/progresscardQRcode/ on every report view for no reason.

                    $chartUid = 'pcn'.$cardIndex.'_'.$student->srstudentID;
                ?>

                <input type="hidden" name="st_ids[]" value="<?=$student->studentID?>">
                <input type="hidden" name="st_names[]" value="<?=$student->name?>">
                <input type="hidden" name="mobile_no[]" value="<?=$student->phone?>">
                <input type="hidden" name="exam_name[]" value="<?=isset($exams[$examID]) ? $exams[$examID] : ''?>">
                <input type="hidden" name="total_marks[]" value="<?=ini_round($totalObtained)?>/<?=ini_round($totalMaxMarks)?>">
                <input type="hidden" name="marks_grade[]" value="<?=$gradeInfo['grade']?>">
                <input type="hidden" name="marks_template[]" value="<?=$marksTemplateStr?>">

                <div class="pcn-card" id="pcn-card-<?=$student->srstudentID?>">
                    <div class="pcn-header">
                        <?php if($siteinfos->photo) { ?>
                            <div class="pcn-header-logo" style="background-image:url('<?=base_url('uploads/images/'.$siteinfos->photo)?>');"></div>
                        <?php } ?>
                        <div class="pcn-header-info">
                            <h2><?=$siteinfos->sname?></h2>
                            <p><?=$siteinfos->address?></p>
                            <p><?=$siteinfos->email?> &nbsp;|&nbsp; <?=$siteinfos->phone?></p>
                        </div>
                        <div class="pcn-header-photo" style="background-image:url('<?=imagelink($student->photo)?>');"></div>
                        <div class="pcn-year-box">
                            <div class="pcn-year-label">ACADEMIC YEAR</div>
                            <div class="pcn-year-big"><?=isset($schoolyearsessionobj->schoolyear) ? $schoolyearsessionobj->schoolyear : ''?></div>
                            <div>Generated On : <?=date('d M Y')?></div>
                            <div>Exam Name : <?=isset($exams[$examID]) ? $exams[$examID] : ''?></div>
                            <div>Class Teacher : <?=$classTeacher ? $classTeacher : '-'?></div>
                        </div>
                    </div>

                    <div class="pcn-title-band">
                        <?=isset($exams[$examID]) ? strtoupper($exams[$examID]) : ''?> PROGRESS CARD REPORT
                    </div>

                    <div class="pcn-body">
                        <div class="pcn-row">
                            <div class="pcn-box pcn-b--navy">
                                <h4 class="pcn-h--navy"><span class="pcn-h-icon"><i class="fa fa-user"></i></span><span class="pcn-h-text">Student Information</span></h4>
                                <div class="pcn-box-body">
                                <div class="pcn-info-line"><span><span class="pcn-info-icon"><i class="fa fa-user"></i></span>Student Name</span><span><?=$student->srname?></span></div>
                                <div class="pcn-info-line"><span><span class="pcn-info-icon"><i class="fa fa-bookmark"></i></span>Reg. No.</span><span><?=$student->srregisterNO?></span></div>
                                <div class="pcn-info-line"><span><span class="pcn-info-icon"><i class="fa fa-graduation-cap"></i></span>Class</span><span><?=isset($classes[$student->srclassesID]) ? $classes[$student->srclassesID] : ''?></span></div>
                                <div class="pcn-info-line"><span><span class="pcn-info-icon"><i class="fa fa-users"></i></span>Section</span><span><?=isset($sections[$student->srsectionID]) ? $sections[$student->srsectionID] : ''?></span></div>
                                <div class="pcn-info-line"><span><span class="pcn-info-icon"><i class="fa fa-list-ol"></i></span>Roll No.</span><span><?=$student->srroll?></span></div>
                                <div class="pcn-info-line"><span><span class="pcn-info-icon"><i class="fa fa-male"></i></span>Father Name</span><span><?=$student->father_name?></span></div>
                                </div>
                            </div>

                            <div class="pcn-box pcn-b--navy" style="flex:1.4;">
                                <h4 class="pcn-h--navy"><span class="pcn-h-icon"><i class="fa fa-bar-chart"></i></span><span class="pcn-h-text">Result Summary</span></h4>
                                <div class="pcn-box-body">
                                <div class="pcn-result-layout">
                                    <div class="pcn-stats-grid-2x2">
                                        <div class="pcn-stat">
                                            <div class="pcn-stat-icon" style="background:#3949ab;"><i class="fa fa-clipboard"></i></div>
                                            <div class="pcn-stat-label">Total Marks</div>
                                            <div class="pcn-stat-value"><?=ini_round($totalObtained)?>/<?=ini_round($totalMaxMarks)?></div>
                                        </div>
                                        <div class="pcn-stat">
                                            <div class="pcn-stat-icon" style="background:#2e7d32;"><i class="fa fa-pie-chart"></i></div>
                                            <div class="pcn-stat-label">Percentage</div>
                                            <div class="pcn-stat-value"><?=$percent?>%</div>
                                        </div>
                                        <div class="pcn-stat">
                                            <div class="pcn-stat-icon" style="background:<?=$gradeInfo['color']?>;"><i class="fa fa-certificate"></i></div>
                                            <div class="pcn-stat-label">Grade</div>
                                            <div class="pcn-stat-value"><span class="pcn-grade-chip" style="background:<?=$gradeInfo['bg']?>;color:<?=$gradeInfo['color']?>;"><?=$gradeInfo['grade']?></span></div>
                                        </div>
                                        <div class="pcn-stat">
                                            <div class="pcn-stat-icon" style="background:#8e24aa;"><i class="fa fa-trophy"></i></div>
                                            <div class="pcn-stat-label">Rank (Class)</div>
                                            <div class="pcn-stat-value"><?=($student->rank !== null && $student->rank !== '') ? $student->rank : '-'?></div>
                                        </div>
                                    </div>
                                    <?php if($is_display_attendance > 0) { ?>
                                    <div class="pcn-att-panel">
                                        <div class="pcn-stat-label">Overall Attendance</div>
                                        <div class="pcn-ring pcn-ring--lg" style="background:conic-gradient(#1a237e <?=$attInfo['yearlyPercentage']?>%, #e3e6f0 0);"><span><?=$attInfo['yearlyPercentage']?>%</span></div>
                                    </div>
                                    <?php } ?>
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="pcn-row">
                            <div class="pcn-box pcn-b--purple" style="flex:2; min-width:400px;">
                                <h4 class="pcn-h--purple"><span class="pcn-h-icon"><i class="fa fa-book"></i></span><span class="pcn-h-text">Subject Wise Performance</span></h4>
                                <div class="pcn-box-body">
                                <table class="pcn-table">
                                    <thead>
                                        <tr><th>Subject</th><th>Max Marks</th><th>Obtained</th><th>Status</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($subjectRows as $row) {
                                            $badge      = subject_badge($row['subject']);
                                            $subjIcon   = pcn_subject_icon($row['subject']);
                                        ?>
                                        <tr>
                                            <td style="text-align:left;">
                                                <span class="pcn-subject-badge" style="background:<?=$badge['color']?>;"><?=$subjIcon ? '<i class="fa '.$subjIcon.'"></i>' : $badge['abbr']?></span>
                                                <?=$row['subject']?>
                                            </td>
                                            <td><?=$row['max']?></td>
                                            <td><?=$row['absent'] ? '<span class="pcn-status-absent">Absent</span>' : ini_round($row['obtained'])?></td>
                                            <td><?=$row['absent'] ? '<span class="pcn-status-absent">&#10008;</span>' : '<span class="pcn-status-ok">&#10004;</span>'?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                </div>
                            </div>

                            <div class="pcn-box pcn-b--slate">
                                <h4 class="pcn-h--slate"><span class="pcn-h-icon"><i class="fa fa-list"></i></span><span class="pcn-h-text">Grade Scale</span></h4>
                                <div class="pcn-box-body">
                                <table class="pcn-table">
                                    <thead><tr><th>Range</th><th>Grade</th><th>Remark</th></tr></thead>
                                    <tbody>
                                        <?php foreach(progresscard_grade_scale() as $gs) {
                                            $legendColor = isset($pcnGradeLegendColors[$gs['grade']]) ? $pcnGradeLegendColors[$gs['grade']] : '#90a4ae';
                                        ?>
                                        <tr><td><span class="pcn-legend-dot" style="background:<?=$legendColor?>;"></span><?=$gs['range']?></td><td><?=$gs['grade']?></td><td><?=$gs['label']?></td></tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>

                        <div class="pcn-row">
                            <div class="pcn-box pcn-b--teal">
                                <h4 class="pcn-h--teal"><span class="pcn-h-icon"><i class="fa fa-users"></i></span><span class="pcn-h-text">Class Performance</span></h4>
                                <div class="pcn-box-body">
                                <div class="pcn-stats-grid">
                                    <div class="pcn-stat">
                                        <div class="pcn-stat-label">Your Percentage</div>
                                        <div class="pcn-stat-value" style="color:#2e7d32 !important;"><?=$percent?>%</div>
                                    </div>
                                    <div class="pcn-stat">
                                        <div class="pcn-stat-label">Class Average</div>
                                        <div class="pcn-stat-value"><?=round($classPerf['classAveragePct'], 2)?>%</div>
                                    </div>
                                    <div class="pcn-stat">
                                        <div class="pcn-stat-label">Class Strength</div>
                                        <div class="pcn-stat-value"><?=$classPerf['totalStudents']?></div>
                                    </div>
                                </div>
                                </div>
                            </div>

                            <?php if($is_display_attendance > 0) { ?>
                            <div class="pcn-box pcn-b--green" style="flex:2;">
                                <h4 class="pcn-h--green"><span class="pcn-h-icon"><i class="fa fa-calendar-check-o"></i></span><span class="pcn-h-text">Attendance Summary (Academic Year: <?=isset($attInfo['schoolyear']) ? $attInfo['schoolyear'] : ''?>)</span></h4>
                                <div class="pcn-box-body">
                                <div style="overflow-x:auto;">
                                <table class="pcn-table">
                                    <thead>
                                        <tr>
                                            <th style="text-align:left;">Month</th>
                                            <?php foreach($attInfo['months'] as $m) { ?><th><?=$m['label']?></th><?php } ?>
                                            <th>Total (Year)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align:left;">Working Days</td>
                                            <?php foreach($attInfo['months'] as $m) { ?><td><?=$m['workingDays']?></td><?php } ?>
                                            <td><b><?=$attInfo['totalWorkingDays']?></b></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;">Present</td>
                                            <?php foreach($attInfo['months'] as $m) { ?><td><?=$m['present']?></td><?php } ?>
                                            <td><b><?=$attInfo['totalPresent']?></b></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;">Absent</td>
                                            <?php foreach($attInfo['months'] as $m) { ?><td><?=$m['absent']?></td><?php } ?>
                                            <td><b><?=$attInfo['totalAbsent']?></b></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align:left;">Attendance %</td>
                                            <?php foreach($attInfo['months'] as $m) {
                                                $mPct = $m['workingDays'] > 0 ? round(($m['present'] / $m['workingDays']) * 100) : 0;
                                                $barColor = $mPct >= 90 ? '#2e7d32' : ($mPct >= 75 ? '#fbc02d' : '#e53935');
                                            ?>
                                            <td>
                                                <?=$mPct?>%
                                                <div class="pcn-att-bar-wrap"><div class="pcn-att-bar" style="width:<?=$mPct?>%; background:<?=$barColor?>;"></div></div>
                                            </td>
                                            <?php } ?>
                                            <td><b><?=$attInfo['yearlyPercentage']?>%</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                                </div>
                                <p style="text-align:right; margin:8px 0 0; font-weight:700; color:#1a237e !important;">Yearly Attendance : <?=$attInfo['yearlyPercentage']?>%</p>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="pcn-row no-print">
                            <div class="pcn-box pcn-b--navy" style="flex:1;">
                                <h4 class="pcn-h--navy"><span class="pcn-h-icon"><i class="fa fa-line-chart"></i></span><span class="pcn-h-text">Performance Overview</span></h4>
                                <div class="pcn-box-body">
                                <div class="pcn-charts-row">
                                    <div class="pcn-chart-box" id="chart-subjects-<?=$chartUid?>"></div>
                                    <div class="pcn-chart-box" id="chart-distribution-<?=$chartUid?>"></div>
                                    <div class="pcn-chart-box" id="chart-gradehist-<?=$chartUid?>"></div>
                                </div>
                                </div>
                            </div>
                        </div>

                        <?php
                            $remarkSuggestions = array(
                                'Excellent'          => 'Keep up the outstanding work — maintain this consistency.',
                                'Very Good'          => 'Great progress — a little more focus can take you to the top.',
                                'Good'               => 'Solid effort — keep practicing regularly to improve further.',
                                'Fair'               => 'Focus on regular study and revision to strengthen your basics.',
                                'Average'            => 'Focus on regular study and revision. Participate more in class activities.',
                                'Need Improvement'   => 'Needs regular attention — please consult subject teachers for extra support.',
                            );
                            $remarkSuggestion = isset($remarkSuggestions[$remarkText]) ? $remarkSuggestions[$remarkText] : '';
                        ?>
                        <div class="pcn-row">
                            <div class="pcn-box pcn-b--orange" style="flex:2;">
                                <h4 class="pcn-h--orange"><span class="pcn-h-icon"><i class="fa fa-comment"></i></span><span class="pcn-h-text">Teacher's Remarks</span></h4>
                                <div class="pcn-box-body">
                                <p class="pcn-remark-text" style="color:<?=$remarkColor?> !important;"><?=$remarkText?></p>
                                <?php if ($remarkSuggestion) { ?><p class="pcn-remark-sub"><?=$remarkSuggestion?></p><?php } ?>
                                </div>
                            </div>
                            <?php /* "Scan to Verify" QR box disabled per request (2026-08-22) — not needed on
                                     screen/JPEG for now, verify page isn't built yet either. QR generation
                                     (writes a PNG to uploads/progresscardQRcode/) lives inside this same
                                     disabled block so nothing is written while the feature is off. Flip to
                                     `if (true)` to bring the whole thing back. */
                            if (false) {
                                $qrFilename = 'pc-'.$student->srstudentID.'-'.$examID;
                                $qrText     = base_url('progresscardreport/verify/'.$student->srregisterNO);
                                $qrFilepath = FCPATH.'uploads/progresscardQRcode/'.$qrFilename.'.png';
                                if(!file_exists($qrFilepath)) {
                                    generate_qrcode($qrText, $qrFilename, 'progresscardQRcode');
                                }
                                $qrUrl = base_url('uploads/progresscardQRcode/'.$qrFilename.'.png');
                            ?>
                            <div class="pcn-box pcn-b--navy pcn-qr-box">
                                <h4 class="pcn-h--navy"><span class="pcn-h-icon"><i class="fa fa-qrcode"></i></span><span class="pcn-h-text">Scan to Verify</span></h4>
                                <div class="pcn-box-body">
                                <img src="<?=$qrUrl?>" alt="QR">
                                <p class="pcn-qr-caption">Student ID: <?=$student->srregisterNO?></p>
                                <p class="pcn-qr-caption">Verify at: <?=$qrText?></p>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="pcn-signatures">
                            <span>Parent's Signature</span>
                            <span>Class Teacher's Signature</span>
                            <span>Principal's Signature</span>
                        </div>
                    </div>
                </div>

                <script>
                (function(){
                    if (typeof Highcharts === 'undefined') { return; }

                    Highcharts.chart('chart-subjects-<?=$chartUid?>', {
                        chart: { type: 'column', height: 240 },
                        title: { text: 'Subject Wise Marks', style: { fontSize: '12px' } },
                        xAxis: { categories: <?=json_encode(array_map(function($r){ return $r['subject']; }, $subjectRows))?> },
                        yAxis: { title: { text: null } },
                        legend: { enabled: false },
                        credits: { enabled: false },
                        series: [{ name: 'Obtained', data: <?=json_encode(array_map(function($r){ return $r['obtained']; }, $subjectRows))?>, color: '#3949ab' }]
                    });

                    Highcharts.chart('chart-distribution-<?=$chartUid?>', {
                        chart: { type: 'pie', height: 240 },
                        title: { text: 'Marks Distribution', style: { fontSize: '12px' } },
                        credits: { enabled: false },
                        plotOptions: { pie: { innerSize: '60%', dataLabels: { enabled: true } } },
                        series: [{
                            name: 'Marks',
                            data: [
                                ['Obtained', <?=$totalObtained?>],
                                ['Remaining', <?=max($totalMaxMarks - $totalObtained, 0)?>]
                            ],
                            colors: ['#1a73e8', '#e0e0e0']
                        }]
                    });

                    Highcharts.chart('chart-gradehist-<?=$chartUid?>', {
                        chart: { type: 'column', height: 240 },
                        title: { text: 'Grade Distribution (Class)', style: { fontSize: '12px' } },
                        xAxis: { categories: ['A+','A','B+','B','C+','C','D'] },
                        yAxis: { title: { text: null } },
                        legend: { enabled: false },
                        credits: { enabled: false },
                        series: [{
                            name: 'Students',
                            data: [
                                <?=isset($classPerf['gradeHistogram']['A+']) ? $classPerf['gradeHistogram']['A+'] : 0?>,
                                <?=isset($classPerf['gradeHistogram']['A']) ? $classPerf['gradeHistogram']['A'] : 0?>,
                                <?=isset($classPerf['gradeHistogram']['B+']) ? $classPerf['gradeHistogram']['B+'] : 0?>,
                                <?=isset($classPerf['gradeHistogram']['B']) ? $classPerf['gradeHistogram']['B'] : 0?>,
                                <?=isset($classPerf['gradeHistogram']['C+']) ? $classPerf['gradeHistogram']['C+'] : 0?>,
                                <?=isset($classPerf['gradeHistogram']['C']) ? $classPerf['gradeHistogram']['C'] : 0?>,
                                <?=isset($classPerf['gradeHistogram']['D']) ? $classPerf['gradeHistogram']['D'] : 0?>
                            ],
                            color: '#00897b'
                        }]
                    });
                })();
                </script>

            <?php } } else { ?>
                <div class="callout callout-danger">
                    <p><b class="text-info"><?=$this->lang->line('progresscardreport_data_not_found')?></b></p>
                </div>
            <?php } ?>
            </form>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.sendSms_new', function() {
    var formDt = $('#marksForm_new').serialize();
    $.ajax({
        type: 'POST',
        url: "<?=base_url('progresscardreport/send_marks_to_sms')?>",
        data: formDt,
        dataType: 'json',
        success: function(response) {
            if (response > 0) {
                toastr.success('SMS sent to ' + response + ' recipient(s).');
            } else {
                toastr.error('No SMS was sent. Check mobile numbers or Notification Config settings.');
            }
        },
        error: function() {
            toastr.error('Request failed. Please try again.');
        }
    });
});

$(document).on('click', '.sendWhatsapp_btn_new', function() {
    $('#students_div_new').toggle('slow');
});

$(document).on('click', '.sendWhatsapp_new', function() {
    var checked = [];
    $('.stud_id_new:checked').each(function() { checked.push($(this).val()); });

    if(checked.length <= 0) {
        $('#stud_error_new').text('Please select at least one student from above list.');
        return false;
    }
    $('#stud_error_new').text('');

    var formDt = {
        'classesID' : '<?=$classesID?>',
        'sectionID' : '<?=$sectionID?>',
        'examID'    : '<?=$examID?>',
        'studentID[]' : checked,
    };

    $.ajax({
        type: 'POST',
        url: "<?=base_url('progresscardreport/send_pdf_to_whatsapp_new')?>",
        data: formDt,
        dataType: 'json',
        success: function(response) {
            if(response.status) {
                toastr.success('Sent to ' + (response.sent || 0) + ' recipient(s).');
            } else {
                toastr.error(response.message || 'Failed to send.');
            }
        },
        error: function() {
            toastr.error('Request failed. Please try again.');
        }
    });
});
</script>

<!-- Download JPEG — captures each on-screen .pcn-card exactly as rendered (same design as
     the screen), since mPDF (used by the Print button) can't reproduce this Flexbox/
     conic-gradient card layout. See srinivas_issues.md for why Print can't just be changed instead. -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
// .off() before .on(): this whole view is re-inserted via AJAX every time the filters
// change, and jQuery auto-executes embedded <script> tags on each insertion — without
// .off() first, every reload would bind ANOTHER click handler on top of the old one(s),
// so one real click fires N handlers at once (duplicate downloads, and the button's
// "please wait" state getting stuck since each handler stomps on the others' captured html).
$(document).off('click', '#pcnDownloadJpegBtn').on('click', '#pcnDownloadJpegBtn', async function() {
    var $btn = $(this);
    var cards = document.querySelectorAll('.pcn-card');

    if (!cards.length) {
        toastr.error('No progress card found to download.');
        return;
    }

    var originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Please wait...');

    var names     = $('input[name="st_names[]"]').map(function() { return this.value; }).get();
    var examNames = $('input[name="exam_name[]"]').map(function() { return this.value; }).get();
    var h2cOpts   = { scale: 2, useCORS: true, allowTaint: false, backgroundColor: '#ffffff' };

    try {
        for (var i = 0; i < cards.length; i++) {
            var canvas  = await html2canvas(cards[i], h2cOpts);
            var imgData = canvas.toDataURL('image/jpeg', 0.95);

            var safeName = (names[i] || ('student_' + (i + 1))).replace(/[^a-z0-9]+/gi, '_');
            var safeExam = (examNames[i] || 'exam').replace(/[^a-z0-9]+/gi, '_');

            var link = document.createElement('a');
            link.href = imgData;
            link.download = 'ProgressCard_' + safeName + '_' + safeExam + '.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            canvas.remove();
        }
    } catch (err) {
        console.error(err);
        toastr.error('Error generating JPEG image.');
    }

    $btn.prop('disabled', false).html(originalHtml);
});
</script>

<!-- Download All as JPEG ZIP — same per-card html2canvas capture as Download JPEG above,
     bundled into one .zip instead of one download per card (mirrors the ID Card report's
     "Download All as JPEG ZIP" button, IdcardReport_new.php). -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
$(document).off('click', '#pcnDownloadZipBtn').on('click', '#pcnDownloadZipBtn', async function() {
    var $btn = $(this);
    var cards = document.querySelectorAll('.pcn-card');

    if (!cards.length) {
        toastr.error('No progress card found to download.');
        return;
    }

    var originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Creating ZIP...');

    var names     = $('input[name="st_names[]"]').map(function() { return this.value; }).get();
    var examNames = $('input[name="exam_name[]"]').map(function() { return this.value; }).get();
    var h2cOpts   = { scale: 2, useCORS: true, allowTaint: false, backgroundColor: '#ffffff' };

    try {
        var zip = new JSZip();

        for (var i = 0; i < cards.length; i++) {
            var canvas  = await html2canvas(cards[i], h2cOpts);
            var imgData = canvas.toDataURL('image/jpeg', 0.95);
            var imgBlob = await (await fetch(imgData)).blob();

            var safeName = (names[i] || ('student_' + (i + 1))).replace(/[^a-z0-9]+/gi, '_');
            var safeExam = (examNames[i] || 'exam').replace(/[^a-z0-9]+/gi, '_');

            zip.file('ProgressCard_' + safeName + '_' + safeExam + '.jpg', imgBlob);

            canvas.remove();
        }

        var content  = await zip.generateAsync({ type: 'blob' });
        var zipExam  = (examNames[0] || 'exam').replace(/[^a-z0-9]+/gi, '_');
        saveAs(content, 'ProgressCards_' + zipExam + '.zip');
    } catch (err) {
        console.error(err);
        toastr.error('Error generating ZIP file.');
    }

    $btn.prop('disabled', false).html(originalHtml);
});
</script>
