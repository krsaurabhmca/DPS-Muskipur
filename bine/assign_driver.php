<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
} else {
    $fee = insert_row('driver_assign');
    $id = $fee['id'];
}

if ($id != '') {
    $res = get_data('driver_assign', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Manage Driver</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Assign Driver </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Assign Driver</h3>
                <div class="box-tools pull-right">
                    <a class='fa fa-plus btn btn-info btn-sm' href='assign_driver' title='Assign New Driver'> </a>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">

                <div class='row'>

                    <div class="col-lg-3 col-sm-6">

                        <form action='assign_driver' id='update_frm' method='post' enctype='multipart/form-data'>
                            <input type='hidden' value='<?php echo $id; ?>' name='id'>
                            <div class="form-group">
                                <label>Driver name</label>
                                <select name="" id="driver">
                                    <option value=""></option>
                                    <?php
                                    $res = get_all("employee", "*", array("e_category" => "DRIVER"));
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $arr_list["id"] = $row['e_name'];
                                        }
                                    }
                                    dropdown_with_key($arr_list, $driver); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Assign Vehicle</label>
                                <select name="vehicle_no" id="vehicle_no" required>
                                    <option value="">-Select Vehicle-</option>
                                    <?php dropdown_list('vehicle', 'id', 'vehicle_type', $vehicle_no); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <?php if ($e_sex != "") {
                                    if ($e_sex == "m") {
                                ?>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="e_sex" id="male" value="male" <?php echo "checked"; ?>>
                                            <label class="form-check-label" for="male">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="e_sex" id="female" value="f">
                                            <label class="form-check-label" for="female">Female</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="e_sex" id="other" value="o">
                                            <label for="other" class="form-check-label"> Other </label>
                                        </div>
                                    <?php } else if ($e_sex == "f") { ?>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="e_sex" id="male" value="male">
                                            <label class="form-check-label" for="male">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="e_sex" id="female" value="f" <?php echo "checked"; ?>>
                                            <label class="form-check-label" for="female">Female</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="e_sex" id="other" value="o">
                                            <label for="other" class="form-check-label"> Other </label>
                                        </div>
                                    <?php } else if ($e_sex == "o") { ?>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="e_sex" id="male" value="male">
                                            <label class="form-check-label" for="male">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="e_sex" id="female" value="f">
                                            <label class="form-check-label" for="female">Female</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="e_sex" id="other" value="o" <?php echo "checked"; ?>>
                                            <label for="other" class="form-check-label"> Other </label>
                                        </div>
                                    <?php }
                                } else { ?>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="e_sex" id="male" value="male">
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="e_sex" id="female" value="f">
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name="e_sex" id="other" value="o">
                                        <label for="other" class="form-check-label"> Other </label>
                                    </div>
                                <?php }
                                ?>
                            </div>
                            <div class="form-group">
                                <label>DOB</label>
                                <input type="date" class="form-control" required name='e_dob' value='<?php echo $e_dob; ?>'>
                            </div>
                            <div class="form-group">
                                <label>Designation</label>
                                <input class="form-control" required name='e_designation' value='<?php echo $e_designation; ?>'>
                            </div>
                            <div class="form-group">
                                <label>Qualification</label>
                                <input class="form-control" type='text' name='e_qualification' value='<?php echo $e_qualification; ?>'>
                            </div>
                            <div class="form-group">
                                <label>Professional</label>
                                <input class="form-control" type='text' name='e_professional' value='<?php echo $e_professional; ?>'>
                            </div>
                            <div class="form-group">
                                <label>Subject</label>
                                <input class="form-control" type='text' name='e_subject' value='<?php echo $e_subject; ?>'>
                            </div>
                            <div class="form-group">
                                <label>Mobile</label>
                                <input class="form-control" type='tel' name='e_mobile' value='<?php echo $e_mobile; ?>' maxlength="10" minlength="10">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" type='email' name='e_email' value='<?php echo $e_email; ?>'>
                            </div>
                            <div class="form-group">
                                <label>Aadhar No.</label>
                                <input class="form-control" type='text' name='e_adhar' value='<?php echo $e_adhar; ?>' maxlength="12" minlength="12">
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea rows="5" class="form-control" name='e_address'><?php echo $e_address; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Status </label>
                                <select name='status' class='form-control'>
                                    <?php dropdown($status_list, $status); ?>
                                </select>
                            </div>
                        </form>
                        <?php if ($_SESSION['user_type'] == 'Admin') { ?>
                            <button class="btn btn-primary btn-block" id='update_btn'> Save </button>
                        <?php } else { ?>
                            <button class="btn btn-border border-danger"> Don't Have Permission </button>
                        <?php } ?>


                    </div>

                    <div class="col-lg-9">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Name </th>
                                        <th> Mobile</th>
                                        <th> Email </th>
                                        <th>Qualification</th>
                                        <th>Status</th>
                                        <th> Operation </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $i = 1;
                                    $res = get_all('employee');
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                            echo "<tr>";
                                            echo "<td>" . $i . "</td>";
                                            echo "<td>" . $row['e_name'] . "</td>";
                                            echo "<td>" . $row['e_mobile'] . "</td>";
                                            echo "<td>" . $row['e_email'] . "</td>";
                                            echo "<td>" . $row['e_qualification'] . "</td>";
                                            echo "<td>" . $row['status'] . "</td>";
                                    ?>
                                            <td>
                                                <a href='add_employee.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
                                                <span class='delete_btn btn btn-danger btn-sm' data-table='employee' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
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
        </div>
    </section>
</div>
<?php require_once('required/footer2.php'); ?>