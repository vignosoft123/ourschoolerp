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
                <div class="idcard-photo" style="margin-top : 49%;width:49%">
                    <img src="<?=imagelink($student->photo)?>" alt="Student Photo">
                </div>

                <!-- Student Name -->
                <div class="idcard-name">
                    <?=strtoupper($student->name)?>
                </div>

                <!-- Student Details -->
                <div class="idcard-details">
                    <!-- <b>Department</b>: <?=$student->department ?? ''?><br> -->
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
document.getElementById("downloadPDF").addEventListener("click", function() {
    const { jsPDF } = window.jspdf;

    html2canvas(document.querySelector("#idCardArea"), { scale: 2 }).then(canvas => {
        const imgData = canvas.toDataURL("image/png");
        const pdf = new jsPDF("p", "mm", "a4");

        const pageWidth  = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();

        const imgWidth = pageWidth;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        let heightLeft = imgHeight;
        let position = 0;

        // First page
        pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Additional pages
        while (heightLeft > 0) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        pdf.save("id-cards.pdf");
    });
});
</script>
 
 
<!-- <script>
document.getElementById("downloadPDF").addEventListener("click", function () {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF("p", "mm", "a4"); // Portrait, A4 size

    const cards = document.querySelectorAll(".id-card"); // your card class
    const pageWidth = 210;
    const pageHeight = 297;

    const cols = 3; // 3 cards per row
    const rows = 4; // 4 rows per page
    const cardWidth = (pageWidth - 20) / cols;  // 10mm left+right margin
    const cardHeight = (pageHeight - 20) / rows; // 10mm top+bottom margin

    let x = 10; // start X margin
    let y = 10; // start Y margin
    let colCount = 0;
    let rowCount = 0;

    let cardIndex = 0;

    function processCard(i) {
        if (i >= cards.length) {
            pdf.save("id-cards.pdf");
            return;
        }

        html2canvas(cards[i], { scale: 2 }).then(canvas => {
            const imgData = canvas.toDataURL("image/png");

            pdf.addImage(imgData, "PNG", x, y, cardWidth, cardHeight);

            colCount++;
            x += cardWidth;

            if (colCount >= cols) {
                colCount = 0;
                x = 10;
                rowCount++;
                y += cardHeight;
            }

            if (rowCount >= rows) {
                // New page
                pdf.addPage();
                x = 10;
                y = 10;
                rowCount = 0;
                colCount = 0;
            }

            processCard(i + 1); // next card
        });
    }

    processCard(0);
}); -->
</script>
