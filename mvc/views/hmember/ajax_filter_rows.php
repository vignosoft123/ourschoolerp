<?php if(customCompute($students)) { $i = 1; foreach($students as $student) { ?>
<tr data-hostel="<?=$student->hostel?>">
    <?php if(permissionChecker('hmember_add') && (($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1))) { ?>
    <td>
        <?php if($student->hostel == 0) { ?>
        <input type="checkbox" class="student-cb" data-student-id="<?=$student->studentID?>">
        <?php } ?>
    </td>
    <?php } ?>
    <td><?=$i?></td>
    <td><?=profileimage($student->photo)?></td>
    <td><?=$student->srname?></td>
    <td><?=$student->srroll?></td>
    <td><?=$student->phone?></td>
    <?php if(permissionChecker('hmember_add') || permissionChecker('hmember_edit') || permissionChecker('hmember_delete') || permissionChecker('hmember_view')) { ?>
    <td>
        <?php if($student->hostel == 0) {
            if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                echo btn_add('hmember/add/'.$student->studentID.'/'.$set, $this->lang->line('hmember'));
            }
        } else {
            echo btn_view('hmember/view/'.$student->studentID.'/'.$set, $this->lang->line('view')).' ';
            if(($siteinfos->school_year == $this->session->userdata('defaultschoolyearID')) || ($this->session->userdata('usertypeID') == 1)) {
                echo btn_edit('hmember/edit/'.$student->studentID.'/'.$set, $this->lang->line('edit')).' ';
                echo btn_delete('hmember/delete/'.$student->studentID.'/'.$set, $this->lang->line('delete'));
            }
        } ?>
    </td>
    <?php } ?>
</tr>
<?php $i++; } } ?>
