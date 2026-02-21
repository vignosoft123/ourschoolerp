<link rel="stylesheet" href="/assets/css/report-buttons.css">
<?php
/* @var $students array */
$yearsText   = isset($yearsText) ? $yearsText : '';
$conductText = isset($conductText) ? $conductText : '';
$dateText    = isset($dateText) ? $dateText : '';
$displayDate = '';
if(!empty($dateText)) {
    $ts = strtotime($dateText);
    if($ts) { $displayDate = date('d-m-Y', $ts); } else { $displayDate = htmlspecialchars($dateText); }
}
?>
<style>
@media print {.no-print{display:none!important}}
.sc-body{font-family: Arial, Helvetica, sans-serif; width: 900px; margin: 20px auto; color:#2632a4}
.sc-mainborder{border:5px solid #2632a4; padding:3px}
.sc-inner{border:2px solid #2632a4}
.sc-header{text-align:center; line-height:.4}
.sc-header h1{font-family:'Times New Roman', Times, serif; font-size:40px; font-style: italic; text-transform:capitalize; font-weight:bold}
.sc-header p {
    line-height: 0.9; /* Adjusted line height for better readability */
}
.sc-institute{font-style: italic; text-transform: capitalize; font-weight: bold; font-size: 18px}
.sc-bordered-text {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin: 20px auto; /* Centered and added spacing */
    gap: 20px; /* Increased spacing between lines and text */
    max-width: 90%; /* Prevents the section from stretching too wide */
    text-align: center; /* Ensures text alignment */
}
.sc-left-line, .sc-right-line {
    height: 2px; /* Reduced thickness for a cleaner look */
    flex: 1; /* Ensures lines take equal space */
    background-color: #2632a4;
}
.sc-text {
    font-size: 24px; /* Slightly increased font size for emphasis */
    padding: 10px 20px; /* Adjusted padding for balance */
    border: 2px solid #2632a4; /* Retained border for emphasis */
    letter-spacing: 1.2px; /* Reduced spacing for a cleaner look */
    text-transform: uppercase; /* Ensures uniformity in text */
    white-space: nowrap; /* Prevents text wrapping */
}
.sc-maincontent{font-style:italic; padding:5px; font-family:'Times New Roman', Times, serif; font-size: 18px;} /* Increased font size for better readability */
.sc-text-container{display:flex; padding:5px}
.sc-border-after{border-bottom:3px dotted #2632a4; flex:1}
/* New centered-line styles */
.sc-line{display:flex; align-items:center; gap:8px; padding:5px}
.sc-fill{position:relative; flex:1; text-align:center; padding-bottom:8px}
.sc-fill:after{content:""; position:absolute; left:0; right:0; bottom:0; border-bottom:1px solid #2632a4}
.sc-dyn,.sc-editable{display:inline-block; position:relative; z-index:1}
.sc-dyn{position:relative; padding:0 10px; font-weight:700; font-family:'Times New Roman', Times, serif; font-size:20px; color:#2632a4}
.sc-editable{position:relative; background:transparent; padding:0 10px; min-width:60px; display:inline-block; font-weight:700; font-family:'Times New Roman', Times, serif; font-size:20px; color:#2632a4; outline:none; cursor: text;}
.sc-editable.empty:before{content:attr(data-placeholder); color:#9aa3c6; font-weight:400; font-size: 14px;}
.sc-date-wrap{display:flex; align-items:center; gap:8px}
.sc-date-input{border:1px solid #9aa3c6; padding:3px 6px; border-radius:4px; color:#2632a4}
.sc-date-print{display:none}
@media print { .sc-date-print{display:inline} }
.sc-footer{margin-top:50px; padding:5px; width:100%; display:flex}
.sc-left{width:75%}
.sc-footeraddress{padding-bottom:1%}
.sc-pagebreak{page-break-after: always}
@media print {
    @page {
        margin: 2mm;
        size: A4 portrait;
    }
    .filters, .box-header, .box-body, #studyCertificateReportForm, .no-print {
        display: none !important;
    }
    body {
        margin: 0;
        padding: 0;
        background: #fff;
    }
    .sc-body {
        width: 100% !important;
        height: 145mm !important; /* Closer to exact half (148.5mm) */
        margin: 0 !important;
        padding: 2mm !important;
        box-sizing: border-box;
        page-break-inside: avoid;
        display: block !important;
    }
    .sc-mainborder {
        height: 140mm !important;
        margin: 0 !important;
        padding: 1mm !important;
        border-width: 3px !important;
    }
    .sc-inner {
        height: 137mm !important;
        min-height: 0 !important;
        padding: 5mm !important;
        position: relative;
    }
    .sc-maincontent {
        line-height: 1.4 !important;
        font-size: 14px !important;
    }
    .sc-line {
        padding: 2px 0 !important;
        gap: 4px !important;
    }
    .sc-header h1 {
        font-size: 26px !important;
        margin: 0 0 2px 0 !important;
    }
    .sc-header p {
        font-size: 16px !important;
        margin: 0 0 2px 0 !important;
    }
    .sc-footer {
        margin-top: 15px !important;
        padding-top: 0 !important;
    }
    .sc-bordered-text {
        margin: 5px auto !important;
    }
    .sc-text {
        font-size: 18px !important;
        padding: 3px 10px !important;
    }
    .sc-editable {
        font-size: 16px !important;
        min-width: 40px !important;
    }
    .sc-fill:after {
        border-bottom-width: 1px !important;
    }
}
</style>

<?php
function dateToWords($date) {
    if(!$date) return '';
    $ts = strtotime($date);
    if(!$ts) return '';
    $day = date('j', $ts);
    $month = date('F', $ts);
    $year = date('Y', $ts);

    $days = array(
        1 => 'First', 2 => 'Second', 3 => 'Third', 4 => 'Fourth', 5 => 'Fifth',
        6 => 'Sixth', 7 => 'Seventh', 8 => 'Eighth', 9 => 'Ninth', 10 => 'Tenth',
        11 => 'Eleventh', 12 => 'Twelfth', 13 => 'Thirteenth', 14 => 'Fourteenth',
        15 => 'Fifteenth', 16 => 'Sixteenth', 17 => 'Seventeenth', 18 => 'Eighteenth',
        19 => 'Nineteenth', 20 => 'Twentieth', 21 => 'Twenty-First', 22 => 'Twenty-Second',
        23 => 'Twenty-Third', 24 => 'Twenty-Fourth', 25 => 'Twenty-Fifth',
        26 => 'Twenty-Sixth', 27 => 'Twenty-Seventh', 28 => 'Twenty-Eighth',
        29 => 'Twenty-Ninth', 30 => 'Thirtieth', 31 => 'Thirty-First'
    );

    $dict = array(
        0=>'', 1=>'One', 2=>'Two', 3=>'Three', 4=>'Four', 5=>'Five', 6=>'Six', 7=>'Seven', 8=>'Eight', 9=>'Nine', 10=>'Ten',
        11=>'Eleven', 12=>'Twelve', 13=>'Thirteen', 14=>'Fourteen', 15=>'Fifteen', 16=>'Sixteen', 17=>'Seventeen', 18=>'Eighteen', 19=>'Nineteen',
        20=>'Twenty', 30=>'Thirty', 40=>'Forty', 50=>'Fifty', 60=>'Sixty', 70=>'Seventy', 80=>'Eighty', 90=>'Ninety'
    );

    $yearWords = "";
    if($year >= 2000 && $year < 3000) {
        $yearWords = "Two Thousand ";
        $rem = $year - 2000;
        if($rem > 0) {
            if(isset($dict[$rem])) {
                $yearWords .= $dict[$rem];
            } else {
                $tens = floor($rem/10)*10;
                $ones = $rem%10;
                $yearWords .= $dict[$tens] . ($ones ? ' ' . $dict[$ones] : '');
            }
        }
    } else {
        $yearWords = $year; // Fallback
    }

    return ($days[$day] ?? $day) . " - " . $month . " - " . $yearWords;
}
?>

<div>
    <div class="filters no-print" style="margin:10px 0; text-align:right">
        <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
    </div>
<?php if(customCompute($students)) { $i = 0; foreach($students as $student) { $i++; ?>
    <div class="sc-body">
        <div class="sc-mainborder">
            <div class="sc-inner" style="min-height: 550px; padding: 20px;">
                <div class="sc-header">
                    <h1><?= isset($siteinfos->sname) && $siteinfos->sname ? htmlspecialchars($siteinfos->sname) : 'School Name' ?></h1>
                    <?php if (isset($siteinfos->affiliation) && $siteinfos->affiliation) { ?>
                        <span align="center">(<?= htmlspecialchars($siteinfos->affiliation) ?>)</span>
                    <?php } ?>
                    <?php if (isset($siteinfos->address) && $siteinfos->address) { ?>
                        <p style="font-size:24px;">
                            <span><?= htmlspecialchars($siteinfos->address) ?></span>
                        </p>
                        <?php if (isset($siteinfos->phone) && $siteinfos->phone) { ?>
                            <p style="font-size:24px;">
                                <span>Phone: <?= htmlspecialchars($siteinfos->phone) ?></span>
                            </p>
                        <?php } ?>
                    <?php } ?>
                    <div class="sc-bordered-text">
                        <div class="sc-left-line"></div>
                        <div class="sc-text">STUDY CUM CONDUCT CERTIFICATE</div>
                        <div class="sc-right-line"></div>
                    </div>
                </div>
                <div class="sc-maincontent" style="font-style: normal; line-height: 2.2;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                        <div style="display: flex; gap: 5px;">
                            <label>Admission No. </label>
                            <span class="sc-editable" contenteditable="true" style="min-width: 100px; border-bottom: 1px solid #2632a4;"><?= htmlspecialchars($student->registerNO) ?></span>
                        </div>
                         <div style="display: flex; gap: 5px;">
                            <label>Date : </label>
                            <span class="sc-editable" contenteditable="true" style="min-width: 150px; border-bottom: 1px solid #2632a4;"><?= htmlspecialchars($displayDate) ?></span>
                        </div>
                    </div>

                    <?php 
                        $sid = isset($student->srstudentID) ? (int)$student->srstudentID : (isset($student->studentID)?(int)$student->studentID:0);
                        $studentName = isset($student->srname) ? $student->srname : '';
                        $parentName = isset($parentNameByStudentID[$sid]) ? $parentNameByStudentID[$sid] : '';
                        $className  = isset($classNameByStudentID[$sid]) ? $classNameByStudentID[$sid] : '';
                    ?>

                    <div class="sc-line">
                        <span>This is to certify that (Kumar /Kumari)</span>
                        <div class="sc-fill"><span class="sc-editable" contenteditable="true"><?= htmlspecialchars($studentName) ?></span></div>
                    </div>

                    <div class="sc-line">
                        <span>Son / Daughter of Sri</span>
                        <div class="sc-fill"><span class="sc-editable" contenteditable="true"><?= htmlspecialchars($parentName) ?></span></div>
                    </div>

                    <div class="sc-line">
                        <span>studying /studied in this Institution from</span>
                        <div style="margin-left: auto; display: flex; align-items: center; gap: 8px; width: 450px;">
                            <div class="sc-fill"><span class="sc-editable" contenteditable="true"></span></div>
                            <span style="padding: 0 10px;">to</span>
                            <div class="sc-fill"><span class="sc-editable" contenteditable="true"><?= htmlspecialchars($className) ?></span></div>
                        </div>
                    </div>

                    <div class="sc-line">
                        <span>during the period from</span>
                        <div style="margin-left: auto; display: flex; align-items: center; gap: 8px; width: 450px;">
                            <div class="sc-fill"><span class="sc-editable" contenteditable="true"></span></div>
                            <span style="padding: 0 10px;">to</span>
                            <div class="sc-fill"><span class="sc-editable" contenteditable="true"><?= htmlspecialchars($yearsText) ?></span></div>
                        </div>
                    </div>

                    <div class="sc-line">
                        <span>His /Her date of birth as per the records of this institution is /was</span>
                        <div style="margin-left: auto; width: 250px;"><div class="sc-fill"><span class="sc-editable" contenteditable="true"><?= isset($student->dob) && !empty($student->dob) ? date('d-m-Y', strtotime($student->dob)) : '' ?></span></div></div>
                    </div>

                    <div class="sc-line">
                        <span>(in words)</span>
                        <div style="margin-left: auto; width: 600px;"><div class="sc-fill"><span class="sc-editable" contenteditable="true"><?= isset($student->dob) && !empty($student->dob) ? dateToWords($student->dob) : '' ?></span></div></div>
                    </div>

                    <div class="sc-line">
                        <span>His /Her conduct during the said period is /was</span>
                        <div style="margin-left: auto; width: 300px;"><div class="sc-fill"><span class="sc-editable" contenteditable="true"><?= htmlspecialchars($conductText) ?></span></div></div>
                    </div>

                </div>
                <div class="sc-footer" style="padding-top: 50px;">
                    <div style="display: flex; justify-content: space-between; width: 100%; align-items: flex-end;">
                        <div style="text-align: left;">
                            <b style="font-size: 18px;">Admission Incharge</b>
                        </div>
                        <div style="text-align: right;">
                            <b style="font-size: 18px;">Sign. of Principal</b>
                        </div>
                    </div>
                </div>
                <div style="font-size: 14px; margin-top: 20px; color: #2632a4;">
                    Note : This certificate will not be valid without the Stamp and Signature of the concerned person.
                    <div style="border-top: 1px dashed #2632a4; margin-top: 5px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
    <?php if($i < count($students)) { ?><div class="sc-pagebreak"></div><?php } ?>
<?php } } else { ?>
    <div class="alert alert-info">No students found for the selected filters.</div>
<?php } ?>
</div>

<script>
(function(){
    // Mark empty contenteditable placeholders
    function refreshEditableState() {
        document.querySelectorAll('.sc-editable').forEach(function(el){
            var text = (el.innerText || '').trim();
            if(text.length === 0) el.classList.add('empty'); else el.classList.remove('empty');
        });
    }
    document.addEventListener('input', function(e){
        if(e.target.classList && e.target.classList.contains('sc-editable')) {
            refreshEditableState();
        }
    });
    refreshEditableState();

    // Mirror date input to print span in dd-mm-YYYY
    var dateInputs = document.querySelectorAll('.sc-date-input');
    dateInputs.forEach(function(inp){
        var printSpan = inp.parentElement.querySelector('.sc-date-print');
        function format(dstr){
            if(!dstr) return '';
            var d = new Date(dstr);
            if(isNaN(d.getTime())) return dstr;
            var dd = ('0'+d.getDate()).slice(-2);
            var mm = ('0'+(d.getMonth()+1)).slice(-2);
            var yyyy = d.getFullYear();
            return dd+'-'+mm+'-'+yyyy;
        }
        function sync(){ printSpan.textContent = format(inp.value); }
        inp.addEventListener('change', sync);
        inp.addEventListener('input', sync);
        sync();
    });

    // Function to handle print
    function handlePrint() {
        // Hide elements not meant for printing
        document.querySelectorAll('.no-print').forEach(function(el) {
            el.style.display = 'none';
        });

        // Trigger the print dialog
        window.print();

        // Restore hidden elements after printing
        document.querySelectorAll('.no-print').forEach(function(el) {
            el.style.display = '';
        });
    }

    // Attach the print handler to the button
    document.querySelector('button[onclick="window.print()"]')?.addEventListener('click', function(event) {
        event.preventDefault();
        handlePrint();
    });
})();
</script>
