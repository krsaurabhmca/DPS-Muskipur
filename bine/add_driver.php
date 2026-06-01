<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
} else {
    $fee = insert_row('employee');
    $id = $fee['id'];
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
        <h1> Manage Driver <span class="badge badge-success badge-sm">NEW</span></h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Driver</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Add & Manage Driver </h3>
                <div class="box-tools pull-right">

                </div>
            </div>
            <!-- box-header -->

            <div class="box-body">

                <div class='row'>

                    <div class="col-lg-12 col-md-12">

                        <form action='update_driver' id='update_frm' method='post' enctype='multipart/form-data'>
                            <input type='hidden' value='<?php echo $id; ?>' name='id'>

                            <div class="card">
                                <div class="card-header bg-warning">
                                    <b class="text-dark">Personal Details</b>
                                </div>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="form-group">
                                                <label>Employee name</label>
                                                <input type="text" class="form-control" required name='e_name' value='<?php echo $e_name; ?>'>
                                            </div>
                                            <div class="form-group">
                                                <label>Employee Category</label>
                                                <input type="text" name="e_category" value="DRIVER" class="form-control" required readonly>
                                            </div>
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <select name="e_sex" id="e_sex" required class="form-control">
                                                    <option value="">-Select Gender-</option>
                                                    <?php dropdown($gender_list, $e_sex); ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
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
                                        </div>
                                        <div class="col-lg-4 col-sm-6">
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
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-warning">
                                    <b class="text-dark">Address Details</b>
                                </div>
                                <div class="card-body">
                                    <div class="col-lg-12 col-sm-6">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea rows="5" class="form-control" name='e_address'><?php echo $e_address; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-warning">
                                    <b class="text-dark">Other Details</b>
                                </div>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="col-lg-6 col-sm-6">
                                            <div class="form-group">
                                                <label>Father's Name</label>
                                                <input class="form-control" type='text' name='e_father_name' value='<?php echo $e_father_name; ?>' required>
                                            </div>
    
                                            <div class="form-group">
                                                <label>DL No.</label>
                                                <input class="form-control" type='text' name='e_dl' value='<?php echo $e_dl; ?>' required>
                                            </div>
    
                                            <div class="form-group">
                                                <label>Status </label>
                                                <input type="hidden" name="e_pic" id="profile" required>
                                                <input type="hidden" name="e_aadhar_profile" id="aadhar" required>
                                                <input type="hidden" name="e_dl_proof" id="dl" required>
                                                <select name='status' class='form-control'>
                                                    <?php dropdown($status_list, $status); ?>
                                                </select>
                                            </div>
                                        </div>
                        </form>

                        <div class="col-lg-6 col-sm-6">
                            <div class="form-group">
                                <form id="uploadProfile" enctype="multipart/form-data">
                                    <div id="displayProfile"><?php if($e_pic != ""){?><img src="required/upload/<?php echo $e_pic; ?>" style="height:100px;weight:100px;"><?php }?></div>
                                    <label class="form-label">Upload/Change Profile</label>
                                    <input type="file" class="form-control" name="e_pic" id="e_pic">
                                </form>
                            </div>
                            <div class="form-group">
                                <form id="uploadAadhar" enctype="multipart/form-data">
                                    <div id="displayAadhar"><?php if($e_aadhar_profile!=""){?><img src="required/upload/<?php echo $e_aadhar_profile; ?>" style="height:100px;weight:100px;"><?php } ?></div>
                                    <label class="form-label">Upload/Change Aadhar</label>
                                    <input type="file" class="form-control" name="e_aadhar_profile" id="e_aadhar_profile">
                                </form>
                            </div>
                            <div class="form-group">
                                <form id="uploadDL" enctype="multipart/form-data">
                                    <div id="displayDL"><?php if($e_dl_proof != ""){?><img src="required/upload/<?php echo $e_dl_proof; ?>" style="height:100px;weight:100px;"><?php } ?></div>
                                    <label class="form-label">Upload/Change DL Proof</label>
                                    <input type="file" class="form-control" name="e_dl_proof" id="e_dl_proof">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<?php if ($_SESSION['user_type'] == 'Admin') { ?>
    <div class="text-center m-auto">
        <button class="btn btn-success btn-sm text-800" id='update_btn'> Save </button>
        <input type="reset" class="btn btn-secondary btn-sm text-800">
    </div>
<?php } else { ?>
    <button class="btn btn-border border-danger"> Don't Have Permission </button>
<?php } ?>
</div>
</div>
</div>
</div>
</div>
</section>
</div>
<?php require_once('required/footer2.php'); ?>