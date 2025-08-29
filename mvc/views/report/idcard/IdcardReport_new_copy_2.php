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
            margin-top: 6px;
            font-size: 20px;
            font-weight: bold;
            color: #c70039;
        }
        .idcard-details {
            margin-top: 0px;
            font-size: 15px;
            line-height: 2.0;
        }
        .idcard-details b {
            width: 120px;
            display: inline-block;
        }
    </style>

<button onclick="window.print()" class="btn btn-primary">
    <i class="fa fa-print"></i> Print
</button>
<button id="downloadPDF" class="btn btn-danger">
    <i class="fa fa-file-pdf-o"></i> Download PDF
</button>

<button id="downloadJPEGs" class="btn btn-primary">Download All as JPEG</button>

<button id="downloadZIP" class="btn btn-info">Download All as JPEG ZIP</button>



    <div class="idcard-container" id="idCardArea" >
        <?php foreach($idcards as $student) { ?>
            <div class="idcard-box">

                <!-- Student Photo -->
                <div class="idcard-photo" style="margin-top : 40%;width:40% ; height:30%">
                    <img src="<?=imagelink($student->photo)?>" alt="Student Photo">
                </div>

                <!-- Student Name -->
                <div class="idcard-name">
                    <?=strtoupper($student->name)?>
                </div>

                <!-- Student Details -->
                 <?php //echo "<pre>"; print_r($student);die;?>
               <b> <div class="idcard-details text-black" style="">
                    <b class="text-black">Medium</b>: <?=$student->medium ?? 'English'?><br>
                    <b class="text-black">Class/Sec</b>: <?=$classes[$student->classesID] ?? ''?> / <?=$sections[$student->sectionID] ?? ''?><br>
                    <b class="text-black">F'Name</b>: <?=$student->father_name ?? ''?><br>
                    <b class="text-black" >Contact No.</b>: <?=$student->phone ?? ''?><br>
                    <b class="text-black">Village</b>: <?=$student->address ?? ''?><br>

                </div></b>

            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <p>No students found for this class/section.</p>
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
