<?php require_once('required/function.php'); 

							
							
		function target_of_month($cmonth_name ='')
		{
		    if($cmonth_name =='')
		    {
		    $cmonth_name = date('F');
		    }
		   $cmonth_name;
		   $res =  get_all('student','*',array('finance_type'=>'NORMAL','status'=>'ACTIVE'));
		   
		   foreach($res['data'] as $student)
		   {
		       $student_id = $student['id'];
		       $prev_dues = get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'];
		       $total_fee[]['previous_dues'] =$prev_dues;
		       $dues_details = monthly_fee($student_id, $cmonth_name)['fee'];
		       //$total = $dues_details['total'] + $prev_dues;
		       $total_fee[] = $dues_details;
		   }
		   	$all_target = array_add($total_fee);
			ksort($all_target);
		   return $all_target;
		}
		
	
		
	echo "<pre>";
	print_r(target_of_month('May'));
	print_r(collection_of_month('May'));
							
?>						