<?php
    $dayHeaders = array();
    for ($d = 1; $d <= $daysInMonth; $d++) {
        $isNonSchoolDay = isset($nonSchoolDays[$d]);
        $dayOfWeek = date('D', mktime(0, 0, 0, $month, $d, $year));
        $dayHeaders[$d] = array(
            'label'    => $isNonSchoolDay ? $nonSchoolDays[$d] : $dayOfWeek,
            'nonSchool' => $isNonSchoolDay
        );
    }
?>
<div class="monthly-grid-wrap">
    <table id="monthly_attendance_table">
        <thead>
            <tr>
                <th class="checkbox-col"><input type="checkbox" id="select-all-students" title="Select all students"></th>
                <th class="student-col"><?= $this->lang->line('attendance_name') ?></th>
                <?php for ($d = 1; $d <= $daysInMonth; $d++): ?>
                    <th class="day-col<?= $dayHeaders[$d]['nonSchool'] ? ' non-school-day' : '' ?>" title="<?= date('d M Y', mktime(0, 0, 0, $month, $d, $year)) ?>">
                        <?php if (!$dayHeaders[$d]['nonSchool']): ?>
                            <span class="bulk-mark-cell">
                                <span class="mark-all-present-btn disabled" data-day="<?= $d ?>" title="Select at least one student below to enable"><i class="fa fa-check"></i></span>
                                <span class="mark-all-absent-btn disabled" data-day="<?= $d ?>" title="Select at least one student below to enable"><i class="fa fa-times"></i></span>
                            </span><br>
                        <?php endif; ?>
                        <?= $d ?><br><small><?= $dayHeaders[$d]['label'] ?></small>
                    </th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <?php
                    $attendance = isset($attendances[$student->studentID]) ? $attendances[$student->studentID] : null;
                ?>
                <tr data-attendanceid="<?= $attendance ? $attendance->attendanceID : 0 ?>">
                    <td class="checkbox-col"><input type="checkbox" class="student-row-check"></td>
                    <td class="student-col">
                        <?= htmlspecialchars($student->name) ?> (<?= htmlspecialchars($student->srroll) ?>)
                    </td>
                    <?php for ($d = 1; $d <= $daysInMonth; $d++): ?>
                        <?php if ($dayHeaders[$d]['nonSchool']): ?>
                            <td class="day-col non-school-day"><?= $dayHeaders[$d]['label'] ?></td>
                        <?php else: ?>
                            <?php
                                $aday = 'a' . $d;
                                $value = ($attendance && isset($attendance->$aday)) ? $attendance->$aday : null;
                                $isPresent = ($value === 'P');
                                $isAbsent  = ($value === 'A');
                            ?>
                            <td class="day-col">
                                <span class="attendance-toggle-cell" data-attendanceid="<?= $attendance ? $attendance->attendanceID : 0 ?>" data-day="<?= $d ?>">
                                    <span class="attendance-toggle-btn present<?= $isPresent ? ' active' : '' ?>" data-value="P" title="Present">P</span>
                                    <span class="attendance-toggle-btn absent<?= $isAbsent ? ' active' : '' ?>" data-value="A" title="Absent">A</span>
                                </span>
                            </td>
                        <?php endif; ?>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<p style="margin-top:8px; color:#888; font-size:12px;">
    <i class="fa fa-info-circle"></i> Each P/A click saves immediately - there is no separate Submit button on this page.
    The <i class="fa fa-check"></i> / <i class="fa fa-times"></i> above a date mark the checked students Present/Absent for that date in one click.
    Notifications (SMS/WhatsApp) for absences are not sent from the monthly grid; use the single-day
    <a href="<?= base_url('sattendance/add') ?>">Add Student Attendance</a> page for same-day marking with parent notifications.
</p>
