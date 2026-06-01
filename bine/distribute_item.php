<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'distribute_item';
if (isset($_SESSION['inv_id'])) {
    $inv_id = $_SESSION['inv_id'];
} else {
    $invoice = insert_row('invoice');
    $inv_id = $invoice['id'];
}

if (isset($_GET['link']) and $_GET['link'] != '') {
    $item = decode($_GET['link']);
    $id = $item['id'];
} else {
    $item = insert_row($table_name);
    $id = $item['id'];
}

if (isset($_SESSION['txn_date'])) {
    $date = $_SESSION['txn_date'];
} else {
    $date = date("Y-m-d");
}

if (isset($_REQUEST['student_id'])) {
    $student_id =  $_REQUEST['student_id'];
}
if (isset($_SESSION['inv_no'])) {
    $inv_no = $_SESSION['inv_no'];
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Item Distribution</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Inventory</a></li>
            <li class="breadcrumb-item active">New Item</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Distribution Entry</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form id='add_item_frm' action='update_distribute_item'>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <input type='hidden' name='id' value='<?php echo $id; ?>' required />
                                    <input type='hidden' name='inv_id' value='<?php echo $inv_id; ?>' required />
                                    <label for="invoice" class="col-form-label">Invoice No.</label>
                                    <?php if (isset($_SESSION['inv_no'])) { ?>
                                        <input type="invoice" name="inv_no" id="inv_no" class="form-control" value="<?php echo $inv_no; ?>" readonly required>
                                    <?php } else { ?>
                                        <input type="invoice" name="inv_no" id="inv_no" class="form-control" value="<?php echo $inv_no; ?>" required>
                                    <?php } ?>
                                </div>
                                <div class="col-md-2">
                                    <label for="example-text-input" class="col-form-label">Class</label>
                                    <div>
                                        <?php
                                        if (isset($_SESSION['class'])) {
                                            $class = $_SESSION['class'];
                                            echo "<input type='text' name='student_class' value='$class' required class='form-control' readonly>";
                                        } else {
                                        ?>
                                            <select name="student_class" id="student_class" class="select2 form-control" required>
                                                <?php dropdown($class_list, $student_class); ?>
                                            </select>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="example-text-input" class="col-form-label">Section</label>
                                    <div>
                                        <?php
                                        if (isset($_SESSION['section'])) {
                                            $section = $_SESSION['section'];
                                            echo "<input type='text' name='student_section' value='$section' required class='form-control' readonly>";
                                        } else {
                                        ?>
                                            <select name="student_section" id="student_section" class="form-control" required onblur="get_student()">
                                                <?php dropdown($section_list, $student_section); ?>
                                            </select>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="example-text-input" class="col-form-label">Student Name</label>

                                    <div>
                                        <?php
                                        if (isset($_SESSION['student_id'])) {
                                            $student_id = $_SESSION['student_id'];
                                            $student_name = get_data('student', $student_id, 'student_name')['data'];
                                            echo "<input type='hidden' value='$student_id' name='student_id' required>";
                                            echo "<input type='text' value='$student_name' readonly class='form-control'>";
                                        } else {
                                        ?>
                                            <select name="student_id" id="stu_list" class="form-control" required>
                                            </select>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="date" class="col-form-label">Date</label>
                                    <?php
                                    if (isset($_SESSION['txn_date'])) {
                                        $inv_date = date("Y-m-d", strtotime($date));
                                        echo "<input type='date' value='$inv_date' name='date' readonly required class='form-control' >";
                                    } else {
                                    ?>
                                        <input type="date" name="date" id="date" class="form-control" value="<?php echo date("Y-m-d", strtotime($date)); ?>" required>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label for="item_id" class="col-form-label">Item</label>
                                    <div>
                                        <select name="item_id" id="item_id" class="form-control " onchange="get_item()" required>
                                            <?php dropdown_list('inventory_item', 'id', 'name', $item_id); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="qty" class="col-form-label">Qty</label>
                                    <div>
                                        <input type="number" name="qty" id="qty" class="form-control" min="0" onblur="checkqty(this)" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="rate" class="col-form-label">Rate</label>
                                    <div>
                                        <input type="number" name="rate" id="rate" class="form-control" onkeyup="cal_amt()" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="amount" class="col-form-label">Amount</label>
                                    <div>
                                        <input name="amount" id="amount" class="form-control" required readonly>
                                    </div>
                                </div>
                </form>
                <div class="col-md-2">
                    <label for="" class="form-label"></label>
                    <div class="">
                        <input type="button" id="add_item_btn" class="my-3 btn btn-warning btn-block form-control" value="Add Item">
                    </div>
                </div>
            </div>
        </div>
</div>
</div>
<!-- /.box-body -->
<hr>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-stripped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $q = 0;
                                $t = 0;
                                if (isset($_SESSION['student_id'])) {
                                    $student_id = $_SESSION['student_id'];
                                    $res = get_all('distribute_item', '*', array("student_id" => $student_id, "inv_id" => $inv_id));
                                     if ($res['count'] > 0) {
                                    foreach ($res['data'] as $row) {
                                        $id = $row['id'];
                                        $item_id = $row['item_id'];
                                        $q += $row['qty'];
                                        $t += $row['amount'];
                                        echo "<tr>";
                                        echo "<td>" . $i . "</td>";
                                        echo "<td>" . get_data('inventory_item', $item_id, 'name')['data'] . "</td>";
                                        echo "<td class='qty'>" . $row['qty'] . "</td>";
                                        echo "<td>" . $row['rate'] . "</td>";
                                        echo "<td class='amt'>" . $row['amount'] . "</td>";
                                        echo "<td align='right'>" . btn_delete('distribute_item', $id) . "</td>";
                                        echo "</tr>";
                                        $i++;
                                    }
                                }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <th colspan="2">Total</th>
                                <td> <b><?php echo $q; ?></b></td>
                                <td align="center" colspan="2"><b id="total_amt"><?php echo $t; ?></b></td>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="close_invoice" id="invoice_frm">
                <div class="form-group row">
                    <div class="col-md-2">
                        <label for="remarks" class="form-label">Remarks</label>
                        <input type="hidden" class="form-control" name="id" value="<?php echo $inv_id; ?>" required>
                        <input type="hidden" class="form-control" name="inv_no" value="<?php echo $inv_no; ?>">
                        <input type="hidden" class="form-control" name="student_id" value="<?php echo $student_id; ?>">
                        <input type="hidden" class="form-control" name="txn_date" value="<?php echo date('Y-m-d', strtotime($date)); ?>">
                        <input type="hidden" class="form-control" name="total" id="total">
                        <input type="text" class="form-control" name="remarks" id="remarks">
                    </div>
                    <div class="col-md-2">
                        <label for="dues" class="form-label">Dues</label>
                        <?php
                        if (isset($_SESSION['student_id'])) {
                            $student_id = $_SESSION['student_id'];
                            $get_dues = get_data('student', $student_id, 'item_dues')['data'];
                        }
                        ?>
                        <input type="number" class="form-control" name="prev_dues" id="dues" value="<?php echo $get_dues; ?>" readonly required>
                    </div>
                    <div class="col-md-2">
                        <label for="paid" class="form-label">Paid</label>
                        <input type="number" class="form-control" name="payment" id="paid" onkeyup="cal_dues()" required>
                    </div>
                    <div class="col-md-2">
                        <label for="cur_dues" class="form-label">Current Dues</label>
                        <input type="number" class="form-control" name="dues" id="cur_dues" readonly required>
                    </div>
                    <div class="col-md-2">
                        <label for="paid" class="form-label">Payment Mode</label>
                        <select name="payment_mode" id="payment_mode" class="form-control" required>
                            <?php dropdown($payment_mode_list, $payment_mode); ?>
                        </select>
                    </div>
            </form>
            <div class="col-md-2">
                <label for=""></label>
                <input type="button" value="CLOSE INVOICE" id="close_invoice" class="my-2 btn btn-danger btn-block">
            </div>
        </div>
    </div>
</div>
</div>
</section>

<section>
</section>
</div>
<!-- /.content-wrapper -->
<?php require_once('required/footer2.php'); ?>
<script>
    function get_student() {
        let stu_class = $("#student_class").find(":selected").text();
        let stu_section = $("#student_section").find(":selected").text();
        $.ajax({
            'url': "required/master_process?task=get_student",
            'type': "POST",
            'data': {
                'stu_class': stu_class,
                'stu_section': stu_section
            },
            'success': function(data) {
                $("#stu_list").html(data);
            }
        })
    }

    function checkqty(ele) {
        const qty = $(ele).val();
        const id = $('#item_id').val();
        $.ajax({
            'url': 'required/master_process?task=check_qty',
            'type': 'post',
            'data': {
                'qty': qty,
                'item_id': id
            },
            'success': function(data) {
                console.log(data);
                var obj = JSON.parse(data);
                $.notify(obj.msg, obj.status);
                if (obj.status == "error") {
                    $('#add_item_btn').attr('disabled',true);
                }else if(obj.status == "success"){
                    $("#add_item_btn").attr('disabled',false);
                }
            }
        });
    }

    function cal_amt() {
        let q = $("#qty").val();
        let r = $("#rate").val();
        let amt = parseFloat(q) * parseFloat(r);
        document.getElementById("amount").value = parseFloat(amt).toFixed(2);
    }

    function cal_dues() {
        let total = $("#total_amt").text();
        let paid = $("#paid").val();
        let due = $("#dues").val();
        let dues = parseFloat(total) + parseFloat(due) - parseFloat(paid);
        // alert(parseFloat(dues));
        document.getElementById("cur_dues").value = parseFloat(dues).toFixed(2);
    }
    let tot_amt = $('#total_amt').text();
    document.getElementById("total").value = parseFloat(tot_amt);
</script>