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
            margin-top: 0px;
            font-size: 20px;
            font-weight: bold;
            color: #c70039;
        }
        .idcard-details {
            margin-top: 0px;
            font-size: 15px;
            line-height: 1.6;
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



    <div class="idcard-container" id="idCardArea" >
        <?php foreach($idcards as $student) { ?>
            <div class="idcard-box">

                <!-- Student Photo -->
                <div class="idcard-photo" style="margin-top : 50%;width:40% ; height:30%">
                    <img src="<?=imagelink($student->photo)?>" alt="Student Photo">
                </div>

                <!-- Student Name -->
                <div class="idcard-name">
                    <?=strtoupper($student->name)?>
                </div>

                <!-- Student Details -->
                 <?php //echo "<pre>"; print_r($student);die;?>
                <div class="idcard-details">
                    <b class="text-black">Village</b>: <?=$student->address ?? ''?><br>
                    <b class="text-black">Medium</b>: <?=$student->medium ?? 'English'?><br>
                    <b class="text-black">C'ass/Sec</b>: <?=$classes[$student->classesID] ?? ''?> / <?=$sections[$student->sectionID] ?? ''?><br>
                    <b class="text-black">F'Name</b>: <?=$student->father_name ?? ''?><br>
                    <b class="text-black" >Contact No.</b>: <?=$student->phone ?? ''?><br><br>
                </div>

            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <p>No students found for this class/section.</p>
<?php } ?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
// function printIDCard() {
//     var divContents = document.getElementById("idCardArea").innerHTML;
//     var printWindow = window.open('', '', 'height=1100,width=750');
//     printWindow.document.write('<html><head><title>Print ID Card</title>');
//     printWindow.document.write('<style>@page{size:auto;margin:0;} body{margin:0;padding:0;}</style>');
//     printWindow.document.write('</head><body>');
//     printWindow.document.write(divContents);
//     printWindow.document.write('</body></html>');
//     printWindow.document.close();
//     printWindow.print();
// }




</script>


<script>
// document.getElementById("downloadPDF").addEventListener("click", function() {
//     const { jsPDF } = window.jspdf;

//     html2canvas(document.querySelector("#idCardArea"), { scale: 2 }).then(canvas => {
//         const imgData = canvas.toDataURL("image/png");
//         const pdf = new jsPDF("p", "mm", "a4");

//         let pageWidth  = pdf.internal.pageSize.getWidth();
//         let pageHeight = pdf.internal.pageSize.getHeight();

//         // Auto center the card inside A4
//         let imgWidth = pageWidth - 20;  
//         let imgHeight = (canvas.height * imgWidth) / canvas.width;

//         pdf.addImage(imgData, "PNG", 10, 10, imgWidth, imgHeight);
//         pdf.save("id-card.pdf");
//     });
// });


</script>

<script>
    // correct but cutting
// document.getElementById("downloadPDF").addEventListener("click", function() {
//     const { jsPDF } = window.jspdf;

//     html2canvas(document.querySelector("#idCardArea"), { scale: 2 }).then(canvas => {
//         const imgData = canvas.toDataURL("image/png");
//         const pdf = new jsPDF("p", "mm", "a4");

//         const pageWidth  = pdf.internal.pageSize.getWidth();
//         const pageHeight = pdf.internal.pageSize.getHeight();

//         const imgWidth = pageWidth;
//         const imgHeight = (canvas.height * imgWidth) / canvas.width;

//         let heightLeft = imgHeight;
//         let position = 0;

//         // First page
//         pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
//         heightLeft -= pageHeight;

//         // Additional pages
//         while (heightLeft > 0) {
//             position = heightLeft - imgHeight;
//             pdf.addPage();
//             pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
//             heightLeft -= pageHeight;
//         }

//         pdf.save("id-cards.pdf");
//     });
// });
</script>
 <script>
 
 
// document.getElementById("downloadPDF").addEventListener("click", async function () {
//     // alert("Please wait until download");
//   const { jsPDF } = window.jspdf;
//   const pdf = new jsPDF("p", "mm", "a4");

//   // Select each card element (update selectors if your class names differ)
//   const cards = document.querySelectorAll(".idcard-box, .idcardreport-frontend");
//   if (!cards.length) {
//     alert("No ID cards found. Check your card CSS classes in the selector.");
//     return;
//   }

//   // A4 layout constants
//   const pageW = 210, pageH = 297;
//   const margin = 10;           // outer page margin (mm)
//   const cols = 3, rows = 4;    // 3 × 4 = 12 per page
//   const gutterX = 4, gutterY = 6; // spacing between slots (mm)

//   const slotW = (pageW - margin * 2 - gutterX * (cols - 1)) / cols;
//   const slotH = (pageH - margin * 2 - gutterY * (rows - 1)) / rows;
//   const perPage = cols * rows;

//   // html2canvas options (CORS + crisp output)
//   const h2cOpts = {
//     scale: 2,
//     useCORS: true,
//     allowTaint: true,
//     backgroundColor: "#FFFFFF" // ensure white background
//   };

//   for (let i = 0; i < cards.length; i++) {
//     // New page every 12 cards
//     if (i > 0 && i % perPage === 0) {
//       pdf.addPage();
//     }

//     // Row/col position for this card within the current page
//     const indexOnPage = i % perPage;
//     const row = Math.floor(indexOnPage / cols);
//     const col = indexOnPage % cols;

//     const xSlot = margin + col * (slotW + gutterX);
//     const ySlot = margin + row * (slotH + gutterY);

//     // Render this card to canvas
//     const canvas = await html2canvas(cards[i], h2cOpts);
//     const imgData = canvas.toDataURL("image/png");

//     // Fit INSIDE the slot while preserving aspect ratio
//     // Convert using canvas aspect only; jsPDF expects mm sizes we supply below.
//     const imgWforSlot = slotW;
//     const imgHforSlot = (canvas.height * imgWforSlot) / canvas.width;

//     let drawW = imgWforSlot;
//     let drawH = imgHforSlot;

//     if (drawH > slotH) {
//       drawH = slotH;
//       drawW = (canvas.width * drawH) / canvas.height;
//     }

//     // Center inside the slot
//     const x = xSlot + (slotW - drawW) / 2;
//     const y = ySlot + (slotH - drawH) / 2;

//     pdf.addImage(imgData, "PNG", x, y, drawW, drawH);
//   }

//   pdf.save("id-cards-12-per-page.pdf");
// }); 



// document.getElementById("downloadPDF").addEventListener("click", async function () {
//   const { jsPDF } = window.jspdf;
//   const pdf = new jsPDF("p", "mm", "a4");

//   const cards = document.querySelectorAll(".idcard-box, .idcardreport-frontend");
//   if (!cards.length) {
//     alert("No ID cards found.");
//     return;
//   }

//   // A4 layout constants
//   const pageW = 210, pageH = 297;
//   const margin = 10;
//   const cols = 3, rows = 4; // 12 per page
//   const gutterX = 4, gutterY = 6;
//   const slotW = (pageW - margin * 2 - gutterX * (cols - 1)) / cols;
//   const slotH = (pageH - margin * 2 - gutterY * (rows - 1)) / rows;
//   const perPage = cols * rows;

//   // html2canvas options (lighter for performance)
//   const h2cOpts = {
//     scale: 1.2,         // reduced scale (good enough quality)
//     useCORS: true,
//     allowTaint: false,
//     backgroundColor: "#FFFFFF"
//   };

//   for (let i = 0; i < cards.length; i++) {
//     if (i > 0 && i % perPage === 0) {
//       pdf.addPage();
//     }

//     const indexOnPage = i % perPage;
//     const row = Math.floor(indexOnPage / cols);
//     const col = indexOnPage % cols;

//     const xSlot = margin + col * (slotW + gutterX);
//     const ySlot = margin + row * (slotH + gutterY);

//     // Render card
//     const canvas = await html2canvas(cards[i], h2cOpts);
//     const imgData = canvas.toDataURL("image/jpeg", 0.8); // JPEG + compression

//     // Fit inside slot
//     let drawW = slotW;
//     let drawH = (canvas.height * slotW) / canvas.width;
//     if (drawH > slotH) {
//       drawH = slotH;
//       drawW = (canvas.width * slotH) / canvas.height;
//     }

//     const x = xSlot + (slotW - drawW) / 2;
//     const y = ySlot + (slotH - drawH) / 2;

//     pdf.addImage(imgData, "JPEG", x, y, drawW, drawH);
    
//     // 💡 Release memory
//     canvas.width = canvas.height = 0;
//   }

//   pdf.save("id-cards-12-per-page.pdf");
// }); //working but late
 
 


</script>
 

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