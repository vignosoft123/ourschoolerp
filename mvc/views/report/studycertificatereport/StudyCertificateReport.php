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
.sc-fill:after{content:""; position:absolute; left:0; right:0; bottom:0; border-bottom:3px dotted #2632a4}
.sc-dyn,.sc-editable{display:inline-block; position:relative; z-index:1}
.sc-dyn{position:relative;    padding:0 10px; font-weight:700; font-family:'Times New Roman', Times, serif; font-size:20px; color:#2632a4}
.sc-editable{position:relative; background:#fff; padding:0 10px; min-width:140px; display:inline-block; font-weight:700; font-family:'Times New Roman', Times, serif; font-size:20px; color:#2632a4; outline:none}
.sc-editable.empty:before{content:attr(data-placeholder); color:#9aa3c6; font-weight:400}
.sc-date-wrap{display:flex; align-items:center; gap:8px}
.sc-date-input{border:1px solid #9aa3c6; padding:3px 6px; border-radius:4px; color:#2632a4}
.sc-date-print{display:none}
@media print { .sc-date-print{display:inline} }
.sc-footer{margin-top:50px; padding:5px; width:100%; display:flex}
.sc-left{width:75%}
.sc-footeraddress{padding-bottom:1%}
.sc-pagebreak{page-break-after: always}
@media print {
    .filters, .box-header, .box-body, #studyCertificateReportForm {
        display: none !important; /* Hide unnecessary elements during printing */
    }

    .sc-body {
        display: block !important; /* Ensure the certificate content is visible */
    }

    .sc-body {
        width: 100%;
        height: 50%; /* Each certificate takes half the A4 page */
        box-sizing: border-box; /* Ensure proper sizing */
        page-break-inside: avoid; /* Prevent breaking within a certificate */
    }

    .sc-body:nth-child(odd) {
        margin-bottom: 0; /* Remove margin between certificates */
    }

    .sc-body:nth-child(even) {
        margin-top: 0; /* Remove margin between certificates */
    }
    .sc-date-wrap {
        display: flex;
        justify-content: flex-end; /* Align the date to the right */
        align-items: center; /* Ensure vertical alignment */
        margin: 10px 0; /* Add spacing above and below */
        font-size: 18px; /* Adjust font size for better readability */
        font-weight: bold; /* Make the date stand out */
    }
    .sc-date-print {
        display: inline-block; /* Ensure the date is visible in print */
        padding: 5px 10px; /* Add padding for better spacing */
        border: 1px solid #2632a4; /* Add a border for emphasis */
        border-radius: 4px; /* Slightly round the corners */
        background-color: #f9f9f9; /* Add a light background for contrast */
    }
    .sc-date-input {
        display: none; /* Hide the input field in print */
    }
    .sc-maincontent > div {
        display: flex; /* Ensure flex layout for alignment */
        justify-content: space-between; /* Space out the elements */
        align-items: center; /* Align items vertically */
        margin-bottom: 10px; /* Maintain spacing */
    }
}
</style>

<div>
    <div class="filters no-print" style="margin:10px 0; text-align:right">
        <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
    </div>
<?php if(customCompute($students)) { $i = 0; foreach($students as $student) { $i++; ?>
    <div class="sc-body">
        <div class="sc-mainborder">
            <div class="sc-inner" style="min-height: 550px;">
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
                        <div class="sc-text">STUDY & CONDUCT CERTIFICATE</div>
                        <div class="sc-right-line"></div>
                    </div>
                </div>
                <div class="sc-maincontent">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <div>
                            <label>Admission No:</label>
                            <span class="sc-dyn"> <?= htmlspecialchars($student->registerNO) ?> </span>
                        </div>
                        <!-- <div>
                            <label>Date:</label>
                            <span class="sc-editable" contenteditable="true" data-placeholder="Enter Date"></span>
                        </div> -->
                         <div class=" ">
                        
                        <div class="sc-date-wrap" >Date
                            <input type="date" class="sc-date-input no-print" value="<?= !empty($dateText) && strtotime($dateText) ? date('Y-m-d', strtotime($dateText)) : '' ?>" />
                            <span class="sc-date-print sc-dyn"><?= htmlspecialchars($displayDate) ?></span>
                        </div>
                    </div>
                    </div>
                    <?php 
                        $sid = isset($student->srstudentID) ? (int)$student->srstudentID : (isset($student->studentID)?(int)$student->studentID:0);
                        $studentName = isset($student->srname) ? $student->srname : '';
                        $parentName = isset($parentNameByStudentID[$sid]) ? $parentNameByStudentID[$sid] : '';
                        $className  = isset($classNameByStudentID[$sid]) ? $classNameByStudentID[$sid] : '';
                        $medium     = isset($mediumByStudentID[$sid]) ? $mediumByStudentID[$sid] : '';
                    ?>
                    <!-- Name centered line -->
                    <div class="sc-line">
                        <span>This is to certify that Mr / Miss</span>
                        <div class="sc-fill"><span class="sc-dyn"><?= htmlspecialchars($studentName) ?></span></div>
                        <span>is / was a student</span>
                    </div>
                    <!-- Parent centered line -->
                    <div class="sc-line">
                        <span>S/o. D/o</span>
                        <div class="sc-fill"><span class="sc-dyn"><?= htmlspecialchars($parentName) ?></span></div>
                    </div>
                    <!-- During the years editable in the middle -->
                    <div class="sc-line">
                        <span>of&nbsp;<span class="sc-institute"><?= isset($siteinfos->sname) && $siteinfos->sname ? htmlspecialchars($siteinfos->sname) : '' ?></span>,&nbsp;<span class="address"><?= isset($siteinfos->address) && $siteinfos->address ? htmlspecialchars($siteinfos->address) : '' ?></span>,&nbsp;During the years</span>
                        <div class="sc-fill"><span class="sc-editable" contenteditable="true" data-placeholder="Enter years">
                            <?= htmlspecialchars($yearsText) ?></span></div>
                    </div>
                    <!-- Class/Medium and Conduct editable -->
                    <div class="sc-line">
                        <span>with group</span>
                        <div class="sc-fill" style="max-width:300px"><span class="sc-dyn"><?= htmlspecialchars($className) ?></span></div>
                        <span>Medium</span>
                        <div class="sc-fill" style="max-width:200px"><span class="sc-dyn"><?= htmlspecialchars($medium) ?></span></div>
                        <span>His/Her Character and Conduct is</span>
                        <div class="sc-fill"><span class="sc-editable" contenteditable="true" data-placeholder="Enter conduct">
                            <?= htmlspecialchars($conductText) ?></span></div>
                    </div>
                    <!-- Date of Birth dynamic content -->
                    <div class="sc-line">
                        <span>His/Her date of birth as per records of this institution is / was</span>
                        <div class="sc-fill">
                            <span class="sc-dyn">
                                <?= isset($student->dob) && !empty($student->dob) ? date('d-m-Y', strtotime($student->dob)) : '__________' ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="sc-footer">
                    <div class="sc-left">
                        <!-- <div class="sc-footeraddress">
                            <?= isset($siteinfos->city) && $siteinfos->city ? htmlspecialchars($siteinfos->city) : (isset($siteinfos->address) && $siteinfos->address ? htmlspecialchars($siteinfos->address) : '') ?>
                        </div> -->
                        <!-- <div class="sc-date-wrap" style="width:60%">Date
                            <input type="date" class="sc-date-input no-print" value="<?= !empty($dateText) && strtotime($dateText) ? date('Y-m-d', strtotime($dateText)) : '' ?>" />
                            <span class="sc-date-print sc-dyn"><?= htmlspecialchars($displayDate) ?></span>
                        </div> -->
                    </div>
                    <div class="right"><span>Principal</span></div>
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
