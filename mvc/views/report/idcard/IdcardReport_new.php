<?php if(!isset($selectedFields) || !is_array($selectedFields)) {
    $selectedFields = array('medium', 'class_section', 'father_name', 'contact_no', 'village', 'blood_group');
} ?>
<?php
    $fontStyle = (isset($fontStyle) && is_array($fontStyle)) ? $fontStyle : array();
    $idcardDetailsStyle = sprintf(
        'font-family:%s; font-size:%dpx; font-weight:%s; font-style:%s;',
        $fontStyle['fontFamily'] ?? 'Arial, sans-serif',
        $fontStyle['fontSize'] ?? 15,
        !empty($fontStyle['bold']) ? 'bold' : 'normal',
        !empty($fontStyle['italic']) ? 'italic' : 'normal'
    );
    $idcardLabelStyle = 'color:'.($fontStyle['labelColor'] ?? '#000000').';';
    $idcardValueStyle = 'color:'.($fontStyle['valueColor'] ?? '#000000').';';
    $photoBorderColor = $fontStyle['photoBorderColor'] ?? '#000000';
?>
<?php if(count($idcards)) { ?>

<div class="box" style="border-top: 3px solid #388e3c;">
    <div class="rpt-box-header">
        <h3><i class="fa fa-id-card"></i> ID Card Report</h3>
    </div>

    <div class="box-body">
        <div class="rpt-action-bar">
            <button onclick="window.print()" class="btn btn-primary rpt-action-btn">
                <i class="fa fa-print"></i> Print
            </button>
            <button id="downloadPDF" class="btn btn-danger rpt-action-btn">
                <i class="fa fa-file-pdf-o"></i> Download PDF
            </button>
            <button id="downloadJPEGs" class="btn btn-primary rpt-action-btn">
                <i class="fa fa-file-image-o"></i> Download All as JPEG
            </button>
            <button id="downloadZIP" class="btn btn-info rpt-action-btn">
                <i class="fa fa-file-zip-o"></i> Download All as JPEG ZIP
            </button>
        </div>

        <div class="idcard-container" id="idCardArea" >
            <?php foreach($idcards as $student) { ?>
                <div class="idcard-box" style="background-image:url('<?=base_url("uploads/idcard_templates/".$id_card_template["value"])?>');">

                    <!-- Student Photo -->
                    <div class="idcard-photo" style="margin-top : 40%;width:40% ; height:30%; border-color:<?=$photoBorderColor?>; background-image:url('<?=imagelink($student->photo)?>');"></div>

                    <!-- Student Name -->
                    <div class="idcard-name">
                        <?=strtoupper($student->name)?>
                    </div>

                    <!-- Student Details -->
                     <?php //echo "<pre>"; print_r($student);die;?>
                    <div class="idcard-details" style="<?=$idcardDetailsStyle?>">
                        <?php if($usertypeID == 3) { ?>
                        <?php if(in_array('medium', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Medium</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->medium ?? 'English'?></span><br>
                        <?php } ?>
                        <?php if(in_array('class_section', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Class/Sec</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$classes[$student->srclassesID] ?? ''?> / <?=$sections[$student->srsectionID] ?? ''?></span><br>
                        <?php } ?>
                        <?php if(in_array('father_name', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">F'Name</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->father_name ?? ''?></span><br>
                        <?php } ?>
                        <?php if(in_array('contact_no', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Contact No.</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->phone ?? ''?></span><br>
                        <?php } ?>
                        <?php if(in_array('village', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Village</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=(get_subdomain() === 'vasavi') ? ($student->address ?? '') : (!empty($student->village_name) ? $student->village_name : ($student->address ?? ''))?></span><br>
                        <?php } ?>
                        <?php if(in_array('blood_group', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Blood Group</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->bloodgroup ?? ''?></span><br>
                        <?php } ?>

                        <?php } elseif($usertypeID == 2) { ?>
                        <?php if(in_array('employee_id', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Employee ID</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->username ?? ''?></span><br>
                        <?php } ?>
                        <?php if(in_array('designation', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Designation</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->designation ?? ''?></span><br>
                        <?php } ?>
                        <?php if(in_array('contact_no', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Contact No.</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->phone ?? ''?></span><br>
                        <?php } ?>
                        <?php if(in_array('village', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Village</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->address ?? ''?></span><br>
                        <?php } ?>

                        <?php } else { ?>
                        <?php if(in_array('employee_id', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Employee ID</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->username ?? ''?></span><br>
                        <?php } ?>
                        <?php if(in_array('role', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Role</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$usertypes[$usertypeID] ?? ''?></span><br>
                        <?php } ?>
                        <?php if(in_array('contact_no', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Contact No.</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->phone ?? ''?></span><br>
                        <?php } ?>
                        <?php if(in_array('village', $selectedFields)) { ?>
                        <span class="idcard-label" style="<?=$idcardLabelStyle?>">Village</span>: <span class="idcard-value" style="<?=$idcardValueStyle?>"><?=$student->address ?? ''?></span><br>
                        <?php } ?>
                        <?php } ?>

                    </div>

                </div>
            <?php } ?>
        </div>
    </div>
</div>

<button class="rpt-scroll-top-btn" id="idcard-scroll-top-btn" title="Back to top">&#8679;</button>

<?php } else { ?>
    <p>No records found for the selected filter.</p>
<?php } ?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  

<script>
document.getElementById("downloadPDF").addEventListener("click", async function () {
  const btn = this;
  btn.disabled = true;
  const originalText = btn.innerText;
  btn.innerText = "Please wait... Downloading...";

  try {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF("p", "mm", "a4");

    const cards = document.querySelectorAll(".idcard-box, .idcardreport-frontend");
    if (!cards.length) {
      alert("No ID cards found.");
      btn.disabled = false;
      btn.innerText = originalText;
      return;
    }

    // A4 layout constants
    const pageW = 210, pageH = 297;
    const margin = 10;
    const cols = 3, rows = 4; // 12 per page
    const gutterX = 4, gutterY = 6;
    const slotW = (pageW - margin * 2 - gutterX * (cols - 1)) / cols;
    const slotH = (pageH - margin * 2 - gutterY * (rows - 1)) / rows;
    const perPage = cols * rows;

    // html2canvas options (lighter for performance)
    const h2cOpts = {
      scale: 1, // reduce for performance (1 = faster, less RAM)
      useCORS: true,
      allowTaint: false,
      backgroundColor: "#FFFFFF"
    };

    for (let i = 0; i < cards.length; i++) {
      if (i > 0 && i % perPage === 0) {
        pdf.addPage();
      }

      const indexOnPage = i % perPage;
      const row = Math.floor(indexOnPage / cols);
      const col = indexOnPage % cols;

      const xSlot = margin + col * (slotW + gutterX);
      const ySlot = margin + row * (slotH + gutterY);

      // Render card
      const canvas = await html2canvas(cards[i], h2cOpts);
      const imgData = canvas.toDataURL("image/jpeg", 0.7); // more compression

      // Fit inside slot
      let drawW = slotW;
      let drawH = (canvas.height * slotW) / canvas.width;
      if (drawH > slotH) {
        drawH = slotH;
        drawW = (canvas.width * slotH) / canvas.height;
      }

      const x = xSlot + (slotW - drawW) / 2;
      const y = ySlot + (slotH - drawH) / 2;

      pdf.addImage(imgData, "JPEG", x, y, drawW, drawH);

      // free memory
      canvas.remove();
    }

    pdf.save("id-cards-12-per-page.pdf");
  } catch (err) {
    console.error(err);
    alert("Error generating PDF");
  }

  // Reset button
  btn.disabled = false;
  btn.innerText = originalText;
});
</script>


<script>
document.getElementById("downloadJPEGs").addEventListener("click", async function () {
  const btn = this;
  btn.disabled = true;
  const originalText = btn.innerText;
  btn.innerText = "Please wait... Preparing...";

  try {
    const cards = document.querySelectorAll(".idcard-box, .idcardreport-frontend");
    if (!cards.length) {
      alert("No ID cards found.");
      btn.disabled = false;
      btn.innerText = originalText;
      return;
    }

    const h2cOpts = {
      scale: 3, // higher = sharper image (3 is good for printing)
      useCORS: true,
      allowTaint: false,
      backgroundColor: "#FFFFFF"
    };

    for (let i = 0; i < cards.length; i++) {
      const card = cards[i];
      const canvas = await html2canvas(card, h2cOpts);
      const imgData = canvas.toDataURL("image/jpeg", 1.0); // full quality

      // download each file
      const link = document.createElement("a");
      link.href = imgData;
      link.download = `idcard_${i + 1}.jpg`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      // free memory
      canvas.remove();
    }
  } catch (err) {
    console.error(err);
    alert("Error generating images");
  }

  btn.disabled = false;
  btn.innerText = originalText;
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>


<script>
document.getElementById("downloadZIP").addEventListener("click", async function () {
  const btn = this;
  btn.disabled = true;
  const originalText = btn.innerText;
  btn.innerText = "Please wait... Creating ZIP...";

  try {
    const zip = new JSZip();
    const cards = document.querySelectorAll(".idcard-box, .idcardreport-frontend");

    if (!cards.length) {
      alert("No ID cards found.");
      btn.disabled = false;
      btn.innerText = originalText;
      return;
    }

    const h2cOpts = {
      scale: 2,  // high quality (increase if needed, but memory usage goes up)
      useCORS: true,
      allowTaint: false,
      backgroundColor: "#FFFFFF"
    };

    for (let i = 0; i < cards.length; i++) {
      const canvas = await html2canvas(cards[i], h2cOpts);
      const imgData = canvas.toDataURL("image/jpeg", 1.0); // best quality
      const imgBlob = await (await fetch(imgData)).blob();

      // add to zip with sequential filenames
      zip.file(`idcard_${i + 1}.jpg`, imgBlob);

      // free memory
      canvas.remove();
    }

    const content = await zip.generateAsync({ type: "blob" });
    saveAs(content, "idcards.zip");

  } catch (err) {
    console.error(err);
    alert("Error generating ZIP");
  }

  btn.disabled = false;
  btn.innerText = originalText;
});
</script>

<script>
$(window).on('scroll', function() {
    $(this).scrollTop() > 200
        ? $('#idcard-scroll-top-btn').fadeIn(300)
        : $('#idcard-scroll-top-btn').fadeOut(300);
});
$('#idcard-scroll-top-btn').on('click', function() {
    $('html, body').animate({ scrollTop: 0 }, 400);
});
</script>
