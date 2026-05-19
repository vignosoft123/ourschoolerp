<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admission Slip - <?= htmlspecialchars($profile->srname) ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; background: #f5f5f5; }
        .slip-wrapper { max-width: 860px; margin: 0 auto; background: #fff; padding: 24px; border: 1px solid #ddd; border-radius: 6px; }

        .reportPage-header { display:flex; align-items:center; padding:16px 10px; border-bottom:2px solid #4CAF50; margin-bottom:18px; }

        .slip-title { text-align:center; font-size:15px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:#1b5e20; background:#e8f5e9; border:1px solid #c8e6c9; padding:8px 0; border-radius:4px; margin-bottom:18px; }

        /* Photo + top summary */
        .slip-top { display:flex; gap:16px; margin-bottom:16px; align-items:flex-start; }
        .slip-photo { flex:0 0 110px; text-align:center; }
        .slip-photo img { width:100px; height:110px; object-fit:cover; border:2px solid #c8e6c9; border-radius:4px; }
        .slip-photo .reg-badge { margin-top:6px; font-size:11px; font-weight:700; color:#1b5e20; background:#e8f5e9; padding:3px 6px; border-radius:3px; display:inline-block; }
        .slip-top-info { flex:1; }

        /* Section header bars */
        .section-bar { background:#1b5e20; color:#fff; padding:5px 10px; font-size:11px; font-weight:700; letter-spacing:1px; text-transform:uppercase; margin:14px 0 4px; border-radius:3px; }

        /* Detail table */
        .slip-table { width:100%; border-collapse:collapse; font-size:12px; margin-bottom:2px; }
        .slip-table th, .slip-table td { padding:6px 10px; border:1px solid #e0e0e0; text-align:left; vertical-align:top; }
        .slip-table th { background:#f5f5f5; color:#555; font-weight:600; width:22%; white-space:nowrap; }
        .slip-table td { color:#222; }

        /* Action buttons */
        .slip-actions { display:flex; gap:12px; justify-content:center; margin-top:22px; }
        .btn-print { background:#4CAF50; color:#fff; border:none; padding:10px 28px; font-size:14px; font-weight:700; border-radius:4px; cursor:pointer; }
        .btn-back  { background:#546e7a; color:#fff; border:none; padding:10px 28px; font-size:14px; font-weight:700; border-radius:4px; cursor:pointer; text-decoration:none; display:inline-block; }
        .btn-print:hover { background:#388e3c; }
        .btn-back:hover  { background:#37474f; }

        .slip-footer { margin-top:20px; text-align:center; font-size:11px; color:#999; border-top:1px solid #eee; padding-top:8px; }

        @media print {
            body { background:#fff; padding:0; }
            .slip-actions { display:none; }
            .slip-wrapper { border:none; padding:8px; box-shadow:none; }
            @page { size:A4; margin:10mm; }
        }
    </style>
</head>
<body>
<div class="slip-wrapper">

    <?= reportheader($siteinfos, $schoolyearsessionobj) ?>

    <div class="slip-title">Admission Slip</div>

    <?php
    $typeMap      = [1 => 'Transport', 2 => 'Hostel', 3 => 'Day Scholar'];
    $motherTongue = [1 => 'Telugu', 2 => 'English', 3 => 'Hindi', 4 => 'Kannada', 5 => 'Malayalam', 6 => 'Urdhu'];
    $photo        = $profile->photo ?? 'default.png';
    $photoPath    = file_exists(FCPATH . 'uploads/student/' . $photo)
                    ? base_url('uploads/student/' . $photo)
                    : base_url('assets/images/default_user.jpg');
    ?>

    <!-- Top: photo + enrollment summary -->
    <div class="slip-top">
        <div class="slip-photo">
            <img src="<?= $photoPath ?>" alt="Photo">
            <div class="reg-badge"><?= htmlspecialchars($profile->srregisterNO) ?></div>
        </div>
        <div class="slip-top-info">
            <table class="slip-table">
                <tr>
                    <th>Student Name</th>
                    <td><strong><?= htmlspecialchars($profile->srname) ?></strong></td>
                    <th>Admission No</th>
                    <td><?= htmlspecialchars($profile->srregisterNO) ?></td>
                </tr>
                <tr>
                    <th>Class</th>
                    <td><?= htmlspecialchars($profile->srclasses ?? '') ?></td>
                    <th>Section</th>
                    <td><?= htmlspecialchars($profile->srsection ?? '') ?></td>
                </tr>
                <tr>
                    <th>Roll No</th>
                    <td><?= htmlspecialchars($profile->srroll ?? '') ?></td>
                    <th>Admission Date</th>
                    <td><?= ($profile->admission_date && $profile->admission_date != '0000-00-00') ? date('d-M-Y', strtotime($profile->admission_date)) : date('d-M-Y') ?></td>
                </tr>
                <tr>
                    <th>Student Type</th>
                    <td><?= $typeMap[(int)($profile->studentType ?? 3)] ?? 'Day Scholar' ?></td>
                    <th>Joined Class</th>
                    <td><?= isset($all_classes[$profile->joined_class]) ? htmlspecialchars($all_classes[$profile->joined_class]) : '' ?></td>
                </tr>
                <tr>
                    <th>Student Group</th>
                    <td><?= isset($student_groups[$profile->srstudentgroupID]) ? htmlspecialchars($student_groups[$profile->srstudentgroupID]) : '' ?></td>
                    <th>Optional Subject</th>
                    <td><?= isset($subjects[$profile->sroptionalsubjectID]) ? htmlspecialchars($subjects[$profile->sroptionalsubjectID]) : '' ?></td>
                </tr>
                <tr>
                    <th>Remarks</th>
                    <td colspan="3"><?= htmlspecialchars($profile->remarks ?? '') ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Personal Details -->
    <div class="section-bar">Personal Details</div>
    <table class="slip-table">
        <tr>
            <th>First Name</th>
            <td><?= htmlspecialchars($profile->first_name ?? '') ?></td>
            <th>Last Name</th>
            <td><?= htmlspecialchars($profile->last_name ?? '') ?></td>
        </tr>
        <tr>
            <th>ID Card Name</th>
            <td><?= htmlspecialchars($profile->name ?? '') ?></td>
            <th>Gender</th>
            <td><?= htmlspecialchars($profile->sex ?? '') ?></td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td><?= ($profile->dob && $profile->dob != '0000-00-00') ? date('d-M-Y', strtotime($profile->dob)) : '' ?></td>
            <th>Blood Group</th>
            <td><?= htmlspecialchars($profile->bloodgroup ?? '') ?></td>
        </tr>
        <tr>
            <th>Religion</th>
            <td><?= htmlspecialchars($profile->religion ?? '') ?></td>
            <th>Caste</th>
            <td><?= htmlspecialchars($profile->caste ?? '') ?></td>
        </tr>
        <tr>
            <th>Sub Caste</th>
            <td><?= htmlspecialchars($profile->sub_caste ?? '') ?></td>
            <th>Mother Tongue</th>
            <td><?= isset($motherTongue[(int)($profile->mother_toungue ?? 0)]) ? $motherTongue[(int)$profile->mother_toungue] : '' ?></td>
        </tr>
        <tr>
            <th>PEN Number</th>
            <td><?= htmlspecialchars($profile->pen_number ?? '') ?></td>
            <th>Child ID</th>
            <td><?= htmlspecialchars($profile->child_id ?? '') ?></td>
        </tr>
        <tr>
            <th>Medium</th>
            <td><?= htmlspecialchars($profile->medium ?? '') ?></td>
            <th>RF ID</th>
            <td><?= htmlspecialchars($profile->rf_id ?? '') ?></td>
        </tr>
        <tr>
            <th>Aadhar No</th>
            <td><?= htmlspecialchars($profile->aadharCardNumber ?? '') ?></td>
            <th>Ration Card No</th>
            <td><?= htmlspecialchars($profile->ration_card ?? '') ?></td>
        </tr>
        <tr>
            <th>Mole 1</th>
            <td><?= htmlspecialchars(trim($profile->mole1 ?? '')) ?></td>
            <th>Mole 2</th>
            <td><?= htmlspecialchars(trim($profile->mole2 ?? '')) ?></td>
        </tr>
    </table>

    <!-- Parent / Guardian Details -->
    <div class="section-bar">Parent / Guardian Details</div>
    <table class="slip-table">
        <tr>
            <th>Father's Name</th>
            <td><?= htmlspecialchars(customCompute($parents) ? ($parents->father_name ?? '') : '') ?></td>
            <th>Father's Aadhar</th>
            <td><?= htmlspecialchars(customCompute($parents) ? ($parents->father_aadhar ?? '') : '') ?></td>
        </tr>
        <tr>
            <th>Mother's Name</th>
            <td><?= htmlspecialchars(customCompute($parents) ? ($parents->mother_name ?? '') : '') ?></td>
            <th>Mother's Aadhar</th>
            <td><?= htmlspecialchars(customCompute($parents) ? ($parents->mother_aadhar ?? '') : '') ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?= htmlspecialchars($profile->phone ?? '') ?></td>
            <th>Whatsapp</th>
            <td><?= htmlspecialchars($profile->alternative_phone1 ?? '') ?></td>
        </tr>
        <tr>
            <th>Alt. Phone 2</th>
            <td><?= htmlspecialchars($profile->alternative_phone2 ?? '') ?></td>
            <th>Email</th>
            <td><?= htmlspecialchars($profile->email ?? '') ?></td>
        </tr>
    </table>

    <!-- Address Details -->
    <div class="section-bar">Address Details</div>
    <table class="slip-table">
        <tr>
            <th>Address</th>
            <td><?= htmlspecialchars($profile->address ?? '') ?></td>
            <th>Village</th>
            <td><?= htmlspecialchars($profile->village_name ?? '') ?></td>
        </tr>
        <tr>
            <th>State</th>
            <td><?= htmlspecialchars($profile->state ?? '') ?></td>
            <th>Country</th>
            <td><?= htmlspecialchars($profile->country ?? '') ?></td>
        </tr>
    </table>

    <!-- Bank Details -->
    <div class="section-bar">Bank Details</div>
    <table class="slip-table">
        <tr>
            <th>Account No</th>
            <td><?= htmlspecialchars($profile->account_no ?? '') ?></td>
            <th>Bank Name</th>
            <td><?= htmlspecialchars($profile->bank_name ?? '') ?></td>
        </tr>
        <tr>
            <th>IFSC Code</th>
            <td><?= htmlspecialchars($profile->ifsc_code ?? '') ?></td>
            <th>Branch Name</th>
            <td><?= htmlspecialchars($profile->branch_name ?? '') ?></td>
        </tr>
    </table>

    <?php if ((int)($profile->studentType ?? 3) == 1 && customCompute($transport_details)): ?>
    <!-- Transport Details -->
    <div class="section-bar">Transport Details</div>
    <table class="slip-table">
        <tr>
            <th>Route</th>
            <td><?= customCompute($transport_route) ? htmlspecialchars($transport_route->route) : '' ?></td>
            <th>Transport Fee</th>
            <td><?= htmlspecialchars($transport_details->tbalance ?? '') ?></td>
        </tr>
        <tr>
            <th>Join Date</th>
            <td colspan="3"><?= ($transport_details->tjoindate && $transport_details->tjoindate != '0000-00-00') ? date('d-M-Y', strtotime($transport_details->tjoindate)) : '' ?></td>
        </tr>
    </table>
    <?php endif; ?>

    <?php if ((int)($profile->studentType ?? 3) == 2 && customCompute($hostel_details)): ?>
    <!-- Hostel Details -->
    <div class="section-bar">Hostel Details</div>
    <table class="slip-table">
        <tr>
            <th>Hostel Fee</th>
            <td><?= htmlspecialchars($hostel_details->hbalance ?? '') ?></td>
            <th>Join Date</th>
            <td><?= ($hostel_details->hjoindate && $hostel_details->hjoindate != '0000-00-00') ? date('d-M-Y', strtotime($hostel_details->hjoindate)) : '' ?></td>
        </tr>
    </table>
    <?php endif; ?>

    <!-- Reference & Sibling Details -->
    <div class="section-bar">Reference &amp; Sibling Details</div>
    <table class="slip-table">
        <tr>
            <th>Referred By</th>
            <td colspan="3"><?= htmlspecialchars($refered_by_label ?? '') ?></td>
        </tr>
    </table>
    <?php if (customCompute($siblings)): ?>
    <table class="slip-table" style="margin-top:4px;">
        <thead>
            <tr>
                <th style="background:#e8f5e9; color:#1b5e20;">Sibling Name</th>
                <th style="background:#e8f5e9; color:#1b5e20;">Class</th>
                <th style="background:#e8f5e9; color:#1b5e20;">Section</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($siblings as $sib): ?>
            <tr>
                <td><?= htmlspecialchars($sib->name) ?></td>
                <td><?= htmlspecialchars($sib->classes ?? '') ?></td>
                <td><?= htmlspecialchars($sib->section ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="slip-footer">
        Generated on <?= date('d M Y h:i A') ?> &nbsp;|&nbsp; <?= htmlspecialchars($siteinfos->sname) ?>
    </div>

    <div class="slip-actions">
        <button class="btn-print" onclick="window.print()">&#128438; Print Slip</button>
        <a class="btn-back" href="<?= base_url('student/index') ?>">&#8592; Back to Student List</a>
    </div>

</div>
<script>
    window.onload = function() { window.print(); };
</script>
</body>
</html>
