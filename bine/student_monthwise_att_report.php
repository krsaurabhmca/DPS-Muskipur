<?php require_once('required/header.php'); ?>
<?php
require_once('required/menu.php');
if (isset($_REQUEST['month'])) {
    $month =  $_REQUEST['month'];
    $year = date('Y');
}
$month_split = strtolower(substr($month, 0, 3));
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Monthwise Student Attendance</h1>
        <!--&nbsp;<span class="badge badge-success badge-sm p-2">NEW</span>-->
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Attendance</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-8">
                        <b class="h6"><span class="text-danger">Month</span> : <?php echo  strtoupper($month_split); ?> <span class="text-danger"> Year</span> : <?php echo $year; ?></b>
                        [<?php if (isset($_REQUEST['class']) and isset($_REQUEST['section'])) {
                                extract($_REQUEST);
                                echo $class . "-" . $section;
                            } ?>] &nbsp; <button class="btn btn-success btn-sm border-rounded" id="export">Export</button>
                    </div>
                    <div class="col-md-4 float-right">
                        <div class="row">
                            <div class="col-md-12">
                                <form>
                                    <select name="class" id="class" class='h6' required>
                                        <?php dropdown($class_list, $class); ?>
                                    </select>
                                    <select name="section" id="section" class='h6' required>
                                        <?php dropdown($section_list, $section); ?>
                                    </select>
                                    <select name="month" id="month" class="h6" required>
                                        <option value=""></option>
                                        <?php dropdown($att_month_list, $month); ?>
                                    </select>
                                    <button class="btn btn-warning btn-md" type="submit">Show</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-stripped">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <?php
                                         $mn = date("m", strtotime($_REQUEST['month']));
                                        $lastd = date("t", strtotime($_REQUEST['month']));
                                       
                                        for ($i = 1; $i <= $lastd; $i++) {
                                            $date =date('Y')."-".$mn."-".$i;
                                           $day =date('D',strtotime($date));
                                         
                                            if($day =='Sun')
                                            {
                                            echo "<th class='bg-warning text-light'>" . $i . " ".$day. "</th>";
                                            }
                                            else{
                                            echo "<th>" . $i . $day. "</th>";
                                            }
                                        }
                                        ?>
                                        <th>Total Present</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if (isset($_REQUEST['class']) and isset($_REQUEST['section']) and isset($_REQUEST['month'])) {
                                        extract($_REQUEST);
                                        $res = get_all('student', '*', array('student_class' => $class, 'student_section' => $section));
                                    ?>
                                    <?php
                                        if ($res['count'] > 0) {
                                            foreach ($res['data'] as $row) {
                                                $id = $row['id'];
                                                $stu_adm = $row['student_admission'];
                                                $stu_class = $row['student_class'];
                                                $stu_section = $row['student_section'];
                                                
                                                
                                                echo "<tr>";
                                                echo "<td>" . $stu_adm . "</td>";
                                                echo "<td>" . $row['student_name'] . "</td>";
                                                echo "<td>" . $stu_class . "</td>";
                                                echo "<td>" . $stu_section . "</td>";
                                                // echo "<td width='185'>";
                                                $tbl_name = "student_att";
                                                $datemonth = $month_split . "_" . $year;
                                                $mvalue = remove_space($datemonth);
                                                $post = array('att_month' => $mvalue, 'student_id' => $id);
                                                $sql = "SELECT * FROM `student_att` WHERE `student_id` = $id AND `att_month` LIKE '$mvalue' ";
                                                $stu_att = direct_sql($sql);
                                                
                                                if ($stu_att['count'] > 0) { {
                                                        foreach ($stu_att['data'] as $row) {
                                                            $count = 0;
                                        for ($i = 1; $i <= $lastd; $i++) {
                                        $day = 'd_' . $i;
                                        
                                        $cdate =date('Y')."-".$mn."-".$i;
                                        
                                        $d =date('D',strtotime($cdate));
                                     
                                        if($d =='Sun')
                                        {
                                        echo "<td class='bg-warning text-light text-center'>X</td>";
                                        }
                                        else if ($row[$day] == "A") {
                                                     
                                            
                                        echo "<td bgcolor='pink'>" . $row[$day] . "</td>";
                                                        
                                                    }
                                        else{
                                              $count++;
                                        echo "<td '>" . $row[$day] . "</td>";
                                                    
                                        }
                                        
                                       
                                                }
                                            }
                                        }
                                                    echo "<td>" . $count . "</td>";
                                                    echo "</tr>";
                                                    $i++;
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    </form>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php require_once('required/footer2.php'); ?>
<script>
    //=========SELECT ALL CHECK BOX WITH SAME NAME=======//
    function selectAll(source) {
        checkboxes = document.getElementsByName('sel_id[]');
        for (var i in checkboxes) {
            checkboxes[i].checked = source.checked;
        }
    }
    $("#export").click(function() {
        $("#example1").table2excel({
            // exclude CSS class
            exclude: ".noExl",
            name: "Worksheet Name",
            filename: "Monthwise_Student_Attendance_Report <?php echo $class . "-" . $section;?> ", //do not include extension
            fileext: ".xls", // file extension
            exclude_img: true,
            exclude_links: false,
            exclude_inputs: true
        });
    });
</script>