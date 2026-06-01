<?php require_once('required/header.php'); ?>
<?php
require_once('required/menu.php');
if (isset($_REQUEST['att_date'])) {
    $att_date = $_REQUEST['att_date'];
} else {
    $att_date = date('Y-m-d');
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Manage Student Attendance &nbsp;<span class="badge badge-success badge-sm p-2">NEW</span></h1>
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
                    <div class="col-md-6">
                        <b class="h6"><span class="text-danger">Month</span> : <?php echo  date('M') ?> <span class="text-danger"> Year</span> : <?php echo date("Y"); ?></b>
                        [<?php if (isset($_REQUEST['class']) and isset($_REQUEST['section'])) {
                                extract($_REQUEST);
                                echo $class . "-" . $section;
                            } ?>]
                    </div>
                    <div class="col-md-6 float-right text-right">
                        <div class="row">
                            <div class="col-md-12">
                                <form>
                                    <select name="class" id="class" class='h6' required>
                                        <?php dropdown($class_list, $class); ?>
                                    </select>
                                    <select name="section" id="section" class='h6' required>
                                        <?php dropdown($section_list, $section); ?>
                                    </select>
                                    <input type='date' name='att_date' value='<?php echo $att_date; ?>' id='att_date' max='<?php echo date('Y-m-d'); ?>'>
                                    <button class="btn btn-warning" type="submit">Show</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="text-end" style="float:right">
                            <span class='btn btn-primary btn-sm border'>
                                <input type="checkbox" id="selectall" onClick="selectAll(this)" /> All Present
                            </span>&nbsp;
                            <button id='att_btn' class='btn btn-success btn-sm' title='Save Data' style="margin-right:8px;"><i class='fa fa-save'></i> </button> &nbsp;
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-stripped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Roll No.</th>
                                        <th>Admission No.</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if (isset($_REQUEST['class']) and isset($_REQUEST['section']) and isset($_REQUEST['att_date'])) {
                                        extract($_REQUEST);
                                        $res = get_all('student', '*', array('student_class' => $class, 'student_section' => $section,'status'=>'ACTIVE') ,'student_roll');
                                    ?>
                                        <form action='required/master_process?task=make_att' method='post' id='att_frm'>
                                        <?php
                                       
                                        if (date('D', strtotime($att_date)) == 'Sun') {
                                            echo "<script> alert('Selected Date is sunday'); </script>";
                                        }
                                        if ($res['count'] > 0) {
                                            //print_r($res);
                                            foreach ($res['data'] as $row) {
                                                $id = $row['id'];
                                                $stu_class = $row['student_class'];
                                                $stu_section = $row['student_section'];
                                                echo "<tr>";
                                                echo "<td>" . $i . "</td>";
                                                echo "<td>" . $row['student_roll'] . "</td>";
                                                echo "<td>" . $row['student_admission'] . "</td>";
                                                echo "<td>" . $row['student_name'] . "</td>";
                                                echo "<td>" . $stu_class . "</td>";
                                                echo "<td>" . $stu_section . "</td>";
                                                echo "<td width='185'>";
                                                $tbl_name = "student_att";
                                                $col_name = 'd_' . date('j', strtotime($att_date));
                                                $mvalue = remove_space(date('M_Y', strtotime($att_date)));
                                                $post = array('att_month' => $mvalue, 'student_id' => $id);
                                                
                                                $sql = "SELECT * FROM student_att WHERE student_id = '$id' AND att_month LIKE '$mvalue' ";
                                                $stu_att = direct_sql($sql);
                                                if ($stu_att['count'] == 0) {
                                                  $res2 = insert_data($tbl_name, $post);
                                                }
                                                
                                                if($att_date ==$today){
                                                    if ($stu_att['data'][0][$col_name] == 'P') {
                                                        echo "<input type='checkbox' data-class='$stu_class' data-section='$stu_section' value ='$id' name='sel_id[]' class='chk' checked>";
                                                
                                                    } else{
                                                         echo "<input type='checkbox' data-class='$stu_class' data-section='$stu_section' value ='$id' name='sel_id[]' class='chk'>";
                                                    }  
                                              
                                                }
                                                else{
                                                if ($stu_att['data'][0][$col_name] == 'P') {
                                                    echo "<span class='badge bg-success p-2'>P</span>";
                                                } else if ($stu_att['data'][0][$col_name] == "A") {
                                                    echo "<span class='badge bg-danger p-2'>A</span>";
                                                }
                                                }
                                                
                                                echo "</td></tr>";
                                                $i++;
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
        for (var i in checkboxes)
            checkboxes[i].checked = source.checked;
    }
</script>