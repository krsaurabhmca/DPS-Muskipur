<?php require_once("required/header.php"); ?>
<?php
require_once("required/menu.php");
$table_name = "question_bank";
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
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Create Questions</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#"> Lesson Plan</a></li>
            <li class="breadcrumb-item active">Add Question</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Question Details </h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <form id="update_frm" action="update_question">
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
                                <label for="example-text-input" class="col-sm-4 col-form-label">Lesson</label>
                                <div class="col-sm-8">
                                    <?php if ($lesson_id != "") { ?>
                                        <input type="hidden" name="lesson_id" id="lesson_id" readonly required value="<?php echo $lesson_id; ?>" class="form-control">
                                        <input type="text" readonly required value="<?php echo get_data('lesson_plan', $lesson_id, 'lesson')['data']; ?>" class="form-control">
                                    <?php } else { ?>
                                        <select name="lesson_id" id="lesson_id" required class="form-control">
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Question</label>
                                <div class="col-sm-8">
                                    <textarea name="question" id="question" rows="1" placeholder="Enter Question" class="form-control"><?php echo $question; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" required class="form-control">
                                        <?php dropdown($status_list, $status) ?>
                                    </select>
                                </div>
                            </div>

                </form>
                <!-- <div align="center">
                    <a class="btn btn-success" id="update_btn">Add Question</a>
                </div> -->
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