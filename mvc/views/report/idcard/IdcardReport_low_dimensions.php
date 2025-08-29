<?php if(count($idcards)) { ?>
    <style>
        .idcard-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        /* --- Screen preview only (not actual export size) --- */
        .idcard-box {
            width: 180px;   /* ~56mm */
            height: 280px;  /* ~88mm */
            background: url('<?=base_url("uploads/idcard_templates/".$id_card_template["value"])?>') no-repeat center center;
            background-size: cover;
            position: relative;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            border: 1px solid #ccc;
            padding: 10px;
        }
        .idcard-photo {
            width: 60px;
            height: 80px;
            border: 1px solid #000;
            margin: 0 auto;
            margin-top: 35%;
        }
        .idcard-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .idcard-name {
            text-align: center;
            margin-top: 4px;
            font-size: 12px;
            font-weight: bold;
            color: #c70039;
        }
        .idcard-details {
            margin-top: 2px;
            font-size: 9px;
            line-height: 1.4;
        }
        .idcard-details b {
            width: 80px;
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

    <div class="idcard-container" id="idCardArea">
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
                <div class="idcard-details text-black">
                    <b>Medium</b>: <?=$student->medium ?? 'English'?><br>
                    <b>Class/Sec</b>: <?=$classes[$student->classesID] ?? ''?> / <?=$sections[$student->sectionID] ?? ''?><br>
                    <b>F'Name</b>: <?=$student->father_name ?? ''?><br>
                    <b>Contact No.</b>: <?=$student->phone ?? ''?><br>
                    <b>Village</b>: <?=$student->address ?? ''?><br>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <p>No students found for this class/section.</p>
<?php } ?>

<!-- JS Libs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
// =================== FIXED CARD SIZE (300 DPI) ===================
const cardWpx = 661;  // 56mm × 300dpi / 25.4
const cardHpx = 1043; // 88mm × 300dpi / 25.4
const cardWmm = 56;
const cardHmm = 88;

// For high-quality JPEG export
const h2cOptsExport = {
  scale: 3,              // higher scale = sharper image
  width: cardWpx,
  height: cardHpx,
  useCORS: true,
  backgroundColor: "#FFFFFF"
};

// ========== PDF EXPORT (12 per A4) ==========
document.getElementById("downloadPDF").addEventListener("click", async function () {
  const btn = this;
  btn.disabled = true;
  const orig = btn.innerText;
  btn.innerText = "Preparing PDF...";

  try {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF("p", "mm", "a4");

    const pageW = 210, pageH = 297;
    const margin = 10;
    const cols = 3, rows = 4; // 12 per page
    const gutterX = 4, gutterY = 6;
    const slotW = (pageW - margin * 2 - gutterX * (cols - 1)) / cols;
    const slotH = (pageH - margin * 2 - gutterY * (rows - 1)) / rows;
    const perPage = cols * rows;

    const cards = document.querySelectorAll(".idcard-box");
    for (let i = 0; i < cards.length; i++) {
      if (i > 0 && i % perPage === 0) pdf.addPage();

      const idx = i % perPage;
      const row = Math.floor(idx / cols);
      const col = idx % cols;

      const x = margin + col * (slotW + gutterX);
      const y = margin + row * (slotH + gutterY);

      const canvas = await html2canvas(cards[i], { scale: 2, useCORS: true });
      const imgData = canvas.toDataURL("image/jpeg", 1.0);

      pdf.addImage(imgData, "JPEG", x, y, slotW, slotH);
      canvas.remove();
    }

    pdf.save("idcards-12-per-page.pdf");
  } catch (err) {
    console.error(err);
    alert("PDF error");
  }
  btn.disabled = false;
  btn.innerText = orig;
});

// ========== INDIVIDUAL JPEG ==========
document.getElementById("downloadJPEGs").addEventListener("click", async function () {
  const btn = this;
  btn.disabled = true;
  const orig = btn.innerText;
  btn.innerText = "Preparing JPEGs...";

  try {
    const cards = document.querySelectorAll(".idcard-box");
    for (let i = 0; i < cards.length; i++) {
      const canvas = await html2canvas(cards[i], h2cOptsExport);
      const imgData = canvas.toDataURL("image/jpeg", 1.0);
      const link = document.createElement("a");
      link.href = imgData;
      link.download = `idcard_${i + 1}.jpg`;
      link.click();
      canvas.remove();
    }
  } catch (err) {
    console.error(err);
    alert("JPEG error");
  }
  btn.disabled = false;
  btn.innerText = orig;
});

// ========== ZIP OF JPEGs ==========
document.getElementById("downloadZIP").addEventListener("click", async function () {
  const btn = this;
  btn.disabled = true;
  const orig = btn.innerText;
  btn.innerText = "Preparing ZIP...";

  try {
    const zip = new JSZip();
    const cards = document.querySelectorAll(".idcard-box");

    for (let i = 0; i < cards.length; i++) {
      const canvas = await html2canvas(cards[i], h2cOptsExport);
      const dataURL = canvas.toDataURL("image/jpeg", 1.0);
      const base64 = dataURL.split(",")[1];
      zip.file(`idcard_${i + 1}.jpg`, base64, { base64: true });
      canvas.remove();
    }

    const blob = await zip.generateAsync({ type: "blob" });
    saveAs(blob, "idcards.zip");
  } catch (err) {
    console.error(err);
    alert("ZIP error");
  }
  btn.disabled = false;
  btn.innerText = orig;
});
</script>
