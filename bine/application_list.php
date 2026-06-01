<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));
$table_name = 'admission';
$res = get_all($table_name);
if(isset($_GET['from_date']))
{
    $sql ="select * from admission where ";
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Online Application
		</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item">Student</li>
			<li class="breadcrumb-item active">Manage Application</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-12">

				<div class="box">
					<div class="box-header with-border">
						<form action='send_sms.php' method='post'>
							<h3 class="box-title">Manage Application
								<input type='hidden' name='mobiles' id='mobiles'>
								<input type='submit' class='btn btn-success btn-xs' value='Send SMS'>
							</h3>
						</form>
						<div class="box-tools pull-right">
						    <form>
							<input type='date' name='from_date'>
							<input type='date' name='to_date' onchange='submit()' onblur='submit()'>
							</form>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
							<table id="example" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>Sl.No.</th>
										<th>Photo</th>
										<th>App. No.</th>
										<th>Name</th>
										<th>Class </th>
										<th>Mobile No</th>
										<th>Whatsapp No</th>
										<th>Payment Status </th>
										<th>Date & Time</th>
										<th class='text-right' width='150px'>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i=1;
									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$link = encode('student_name=' . $row['student_name'] . '&id=' . $row['id']);
											$display_img = "<img src='upload/{$row['student_photo']}' height='30px' width='30px' >";
									?>
									        
											<tr>
											    <td><?php echo $i; ?></td>
											    <td><?php echo $display_img; ?></td>
												<!--<td><?php echo $row['app_no'] ?></td>-->
												<td><a href="new_adm.php?app_no=<?=$row['app_no']; ?>" onclick="return confirm('Do you Really want to admit this student?')" class='text-primary'><?php echo $row['app_no'] ?></a></td>
												<td><?php echo $row['student_name'] ?></td>
												<!--<td><?php echo $row['student_father'] ?></td>-->
												<td><?php echo $row['student_class'] ?></td>
												<td><span class='mobile' title='Click to send SMS'><?php echo $row['student_mobile'] ?></span></td>
												<td><?php echo $row['student_whatsapp'] ?></td>
												<td><?php echo $row['pay_status'] ?></td>
												<td><?php echo date('d-M-Y',strtotime($row['created_at'])) ?></td>
												<td class='text-right'>
													<?php echo btn_view($table_name, $id,  $row['student_name']); ?>
												
														<a class='fa fa-file-text-o btn btn-dark btn-xs' href='application_print?link=<?php echo $link; ?>' target='_op'></a>
														
													<?php if($row['pay_status'] =='UNPAID'){ ?>
												
										<?= btn_delete('admission',$id); ?> 	
													<button type="button" class="pay_btn btn btn-primary btn-xs" data-name='<?php echo $row['student_name']; ?>' 
													data-id='<?php echo $row['id'] ?>'  data-app_no='<?php echo $row['app_no'] ?>' >
                                                      ₹
                                                    </button>
                                                    <?php } else { ?>
                                                        
	                                                <a class='fa fa-print btn btn-success btn-xs' href='form_receipt?link=<?php echo $link; ?>' title='Print Receipt' target='_op'></a>
												
                                                    <?php } ?>
												</td>
											</tr>
									<?php 
										    $i++;
										    
										}
									} ?>
								</tbody>
								<!--<tfoot>
						<tr>
							<th>Name</th>
							<th>Position</th>
							<th>Office</th>
							<th>Age</th>
							<th>Start date</th>
							<th>Salary</th>
						</tr>
					</tfoot>-->
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

<?php require_once('required/footer2.php'); ?>

<script>
$(document).on('click',".pay_btn",function(){
    var id = $(this).data('id');
    var app_no = $(this).data('app_no');
    var name = $(this).data('name');
   
   
  $("#app_id").val(id);
  $("#student_name").val(name);
  $("#app_no").val(app_no);
  $("#exampleModal").modal('show');
});

</script>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Payment Details </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       	<form id='update_frm' action='update_adm_payment'>
        <label> Application No. </label>
        <input type='text' id='app_no' class='form-control' readonly >
        
        <label> Student Name </label>
        <input type='hidden' id='app_id' name='id' class='form-control' readonly >
        <input type='text' id='student_name' name='student_name' class='form-control' readonly >
        
        <label> Payment Date </label>
        <input type='date' name='pay_date' class='form-control'>
        
        <label> Payment Amount </label>
        <input type='number' name='pay_amount' class='form-control' value='500'>
        
        <label> Payment Mode </label>
        <select name='pay_mode' class='form-control' >
                <?php dropdown($payment_mode_list); ?>
        </select>
        
        <label> Payment Remarks / Txn No.</label>
        <input type='text' name='pay_txn_no' class='form-control' >
        </form>
      </div>
      <div class="modal-footer">
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
        	<button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
      </div>
    </div>
  </div>
</div>

