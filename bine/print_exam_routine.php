<?php
require_once("required/op_lib.php");
$_POST = post_clean($_POST);
extract($_POST);
if (isset($student_class)) {
    $res = get_all('admit_card', '*', array('student_class' => $student_class));
} else { ?>
    <script>
        window.location = <?php echo $_SERVER['HTTP_REFERER']; ?>
    </script>
<?php }
?>


<style>
    /*body{*/
    /*	color:#000;*/
    /*	padding:0px;*/
    /*	margin:0px;*/
    /*}*/

    /*td{font-size:16px;padding:10px;font-family:calibri,arial;font-weight:600;}*/

    /*.idcard{width:750px; height:550px; background:url('assets/img/idcardpng') no-repeat; text-align:center; float:left; margin:6px; page-break-after:always;position:relative;}*/
    /*.photo{position:absolute;margin:auto;top:10px;left:10px;margin-right:50px;z-index:0;border:solid 0px #ddd;border-radius:100%;}*/
    /*.qr{position:absolute;width:55px; height:55px;margin:auto;top:255px;right:18px;z-index:0;border:solid 0px #000;border-radius:5px;}*/

    @media print {
        #printbtn {
            display: none;
        }

        @page {
            size: portrait;
        }

        #DivIdToPrint {
            page-break-inside: avoid;
            margin: 10px;
        }
    }
</style>
<table border='1' class='idcard mt-4' cellpadding='3' rules='all' width='750px' id='DivIdToPrint'>

    <tr height='85px'>

        <td align='center' class='header'>
            <img src='images/logo.png' height='120px' align='left'>
            <span style='font-size:36px;font-weight:800;font-family:calibri;text-transform:uppercase;color:maroon;'> <?php echo $full_name; ?> </span><br>
            <b>(Affiliated to CBSE, New Delhi upto 10+2) <br>
                <b>Affiliation No. : <?php echo $aff_no; ?> School No. : <?php echo $school_code; ?></b><br>
                <?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> 
                Contact No.: <?php echo $inst_contact; ?><br>
                Email : <?php echo $inst_email; ?> | Website : <?php echo $inst_url; ?>
        </td>

    </tr>
    <tr height='30px' bgcolor='#d5d5d5'>
        <td align="center">
            <center>
                <h3 class="text-center"> Time Table for Class <?php echo $student_class; ?></h3>
            </center>
        </td>
    </tr>
    <tr>
        <td style='text-align:left;padding:10px;vertical-align:top;'>
            <table class="table" width="100%" cellpadding='5' rules='all'>
                
                <tr align='center' >
                    <th> Date </th>
                    <th width='35%'> Subject </th>
                    <th> In Time </th>
                    <th> Out Time </th>
                   
                </tr>
          
            <?php
            
            $sql1 = "select distinct(exam_date) as edate from admit_card where student_class ='$student_class' order by exam_date "; 
            $res = direct_sql($sql1)['data'];
            
            
            foreach($res as $row1)
            {
                $edate = $row1['edate'];
                echo "<tr><td>" . date('d-M-Y', strtotime($edate)). "</td><td colspan='3'> <table width='100%' rules='none' border='0'>";
                
                $sql = "select * from admit_card where student_class ='$student_class' and exam_date ='$edate' order by in_time ";
                $routine = direct_sql($sql)['data'];
            
          //  $routine = get_all('admit_card', '*', array('student_class'=> $student_class),'')['data'];
                foreach($routine as $row)
                {
                    echo "<tr>";
                    echo "<td width='55%'>" .$row['subject'] ."</td>";
                    echo "<td>" . date('h:i A',strtotime($row['in_time'])) ."</td>";
                    echo "<td>" . date('h:i A',strtotime($row['out_time'])) ."</td>";
                    echo "</tr>";
                }
                
                echo "</td></table></tr>";
            }
	        ?>
	   
            </table>
        </td>
    </tr>
    <tr>
        <td valign='bottom'>

            <div style='float:right;bottom:10px;'>
                <img src='images/exam.png' align='right' height='65px'> <br>
                Exam Controller
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <b> Instructions :</b>
            <ul>
                <li>
                    Candidates should note that an authenticated Admit Card is an important document without which the candidates will not be permitted to appear for further selection
                </li>
                <li>Cell phones, calculators, watch calculators, alarm clocks, digital watches with built in calculators/ memory or any electronic or smart devices are not be allowed in the examination hall.</li>
                <li>
                    Candidates will not be allowed to leave the Examination hall till the test is completed. After submission of the test, candidates will not be allowed to re-enter the Examination hall.
                </li>
            </ul>
        </td>
    </tr>
</table>