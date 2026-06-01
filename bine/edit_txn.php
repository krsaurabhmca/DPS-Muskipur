<?php require_once('header.php'); ?>
<?php require_once('menu.php'); 
$table_name ='account_txn';
if(isset($_GET['link']) and $_GET['link']!='')
{
	$data = decode($_GET['link']);
	$id = $data['id'];
}
else{	
	$fee =insert_row($table_name);
	$id = $fee['id'];
}

if($id!='')
{
  $res = get_data($table_name,$id);
  if($res['count']>0 and $res['status']=='success')
  {
	  extract($res['data']);
  }
}
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> Modify Transaction</h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#fee">Account</a></li>
        <li class="breadcrumb-item active">Modify Transaction</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
     
     <!-- Basic Forms -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">Add and Update Transaction Details </h3>

          <div class="box-tools pull-right">
			<!--<a class="btn btn-info btn-sm" href='add_fee' title='Add Fee Head' ><i class='fa fa-plus'></i> New </a>-->
			<button class="btn btn-success btn-sm" id='update_btn'><i class='fa fa-save'></i> Save </button>
			 
          </div>
        </div>
        <!-- /.box-header -->
	
        <div class="box-body">
		 	
				<div class="row">
						<div class="col-lg-4"></div>
						<div class="col-lg-4">
					
								<form action ='exp_update' id='update_frm' enctype='multipart/form-data'>
									<form action ='exp_entry' method ='post' id='update_frm'	enctype='multipart/form-data'>
					    <input type='text' type='hidden' value='<?php echo $id; ?>' readonly name='id' >
					    <div class="form-group">
								<label>Expense Type </label>
									<?php $account_head = get_data('account_head', $account_id,'account_type')['data']; ?>
								<select id='account_type' class='form-control'>
									<?php dropdown($account_head_list, $account_head); ?>
								</select>
						</div>		
					
						<div class="form-group">
							<label> Account Name </label>
						 
							<select name='account_id' class="form-control" id='account_id' value ='<?php echo $account_id;?>'>
							   	<?php dropdown_list('account_head','id','account_name',$account_id); ?>    
							</select>
						</div>
						
					
						<div class="form-group">
							<label>Txn Date</label>
							<input class="form-control"  type='date' value='<?php echo $txn_date; ?>'  name='txn_date' required>
						</div>	
						<div class="form-group">
							<label>Txn Amount</label>
							<input class="form-control"  type='number' name='txn_amount' value='<?php echo $txn_amount; ?>'  required autofocus>
						</div>
						
						<div class="form-group">
							<label>Txn Mode</label>
							<select name ='txn_mode' class='form-control'>
							<?php dropdown($txn_mode_list, $txn_mode); ?>
							</select>
						</div>
																
						<div class="form-group">
							<label>Remarks </label>
							<input class="form-control" placeholder="Details of Transaction" name='txn_remarks' value='<?php echo $txn_remarks; ?>'  required>
						</div>
					</form>
					
						</div>
				</div>
				
          </div>
          <!-- /.row -->
        </div>
        <!-- /.box-body -->
    </section>
  </div>
  <!-- /.content-wrapper -->
<?php require_once('footer2.php'); ?>