<?php
require_once("required/op_lib.php");
$_POST = post_clean($_POST);
extract($_POST);

$student_list = [];

if (!empty($student_admission)) {
    $student_admission = str_replace(' ', '', $student_admission);
    $student_list = explode(",", $student_admission);
} else {
    // $sql = "SELECT * FROM student WHERE student_class = '$student_class' AND student_section = '$student_section' AND status = 'ACTIVE'";
    $sql = "SELECT * FROM student_new WHERE student_class = '$student_class' AND student_section = '$student_section' AND status = 'ACTIVE'";

    if (!empty($student_roll)) {
        $sql .= " AND finance_type = 'NORMAL' AND student_roll IN ($student_roll)";
    }

    $res = direct_sql($sql)['data'];

    foreach ($res as $row) {
        $student_list[] = $row['id'];
    }
}
?>

<style>
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

    .sign {
        padding: 15px;
        padding-top: 50px;
    }

    .idcard {
        margin-top: 20px;
    }

    .admit {
        width: 150px;
        padding: 5px;
        margin: 5px;
        margin-left: 280px;
        border: 1px solid #000;
        border-radius: 25px;
    }

    .head {
        width: 650px;
        justify-content: center;
        text-align: center;
    }
</style>

<?php
$current_year = date("Y");
foreach ($student_list as $sid) : 
    // $student = get_data("student", $sid)['data'];
    $student = get_data("student_new", $sid)['data'];
    extract($student);

    $exam_details = get_all('admit_card', '*', array('student_class' => $student_class))['data'][0];
    $exam_name = $exam_details['exam_name'];
?>

