<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'inventory_item';
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
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Item Details </h1>
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
                <h3 class="box-title">Add /Update Item Details </h3>

                <div class="box-tools pull-right">
                    <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <form id='update_frm' action='update_item'>
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Item Name</label>
                                <div class="col-sm-8">
                                    <input type='hidden' name='id' value='<?php echo $id; ?>' />
                                    <input class="form-control " type="text" value='<?php echo $name; ?>' name="name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Select Category</label>
                                <div class="col-sm-8">
                                    <select name="cat_id" id="cat_id" required class="form-control ">
                                        <?php dropdown_list('item_cat', 'id', 'cat_name', $cat_id) ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Opening Stock</label>
                                <div class="col-sm-8">
                                    <input type="number" min="0" name="opening_stock" required class="form-control " value="<?php echo $opening_stock; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Current Stock</label>
                                <div class="col-sm-8">
                                    <input type="number" min="0" name='current_stock' class='form-control ' required value="<?php echo $current_stock; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Unit</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="text" value='<?php echo $unit; ?>' name="unit">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Min Alert Limit</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="number" value='<?php echo $min_alert_limit; ?>' name="min_alert_limit" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Rate</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="number" min="0" value='<?php echo $rate; ?>' name="rate" id="rate">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">GST</label>
                                <div class="col-sm-8">
                                    <select name="gst" id="gst" class="form-control " required>
                                        <?php dropdown($gst_percent_list, $gst); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">MRP</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="number" min="0" value='<?php echo $mrp; ?>' name="mrp" id="mrp" required>
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Barcode </label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="text" value='<?php echo $barcode; ?>' name="barcode">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Status </label>
                                <div class="col-sm-8">
                                    <select name='status' class='form-control ' required>
                                        <?php dropdown($item_status, $status); ?>
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
    function sum_gst() {
        let rate = $("#rate").val();
        let gst = $("#gst").find(":selected").val();
        let mrp = (parseFloat(gst) * parseFloat(rate) / 100) + parseFloat(rate);
        $("#mrp").val(parseFloat(mrp, 2));
    }
</script>