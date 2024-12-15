<?php 
?>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>School</title>
    <style>
  body {
    margin: 40px;
}
.main-wrapper {
    border: 5px rgb(141, 139, 139);
    border-style: groove;
    height: 600px;
    width: 850px;
    margin: auto;
    border-radius: 3%;
}
.logo-heading {
    display: flex;
}
.logo-heading img {
    width: 100px;
}
.main-heading h1 {
    color: #9c9898;
}
.logo {
    width: 100px;
    margin-left: 20px;
    margin-top: 5px;
}
.main-heading {
    margin: auto;
    text-align: center;
}
.table-start table {
    width: 100%;
    font-weight: bolder;
    border-collapse: collapse;
    border-bottom: 2px solid lightgray;
}
.table-start table thead {
    background-color: #4b4646 !important; /* Make sure this color is applied */
    color: #fff;
    width: 100%;
}
.table-start table thead th {
    padding: 12px;
}
.center {
    text-align: center !important;
}
.table-start table tr td, .table-start table tr {
    padding: 10px;
}
.table-start thead th {
    border-left: 0px solid black;
}
.table-start tbody td:nth-child(2) {
    border-left: 2px solid lightgray;
    width: 30%;
}
.footer {
    padding: 10px;
    display: flex;
}
.student-details {
    padding-left: 20px;
    padding-right: 20px;
    display: flex;
    justify-content: space-between;
}
.student-details table {
    font-weight: bold;
}

/* Add a style to print */
@media print {
    body {
        margin: 0;
        padding: 0;
    }
    .main-wrapper {
    border: 5px rgb(141, 139, 139);
    border-style: groove;
    height: 600px;
    width: 850px;
    margin: auto;
    border-radius: 3%;
}
    .print-button {
        display: none;
    }
    /* Ensure background is visible in print */
    .table-start thead {
      background-color: #4b4646 !important; /* Force background in print */
    }
    .table-start thead th {
        color: #fff !important;
    }
}

    </style>
  </head>