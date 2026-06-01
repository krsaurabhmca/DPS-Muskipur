<?php require_once('required/header.php'); ?>
<?php
require_once('required/menu.php');
if (isset($_GET['att_date'])) {
    $category = $_REQUEST['category'];
    $att_date = $_GET['att_date'];
} else {
    $att_date = date('Y-m-d');
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h2> Manage Employee Attendance &nbsp;<span class="badge badge-warning badge-sm p-2 circle">NEW</span></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Attendance</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <!-- Basic Forms -->
                <div class="box box-default">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-9">
                                <b><span class="text-danger">Month</span> : <?php echo  date('M') ?> <span class="text-danger"> Year</span> : <?php echo date("Y"); ?></b> [<?php echo $category; ?>]
                            </div>
                            <div class="col-md-3 text-right">
                                
                                        <form>
                                            <input type='date' name='att_date' value='<?php echo $att_date; ?>' id='att_date' max='<?php echo date('Y-m-d'); ?>' required>
                                           
                                            <button class="btn btn-warning btn-sm">Show</button>
                                        </form>
                               
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="text-end" style="float:right">
                                    <span class='btn btn-primary btn-sm border ' style="float:right">
                                        <input type="checkbox" id="selectall" onclick="selectAll(this)" /> Check All
                                    </span>&nbsp;
                                    <button id='present_btn' class='btn btn-success btn-sm' title='Present All Checked Data' style="float:right; margin-right:8px;">Present</button> &nbsp;
                                    <button id='abs_btn' class='btn btn-danger btn-sm' title='Absent All Checked Data' style="float:right; margin-right:8px;">Absent</button> &nbsp;
                                    <!-- <button id='att_btn' class='btn btn-warning btn-sm' title='Leave Checked Data' style="float:right; margin-right:8px;">Leave</i> </button> &nbsp; -->
                                    <!-- <button id='att_btn' class='btn btn-success btn-sm' title='Save Data' style="float:right; margin-right:8px;"><i class='fa fa-save'></i> </button> &nbsp; -->
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-lg-12 col-md-12">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-stripped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <!--<th>Code</th>-->
                                                <th>Name</th>
                                                <th>Department</th>
                                                <th>Designation</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                           
                                                //$res = get_all('employee', '*', array('e_category' => $category));
                                                $res = get_all('instructor','*', array('status'=>'ACTIVE'),'sl_no'); 
                                                ?>
                                                <form action='required/master_process.php?task=make_emp_att' method='post' id='att_frm'>
                                                <?php
                                                if ($res['count'] > 0) {
                                                    foreach ($res['data'] as $row) {
                                                        $id = $row['id'];
                                                        $emp_type = $row['e_category'];
                                                        echo "<tr>";
                                                        echo "<td>" . $row['sl_no'] . "</td>";
                                                        //echo "<td>" . $row['e_code'] . "</td>";
                                                        echo "<td>" . $row['name'] . "</td>";
                                                        echo "<td>" . $row['department'] . "</td>";
                                                        echo "<td>" . $row['designation'] . "</td>";
                                                        echo "<td width='185'>";
                                                        $tbl_name = "employee_att";
                                                        $col_name = 'd_' . date('j', strtotime($att_date));
                                                        if (date('D', strtotime($att_date)) == 'Sun') {
                                                            echo "<script> alert('Selected Date is sunday');</script>";
                                                        }
                                                        $mvalue = remove_space(date('M_Y', strtotime($att_date)));
                                                        $post = array('att_month' => $mvalue, 'emp_id' => $id);
                                                        $sql = "SELECT * from $tbl_name where emp_id = $id and att_month like '$mvalue' ";
                                                        $emp_att = direct_sql($sql);
                                                        if ($emp_att['count'] == 0) {
                                                            insert_data($tbl_name, $post);
                                                        }
                                                        if ($emp_att['data'][0][$col_name] == 'P') {
                                                            echo "P";
                                                        } else if ($emp_att['data'][0][$col_name] == 'A') {
                                                            echo "A";
                                                        } else if ($emp_att['data'][0][$col_name] == 'L') {
                                                            echo "L";
                                                        } else {
                                                            echo "<input data-emp='$emp_type' type='checkbox' value ='$id' name='sel_id[]' class='chk'>&nbsp;";
                                                            echo "<a data-id='$id' class='btn btn-danger btn-sm text-light' data-target='#addLeave' data-toggle='modal' id='add_leave_btn'>Add Leave</a>";
                                                        }
                                                        echo "</td></tr>";
                                                        $i++;
                                                    }
                                                }
                                            
                                                ?>
                                                </form>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <?php
                // Fetch GPS settings
                $create_settings_table_sql = "CREATE TABLE IF NOT EXISTS attendance_settings (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        latitude VARCHAR(50) NOT NULL,
                        longitude VARCHAR(50) NOT NULL,
                        radius DOUBLE NOT NULL DEFAULT 0.0,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )";
                mysqli_query($con, $create_settings_table_sql);

                $gps_settings = direct_sql("SELECT * FROM attendance_settings LIMIT 1");
                $allowed_lat = "";
                $allowed_lng = "";
                $radius = "";
                if ($gps_settings['count'] > 0) {
                    $allowed_lat = $gps_settings['data'][0]['latitude'];
                    $allowed_lng = $gps_settings['data'][0]['longitude'];
                    $radius = $gps_settings['data'][0]['radius'];
                }
                ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-map-marker text-primary"></i> GPS Attendance Boundary</h3>
                    </div>
                    <div class="box-body">
                        <form id="gps_settings_frm" method="POST" action="save_attendance_settings">
                            <div class="form-group">
                                <label for="gps_latitude">Latitude*</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="latitude" id="gps_latitude" value="<?php echo $allowed_lat; ?>" required placeholder="e.g. 25.123456">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-info btn-flat" onclick="getCurrentLocation()"><i class="fa fa-crosshairs"></i> Get</button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="gps_longitude">Longitude*</label>
                                <input type="text" class="form-control" name="longitude" id="gps_longitude" value="<?php echo $allowed_lng; ?>" required placeholder="e.g. 85.123456">
                            </div>
                            <div class="form-group">
                                <label for="gps_radius">Allowed Radius (in meters)*</label>
                                <input type="number" class="form-control" name="radius" id="gps_radius" value="<?php echo $radius; ?>" required placeholder="e.g. 100" min="5">
                            </div>
                            <button type="button" class="btn btn-primary btn-block" id="gps_save_btn"><i class="fa fa-save"></i> Save Settings</button>
                        </form>
                        
                        <?php if (!empty($allowed_lat) && !empty($allowed_lng)): ?>
                            <hr>
                            <div class="alert alert-info py-2" style="font-size:12px; margin-bottom: 10px;">
                                <strong>Current Boundary:</strong><br>
                                Lat: <?php echo $allowed_lat; ?>, Lng: <?php echo $allowed_lng; ?><br>
                                Allowed Radius: <?php echo $radius; ?> meters
                            </div>
                            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo $allowed_lat; ?>,<?php echo $allowed_lng; ?>" target="_blank" class="btn btn-default btn-xs btn-block"><i class="fa fa-external-link"></i> View on Google Maps</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal for Leave Starts-->
