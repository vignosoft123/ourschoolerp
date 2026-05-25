<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admission Form - <?= htmlspecialchars($profile->srname) ?></title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 10.5px; color: #000; background: #fff; padding: 14px; }

/* ── School Header ── */
.sch-header { display:flex; align-items:center; gap:14px; padding-bottom:8px; border-bottom:3px solid #1a237e; margin-bottom:8px; }
.sch-logo img { width:78px; height:78px; object-fit:contain; }
.sch-info { flex:1; text-align:center; }
.sch-name { font-size:28px; font-weight:900; color:#1a237e; text-transform:uppercase; letter-spacing:2px; line-height:1.15; }
.sch-addr { font-size:10px; font-weight:700; color:#333; text-transform:uppercase; margin-top:2px; letter-spacing:.4px; }
.sch-divider { display:flex; align-items:center; gap:6px; margin:4px auto; max-width:400px; }
.sch-divider .ln { flex:1; height:2px; background:linear-gradient(to right,#1a237e,#4caf50,#1a237e); }
.sch-divider .dm { color:#4caf50; font-size:11px; }
.sch-year { font-size:13px; font-weight:700; color:#1a237e; margin-top:3px; }

/* ── Form Title ── */
.form-title { text-align:center; border:2px solid #1a237e; padding:7px 0; font-size:12px; font-weight:800; color:#1a237e; text-transform:uppercase; letter-spacing:1px; margin:8px 0; }

/* ── Section Header ── */
.sec-hdr { display:flex; align-items:center; background:#1a237e; color:#fff; padding:4px 8px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; }
.sec-num { background:#fff; color:#1a237e; font-weight:900; font-size:10px; width:16px; height:16px; border-radius:2px; display:inline-flex; align-items:center; justify-content:center; margin-right:7px; flex-shrink:0; }

/* ── Tables ── */
table { width:100%; border-collapse:collapse; font-size:10.5px; }
td, th { border:1px solid #555; padding:4px 6px; vertical-align:middle; }
.lbl { background:#f0f0f0; font-weight:600; white-space:nowrap; }
.val { min-height:18px; }
.th-green { background:#2e7d32; color:#fff; font-weight:700; text-align:center; }
.th-gray  { background:#d5d5d5; font-weight:700; text-align:center; }

/* ── Section 2 photo ── */
.photo-cell { width:120px; text-align:center; vertical-align:top; padding:10px 6px; }
.photo-cell img { width:95px; height:110px; object-fit:cover; border:1px solid #888; display:block; margin:0 auto; }
.photo-cell p { font-size:9.5px; color:#555; margin-top:5px; font-style:italic; }

/* ── Side-by-side for Sections 4/5/6 ── */
.side-wrap { display:flex; }
.side-left  { flex:0 0 52%; }
.side-right { flex:0 0 48%; }

/* Remove double-border between side sections */
.side-left table td:last-child, .side-left table th:last-child { border-right:none; }
.side-right table td:first-child, .side-right table th:first-child { border-left:none; }
.side-left .sec-hdr { border-right:none; }
.side-right .sec-hdr { border-left:none; }

/* ── PIN boxes ── */
.pin-box { display:inline-block; width:15px; height:17px; border:1px solid #555; margin-right:1px; vertical-align:middle; }

/* ── Checkbox ── */
.chk { display:inline-block; width:11px; height:11px; border:1px solid #000; vertical-align:middle; }

/* ── Documents table ── */
.doc-table { border-collapse:collapse; font-size:10px; }
.doc-table th { background:#2e7d32; color:#fff; font-weight:700; text-align:center; padding:4px 5px; }
.doc-table td { padding:3px 6px; border:1px solid #666; }
.doc-chk { text-align:center; width:56px; }

/* ── Declaration ── */
.decl-text { font-size:9.5px; line-height:1.6; padding:6px 8px; border:1px solid #555; border-top:none; }
.sig-row td { border:none; padding:4px 8px; font-size:10px; }
.sig-underline { display:inline-block; border-bottom:1px solid #000; min-width:140px; height:14px; vertical-align:bottom; }

/* ── Important Note ── */
.imp-note { font-size:9px; line-height:1.5; margin-top:8px; padding:5px 8px; background:#f9f9f9; border-left:3px solid #4caf50; border-right:3px solid #4caf50; }

/* ── Buttons ── */
.slip-actions { display:flex; gap:12px; justify-content:center; margin-top:16px; }
.btn-print { background:#4CAF50; color:#fff; border:none; padding:10px 28px; font-size:14px; font-weight:700; border-radius:4px; cursor:pointer; }
.btn-back  { background:#546e7a; color:#fff; padding:10px 28px; font-size:14px; font-weight:700; border-radius:4px; text-decoration:none; display:inline-block; }

@media print {
    body { padding:0; }
    .slip-actions { display:none; }
    @page { size:A4 portrait; margin:8mm; }
}
</style>
</head>
<body>
<?php
/* ── Pre-compute ── */
$typeMap      = [1 => 'Transport', 2 => 'Hostel', 3 => 'Day Scholar'];
$motherTongue = [1 => 'Telugu', 2 => 'English', 3 => 'Hindi', 4 => 'Kannada', 5 => 'Malayalam', 6 => 'Urdhu'];

$yearParts = explode('-', $schoolyearsessionobj->schoolyear ?? '');
$endYear   = count($yearParts) == 2 ? $yearParts[1] : date('Y');
$ageLabel  = 'Age as on 31-03-' . $endYear;
$age = '';
if (!empty($profile->dob) && $profile->dob != '0000-00-00') {
    try {
        $age = (new DateTime($profile->dob))->diff(new DateTime($endYear . '-03-31'))->y . ' yrs';
    } catch (Exception $e) {}
}

$photo = $profile->photo ?? 'default.png';
$photoPath = file_exists(FCPATH . 'uploads/student/' . $photo)
    ? base_url('uploads/student/' . $photo)
    : base_url('assets/images/default_user.jpg');

$fatherName   = customCompute($parents) ? ($parents->father_name   ?? '') : '';
$fatherAadhar = customCompute($parents) ? ($parents->father_aadhar ?? '') : '';
$motherName   = customCompute($parents) ? ($parents->mother_name   ?? '') : '';
$motherAadhar = customCompute($parents) ? ($parents->mother_aadhar ?? '') : '';
$parentPhone  = customCompute($parents) ? ($parents->phone ?? $profile->phone ?? '') : ($profile->phone ?? '');

$studentType = (int)($profile->studentType ?? 3);
$routeName   = ($studentType == 1 && customCompute($transport_route)) ? $transport_route->route : '';
?>

<!-- ═══════════════ SCHOOL HEADER ═══════════════ -->
<div class="sch-header">
    <div class="sch-logo">
        <img src="<?= base_url('uploads/images/' . $siteinfos->photo) ?>" alt="Logo">
    </div>
    <div class="sch-info">
        <div class="sch-name"><?= htmlspecialchars($siteinfos->sname) ?></div>
        <div class="sch-addr"><?php
            $addr    = rtrim(trim($siteinfos->address ?? ''), ', ');
            $village = trim($siteinfos->village_name ?? '');
            echo htmlspecialchars($addr . ($village ? ', ' . $village : ''));
        ?></div>
        <div class="sch-divider"><span class="ln"></span><span class="dm">&#9670;</span><span class="ln"></span></div>
        <div class="sch-year">Academic Year : <strong><?= htmlspecialchars($schoolyearsessionobj->schoolyear) ?></strong></div>
    </div>
</div>

<!-- ═══════════════ FORM TITLE ═══════════════ -->
<div class="form-title">Admission Details Rectification &amp; Document Verification Form</div>

<!-- ═══════════════ SECTION 1: BASIC ADMISSION DETAILS ═══════════════ -->
<div class="sec-hdr"><span class="sec-num">1</span> Basic Admission Details</div>
<table>
    <tr>
        <td class="lbl" style="width:20%">Admission No.</td>
        <td class="val" style="width:30%"><?= htmlspecialchars($profile->srregisterNO ?? '') ?></td>
        <td class="lbl" style="width:22%">Date of Admission</td>
        <td class="val"><?= (!empty($profile->admission_date) && $profile->admission_date != '0000-00-00') ? date('d-m-Y', strtotime($profile->admission_date)) : '' ?></td>
    </tr>
    <tr>
        <td class="lbl">Class Admitted</td>
        <td class="val"><?= htmlspecialchars($profile->srclasses ?? '') ?></td>
        <td class="lbl">Section</td>
        <td class="val"><?= htmlspecialchars($profile->srsection ?? '') ?></td>
    </tr>
    <tr>
        <td class="lbl">Medium</td>
        <td class="val"><?= htmlspecialchars($profile->medium ?? '') ?></td>
        <td class="lbl">Student Type</td>
        <td class="val"><?= $typeMap[$studentType] ?? 'Day Scholar' ?></td>
    </tr>
</table>

<!-- ═══════════════ SECTION 2: STUDENT PERSONAL DETAILS ═══════════════ -->
<div class="sec-hdr" style="margin-top:8px;"><span class="sec-num">2</span> Student Personal Details</div>
<table>
    <col style="width:26%"><col style="width:28%"><col style="width:20%"><col style="width:14%"><col style="width:12%">
    <tr>
        <td class="lbl">Student Full Name as per Birth Certificate</td>
        <td class="val" colspan="3"><?= htmlspecialchars(trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? ''))) ?></td>
        <td rowspan="10" class="photo-cell">
            <img src="<?= $photoPath ?>" alt="Photo">
            <p>Paste<br>Student Photo</p>
        </td>
    </tr>
    <tr>
        <td class="lbl">Student Full Name as per Aadhaar</td>
        <td class="val" colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">First Name</td>
        <td class="val"><?= htmlspecialchars($profile->first_name ?? '') ?></td>
        <td class="lbl">Surname / Last Name</td>
        <td class="val"><?= htmlspecialchars($profile->last_name ?? '') ?></td>
    </tr>
    <tr>
        <td class="lbl">Date of Birth</td>
        <td class="val"><?= (!empty($profile->dob) && $profile->dob != '0000-00-00') ? date('d-m-Y', strtotime($profile->dob)) : '' ?></td>
        <td class="lbl"><?= htmlspecialchars($ageLabel) ?></td>
        <td class="val"><?= htmlspecialchars($age) ?></td>
    </tr>
    <tr>
        <td class="lbl">Gender (Male / Female)</td>
        <td class="val"><?= htmlspecialchars($profile->sex ?? '') ?></td>
        <td class="lbl">Blood Group</td>
        <td class="val"><?= htmlspecialchars($profile->bloodgroup ?? '') ?></td>
    </tr>
    <tr>
        <td class="lbl">Religion</td>
        <td class="val"><?= htmlspecialchars($profile->religion ?? '') ?></td>
        <td class="lbl">Caste</td>
        <td class="val"><?= htmlspecialchars($profile->caste ?? '') ?></td>
    </tr>
    <tr>
        <td class="lbl">Sub-Caste</td>
        <td class="val"><?= htmlspecialchars($profile->sub_caste ?? '') ?></td>
        <td class="lbl">Mother Tongue</td>
        <td class="val"><?= isset($motherTongue[(int)($profile->mother_toungue ?? 0)]) ? $motherTongue[(int)$profile->mother_toungue] : '' ?></td>
    </tr>
    <tr>
        <td class="lbl">Nationality</td>
        <td class="val"><?= htmlspecialchars($profile->country ?? '') ?></td>
        <td class="lbl">Student Aadhaar No.</td>
        <td class="val"><?= htmlspecialchars($profile->aadharCardNumber ?? '') ?></td>
    </tr>
    <tr>
        <td class="lbl">PEN No. (if available)</td>
        <td class="val"><?= htmlspecialchars($profile->pen_number ?? '') ?></td>
        <td class="lbl">Child ID (if available)</td>
        <td class="val"><?= htmlspecialchars($profile->child_id ?? '') ?></td>
    </tr>
    <tr>
        <td class="lbl">Previous School (if any)</td>
        <td class="val" colspan="3">&nbsp;</td>
    </tr>
</table>

<!-- ═══════════════ SECTION 3: PARENT / GUARDIAN DETAILS ═══════════════ -->
<div class="sec-hdr" style="margin-top:8px;"><span class="sec-num">3</span> Parent / Guardian Details</div>
<table>
    <tr>
        <th class="th-gray" style="width:22%">Particulars</th>
        <th class="th-green" style="width:39%">Father Details</th>
        <th class="th-green" style="width:39%">Mother Details</th>
    </tr>
    <tr>
        <td class="lbl">Name as per Aadhaar</td>
        <td class="val"><?= htmlspecialchars($fatherName) ?></td>
        <td class="val"><?= htmlspecialchars($motherName) ?></td>
    </tr>
    <tr>
        <td class="lbl">Qualification</td>
        <td class="val">&nbsp;</td>
        <td class="val">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Occupation</td>
        <td class="val">&nbsp;</td>
        <td class="val">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Aadhaar No.</td>
        <td class="val"><?= htmlspecialchars($fatherAadhar) ?></td>
        <td class="val"><?= htmlspecialchars($motherAadhar) ?></td>
    </tr>
    <tr>
        <td class="lbl">Mobile No.</td>
        <td class="val"><?= htmlspecialchars($parentPhone) ?></td>
        <td class="val">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">WhatsApp No.</td>
        <td class="val"><?= htmlspecialchars($profile->alternative_phone1 ?? '') ?></td>
        <td class="val">&nbsp;</td>
    </tr>
    <tr>
        <td class="lbl">Email ID</td>
        <td class="val"><?= htmlspecialchars($profile->email ?? '') ?></td>
        <td class="val">&nbsp;</td>
    </tr>
</table>
<table style="margin-top:2px; border-collapse:collapse;">
    <tr>
        <td style="border:none; padding:4px 8px; font-size:10px; width:38%;">
            Guardian Name, if applicable: <span class="sig-underline" style="min-width:130px;"><?= htmlspecialchars($refered_by_label ?? '') ?></span>
        </td>
        <td style="border:none; padding:4px 8px; font-size:10px; width:32%;">
            Relation with Student: <span class="sig-underline" style="min-width:100px;">&nbsp;</span>
        </td>
        <td style="border:none; padding:4px 8px; font-size:10px; width:30%;">
            Guardian Mobile No.: <span class="sig-underline" style="min-width:100px;">&nbsp;</span>
        </td>
    </tr>
</table>

<!-- ═══════════════ SECTIONS 4 + 5 + 6  (side by side) ═══════════════ -->
<div class="side-wrap" style="margin-top:8px;">

    <!-- LEFT: Section 4 — Address Details -->
    <div class="side-left">
        <div class="sec-hdr"><span class="sec-num">4</span> Address Details</div>
        <table>
            <tr>
                <td class="lbl" style="width:45%">Door No. / House No.</td>
                <td class="val"><?= htmlspecialchars($profile->address ?? '') ?></td>
            </tr>
            <tr>
                <td class="lbl">Street / Area</td>
                <td class="val">&nbsp;</td>
            </tr>
            <tr>
                <td class="lbl">Village</td>
                <td class="val"><?= htmlspecialchars($profile->village_name ?? '') ?></td>
            </tr>
            <tr>
                <td class="lbl">Mandal</td>
                <td class="val">&nbsp;</td>
            </tr>
            <tr>
                <td class="lbl">District</td>
                <td class="val">&nbsp;</td>
            </tr>
            <tr>
                <td class="lbl">State</td>
                <td class="val"><?= htmlspecialchars($profile->state ?? '') ?></td>
            </tr>
            <tr>
                <td class="lbl">PIN Code</td>
                <td class="val">
                    <span class="pin-box"></span><span class="pin-box"></span><span class="pin-box"></span>
                    &nbsp;
                    <span class="pin-box"></span><span class="pin-box"></span><span class="pin-box"></span>
                </td>
            </tr>
            <tr>
                <td class="lbl">Ration Card No. (if available)</td>
                <td class="val"><?= htmlspecialchars($profile->ration_card ?? '') ?></td>
            </tr>
        </table>
    </div>

    <!-- RIGHT: Section 5 — Bank + Section 6 — Transport -->
    <div class="side-right">
        <div class="sec-hdr"><span class="sec-num">5</span> Bank Details</div>
        <table>
            <tr>
                <td class="lbl" style="width:48%">Account Holder Name</td>
                <td class="val"><?= htmlspecialchars($profile->name ?? '') ?></td>
            </tr>
            <tr>
                <td class="lbl">Bank Name</td>
                <td class="val"><?= htmlspecialchars($profile->bank_name ?? '') ?></td>
            </tr>
            <tr>
                <td class="lbl">Branch Name</td>
                <td class="val"><?= htmlspecialchars($profile->branch_name ?? '') ?></td>
            </tr>
            <tr>
                <td class="lbl">Account No.</td>
                <td class="val"><?= htmlspecialchars($profile->account_no ?? '') ?></td>
            </tr>
            <tr>
                <td class="lbl">IFSC Code</td>
                <td class="val"><?= htmlspecialchars($profile->ifsc_code ?? '') ?></td>
            </tr>
        </table>

        <div class="sec-hdr" style="margin-top:0; border-top:1px solid #1a237e;"><span class="sec-num">6</span> Transport Details</div>
        <table>
            <tr>
                <td class="lbl" style="width:48%">Transport Required</td>
                <td class="val">
                    <span class="chk"><?= $studentType == 1 ? '&#10003;' : '' ?></span> Yes &nbsp;&nbsp;
                    <span class="chk"><?= $studentType != 1 ? '&#10003;' : '' ?></span> No
                </td>
            </tr>
            <tr>
                <td class="lbl">Bus Route / Stop</td>
                <td class="val"><?= htmlspecialchars($routeName) ?></td>
            </tr>
            <tr>
                <td class="lbl">Pickup Point</td>
                <td class="val">&nbsp;</td>
            </tr>
            <tr>
                <td class="lbl">Drop Point</td>
                <td class="val">&nbsp;</td>
            </tr>
        </table>
    </div>
</div>

<!-- ═══════════════ SECTION 7: DOCUMENTS SUBMITTED ═══════════════ -->
<div class="sec-hdr" style="margin-top:8px;"><span class="sec-num">7</span> Documents Submitted</div>
<table class="doc-table">
    <thead>
        <tr>
            <th style="width:5%">Sl. No.</th>
            <th style="width:35%">Document</th>
            <th class="doc-chk">Submitted</th>
            <th class="doc-chk">Verified</th>
            <th style="width:5%">Sl. No.</th>
            <th style="width:35%">Document</th>
            <th class="doc-chk">Submitted</th>
            <th class="doc-chk">Verified</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $docs = [
            ['1.','Birth Certificate'],
            ['2.','Student Aadhaar Copy'],
            ['3.','Father Aadhaar Copy'],
            ['4.','Mother Aadhaar Copy'],
            ['5.','Caste Certificate, if applicable'],
            ['6.','Ration Card Copy, if available'],
            ['7.','Bank Passbook Copy'],
            ['8.','Passport Size Photos'],
            ['9.','TC from Previous School, if applicable'],
            ['10.','Previous Progress Report, if applicable'],
        ];
        $half = ceil(count($docs)/2);
        for ($i = 0; $i < $half; $i++) {
            $left  = $docs[$i];
            $right = $docs[$i + $half] ?? null;
            ?>
            <tr>
                <td style="text-align:center;"><?= $left[0] ?></td>
                <td><?= $left[1] ?></td>
                <td class="doc-chk"><span class="chk"></span></td>
                <td class="doc-chk"><span class="chk"></span></td>
                <?php if ($right): ?>
                <td style="text-align:center;"><?= $right[0] ?></td>
                <td><?= $right[1] ?></td>
                <td class="doc-chk"><span class="chk"></span></td>
                <td class="doc-chk"><span class="chk"></span></td>
                <?php else: ?>
                <td colspan="4">&nbsp;</td>
                <?php endif; ?>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- ═══════════════ SECTION 8: DECLARATION ═══════════════ -->
<div class="sec-hdr" style="margin-top:8px;"><span class="sec-num">8</span> Declaration by Parent / Guardian</div>
<div class="decl-text">
    I hereby declare that the above details furnished by me are true and correct to the best of my knowledge. I have verified the spelling of the
    student's name, date of birth, parent names, Aadhaar details, address, caste details, mobile numbers and bank details. I understand that the
    school will enter these details in the official school records, Admission Register and other student records. Any correction required in future
    shall be supported by valid documents.
</div>
<table style="border-collapse:collapse; border-top:none; margin-top:2px;">
    <tr class="sig-row">
        <td style="width:36%; border:1px solid #555; border-top:none; padding:5px 8px; font-size:10px;">
            Parent / Guardian Name: <span class="sig-underline" style="min-width:130px;">&nbsp;</span>
        </td>
        <td style="width:34%; border:1px solid #555; border-top:none; border-left:none; padding:5px 8px; font-size:10px;">
            Signature: <span class="sig-underline" style="min-width:140px;">&nbsp;</span>
        </td>
        <td style="width:30%; border:1px solid #555; border-top:none; border-left:none; padding:5px 8px; font-size:10px;">
            Date: <span class="sig-underline" style="min-width:100px;"><?= date('d-m-Y') ?></span>
        </td>
    </tr>
</table>

<!-- ═══════════════ IMPORTANT NOTE ═══════════════ -->
<div class="imp-note">
    <strong>Important:</strong> Please ensure that student name, date of birth, father name and mother name are entered exactly as per official documents.
    Aadhaar and bank details should be kept confidential and should not be shared publicly.
</div>

<!-- ═══════════════ ACTION BUTTONS ═══════════════ -->
<div class="slip-actions">
    <button class="btn-print" onclick="window.print()">&#128438; Print Form</button>
    <a class="btn-back" href="<?= base_url('student/index') ?>">&#8592; Back to Student List</a>
</div>

<script>
    window.onload = function() { window.print(); };
</script>
</body>
</html>
