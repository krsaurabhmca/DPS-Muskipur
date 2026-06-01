<?php require_once('required/header.php'); ?>
<?php
require_once('required/menu.php');
if (isset($_REQUEST['subject']) and isset($_REQUEST['class'])) {
    $subject = $_REQUEST['subject'];
    $class = $_REQUEST['class'];
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h2>Subject Wise Topic Status </h2>
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
                        <b><span class="text-danger">Subject</span> : <?php echo  $subject; ?></b>
                    </div>
                    <div class="col-md-4 float-right">
                        <div class="row">
                            <div class="col-12">
                                <form>
                                    <!-- <button class="btn btn-danger display6 float-right" onclick="submit()">Show</button> -->
                                    <select name="subject" id="subject_list" class="display6 float-right" required onchange="submit()">
                                    </select>
                                    <select name="class" id="class" class="display6 float-right" required>
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
                                        <th>Subject</th>
                                        <th>Topic</th>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    if (isset($_REQUEST['class']) and isset($_REQUEST['subject'])) {
                                        extract($_REQUEST);
                                        $res = get_all('lesson_plan', '*', array('class' => $class, 'subject' => $subject));
                                    ?>
                                    <?php
                                        if ($res['count'] > 0) {
                                            foreach ($res['data'] as $row) {
                                                extract($row);
                                                echo "<tr>";
                                                echo "<td>" . $i . "</td>";
                                                echo "<td>" . $class . "</td>";
                                                echo "<td>" . $subject . "</td>";
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
</script>