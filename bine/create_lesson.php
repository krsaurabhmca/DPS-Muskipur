<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'lesson_plan';
if (isset($_GET['link']) and $_GET['link'] != '') {
    $lesson = decode($_GET['link']);
    $id = $lesson['id'];
} else {

    $lesson = insert_row($table_name);
    $id = $lesson['id'];
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
        <h1>Lesson Details </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Lesson Plan</a></li>
            <li class="breadcrumb-item active">New Lesson</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Add /Update Lesson Details </h3>

                <div class="box-tools pull-right">
                    <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <form id='update_frm' action='update_lesson_plan'>
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="form-group row">

                                <label for="example-text-input" class="col-sm-4 col-form-label">Class</label>
                                <div class="col-sm-8">
                                    <input type='hidden' name='id' value='<?php echo $id; ?>' />
                                    <select name="class" id="student_class" required class="form-control">
                                        <?php dropdown($class_list, $class); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Subject</label>
                                <div class="col-sm-8">
                                    <?php if ($subject != "") { ?>
                                        <input type="text" class="form-control" value="<?php echo $subject; ?>" name="subject" readonly>
                                    <?php } else { ?>
                                        <select name="subject" id="subject_list" class="form-control" required>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Date</label>
                                <div class="col-sm-8">
                                    <input type="date" name="date" id="date" class="form-control" value="<?php echo $date; ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Lesson</label>
                                <div class="col-sm-8">
                                    <input type="text" name="lesson" id="lesson" class="form-control" value="<?php echo $lesson; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Topic</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" value='<?php echo $topic; ?>' name="topic" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label"> Period </label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" value='<?php echo $period; ?>' name="period" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">TimeSlot</label>
                                <div class="col-sm-8">
                                    <input name='timeslot' class='form-control' value="<?php echo  $timeslot; ?>" required placeholder="9:00 AM - 10:00 AM">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Status </label>
                                <div class="col-sm-8">
                                    <select name='status' class='form-control' required>
                                        <?php dropdown($timeslot_status_list, $status); ?>
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
    //Automatic subject displayed on selecting class
    $("#student_class").on('change', function() {
        let str1 = $(this).val();
        $.ajax({
            'type': "post",
            'url': 'required/master_process?task=select_class',
            'data': {
                'str': str1
            },
            success: function(data) {
                $("#subject_list").html(data);
            }
        })
    })
</script>