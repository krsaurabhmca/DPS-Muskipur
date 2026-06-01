<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8">
    <link rel="stylesheet" href="#"/>
</head>
<body>
    <!--<form id="checkout-selection" method="POST" action='pay.php?checkout=manual'>-->
    <form method="POST" action='pay.php?checkout=automatic'>
        <input type='number' value='' placeholder='Demand No.' name='pay_req_no'><br>
        <input type='number' value='' placeholder='Enter Amount to Pay' name='amount'><br>
        <input type='text' value='' placeholder='Name of Student' name='student_name'><br>
        <input type='text' value='' placeholder='Mobile No' name='student_mobile'> <br>
        <input type='text' value='' placeholder='Email Id ' name='student_email'> <br>
        <input type='text' value='' placeholder='Order No.' name='order_no'><br>
        <input type='text' value='' placeholder='Fee Details' name='fee_details'>
        
        
        <input type="submit" value="Continute to Pay">
    </form>
   
</body>
</html>