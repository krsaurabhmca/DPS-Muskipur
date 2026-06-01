<?php require_once("required/header.php"); ?>
<?php require_once("required/menu.php");
if (isset($_REQUEST['date'])) {
    $date = $_REQUEST['date'];
} else {
    $date = date("Y-m-d");
}
if (isset($_REQUEST['subject']) and isset($_REQUEST['class'])) {
    extract($_REQUEST);
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Lesson Tracker</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Lesson Tracker</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Lesson Tracker [<span class="text-md text-bold"><?php echo $class . "-" . $subject ?></span>] </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="container">
                    <form action="">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <select name="class" id="class" required class="form-control">
                                    <option value="">--Select Class--</option>
                                    <?php
                                    dropdown($class_list, $class);
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <select name="subject" id="subject_list" required class="form-control">
                                </select>
                            </div>
                            <div class="form-group col-md-2 mt-5">
                                <button class="btn btn-warning btn-md" onclick="submit()">Show</button>
                            </div>
                    </form>
                </div>
            </div>
            <?php if (isset($_REQUEST['class']) and isset($_REQUEST['subject'])) { ?>
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Lesson</th>
                                                        <th>Topic</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $i = 1;
                                                    if (isset($_REQUEST['class']) and isset($_REQUEST['subject'])) {
                                                        extract($_REQUEST);
                                                        $res = get_all("lesson_plan", '*', array('subject' => $subject, 'class' => $class));
                                                        foreach ($res['data'] as $row) {
                                                            extract($row);
                                                            echo "<tr>";
                                                            echo "<td> $i </td>";
                                                            echo "<td> $lesson </td>";
                                                            echo "<td> $topic </td>";
                                                            echo "<td> $status </td>";
                                                            echo "</tr>";
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
                    </div>
                </div>
            <?php } ?>
        </div>
</div>
</div>
</section>
</div>
<?php require_once("required/footer2.php"); ?>
<script>
    // function get_student() {
    //     let stu_class = $("#class").find(":selected").text();
    //     let stu_section = $("#section").find(":selected").text();
    //     $.ajax({
    //         'url': "required/master_process?task=get_student",
    //         'type': "POST",
    //         'data': {
    //             'stu_class': stu_class,
    //             'stu_section': stu_section
    //         },
    //         'success': function(data) {
    //             $("#student_list").html(data);
    //         }
    //     })
    // }
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
</script>