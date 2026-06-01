<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
extract(post_clean($_GET));

if (isset($_GET['item_id']) and $_GET['item_id'] <> '') {
    $res = get_all('inventory_item', '*', array('cat_id' => $_GET['cat_id']));
} else {
    $res = get_all('inventory_item');
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage Items
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Inventory</li>
            <li class="breadcrumb-item active">Manage Item</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Details of Items (<?php echo get_data('item_cat', $_GET['cat_id'], 'cat_name')['data']; ?> ) </h3>
                        <div class="box-tools pull-right">
                            <a class='fa fa-plus btn btn-success btn-sm' title='New Item' href='add_item'> </a>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Item Name</th>
                                        <th>Opening Stock</th>
                                        <th>Current Stock</th>
                                        <th>Unit</th>
                                        <th>Rate</th>
                                        <th>MRP</th>
                                        <th>Barcode</th>
                                        <th class='text-right'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($res['count'] > 0) {
                                        $fee = 0;
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                            $cat_name = get_data('item_cat', $row['cat_id'], 'cat_name')['data'];
                                    ?>
                                            <tr>
                                                <td><?php echo $cat_name; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['opening_stock']; ?></td>
                                                <td><?php echo $row['current_stock']; ?></td>
                                                <td><?php echo $row['unit']; ?></td>
                                                <td><?php echo $row['rate']; ?></td>
                                                <td><?php echo $row['mrp']; ?></td>
                                                <td><?php echo $row['barcode']; ?></td>
                                                <td class='text-right'>
                                                    <?php echo btn_view('inventory_item', $id, $row['name']); ?>
                                                    <?php echo btn_edit('add_item', $id); ?>
                                                    <?php echo btn_delete('inventory_item', $id); ?>
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