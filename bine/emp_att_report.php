<?php require_once('required/header.php'); ?>
<?php
require_once('required/menu.php');
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Attendance Summary &nbsp;</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Summary</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-lg-10 col-md-8">
                        <b>Employee Attendance Report </b>
                    </div>
                    <div class="col-lg-2 col-md-2 float-right">
                        <button class="btn btn-success" id="export">Export</button>
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
                                        <th>Employee</th>
                                        <?php
                                        foreach ($att_month_list as $month) {
                                            echo "<th>" . $month . "</th>";
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = get_all("instructor");
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                            echo "<tr>";
                                            echo "<td>" . $row['name'] . "(" . $row['designation'] . ")" . "</td>";
                                            get_present_emp($id);
                                        }
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <th>Total Strength</th>
                                    <?php get_total_present_in_month('employee_att'); ?>
                                </tfoot>
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
    //=========SELECT ALL CHECK BOX WITH SAME NAME=======//
    function selectAll(source) {
        checkboxes = document.getElementsByName('sel_id[]');
        for (var i in checkboxes) {
            checkboxes[i].checked = source.checked;
        }
    }
    $("#export").click(function() {
        $("#example1").table2excel({
            // exclude CSS class
            exclude: ".noExl",
            name: "Worksheet Name",
            filename: "Employee_Attendance_Report", //do not include extension
            fileext: ".xls", // file extension
            exclude_img: true,
            exclude_links: true,
            exclude_inputs: true
        });
    });
</script>