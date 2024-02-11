<!DOCTYPE html>

<?php 

?>
<html>
<head>
<title>Study Certificate</title>
<style>
body{
    font-family: Arial, Helvetica, sans-serif;
    width: 900px;
    margin: auto;
}
.mainborder{
    border: 5px solid #2632a4;
    padding: 3px;
}
.innder-border{
    border: 2px solid #2632a4;
}
.header{
    text-align: center;
    line-height: .4;
}
.header h1{
    font-family:'Times New Roman', Times, serif;
    font-size: 52px;
    font-style: italic;
    text-transform: capitalize;
    font-weight: bold;
   
}
.institute_name{
    font-style: italic;
    text-transform: capitalize;
    font-weight: bold;
    font-size: 18px;
}
 
.bordered-text {
  display: flex;
  align-items: center; /* Center text vertically */
  
  justify-content: center;
  font-weight: 1000;
}
 
.left-line,
.right-line {
  height: 2px; /* Adjust line height */
  width: 23.3%; /* Adjust line width */
  background-color: #2632a4; /* Line color */
}
 
.text {
    font-size: 24px;

    padding: 10px;  
    border: 2px solid #2632a4;
}
.maincontent{
    font-style: italic;
    padding: 5px;
    font-family:'Times New Roman', Times, serif;
}

.input {
  display: flex; 
  padding: 5px;
  
} 
.input::after {
  border-bottom: 3px dotted #2632a4;
  content: '';
  flex: 1;
}

.dateinput {
  display: flex; 
  padding: 5px;
  
} 
.dateinput::after {
  border-bottom: 3px dotted #2632a4;
  content: '';
  width: 30%;
}

.text-container {
    display: flex;
    padding: 5px;
}
.border-after-text {
    border-bottom: 3px dotted #2632a4;
    flex: 1;
}
 
.footer{
    margin-top:50px;
    padding: 5px;
    width: 100%;
    display: flex;
}
.footer .left{
    width: 75%;
}
.footeraddress{
    padding-bottom: 1%;
}
</style>
</head>
<body>
    <main style="color: #2632a4;">

          
        <div class="mainborder">
            <div class = "innder-border" style="height: 550px;">
                <div class="header">
                    <h1>Sri potti sriramulu degree College</h1>
                    <span align ="center">(Affiliated to Acharya Nagarjuna University)</span>
                    <p style="font-size:24px;;"><span style="font-weight: bolder;">DARSI</span><span>&nbsp;- 523 247,</span>  <span>Prakasam Dist. A.P</span></p>

                  

                    <div class="bordered-text">
                        <div class="left-line"></div>
                        <div class="text">STUDY & CONDUCT CERTIFICATE</div>
                        <div class="right-line"></div>
                    </div>
                </div>
                <div class="maincontent">
                    <p class="input">This is to certify that Mr / Miss</p>
                    <p class="text-container"><span class="input_text">S/o. D/o</span><span class="border-after-text"></span><span>is / was a student</span></p>

                    <p class="input">of &nbsp;<span class="institute_name">Sri Potti sriramulu Degree College,</span> &nbsp;<span class="address">Darsi</span>, During the years</span></p>
                    <p class="text-container"><span>with group</span><span class="border-after-text"></span>Medium His/Her Character and Conduct is <span class="border-after-text"></span></p>
                </div>

                <div></div>
                <div class="footer">
                    <div class="left">
                        <div class="footeraddress">DARSI</div>
                        
                        <div class="dateinput">Date</div>
                    </div>
                    <div class="right">
                        <div>
                            <span>Principal</span>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>

    </main>

</body>
</html>