<?php require_once("required/header.php") ?>
<?php require_once("required/menu.php") ?>

<?php require_once("required/header.php"); ?>
<?php
require_once("required/menu.php");
$table_name = "homework";
if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
} else {
    $emp = insert_row($table_name);
    $id = $emp['id'];
}

if ($id != '') {
    $res = get_data($table_name, $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
if ($date != "") {
    $h_date = date("Y-m-d", strtotime($date));
} else {
    $h_date = date("Y-m-d");
}
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Add Homework</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#"> Student Management</a></li>
            <li class="breadcrumb-item active">Homework</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Homework Details </h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <form id="update_frm" action="update_homework">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Class</label>
                                <div class="col-sm-8">
                                    <input type='hidden' name='id' value='<?php echo $id; ?>' />
                                    <select name="class" id="class" required class="form-control">
                                        <?php dropdown($class_list, $class); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Section</label>
                                <div class="col-sm-8">
                                    <input type='hidden' name='id' value='<?php echo $id; ?>' />
                                    <select name="section" id="section" required class="form-control">
                                        <?php dropdown($section_list, $section); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Subject</label>
                                <div class="col-sm-8">
                                    <?php if ($subject != "") { ?>
                                        <input type="text" name="subject" id="subject" readonly required value="<?php echo $subject ?>" class="form-control">
                                    <?php } else { ?>
                                        <select name="subject" id="subject_list" required class="form-control">
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Date</label>
                                <div class="col-sm-8">
                                    <input type="date" name="date" id="date" required class="form-control" value="<?php echo $h_date; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <input type="hidden" name="homework" id="targetimg" class="form-control">
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" required class="form-control">
                                        <?php dropdown($status_list, $status) ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea name="description" id="description" required class="form-control" rows="3"><?php echo $description; ?></textarea>
                                </div>
                            </div>
                </form>
                <div class="form-group row">
                    <label for="example-text-input" class="col-sm-4 col-form-label">Change/Upload Homework</label>
                    <div class="col-sm-8">
                            <form id="uploadHomework" enctype="multipart/form-data">
                                <div id="displayHW"><?php if($homework != ""){ ?> <img src="required/upload/<?php echo $homework; ?>" style="height:100px;width:100px;" > <?php } ?></div>
                                <input type="file" name="homework" id="homework" class="form-control">
                            </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col  -->
</div>
<!-- /.row -->
</div>
<!-- /.box-body -->
</section>
</div>
<?php require_once("required/footer2.php"); ?>
<script>
    $("#class").on('change', function() {
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
    $("#subject_list").on('change', function() {
        let str1 = $(this).val();
        $.ajax({
            'type': "post",
            'url': 'required/master_process?task=select_lesson',
            'data': {
                'str': str1
            },
            success: function(data) {
                $("#lesson_id").html(data);
            }
        })
    })
</script>
















<?php require_once("required/footer2.php") ?>