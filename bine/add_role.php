<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); 

// create_table('meta_table');
// add_column('meta_table', 'display_name');
// add_column('meta_table', 'can_edit');
// add_column('meta_table', 'can_add');
// add_column('meta_table', 'can_view');
// add_column('meta_table', 'can_delete');

//Add All Table in Meta Data
// $res2 = table_list();
// foreach($res2['data'] as $tbl)
// {
//     insert_data('meta_table', array('table_name'=>$tbl,'display_name'=>add_space($tbl),'status'=>'ACTIVE'));
// }

$table_name = 'user';
if(isset($_GET['link']) and $_GET['link']!='')
{
	$data = decode($_GET['link']);
	$id = $data['id'];
	$isedit ='yes';
}
else{	
	$fee =insert_row($table_name);
	$id = $fee['id'];
	$isedit ='no';
}

if($id!='')
{
  $res = get_data($table_name,$id);
  if($res['count']>0 and $res['status']=='success')
  {
	  extract($res['data']);
  }
}
if($_GET['user_id'] && $_GET['user_id'] !='')
{
    $user_id = $_GET['user_id'];
}

?>	
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Role & Responsibilty
			<?php if ($user_type == 'ADMIN') { ?>
				<small><a href='dashboard'>( Dashboard)</a></small>
			<?php } ?>
		</h1>
		<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
			
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

	<div class="card mb-4">
	        <div class='card-header'>
	             	 Role of <?php echo get_data('user', $user_id,'full_name')['data']; ?> 
					<form>
					<select name='user_id' class='form-control col-md-3 float-right' onchange='submit()' onblur='submit()' >
						<option value=''>Select User</option>
					<?php dropdown_list('user','id','user_name'); ?>
					</select>
					</form>
	       </div>
            <div class='card-body'>
								<!--    Basic Table  -->
								<table id="data_tbl" class="table table-hover" cellspacing="0" width="100%">
                                    <thead >
                                        <tr class='bg-warning text-light'>
                                            
                                            <th>Task </th>
                                            <th>Can View</th>
                                            <th>Can Add </th>
                                            <th>Can Edit</th>
                                            <th>Can Delete</th>
                                            
                                        </tr>
										
                                    </thead>
                                    <tbody>
									<?php
									
									$res = get_all('meta_table','*',array('status'=>"ACTIVE"));    
									if($res['count']>0)
									{	
									foreach($res['data'] as $row)
									{
										echo "<tr>";
										$id=$row['id'];
										$link =encode("id=".$id);
								 	echo "<td> ". $row['display_name']."</td>";
								    echo "<td> ". check_role($row['table_name'], $user_id,'can_view')['data'] ."</td>";
								    echo "<td> ". check_role($row['table_name'], $user_id,'can_add')['data'] ."</td>";
								    echo "<td> ". check_role($row['table_name'], $user_id,'can_edit')['data'] ."</td>";
								    echo "<td> ". check_role($row['table_name'], $user_id,'can_delete')['data'] ."</td>";
								    ?>
									
									   	</tr>
									<?php
									}
									}
									?>
                                       
                                    </tbody>
                                </table>
                      </div>
                      </div>
                   </div>
              </div>
                 
<?php require_once('required/footer.php'); ?>

	