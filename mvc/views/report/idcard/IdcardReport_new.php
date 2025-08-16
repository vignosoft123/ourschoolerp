<?php if(count($idcards)) { ?>
    <style>
        .idcard-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .idcard-box {
            width: 400px;
            height: 600px;
            background: url('<?=base_url("uploads/idcard_templates/".$id_card_template["value"])?>') no-repeat center center;
            background-size: cover;
            position: relative;
            padding: 30px;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            border: 1px solid #ccc;
        }
        .idcard-photo {
            width: 150px;
            height: 180px;
            border: 2px solid #000;
            margin: 0 auto;
            margin-top: 140px;
        }
        .idcard-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .idcard-name {
            text-align: center;
            margin-top: 20px;
            font-size: 20px;
            font-weight: bold;
            color: #c70039;
        }
        .idcard-details {
            margin-top: 20px;
            font-size: 16px;
            line-height: 1.6;
        }
        .idcard-details b {
            width: 120px;
            display: inline-block;
        }
    </style>

    <div class="idcard-container">
        <?php foreach($idcards as $student) { ?>
            <div class="idcard-box">

                <!-- Student Photo -->
                <div class="idcard-photo">
                    <img src="<?=imagelink($student->photo)?>" alt="Student Photo">
                </div>

                <!-- Student Name -->
                <div class="idcard-name">
                    <?=strtoupper($student->name)?>
                </div>

                <!-- Student Details -->
                <div class="idcard-details">
                    <b>Department</b>: <?=$student->department ?? ''?><br>
                    <b>Medium</b>: <?=$student->medium ?? ''?><br>
                    <b>C'ass/Sec</b>: <?=$classes[$student->classesID] ?? ''?> / <?=$sections[$student->sectionID] ?? ''?><br>
                    <b>F'Name</b>: <?=$student->father_name ?? ''?><br>
                    <b>Contact No.</b>: <?=$student->phone ?? ''?><br>
                </div>

            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <p>No students found for this class/section.</p>
<?php } ?>