<div class="modal fade" id="addLeave" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog w-90" role="document">
        <div class="modal-content">
            <!--<div class="modal-body"> -->
            <div class="card rounded">
                <div class="card-header">
                    <h3 class="text-center">Add Leave</h3>
                </div>
                <div class="card-body">
                    <?php
                    $leave = insert_row("leave_details");
                    $lid = $leave['id'];
                    ?>
                    <form action="leave_details" id="leave_frm" method="POST">
                        <div class="form-group">
                            <label for="leave_type">Leave Type</label>
                            <select name="leave_type" id="leave_type" class="form-control" required>
                                <option value="">--Select Leave Type--</option>
                                <?php dropdown($leave_type_list, $leave_type); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="<?php echo date("Y-m-d"); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="<?php echo date("Y-m-d"); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="leave_cause">Leave Cause (upto 100 words)</label>
                            <input type="hidden" name="id" value="<?php echo $lid; ?>">
                            <input type="hidden" name="emp_id" id="emp_id">
                            <textarea type="text" name="leave_cause" id="leave_cause" class="form-control" required></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="leave_app" id="leave">
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" name="remarks" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <?php dropdown($status_list, $status); ?>
                            </select>
                        </div>
                    </form>
                    <form id="uploadLeaveApp" enctype="multipart/form-data">
                        <div id="displayLeaveApp"></div>
                        <label class="form-label">Upload Application</label>
                        <input type="file" class="form-control" name="leave_app" id="leave_app">
                    </form>
                </div>
                <!-- </div>
            </div> -->
                <div class="card-footer p-2 text-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="leave_btn">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ends -->

<?php require_once('required/footer2.php'); ?>
<script>
    //========== SELECT ALL CHECK BOX WITH PRESENT =======//

    function selectAll(source) {
        checkboxes = document.getElementsByName('sel_id[]');
        for (var i in checkboxes)
            checkboxes[i].checked = source.checked;
    }

    $("#add_leave_btn").on('click', function() {
        let emp_id = $(this).attr("data-id");
        document.getElementById("emp_id").value = emp_id;
    })

    //========================UPLOAD LEAVE APPLICATION=======================
    // $('#leave_app').change(function() {
    //     $("#uploadLeaveApp").submit();
    // });

    // $("#uploadLeaveApp").on('submit', (function(e) {
    //     e.preventDefault();
    //     $.ajax({
    //         url: "required/master_process?task=uploadLeaveApp",
    //         type: "POST",
    //         data: new FormData(this),
    //         contentType: false,
    //         cache: false,
    //         processData: false,
    //         success: function(data) {
    //             var obj = JSON.parse(data);
    //             $("#leave").val(obj.id);
    //             $("#displayLeaveApp").html("<img src='required/upload/" + obj.id + "' width='100px' height='100px' class='img-thumbnail'>");
    //             $.notify(obj.msg, obj.status);
    //         },
    //         error: function() {}
    //     });
    // }));

    function getCurrentLocation() {
        if (navigator.geolocation) {
            $.notify("Fetching your current location...", "info");
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('gps_latitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('gps_longitude').value = position.coords.longitude.toFixed(6);
                $.notify("Location retrieved successfully!", "success");
            }, function(error) {
                $.notify("Failed to get location: " + error.message, "error");
            });
        } else {
            $.notify("Geolocation is not supported by this browser.", "error");
        }
    }

    $("#gps_save_btn").click(function () {
        $("#gps_settings_frm").validate();

        if ($("#gps_settings_frm").valid()) {
            var task = $("#gps_settings_frm").attr('action');
            $(this).attr("disabled", true);
            $(this).html("Please Wait...");
            var data = $("#gps_settings_frm").serialize();
            $.ajax({
                'type': 'POST',
                'url': 'required/master_process?task=' + task,
                'data': data,
                success: function (data) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    $("#gps_save_btn").html("<i class='fa fa-save'></i> Save Settings");
                    $("#gps_save_btn").removeAttr("disabled");
                    if (obj.status == 'success') {
                        bootbox.alert(obj.msg, function () {
                            location.reload();
                        });
                    } else {
                        $.notify(obj.msg, obj.status);
                    }
                }
            });
        }
    });
</script>