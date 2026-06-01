<?php
require_once('required/function.php');
extract($_POST);
if(isset($_FILES['student_photo']['name']) and $_FILES['student_photo']['name']!='')
{
   $_POST['student_photo'] = upload_img('student_photo')['id'];
}

if(isset($_FILES['student_tc']['name']) and $_FILES['student_tc']['name']!='')
{
   $_POST['student_tc'] = upload_img('student_tc')['id'];
}

$res = insert_data('admission',$_POST);
$app_id = date('ymd').sprintf("%04d", $res['id']);
$res2= update_data('admission',array('app_no' => $app_id), $res['id']);

$link = encode('student_name=' . $_POST['student_name'] . '&id=' . $res['id']);

$full_link = $base_url.'application_print.php?link='.$link;
$wa_sms ="Dear *$student_name*,
Your application for admission in class *$student_class* is recevied. 
Your application No. is $app_id Kindly pay amount *₹ 500* to complete the your application.

Click on link to Print Application :  
$full_link

Regards
*$full_name*
$inst_contact
$inst_url
";

$wa_sms = urlencode($wa_sms);

$wa_link ="http://148.251.129.118/wapp/api/send?apikey=38c8df57e046494ea97daa6394802c6a&mobile=$student_whatsapp&msg=$wa_sms";

$st = api_call($wa_link);

?>
<script> window.location ='<?php echo $full_link; ?>' </script>
