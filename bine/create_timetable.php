<?php require_once("required/header.php"); ?>
<?php require_once("required/menu.php");
$table_name = 'timetable';
if (isset($_GET['link']) and $_GET['link'] != '') {
    $timetable = decode($_GET['link']);
    $id = $timetable['id'];
} else {

    $timetable = insert_row($table_name);
    $id = $timetable['id'];
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
        <h1>Timetable</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Timetable Management</a></li>
            <li class="breadcrumb-item active">Create Timetable</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Create or Update Timetable <span class="float-right"><a id="update_btn" class="btn btn-success btn-sm" title="Save Details"><i class="fa fa-save"></i></a></span></h5>
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <form action="update_timetable" id="update_frm">
                                    <div class="form-group">
                                        <label for="class" class="col-sm-4 form-label">Class</label>
                                        <div class="col-sm-8">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>" required>
                                            <select name="class" id="class" class="form-control" required>
                                                <?php dropdown($class_list, $class); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="section" class="col-sm-4 form-label">Section</label>
                                        <div class="col-sm-8">
                                            <select name="section" id="section" class="form-control" required>
                                                <?php dropdown($section_list, $section); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="subject" class="col-sm-4 form-label">Subject</label>
                                        <div class="col-sm-8">
                                            <?php if ($subject != "") { ?>
                                                <input type="text" name="subject" id="subject" class="form-control" required value="<?php echo $subject ?>" readonly>
                                            <?php } else { ?>
                                                <select name="subject" id="subject_list" class="form-control" required>
                                                </select>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="faculty" class="col-sm-4 form-label">Faculty</label>
                                        <div class="col-sm-8">
                                            <select name="faculty_id" id="faculty" class="select2 form-control">
                                                <?php
                                                dropdown_where('employee', 'id', 'e_name', array("e_category" => 'TEACHER'), $faculty_id);
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="day" class="col-sm-4 form-label">Day</label>
                                        <div class="col-sm-8">
                                            <select name="week_day" id="day" class="form-control">
                                                <?php dropdown($day_list, $week_day); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="timeslot" class="col-sm-4 form-label">Timeslot <span class="badge badge-warning badge-sm"><a href="create_timeslot" title="create_timeslot"><i class="fa fa-plus"></i></a></span></label>
                                        <div class="col-sm-8">
                                            <select name="timeslot" id="timeslot" class="form-control">
                                                <?php dropdown_list('timeslot_table', 'id', 'timeslot', $timeslot); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="status" class="col-sm-4 form-label">Status</label>
                                        <div class="col-sm-8">
                                            <select name="status" id="status" class="form-control">
                                                <?php dropdown($status_list, $status); ?>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
</div>
</section>
</div>
<?php require_once("required/footer2.php"); ?>
<script>
    $("#class").on('change', function() {
        let str1 = $(this).val();
        // alert(str1);
        $.ajax({
            'type': "post",
            'url': 'required/master_process.php?task=select_class',
            'data': {
                'str': str1
            },
            success: function(data) {
                $("#subject_list").html(data);
            }
        })
    })
</script>