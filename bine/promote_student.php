<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage Student
    </h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item">Student</li>
      <li class="breadcrumb-item active">Promote Student</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <!-- Advanced Tables -->
        <div class="box">
          <div class="box-header with-border">
            Promote Student
            <div style='float:right;'>
              <form action='promote_student.php' action='post'>
                Fi<akey>l</akey>ter By
                <select name="student_class">
                  <?php dropdown($class_list, $_GET['student_class']) ?>
                </select>
                <select name="student_section" onchange='submit()' accesskey='l'>
                  <?php dropdown($section_list, $_GET['student_section']) ?>
                </select>
              </form>
            </div>
          </div>
          <div class="box-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-hover">
                <thead>
                  <tr>

                    <th>ID</th>
                    <th>Adm No.</th>
                    <th>Class </th>
                    <th>Roll No.</th>
                    <th>Student Name</th>
                    <th>Area/Fare</th>
                    <th>Student Type </th>
                    <th>Status </th>
                    <th>Current Dues </th>
                    <th>Final Dues </th>
                    <th>Promote to </th>
                    <th>Operation.</th>
                  </tr>
                </thead>

                <tbody>
                  <form action='' method='post'>
                    <?php
                    if (isset($_GET['student_class']) and isset($_GET['student_class'])) {
                      $student_class = trim($_GET['student_class']);
                      $student_section = trim($_GET['student_section']);
                      $sql = "select * from student where student_class = '$student_class' and student_section = '$student_section' and status ='ACTIVE' ";


                      $res = mysqli_query($con, $sql) or die("Error in selecting Student" . mysqli_error($con));
                      $i = 1;
                      while ($row = mysqli_fetch_array($res)) {

                        $id = $row['id'];
                        $status = $row['status'];

                        $dues_list  = implode(duesmonthcount($id)['list'], ',');

                        echo "<tr class='odd gradeX'>";
                        $cid = array_search($row['student_class'], $class_list);
                        $promote_class = $class_list[$cid + 1];
                        $finaldues = finaldues($id);
                        $adm = $row['student_admission'];

                        $promote_link = 'student_id=' . $id . '&student_admission=' . $adm . '&final_dues=' . $finaldues . '&promote_class=' . $promote_class;
                        //echo"<td><a href='print_application.php?student_id=$stu_id' target='_blank'>".$row['student_name']."</a></td>";
                        echo "<td>" . $i . "</td>";
                        echo "<td>" . $row['student_admission'] . "</td>";
                        echo "<td>" . $row['student_class'] . "-" . $row['student_section'] . "</td>";
                        echo "<td>" . $row['student_roll'] . "</td>";
                        echo "<td>" . $row['student_name'] . "</td>";
                        echo "<td align='right'>";
                        if (get_data('student', $id, 'student_type')['data'] == 'TRANSPORT') {
                          echo get_data('transport_area', $row['area_id'], 'area_fee')['data'];
                        }
                        echo "</td>";


                        echo "<td>" . $row['student_type'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td align='right'>" . get_data('student_fee', $id, 'current_dues', 'student_id')['data'] . "</td>";


                        echo "<td class='text-right text-danger' title='" . $dues_list . "'>" . $finaldues . "</td>";
                        //echo"<td><a title='". finaldues($id)['month']."' data-toggle='tooltip' data-placement='top' >".$finaldues."</a></td>";
                        echo "<td>" . $promote_class . "</td>";


                        echo "<td width='105'>";
                        create_check('PID' . $id, encode($promote_link));
                        //	echo"<input type='checkbox' name ='link[]' value='".encode($promote_link)."'>";
                        //echo "<a href='promote_process.php?link=".encode($promote_link)."'  title='Promote to Next Class ' ><button class='btn btn-warning btn-xs' >Promote </button></a>";


                        echo "</td></tr>";
                        $i = $i + 1;
                      }

                    ?>
                      </tr>
                      <tr>
                        <td align='right' colspan='6'>

                          <?php create_check('SelectAll', encode($promote_link));
                          ?>
                        </td>
                        <td align='left' colspan='6'>
                          <input type='submit' value=' Promote Selected ' class='btn btn-sm btn-success' name='promote'>
                        </td>
                      </tr>
                    <?php } ?>
                  </form>
                </tbody>


              </table>
            </div>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
       
    $new_db = 'u673864504_dps_2627';
    $new_session = '2026-2027';
    $created_at = date('Y-m-d h:i:s');
    
    $all_list = post_clean($_POST);
    print_r($all_list);
      foreach ($all_list  as $list) {
      
        $data = decode(xss_clean($list));
        extract($data);
        
        $con = mysqli_connect('localhost', 'u673864504_dps_2627', '@Dps_2001', 'u673864504_dps_2627') or die("Unable to Connect, Check the Connection Parameter. " . mysqli_error($con));
        
           $udata = array('student_class' => $promote_class, 'base_dues' => $final_dues, 'student_session' => $new_session, 'created_at' => $created_at, 'status' => 'ACTIVE','admission_type'=>'OLD');
           $res2  = update_multi_data($new_db . '.student', $udata, ['student_admission'=>$student_admission]);
            // print_r($res2);
            
           echo $sql ="UPDATE `student_fee` SET `current_dues` = '$final_dues' WHERE student_admission = '$student_admission'";
           $res4 = direct_sql($sql);
            // print_r($res4);
            
         $con = mysqli_connect('localhost',$db_name,'@Dps_2001',$db_name) or die("connection Error");
         $res5 = update_data('student', array('status' => 'PROMOTED'),$student_admission,'student_admission'); 
        //   print_r($res5);
         
    }
?>
<?php require_once('required/footer2.php'); ?>

<script>
  $(document).ready(function() {
    $('#selectall').change(function() {
      if (this.checked) {
        $(".fee-month").prop('checked', true);
      } else {
        $(".fee-month").prop('checked', false);
      }
    });
  });
</script>