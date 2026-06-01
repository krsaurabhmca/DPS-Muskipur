<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table = "vendor";
if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
} else {
    $vendor = insert_row($table);
    $id = $vendor['id'];
}

if ($id != '') {
    $res = get_data($table, $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Manage Vendor <span class="badge badge-success badge-sm p-2">NEW</span></h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Inventory</a></li>
            <li class="breadcrumb-item active">Vendor</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Add Vendor </h3>
                <div class="box-tools pull-right">

                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form action='update_vendor' id='update_frm' method='post' enctype='multipart/form-data'>
                                    <input type='hidden' value='<?php echo $id; ?>' name='id'>
                                    <div class="form-row">
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Vendor name</label>
                                                <input type="text" class="form-control" required name='name' value='<?php echo $name; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Vendor Category &nbsp;<a data-toggle="modal" data-target="#addCategory" title="Create New Vendor type" style="cursor:pointer"><span class="badge badge-warning">+</span></a></label>
                                                <select name="category" id="category" class="form-control" required>
                                                    <option value="">-Select Type-</option>
                                                    <?php dropdown_list("vendor_cat", "id", "cat_name", $category); ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>DOJ</label>
                                                <input type="date" class="form-control" required name='doj' value='<?php echo $doj; ?>'>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Opening Balance</label>
                                                <input type="number" min="0" name="opening_balance" class="form-control" required value='<?php echo $opening_balance; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Closing Balance</label>
                                                <input class="form-control" type='number' min="0" name='closing_balance' value='<?php echo $closing_balance; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>GST No. (if Applicable)</label>
                                                <input class="form-control" type='text' name='gst' value='<?php echo $gst; ?>'>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Mobile</label>
                                                <input class="form-control" type='tel' name='mobile' value='<?php echo $mobile; ?>' maxlength="10" minlength="10">
                                            </div>
                                            <div class="form-group">
                                                <label>Address</label>
                                                <input class="form-control" required name='address' value='<?php echo $address; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" id="status" class="form-control" required>
                                                    <?php dropdown($status_list, $status); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            </form>
                        </div>
                        <?php
                        if ($_SESSION['user_type'] == 'ADMIN') {
                            echo '<div class="text-center">
                            <button class="btn btn-success btn-sm" id="update_btn"> Save </button>
                            <input type="reset" class="btn btn-secondary btn-sm">
                        </div>';
                        } else {
                            echo '<div class="m-auto">
                                <button class="btn btn-border border-danger"> Do Not Have Permission </button>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
</div>
<!-- Modal for adding Category  -->
<div class="modal fade" id="addCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="update_vendor_cat" id="cat_frm">
                    <div class="form-group">
                        <label for="form-label">Category</label>
                        <?php
                        $cat = insert_row("vendor_cat");
                        $id = $cat['id'];
                        ?>
                        <input type="hidden" name="id" value="<?php echo $id; ?>" required>
                        <input type="text" class="form-control" name="cat_name" required>
                    </div>
                    <div class="form-group">
                        <label for="form-label">Status</label>
                        <select name="status" id="status" required class="form-control">
                            <?php dropdown($status_list, $status); ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="cat_btn">Save</button>
            </div>
        </div>
    </div>
</div>
<?php require_once('required/footer.php'); ?>