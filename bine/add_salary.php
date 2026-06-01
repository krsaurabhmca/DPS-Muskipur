<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'salary';
if (isset($_GET['link']) and $_GET['link'] != '') {
    $item = decode($_GET['link']);
    $id = $item['id'];
} else {
    $item = insert_row($table_name);
    $id = $item['id'];
}

if ($id != '') {
    $res = get_data($table_name, $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
if (!isset($payment_date)) {
    $payment_date = date("Y-m-d");
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Salary Details</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Employee Management</a></li>
            <li class="breadcrumb-item active">Add Salary</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Add /Update Salary </h3>

                <div class="box-tools pull-right">
                    <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <form id='update_frm' action='update_salary'>
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="" class="col-sm-4 col-form-label">Employee ID</label>
                                <input type='hidden' name='id' value='<?php echo $id; ?>' />
                                <div class="col-sm-8">
                                    <input type="text" id="emp_code" value="<?php echo get_data('employee',$emp_id,'e_code')['data']; ?>" class="form-control" onblur="get_emp_data()">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label"> Employee Name</label>
                                <div class="col-sm-8">
                                    <input type="hidden" name="emp_id" id="emp_id" class="form-control" required>
                                    <input type="text" name="emp_name" id="emp_name" class="form-control" value="<?php echo get_data('employee',$emp_id,'e_name')['data']; ?>" required readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Select Month</label>
                                <div class="col-sm-8">
                                    <select name="month" id="month" required class="form-control" onchange="get_att()">
                                        <option value=""></option>
                                        <?php dropdown($att_month_list, $month); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Basic Salary</label>
                                <div class="col-sm-8">
                                    <input type="number" min="0" name="basic_salary" id="basic_salary" required class="form-control " value="<?php echo $basic_salary; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Total Present</label>
                                <div class="col-sm-8">
                                    <input type="number" min="0" name="total_present" id="total_present" required class="form-control " value="<?php echo $total_present; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Total Absent</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="total_absent" type="number" id="total_absent" value='<?php echo $total_absent; ?>'>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Total Leave</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="total_leave" type="number" id="total_leave" value='<?php echo $total_leave; ?>'>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Payable_amount</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="payable_amount" type="text" value='<?php echo $payable_amount; ?>'>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Payment Date</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="date" value='<?php echo date('Y-m-d', strtotime($payment_date)); ?>' name="payment_date">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Payment Status </label>
                                <div class="col-sm-8">
                                    <select name='payment_status' class='form-control ' required>
                                        <option value=""></option>
                                        <?php dropdown($salary_status_list, $payment_status); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.box-body -->
        </form>
    </section>
</div>
<!-- /.content-wrapper -->
<?php require_once('required/footer2.php'); ?>
<script>
    // function get_code(){
    //     $("#emp_id").change(funtion(){
    //         $id = $(this).
    //     })
    // }
    function get_emp_data() {
        const e_code = $("#emp_code").val();
        $.ajax({
            url: "required/master_process?task=get_emp_data",
            type: "POST",
            data: {
                "e_code": e_code
            },
            success: function(data) {
                console.log(data);
                let myobj = JSON.parse(data);
                $("#emp_id").val(myobj.id);
                $("#emp_name").val(myobj.e_name);
                $("#basic_salary").val(myobj.e_salary);
            }
        })
    }
    
    function get_att() {
        const id = $("#emp_id").val();
        const month = $("#month").val();
        $.ajax({
            url: "required/master_process?task=get_emp_att",
            type: "POST",
            data: {
                id,month
            },
            success: function(data) {
                console.log(data);
                let obj = JSON.parse(data);
                $("#total_present").val(obj.tp);
                $("#total_absent").val(obj.ta);
                $("#total_leave").val(obj.tl);
            }
        })
    }
</script>