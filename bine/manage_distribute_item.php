<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
// extract(post_clean($_GET));
// if (isset($_GET['item_id']) and $_GET['item_id'] <> '') {
//     $res = get_all('distribute_item', '*', array('item_id' => $_GET['item_id']));
// } else if (isset($_GET['student_id']) and $_GET['student_id'] <> '') {
//     $res = get_all('distribute_item', '*', array('student_id' => $_GET['student_id']));
// } else {
//     $res = get_all('distribute_item');
// }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage Distribute Item
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Inventory</li>
            <li class="breadcrumb-item active">Distribute Item</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Details of Distribute Items </h3>
                        <div class="box-tools pull-right">
                            <a class='fa fa-plus btn btn-success btn-sm' title='Distribute New Item' href='distribute_item'> </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Total</th>
                                        <th>Amount Paid </th>
                                        <th>Pay Mode</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                        <th class='text-right'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = get_all('invoice', "*", array('status' => 'CLOSED'));
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                            $inv_no = $row['inv_no'];
                                            $student_name = get_data('student', $row['student_id'], 'student_name')['data'];
                                            $student_class = get_data('student', $row['student_id'], 'student_class')['data'];
                                            $student_section = get_data('student', $row['student_id'], 'student_section')['data'];
                                    ?>
                                            <tr>
                                                <td><?php echo $inv_no; ?></td>
                                                <td><?php echo $student_name; ?></td>
                                                <td><?php echo $student_class . "-" . $student_section; ?></td>
                                                <td><?php echo $row['total']; ?></td>
                                                <td><?php echo $row['payment']; ?></td>
                                                <td><?php echo $row['payment_mode']; ?></td>
                                                <td><?php echo $row['remarks']; ?></td>
                                                <td><?php echo $row['status']; ?></td>
                                                <td class='text-right'>
                                                    <a href="<?php echo "distribute_item_receipt?link=" . encode('student_id=' . $row['student_id'] . '&inv_id=' . $id) ?>" class="btn btn-warning btn-sm"><i class='fa fa-print'></i></i></a>
                                                    <a class="cancel_invoice_btn btn btn-danger btn-sm text-light" title="Cancel/Reverse Receipt" data-id="<?php echo $row['student_id']; ?>" data-inv="<?php echo $id; ?>"><i class="fa fa-ban"></i></a>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
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