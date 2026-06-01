<?php require_once('required/header.php'); ?>
<?php
require_once('required/menu.php');

if (isset($_REQUEST['month'])) {
    $att_date = date('Y')."-".$_REQUEST['month']."-1";
} else {
    $att_date = date('Y-m-d');
}
$monthname = date('F', strtotime($att_date));
$month = date('m', strtotime($att_date));
$year = date('Y', strtotime($att_date));
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Classwise Attendance [<?php echo $monthname; ?>]</h1>
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
                        [<?php if (isset($_REQUEST['class']) and isset($_REQUEST['section'])) {
                                extract($_REQUEST);
                                echo $class . "-" . $section;
                            } ?>] &nbsp; <buuton class="btn btn-success btn-sm" id="export">Export</buuton>
                    </div>
                    <div class="col-md-4 float-right text-right">
                        <div class="row">
                            <div class="col-md-12">
                                <form action='#' method='post'> 
							Select Month 
							<select name='tbl_name' onchange='submit()'>
							    <option value=''></option>
								<?php for($i=1; $i<13;$i++){
									$dt =date('Y')."-".$i."-1";
									$val =remove_space(date('M_Y',strtotime($dt)));
									$mval =remove_space(date('F',strtotime($dt)));
									
									echo"<option value='$val'>". add_space($mval)."</option>";
								}?>
							</select>
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
                            <?php
		if($ct <>0)
		{
		$yr =substr($month_name,-4);
		$mn =substr($month_name,0,(strlen($month_name)-5));
		$ndate =$yr."-".$mn."-1";
		$lastday = date('t',strtotime($ndate));
		$day_list="count(d_1)";
			for($i=2; $i<=$lastday; $i++)
			{
			$day_list =$day_list.', count(d_'.$i.')';
			}
			
	   $sql = "select student.student_class, student.student_section, att_month, $day_list from $tbl_name ,student where att_month ='$month_name' and student.id =$tbl_name.id group by student_class, student_section order by student_class, student_section ";
		
	?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover"  id='dataTables-example'>
								
                                    <thead>
										
                                        <tr>
                                            <th>Class/ Section</th>
											<th>Total</th>
											<?php
											for($i=1; $i<=$lastday; $i++)
											{
											echo "<th>$i</th>";
											}
											?>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php 
										
										$res = mysqli_query($con,$sql) or die ("Error in selecting Student". mysqli_error($con));
										while($row =mysqli_fetch_array($res))
										{
																				
										echo"<tr class='odd gradeX'>";
										
							echo"<td>".$row['student_class']."-".$row['student_section']."</td>";
							echo"<td>".studentcount($row['student_class'],$row['student_section'])."</td>";
										for($i=1; $i<=$lastday; $i++)
											{
												$flist ='count(d_'.$i.')';
												$daysum[$i] =$daysum[$i] +$row[$flist];
												
												if ($row[$flist]<>0)
												{
													echo "<td>$row[$flist]</td>";
												}
												else{
													echo "<td></td>";
												}
											
											
											}
										echo "</tr>";
										}
									
                                       ?>
                                     
                                    </tbody>
									<tfoot>
									<tr bgcolor='pink'>
									<td colspan='2'> Total </td>
									<?php 
									for($j=1; $j<=count($daysum);  $j++){
										echo "<td> $daysum[$j] </td>";
									}
									?>
									</tr>
									<tfoot>
                                </table>
	<?php }
	
	else{
		echo "<center><h3><i class='fa fa-info-circle'></i> Data Not Available of Selected Month . </h3></center>";
	}
	?>
								
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
            filename: "Datewise_Student_Attendance_Report", //do not include extension
            fileext: ".xls", // file extension
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true
        });
    });
</script>