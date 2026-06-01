<?php
require_once('required/header.php');
require_once('required/menu.php');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> Trip Management</h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item"><a href="#transport">Transport</a></li>
      <li class="breadcrumb-item active">Trip Management</li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-12">
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
                print_r($res2);
                
               echo $sql ="UPDATE `student_fee` SET `current_dues` = '$final_dues' WHERE student_admission = '$student_admission'";
               $res4 = direct_sql($sql);
                print_r($res4);
                
             $con = mysqli_connect('localhost',$db_name,'@Dps_2001',$db_name) or die("connection Error");
             $res5 = update_data('student', array('status' => 'PROMOTED'),$student_admission,'student_admission'); 
              print_r($res5);
             
          }
        ?>
      </div>

    </div>
  </section>
</div>
<?php require_once('required/footer2.php'); ?>