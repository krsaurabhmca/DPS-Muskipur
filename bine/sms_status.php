<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); 
	
$res= get_all('op_sms');
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
     <h1> SMS Delivery Report </h1>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="#extra">Extra</a></li>
        <li class="breadcrumb-item active">SMS</li>
      </ol>
    </section>
	 <section class="content">
      <div class="row">
        <div class="col-12">
          <div class="box">
            <div class="box-header with-border">
             
              <h3 class="box-title">SMS Details 
				
			  </h3>
			  </form>
				<div class="box-tools pull-right">
				<!--<form>-->
				<!--	<select name='status' > -->
				<!--		<?php dropdown($status_list,$status); ?>-->
				<!--	</select>-->
				<!--	<select name='employee_type' > -->
				<!--		<?php dropdown($employee_type_list,$employee_type); ?>-->
				<!--	</select>-->
				<!--	<button class='btn btn-orange'> Show </button>-->
				<!--</form>-->
				</div>
			</div>
            <!-- /.box-header -->
            <div class="box-body">
				<div class="table-responsive">
				  <table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
									<thead>
                                        <tr>
                                            
										
											<th width='150px'>Mobile No</th>
											<th>Text </th>
											<th>Request id</th>
											<th>Delivery Time</th></th>
											<th width='80px'>Status</th>
										
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php 
										
										if($res['count']>0)
										{
										foreach($res['data'] as $row)
										{
										$id =$row['id'];
										$status = $row['status'];
									$mobiles=str_replace(',', ' ', urldecode($row['mobile']));
									echo"<tr class='odd gradeX'>";
								    echo"<td><div style='overflow-y:scroll;height:80px;'>".$mobiles."</div></td>";
									echo"<td>".urldecode($row['text'])."</td>";
									echo"<td>".$row['request_id']."</td>";
									echo"<td>".$row['delivery_time']."</td>";
									echo"<td>".$row['status']."</td>";
									
										echo "</tr>";
										
										}
										}
                                       ?>
                                   
                                    </tbody>
					</table>
				</div>
			</div>

        </div>
       
    </div>
</section>
</div>
<?php require_once('required/footer2.php'); ?>