<table border='1' class='idcard mt-4' cellpadding='3' rules='all' width='750px' id='DivIdToPrint'>
    <tr height='85px'>
        <td class='header'>
            <img src='images/dps_logo.png' height='120px' align='left'>
            <div class='head'>
                <span class='head' style='font-size:34px;font-weight:800;font-family:calibri;text-transform:uppercase;color:maroon;'><?php echo $full_name; ?></span><br>
                <span class='head1' style='font-size:16px;font-weight:800;font-family:calibri;'><?php echo "$inst_address1, $inst_address2"; ?></span><br>
                <span class='head2' style='font-size:16px;font-weight:800;font-family:calibri;'>Contact No.:<?php echo $inst_contact; ?></span><br>
                <span class='head3' style='font-size:16px;font-weight:800;font-family:calibri;'>Email :<?php echo $inst_email; ?></span>
                <span class='head4' style='font-size:16px;font-weight:800;font-family:calibri;'>, Website :<?php echo $inst_url; ?></span>
               
                <div class='admit'><b>ADMIT CARD</b></div>
            </div>
        </td>
    </tr>
    <tr height='30px' bgcolor='#d5d5d5'>
        <td>
            <center><b><?php echo "PT-1 ( $current_year )"; ?></b></center>
        </td>
    </tr>
    <tr>
        <td style='text-align:left;padding:5px;vertical-align:top;'>
            <table class='table' width='750px' cellpadding='5' style='border: 1px solid #000;'>
                <tr>
                    <td colspan='4' style='vertical-align:top;'>
                        <table>
                            <tr>
                                <td style='width:180px;line-height:2'>Admission No.:</td>
                                <td><b><?php echo $student_admission; ?></b></td>
                            </tr>
                            <tr>
                                <td style='width:180px;line-height:2'>Class & Section:</td>
                                <td><b><?php echo "$student_class ($student_section)"; ?></b></td>
                            </tr>
                            <tr>
                                <td style='width:180px;line-height:2'>Roll No.:</td>
                                <td><b><?php echo $student_roll; ?></b></td>
                            </tr>
                            <tr>
                                <td style='width:180px;line-height:2'>Name:</td>
                                <td><b><?php echo $student_name; ?></b></td>
                            </tr>
                            <tr>
                                <td style='width:180px;line-height:2'>Father's Name:</td>
                                <td><b><?php echo $student_father; ?></b></td>
                            </tr>
                            <tr>
                                <td style='width:180px;line-height:2'>Address:</td>
                                <td><b><?php echo $student_address1; ?></b></td>
                            </tr>
                        </table>
                    </td>
                    <td style='text-align:right; padding-left: 20px;'>
                        <img src='required/upload/<?php echo $student_photo; ?>' alt='Student Photo' width='150px' height='180px' />
                    </td>
                </tr>
                <tr class='bg-dark text-light text-center'>
                    <!--<th colspan='7' style='border: 1px solid #000;'><h2><b>EXAM SCHEDULE</b></h2></th>-->
                    <th colspan='7' style='border: 1px solid #000;'><h2><b>EXAM SCHEDULE</b></h2></th>
                </tr>
                <tr align='center'>
                    <th style='border: 1px solid #000; width:20%;'>Date</th>
                    <th style='border: 1px solid #000; width:20%;'>Subject Name</th>
                    <th style='border: 1px solid #000; width:10%;'>Start Time</th>
                    <th style='border: 1px solid #000; width:12%;'>Duration</th>
                    <!--<th style='border: 1px solid #000; width:20%;'>Oral Subject</th>-->
                    <!--<th style='border: 1px solid #000; width:15%;'>Start Time</th>-->
                    <!--<th style='border: 1px solid #000; width:20%;'>Duration</th>-->
                    <th style='border: 1px solid #000; width:20%;'>Inv. Sign.</th>
                </tr>

                <?php
                $sql = "SELECT admit_card.*, subject.subject_name FROM admit_card 
                        JOIN subject ON admit_card.subject_id = subject.id 
                        WHERE admit_card.student_class = '$student_class' 
                        ORDER BY exam_date,start_time ASC";
                $routine = direct_sql($sql)['data'];

                foreach ($routine as $row) {
                    echo "<tr align='center'>";
                    echo "<td style='border: 1px solid #000;'>" . date('d-M-Y', strtotime($row['exam_date'])) . "</td>";
                    echo "<td style='border: 1px solid #000;'>" . $row['subject_name'] . "</td>";
                    echo "<td style='border: 1px solid #000;'>" . $row['start_time'] . "</td>";
                    echo "<td style='border: 1px solid #000;'>45 Minute</td>";
                    echo "<td style='border: 1px solid #000;'></td>";

                    // Add Oral Subject only if it's not "Drawing"
                    // if (strtolower($row['subject_name']) !== 'drawing') {
                    //     echo "<td style='border: 1px solid #000;'>" . $row['subject_name'] . "</td>";
                    //     // echo "<td style='border: 1px solid #000;'>" . $row['start_time'] . "</td>";
                    //     echo "<td style='border: 1px solid #000;'>30 Minute</td>";
                    // } else {
                    //     echo "<td colspan='3' style='border: 1px solid #000;'>-</td>";
                    // }

                    echo "</tr>";
                }
                ?>
            </table>
        </td>
    </tr>
    <tr>
        <td valign='bottom'>
            <div class='sign' style='float:right;bottom:10px;'>
                <img src='images/principal.png' style="height:40px;margin-right:-130px;margin-bottom:20px">
                Signature of Principal</div>
            <div class='sign' style='float:left;bottom:10px;'>
                <img src='images/ec.png' style="height:40px;margin-left:60px;margin-bottom:20px">
            <span style='margin-left:-100px;'>Controller of Examination</span></div>
        </td>
    </tr>
    <tr>
        <td>
            <b>Instructions:</b>
            <ul>
                <li>Candidates should note that an authenticated Admit Card is an important document without which the candidate will not be permitted to appear for further selection.</li>
                <li>Cell phones, calculators, digital watches with built-in calculators/memory, or any electronic or smart devices are not allowed in the exam hall.</li>
                <li>Candidates will not be allowed to leave the test hall until the test is completed. After submission of the test, candidates will not be allowed to re-enter the test hall.</li>
            </ul>
        </td>
    </tr>
</table>

<?php endforeach; ?>
