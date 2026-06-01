<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
} else {
    $emp = insert_row('employee');
    $id = $emp['id'];
}

if ($id != '') {
    $res = get_data('employee', $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Add Employee </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Employee</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Add Employee </h3>
                <div class="box-tools pull-right">

                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-header bg-warning text-black">
                                <b class="text-uppercase">Personal Details</b>
                            </div>
                            <div class="card-body">
                                <form action='update_employee' id='update_frm' method='post' name="emp_frm" enctype='multipart/form-data'>
                                    <input type='hidden' value='<?php echo $id; ?>' name='id'>
                                    <div class="form-row">
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Employee name</label>
                                                <input type="text" class="form-control" required name='e_name' value='<?php echo $e_name; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Employee Type &nbsp;<a data-toggle="modal" data-target="#addCategory" title="Create New Employee type" style="cursor:pointer"><span class="badge badge-warning">+</span></a></label>
                                                <select name="e_category" id="category" class="form-control" required>
                                                    <option value="">-Select Type-</option>
                                                    <?php
                                                    $sql = "SELECT * FROM emp_cat WHERE cat_name NOT IN ('DRIVER') ";
                                                    $emp_category = direct_sql($sql)['data'];
                                                    foreach ($emp_category as $data) {
                                                        extract($data);
                                                        $cat_list[$cat_name] = $cat_name;
                                                    }
                                                    dropdown($cat_list, $category); ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <select name="sex" id="sex" class="form-control" required>
                                                    <option value="">-Select Your Gender-</option>
                                                    <?php dropdown($gender_list, $sex); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>DOB</label>
                                                <input type="date" class="form-control" required name='dob' value='<?php echo $dob; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Blood Group</label>
                                                <select name="blood_group" id="blood_group" class="form-control">
                                                    <?php dropdown($bloodgroup_list, $blood_group); ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Mobile</label>
                                                <input class="form-control" type='tel' name='mobile' value='<?php echo $mobile; ?>' maxlength="10" minlength="10">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input class="form-control" type='email' name='email' value='<?php echo $email; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Aadhar No.</label>
                                                <input class="form-control" type='text' name='adhar' value='<?php echo $adhar; ?>' maxlength="12" minlength="12">
                                            </div>
                                            <div class="form-group">
                                                <label>Designation</label>
                                                <input class="form-control" required name='designation' value='<?php echo $designation; ?>'>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="card">
                                <div class="card-header bg-warning text-black">
                                    <b class="text-uppercase">Address Details</b>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="">Address</label>
                                        <textarea name="address" id="address" rows="3" class="form-control"><?php echo $address; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header bg-warning text-black">
                                    <b class="text-uppercase">Other Details</b>
                                </div>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label>Joining Date</label>
                                                <input type="date" class="form-control" required name='doj' value='<?php echo $doj; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Qualification</label>
                                                <input class="form-control" type='text' name='qualification' value='<?php echo $qualification; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Profession</label>
                                                <input class="form-control" type='text' name='professional' value='<?php echo $professional; ?>'>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label>Subject</label>
                                                <input class="form-control" type='text' name='subject' value='<?php echo $subject; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Salary(in <i class="fa fa-inr fa-fw"></i> )</label>
                                                <input class="form-control" type='number' name='salary' value='<?php echo $salary; ?>' min="5000" placeholder="0.00">
                                            </div>
                                            <div class="form-group">
                                                <label>Status </label>
                                                <select name='status' class='form-control'>
                                                    <?php dropdown($status_list, $status); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12 text-center">
                                    <div id='display'>
                                        <img src='required/upload/<?php if($photo == ""){ echo "no_image.jpg"; } else{ echo $photo; } ?>' width='150px' height='160px' id='result'>
                                    </div>
                                    <input type='hidden' name='photo' id='targetimg' class='form-control' readonly value='<?php echo $photo; ?>'>
                                    <span id='uploadarea' class='btn btn-secondary'>UPLOAD /CHANGE PHOTO </span>
                                </div>
                            </div>
                            </form>
                        </div>
                        <?php
                        if ($_SESSION['user_type'] == 'Admin' || $_SESSION['user_type'] == 'ADMIN') {
                            echo '<div class="text-center">
                            <button class="btn btn-success btn-sm" id="update_btn"> Save </button>
                            <input type="reset" onclick="emp_frm.reset()" class="btn btn-secondary btn-sm">
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
                <form action="add_category" id="cat_frm">
                    <div class="form-group">
                        <label for="form-label">Category</label>
                        <?php
                        $cat = insert_row("emp_cat");
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
<!-- Modal for photograph upload  -->
<div class='modal' id='uploadmodal'>
    <div class='modal-dialog'>
        <div class='modal-content'>
            <div class='modal-body'>
                <h4> Upload Image </h4>
                <hr>
                <form id='uploadForm' enctype='multipart/form-data'>
                    <div class='form-group'>
                        <label>Upload Photograph (Max 100 KB)</label>
                        <input type='file' name='uploadimg' id='uploadimg' accept='image'>
                        <br><small> Only Jpg and Png image upto 100KB. </small>
                    </div>
                </form>
                <div id="my_camera"></div>
                <form>
                    <input type=button value="Take Snapshot" onclick="take_snapshot2()">
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once('required/footer2.php'); ?>
<script>
    function reset() {
        $("#update_frm").reset();
    }
</script>