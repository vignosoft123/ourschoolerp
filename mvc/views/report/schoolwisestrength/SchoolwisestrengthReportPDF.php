<?php
    $pct = function($n, $total) {
        return $total > 0 ? round(($n / $total) * 100, 2) : 0;
    };
?>
<div id="printablediv">
    <div class="box">
        <div class="col-sm-12">
            <?=reportheader($siteinfos, $schoolyear, true)?>
        </div>

        <h2 class="text-center"><?=$this->lang->line('panel_title')?></h2>

        <div class="box-body">

            <?php
                $cardBaseStyle = 'width:25%; text-align:center; padding:12px 4px; border:none; -webkit-border-radius:5px; border-radius:5px;';
                $valueStyle = 'font-size:18px; font-weight:bold; color:#ffffff;';
                $labelStyle = 'font-size:9px; color:#ffffff;';
            ?>
            <table style="width:100%; border-spacing:6px 0; margin-bottom:14px; border:none;">
                <tr>
                    <td style="<?=$cardBaseStyle?> background-color:#1565c0;">
                        <span style="<?=$valueStyle?>"><?=$totalStudents?></span><br>
                        <span style="<?=$labelStyle?>">Total Students</span>
                    </td>
                    <td style="<?=$cardBaseStyle?> background-color:#2e7d32;">
                        <span style="<?=$valueStyle?>"><?=$totalBoys?></span><br>
                        <span style="<?=$labelStyle?>">Boys (<?=$pct($totalBoys, $totalStudents)?>%)</span>
                    </td>
                    <td style="<?=$cardBaseStyle?> background-color:#c2185b;">
                        <span style="<?=$valueStyle?>"><?=$totalGirls?></span><br>
                        <span style="<?=$labelStyle?>">Girls (<?=$pct($totalGirls, $totalStudents)?>%)</span>
                    </td>
                    <td style="<?=$cardBaseStyle?> background-color:#e65100;">
                        <span style="<?=$valueStyle?>"><?=$totalClasses?></span><br>
                        <span style="<?=$labelStyle?>">Classes</span>
                    </td>
                </tr>
            </table>

            <div class="box box-solid classinfo">
                <div class="box-header bg-gray with-border">
                    <h3 class="box-title text-navy">Class &amp; Section Wise Strength</h3>
                </div>
                <div class="box-body">
                    <?php if (customCompute($classAgg)) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th rowspan="2">Class</th>
                                <th rowspan="2">Sections</th>
                                <th colspan="<?=count($sectionNameList) + 1?>">Boys</th>
                                <th colspan="<?=count($sectionNameList) + 1?>">Girls</th>
                                <th colspan="<?=count($sectionNameList) + 1?>">Total</th>
                            </tr>
                            <tr>
                                <?php foreach ($sectionNameList as $secName) { ?><th><?=$secName?></th><?php } ?>
                                <th>Total</th>
                                <?php foreach ($sectionNameList as $secName) { ?><th><?=$secName?></th><?php } ?>
                                <th>Total</th>
                                <?php foreach ($sectionNameList as $secName) { ?><th><?=$secName?></th><?php } ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $grandBySection = [];
                                $grandBoys = 0; $grandGirls = 0;
                                foreach ($classAgg as $c) {
                            ?>
                            <tr>
                                <td><?=$c['classes']?></td>
                                <td><?=count($c['sectionIDs'])?></td>
                                <?php foreach ($sectionNameList as $secName) {
                                    $b = $c['sections'][$secName]['M'] ?? 0;
                                    $grandBySection[$secName]['M'] = ($grandBySection[$secName]['M'] ?? 0) + $b;
                                ?><td><?=$b ?: '-'?></td><?php } ?>
                                <td><?=$c['totalM']?></td>
                                <?php foreach ($sectionNameList as $secName) {
                                    $g = $c['sections'][$secName]['F'] ?? 0;
                                    $grandBySection[$secName]['F'] = ($grandBySection[$secName]['F'] ?? 0) + $g;
                                ?><td><?=$g ?: '-'?></td><?php } ?>
                                <td><?=$c['totalF']?></td>
                                <?php foreach ($sectionNameList as $secName) {
                                    $t = ($c['sections'][$secName]['M'] ?? 0) + ($c['sections'][$secName]['F'] ?? 0);
                                ?><td><?=$t ?: '-'?></td><?php } ?>
                                <td><?=$c['totalM'] + $c['totalF']?></td>
                            </tr>
                            <?php
                                    $grandBoys  += $c['totalM'];
                                    $grandGirls += $c['totalF'];
                                }
                            ?>
                            <tr>
                                <td><b>Grand Total</b></td>
                                <td><b><?=$totalSections?></b></td>
                                <?php foreach ($sectionNameList as $secName) { ?><td><b><?=$grandBySection[$secName]['M'] ?? 0?></b></td><?php } ?>
                                <td><b><?=$grandBoys?></b></td>
                                <?php foreach ($sectionNameList as $secName) { ?><td><b><?=$grandBySection[$secName]['F'] ?? 0?></b></td><?php } ?>
                                <td><b><?=$grandGirls?></b></td>
                                <?php foreach ($sectionNameList as $secName) { ?><td><b><?=($grandBySection[$secName]['M'] ?? 0) + ($grandBySection[$secName]['F'] ?? 0)?></b></td><?php } ?>
                                <td><b><?=$grandBoys + $grandGirls?></b></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php } else { ?>
                        <div class="notfound">No students found for the selected filter.</div>
                    <?php } ?>
                </div>
            </div>

            <br>

            <div class="box box-solid subjectandteacher">
                <div class="box-header bg-gray with-border">
                    <h3 class="box-title text-navy">Caste Wise Strength</h3>
                </div>
                <div class="box-body">
                    <?php if (customCompute($casteAgg)) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Caste Category</th>
                                <th>Boys</th>
                                <th>Girls</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($casteAgg as $label => $counts) {
                                $ctotal = $counts['M'] + $counts['F'];
                            ?>
                            <tr>
                                <td><?=$label?></td>
                                <td><?=$counts['M']?></td>
                                <td><?=$counts['F']?></td>
                                <td><?=$ctotal?> (<?=$pct($ctotal, $totalStudents)?>%)</td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td><b>Total</b></td>
                                <td><b><?=$totalBoys?></b></td>
                                <td><b><?=$totalGirls?></b></td>
                                <td><b><?=$totalStudents?></b></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php } else { ?>
                        <div class="notfound">No caste data found for the selected filter.</div>
                    <?php } ?>
                </div>
            </div>

        </div><!-- Body -->
        <hr class="hr">
        <div class="col-sm-12">
            <?=reportfooter($siteinfos, $schoolyear, true)?>
        </div>
    </div>
</div>
