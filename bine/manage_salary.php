<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));

// if (isset($_GET['item_id']) and $_GET['item_id'] <> '') {
//     $res = get_all('inventory_item', '*', array('cat_id' => $_GET['cat_id']));
// } else {
//     $res = get_all('inventory_item');
// }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage Salary
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Employee Management</li>
            <li class="breadcrumb-item active">Manage Salary</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Salary Details</h3>
                        <div class="box-tools pull-right">
                            <a class='fa fa-plus btn btn-success btn-sm' title='Create Salary' href='add_salary'> </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee Code</th>
                                        <th>Employee Name</th>
                                        <th>Month</th>
                                        <th>Basic Salary</th>
                                        <th>Paid Amount</th>
                                        <th>Payment Date</th>
                                        <th>Payment Status</th>
                                        <th class='text-right'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $res = get_all("salary");
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                            $emp_id = $row['emp_id'];
                                            $name = get_data('employee', $emp_id, 'e_name')['data'];
                                            $e_code = get_data('employee',$emp_id,'e_code')['data'];
                                    ?>
                                            <tr>
                                                <td><?php echo $e_code; ?></td>
                                                <td><?php echo $name; ?></td>
                                                <td><?php echo $row['month']; ?></td>
                                                <td><?php echo $row['basic_salary']; ?></td>
                                                <td><?php echo $row['payable_amount']; ?></td>
                                                <td><?php echo date('d M Y',strtotime($row['payment_date'])); ?></td>
                                                <td><?php echo $row['payment_status']; ?></td>
                                                <td class='text-right'>
                                                    <?php echo btn_view('salary', $id, $name); ?>
                                                    <?php echo btn_edit('add_salary', $id); ?>
                                                    <?php echo btn_delete('salary', $id); ?>
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