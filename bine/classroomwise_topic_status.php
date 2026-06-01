<?php require_once('required/header.php'); ?>
<?php
require_once('required/menu.php');
if (isset($_REQUEST['class'])) {
    $class = $_REQUEST['class'];
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h2> ClassRoom Wise Topic Status </h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Lesson</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-8">
                        <b><span class="text-danger">Classroom</span> : <?php echo  $class; ?>
                    </div>
                    <div class="col-md-4 float-right">
                        <div class="row">
                            <div class="col-md-12">
                                <form>
                                    <select name="class" id="class" class="display6 float-right" required onchange='submit()'>
                                        <?php
                                        dropdown($class_list, $class);
                                        ?>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-12 col-md-12">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-stripped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Class</th>
                                        <th>Topic</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if (isset($_REQUEST['class'])) {
                                        extract($_REQUEST);
                                        $res = get_all('lesson_plan', '*', array('class' => $class)); ?>
                                    <?php
                                        if ($res['count'] > 0) {
                                            foreach ($res['data'] as $row) {
                                                extract($row);
                                                $id = $row['id'];
                                                $class = $row['class'];
                                                echo "<tr>";
                                                echo "<td>" . $i . "</td>";
                                                echo "<td>" . $class . "</td>";
                                                echo "<td>" . $topic . "</td>";
                                                echo "<td>" . $period . "</td>";
                                                echo "<td>" . $status . "</td>";
                                                echo "<td width='185'>";
                                                echo btn_view('lesson_plan', $id);
                                                echo "</td></tr>";
                                                $i++;
                                            }
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
<!-- <script>
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
    $('#leave_app').change(function() {
        $("#uploadLeaveApp").submit();
    });

    $("#uploadLeaveApp").on('submit', (function(e) {
        e.preventDefault();
        $.ajax({
            url: "required/master_process?task=uploadLeaveApp",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                var obj = JSON.parse(data);
                $("#leave").val(obj.id);
                $("#displayLeaveApp").html("<img src='required/upload/" + obj.id + "' width='100px' height='100px' class='img-thumbnail'>");
                $.notify(obj.msg, obj.status);
            },
            error: function() {}
        });
    }));
</script> -->