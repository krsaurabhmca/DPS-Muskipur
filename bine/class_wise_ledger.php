<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));
if (isset($_GET['student_class']) and $_GET['student_section'] == '') {
  $res = get_all('student', '*', array('status' => 'ACTIVE', 'student_class' => $student_class));
} else if (isset($_GET['student_section']) != ''  and $_GET['student_class'] != '') {
  $res = get_all('student', '*', array('status' => 'ACTIVE', 'student_section' => $student_section, 'student_class' => $student_class));
} else {
  //$res = get_all('student','*',array('status'=>'ACTIVE'));
}

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> Monthly Ledger</h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item"><a href="#transport">Fee</a></li>
      <li class="breadcrumb-item active">Ledger</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Basic Forms -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Fee Ledger of <?php echo $student_class . $student_section; ?> </h3>
        <div class="box-tools pull-right">
          <form>
            <select name='student_class' required>
              <?php dropdown($class_list, $student_class); ?>
            </select>
            <select name='student_section'>
              <?php dropdown($section_list, $student_section); ?>
            </select>
            <button class='btn btn-orange'> Show </button>
          </form>
        </div>
      </div>
      <!-- /.box-header -->

      <div class="box-body">

        <div class="table-responsive">
          <table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
            <thead>
              <tr>
                <th> Admission</th>
                <th> Roll No.</th>
                <th>Student Name</th>

                <?php
                foreach ($month_list as $month) {
                  echo "<th>" . $month . "</th>";
                }

                ?>

              </tr>
            </thead>
            <tbody>
              <?php
              if ($res['count'] > 0) {
                foreach ($res['data'] as $row) {
                  $stu_id = $row['id'];
                  $status = $row['student_status'];
                  echo "<tr class='odd gradeX'>";
                  echo "<td>" . $row['student_admission'] . "</td>";
                  echo "<td>" . $row['student_roll'] . "</td>";
                  echo "<td>" . $row['student_name'] . "</td>";

                  foreach ($month_list as $month) {
                    $pst = get_data('student_fee', $stu_id, remove_space($month), 'student_id')['data'];

                    echo "<td><a href='receipt?receipt_id=$pst' target='_blank' > $pst </a></td>";
                  }


                  echo "</td></tr>";
                }
              }
              ?>
              </tr>
            </tbody>
          </table>
        </div>


      </div>

    </div>
    <!-- end page-wrapper -->

</div>
<!-- end wrapper -->
</div>
<!-- end wrapper -->
</section>
</div>
<?php require_once('required/footer2.php'); ?>