<?php $sl = $this->input->post('offset')+1; ?>
<?php foreach($students as $stu): ?>
<tr>
    <td><?= $sl++; ?></td>
    <td><img src="<?=base_url('uploads/images/'.$stu->photo)?>" width="30" /></td>
    <td class="excel-only1"><?= $stu->studentID ?></td>
    <td><?= $stu->name ?> (<?= $stu->roll ?>)</td>
    <?php foreach($subjects as $sub): 
        $mark = isset($marksArr[$stu->studentID][$sub->subjectID]) ? $marksArr[$stu->studentID][$sub->subjectID] : 0;
    ?>
    <td><?= $mark ?></td>
    <td class="excel-only"><?= $mark ?></td>
    <?php endforeach; ?>
    <td><?= $studentResults[$stu->studentID]['total'] ?></td>
    <td><?= ($studentResults[$stu->studentID]['isFail']) ? 'F' : '' ?></td>
    <td><?= $studentResults[$stu->studentID]['rank'] ?></td>
    <td><input type="checkbox" name="send_sms[]" value="<?= $stu->studentID ?>"></td>
</tr>
<?php endforeach; ?>
