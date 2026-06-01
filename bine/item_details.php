<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Stock Details
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Inventory</li>
            <li class="breadcrumb-item active">Item Stock</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Items Stock</h3>
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
                                        <th>#</th>
                                        <th>Item Detail</th>
                                        <th>Opening Stock</th>
                                        <th>Current Stock</th>
                                        <th>Status</th>
                                        <th class='text-right'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $sql = "SELECT * from inventory_item where status not in('AUTO','DELETED','REMOVED') ORDER BY name";
                                    $res = direct_sql($sql);
                                    // $res = get_all('inventory_item');
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                            if($row['current_stock'] == 0){
                                                update_data('inventory_item',array('status'=>'OUT OF STOCK'),$id);
                                            }else{
                                                update_data('inventory_item',array('status'=>'IN STOCK'),$id);
                                            }
                                    ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['opening_stock']; ?></td>
                                                <td><?php echo $row['current_stock']; ?></td>
                                                <td><?php echo $row['status']; ?></td>
                                                <td class='text-right'>
                                                    <?php
                                                    echo btn_view('inventory_item', $id, $row['name']);
                                                    ?>
                                                </td>
                                            </tr>

                                    <?php $i++;
                                        }
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