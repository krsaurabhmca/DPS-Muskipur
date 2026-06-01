<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
} else {
    $fee = insert_row('vehicle');
    $id = $fee['id'];
}

if ($id != '') {
    $res = get_data('vehicle', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Manage Vehicle</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Vehicle </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Add & manage vehicle </h3>
            </div>
            <!-- /.box-header -->

            <div class='row'>
                <div class=" col-lg-12 col-md-12 ">
                    
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <b>Vehicle Details</b>
                                <div class="box-tools pull-right">
                                    <?php if ($_SESSION['user_type'] == 'ADMIN') { ?>
                                        <button class="btn btn-success btn-block" id='update_btn'> Save </button>
                                    <?php } else { ?>
                                        <button class="btn btn-border border-danger"> Don't Have Permission </button>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action='update_vehicle' id='update_frm' method='post' enctype='multipart/form-data'>
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            
                        <input type='hidden' value='<?php echo $id; ?>' name='id'>
                                            <label>Vehicle Model</label>
                                            <input class="form-control" required name='model' value='<?php echo $model; ?>'>
                                        </div>
                                        <div class="form-group">
                                            <label>Vehicle Type <span class="badge badge-warning badge-sm p-2"><a class="text-white" data-target="#vehicle_type" data-toggle="modal"><i class="fa fa-plus"></i></a></span></label>
                                            <select name='vehicle_type' class='form-control' required>
                                                <?php dropdown_list('vehicle_cat', 'id', 'cat_name', $vehicle_type); ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Vehicle No.</label>
                                            <input class="form-control" required name='vehicle_no' value='<?php echo $vehicle_no; ?>'>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Purchase Date</label>
                                            <input type="date" class="form-control" required name='purchase_date' value='<?php echo $purchase_date; ?>'>
                                        </div>
                                        <div class="form-group">
                                            <label>Pollution Expiry</label>
                                            <input class="form-control" type="date" required name='pollution_expiry' value='<?php echo $pollution_expiry; ?>'>
                                        </div>
                                        <div class="form-group">
                                            <label>Insurance Expiry</label>
                                            <input class="form-control" type='date' name='insurance_expiry' value='<?php echo $insurance_expiry; ?>'>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Fitness Expiry</label>
                                            <input class="form-control" type='date' name='fitness_expiry' value='<?php echo $fitness_expiry; ?>'>
                                        </div>
                                        <div class="form-group">
                                            <label>Road Tax Expiry</label>
                                            <input class="form-control" type='date' name='road_tax_expiry' value='<?php echo $road_tax_expiry; ?>'>
                                        </div>
                                        <div class="form-group">
                                            <label>EMI Start Date</label>
                                            <input class="form-control" type='date' name='emi_start_date' value='<?php echo $emi_start_date; ?>'>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>EMI Period(e.g. 12)</label>
                                            <input class="form-control" type='text' name='emi_period' value='<?php echo $emi_period; ?>'>
                                        </div>
                                        <div class="form-group">
                                            <label>Status </label>
                                            <select name='status' class='form-control'>
                                                <?php dropdown($status_list, $status); ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                 </form>
                            </div>
                        </div>
                   

                </div>
            </div>
            <hr>
            <!--
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-header bg-warning text-black">
                            <b>Manage Vehicles</b>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Vehicle Type</th>
                                            <th> Vehicle No.</th>
                                            <th> Model</th>
                                            <th> Status</th>
                                            <th> Operation </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $res = get_all('vehicle');
                                        if ($res['count'] > 0) {
                                            foreach ($res['data'] as $row) {
                                                $id = $row['id'];
                                                echo "<tr>";
                                                echo "<td>" . $row['vehicle_type'] . "</td>";
                                                echo "<td>" . $row['vehicle_no'] . "</td>";
                                                echo "<td>" . $row['model'] . "</td>";
                                                echo "<td>" . $row['status'] . "</td>";

                                        ?>
                                                <td>
                                                    <?php
                                                    if ($row['status'] == "ASSIGNED") { ?>
                                                        <a id="change_vehicle_driver" data-toggle="modal" data-target="#change_driver" class='btn btn-info btn-xs text-white' data-id="<?php echo $id; ?>" onclick="get_id2()">Change Driver</a>
                                                    <?php } else {
                                                    ?>
                                                        <a id="assign_vehicle" data-toggle="modal" data-target="#assign_driver" class='btn btn-info btn-xs text-white' data-id="<?php echo $id; ?>" onclick="get_id()">Assign Driver</a>
                                                    <?php } ?>
                                                    <a href='add_vehicle.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
                                                    <span class='delete_btn btn btn-danger btn-sm' data-table='vehicle' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
                                                    <?php echo btn_view('vehicle', $id, $row['model']); ?>

                                                </td>
                                        <?php
                                                $i++;
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
</div>
</section>
</div>

<!-- Modal for adding Category  
<div class="modal fade" id="assign_driver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Assign Driver</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="assign_driver" id="assign_frm">
                    <div class="form-group">
                        <label for="form-label">Driver Name</label>
                        <input type="hidden" name="id" id="vehicle_id" required>
                        <select name="driver_id" id="driver_id" class="form-control" required>
                            <option value="">-Select Driver-</option>
                            <?php
                            $res = get_all("employee", "*", array("e_category" => "DRIVER"));
                            if ($res['count'] > 0) {
                                foreach ($res['data'] as $row) {
                                    $id = $row['id'];
                                    $driver_list[$id] = $row['e_name'];
                                }
                            }
                            dropdown_with_key($driver_list, $driver_id); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="form-label">Status</label>
                        <select name="vehicle_status" id="vehicle_status" required class="form-control">
                            <?php dropdown($vehicle_status_list, $status); ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="assign_btn">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal for adding Category  
<div class="modal fade" id="change_driver" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Change Driver</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="change_driver" id="change_frm">
                    <div class="form-group">
                        <label for="form-label">Driver Name</label>
                        <input type="hidden" name="id" id="vid" required>
                        <select name="driver_id" id="driver_id2" class="form-control" required>
                            <option value="">-Select Driver-</option>
                            <?php
                            $res = get_all("employee", "*", array("e_category" => "DRIVER"));
                            if ($res['count'] > 0) {
                                foreach ($res['data'] as $row) {
                                    $id = $row['id'];
                                    $driver_list[$id] = $row['e_name'];
                                }
                            }
                            dropdown_with_key($driver_list, $driver_id); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="form-label">Status</label>
                        <select name="vehicle_status" id="vehicle_status" required class="form-control">
                            <?php dropdown($vehicle_status_list, $vehicle_status); ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="change_btn">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal for adding Category  
<div class="modal fade" id="vehicle_type" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Vehicle Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="create_vehicle_type" id="vehicle_type_frm">
                    <div class="form-group">
                        <label for="form-label">Vehicle Type</label>
                        <?php
                        $vehicle_type_row = insert_row('vehicle_cat');
                        $cat_id = $vehicle_type_row['id'];
                        ?>
                        <input type="hidden" name="id" value="<?php echo $cat_id; ?>" required>
                        <input type="text" name="cat_name" value="<?php echo $cat_name; ?>" required class="form-control" oninput="this.value = this.value.toUpperCase()">
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
                <button type="button" class="btn btn-primary" id="vehicle_type_btn">Save</button>
            </div>
        </div>
    </div>
</div>
--?>
<?php require_once('required/footer2.php'); ?>