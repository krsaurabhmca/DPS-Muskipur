<?php
require_once('required/header.php');
require_once('required/menu.php');
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1> Cross Recheck Receipt</h1>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="breadcrumb-item"><a href="#fee">Account</a></li>
      <li class="breadcrumb-item active">Receipt </li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">

    <!-- Basic Forms -->
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Matching Current Dues with Last Receipt</h3>
        <div class="box-tools pull-right">
          <i class='fa fa-file-excel-o btn btn-info btn-sm' onclick='exportxls()'> </i>

        </div>
      </div>
      <!-- /.box-header -->

      <div class="box-body">
          <div class="table-responsive">
        <?php
        //echo "<pre>";
        function rtest($rid)
        {
          $res = get_data('receipt', $rid)['data'];
          $total = 0;
          $mlist = $res['paid_month'];
          $marr = explode(',', $mlist);
          $student_id = $res['student_id'];

          foreach ($marr as $month) {
            $all[$month] = monthly_fee($student_id, remove_space($month))['fee'];
          }
          $fee = array_add($all);
          //print_r($fee);
          $net_amount = ($fee['total'] + $res['previous_dues'] + $res['other_fee']) - $res['discount'];
          return $net_amount;
        }

        //print_r(rtest(317));

        $res = get_all('student', '*', array('status' => 'ACTIVE'), 'student_class, student_section,student_roll');
        echo '<table id="example1" class="table table-bordered table-hover display nowrap margin-top-10">'; 
        //echo "<table rules='all' cellpadding=3 border='1' width='100%' id='example'>";
        
        echo "<thead>";
        echo "<tr>
    <th> #</th>
    <th> Admission No.</th>
    <th> Student Class</th>
    <th> Student Roll</th>
    <th> Base Dues</th>
    <th> Current Dues</th>
    <th> Last Reciept No.</th>
    <th> Reciept Date</th>
    <th> Reciept Prev Dues</th>
    <th> Total Dues</th>
    <th> Receipt Re-Calculation</th>
    <th> Last Paid Amount</th>
    <th> Last Reciept Current Dues</th>
    </tr></thead><tbody>";
        $i = 1;
        foreach ($res['data']  as $row) {

          $cur_dues = get_data('student_fee', $row['id'], 'current_dues', 'student_id')['data'];

          $student_id = $row['id'];
          $sql = "select * from receipt where student_id =$student_id and status ='PAID' order by id desc limit 1";

          $res5 = direct_sql($sql)['data'][0];

          $rid =  $res5['id'];
          $cross_total = rtest($rid);
          if (intval($cross_total) != intval($res5['total'])) {
            $bgcolor = "bgcolor ='orange'";
          } else {
            $bgcolor = "bgcolor ='lightyellow'";
          }
          if ($cur_dues != $res5['current_dues'] and $res5['paid_amount'] != '') {
            $rid = $res5['id'];
            echo "<tr class='odd gradeX'>";
            echo "<td>" . $i . "</td>";
            // echo "<td>". $row['id'] ."</td>";
            echo "<td>" . $row['student_admission'] . "</td>";
            echo "<td>" . $row['student_class'] . "-" . $row['student_section'] . "</td>";
            echo "<td>" . $row['student_roll'] . "</td>";
            echo "<td>" . $row['base_dues'] . "</td>";
            echo "<td bgcolor='lightgreen'>" . $cur_dues . "</td>";
            echo "<td><a href='receipt.php?receipt_id=$rid' target='_blank'>" . $rid . "</a></td>";
            echo "<td>" . $res5['paid_date'] . "</td>";
            // echo "<td>". $res5['paid_month'] ."</td>";
            echo "<td>" . $res5['previous_dues'] . "</td>";
            echo "<td>" . $res5['total'] . "</td>";
            echo "<td $bgcolor >" . rtest($rid) . "</td>";
            echo "<td>" . $res5['paid_amount'] . "</td>";
            echo "<td>" . $res5['current_dues'] . "</td>";
            echo "</tr>";

            //  update_data('student_fee',array('current_dues'=>$res5['current_dues']), $row['id'], 'student_id'); 
          } else {
            $color = '';
            if ($res5['id'] == '' and $row['base_dues'] <> $cur_dues) {
              $color = 'pink';
            }
            echo "<tr>";

            echo "<td>" . $i . "</td>";
            echo "<td>" . $row['student_admission'] . "</td>";
            echo "<td>" . $row['student_class'] . "-" . $row['student_section'] . "</td>";
            echo "<td>" . $row['student_roll'] . "</td>";
            echo "<td  bgcolor='$color' >" . $row['base_dues'] . "</td>";
            echo "<td bgcolor='lightgreen'>" . $cur_dues . "</td>";
            echo "<td><a href='receipt.php?receipt_id=$rid' target='_blank'>" . $rid . "</a></td>";
            //echo "<td>" . $res5['id'] . "</td>";
            echo "<td>" . $res5['paid_date'] . "</td>";
            //echo "<td>". $res5['paid_month'] ."</td>";
            echo "<td>" . $res5['previous_dues'] . "</td>";
            echo "<td>" . $res5['total'] . "</td>";
            echo "<td $bgcolor>" . rtest($rid) . "</td>";
            echo "<td>" . $res5['paid_amount'] . "</td>";
            echo "<td>" . $res5['current_dues'] . "</td>";
            echo "</tr>";
          }


          $i++;
        }


        ?>
        </tbody></table>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require_once('required/footer2.php'); ?>