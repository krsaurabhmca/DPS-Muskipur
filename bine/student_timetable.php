<?php require_once("required/header.php"); ?>
<?php require_once("required/menu.php");
if (isset($_REQUEST['date'])) {
    $date = $_REQUEST['date'];
} else {
    $date = date("Y-m-d");
}
if (isset($_REQUEST['class']) and isset($_REQUEST['section'])) {
    extract($_REQUEST);
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Student Timetable</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Timetable Management</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Student Timetable [<span class="text-md text-bold"><?php echo $class . "-" . $section ?></span>] </h3>
                <!-- <div class="box-tools pull-right"> -->
                <!-- <a class='fa fa-plus btn btn-success btn-sm' title='New Timetable' href='create_timetable'> </a> -->
                <!-- </div> -->
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form>
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label for="class" class="">Class</label>
                                                    <div>
                                                        <select name="class" id="class" required class="form-control">
                                                            <?php dropdown($class_list, $class); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label for="section" class="">Section</label>
                                                    <div>
                                                        <select name="section" id="section" required class="form-control">
                                                            <?php dropdown($section_list, $section); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label for="date" class="">Date</label>
                                                    <div>
                                                        <input type="date" name="date" id="date" class="form-control" value="<?php echo date('Y-m-d', strtotime($date)); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 btn-submit my-auto">
                                                    <button class="btn btn-warning btn-sm" onclick="submit()"> Show </button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <?php
                                                if (isset($_REQUEST['class']) and isset($_REQUEST['section']) and isset($_REQUEST['date'])) {
                                                    extract($_REQUEST);
                                                    $day = strtoupper(date('l', strtotime($date)));
                                                    $res = get_all('timetable', '*', array('class' => $class, 'section' => $section, 'week_day' => $day));
                                                    foreach ($res['data'] as $row) {
                                                        $id = $row['id'];
                                                        echo "<div class='card shadow bg-success text-white my-4'>";
                                                        echo "<div class='card-body'>";
                                                        echo "<b>Timing: </b>" . get_data('timeslot_table', $row['timeslot'], 'timeslot')['data'] . "<br>";
                                                        echo "<b>Subject: </b>" . strtoupper($row['subject']) . "<br>";
                                                        echo "</div></div>";
                                                    }
                                                }
                                                ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
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
    function get_student() {
        let stu_class = $("#class").find(":selected").text();
        let stu_section = $("#section").find(":selected").text();
        $.ajax({
            'url': "required/master_process?task=get_student",
            'type': "POST",
            'data': {
                'stu_class': stu_class,
                'stu_section': stu_section
            },
            'success': function(data) {
                $("#student_list").html(data);
            }
        })
    }
</script>