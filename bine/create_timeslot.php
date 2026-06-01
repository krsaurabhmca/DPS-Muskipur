<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php');
$table_name = 'timeslot_table';
if (isset($_GET['link']) and $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $id = $data['id'];
} else {
    $fee = insert_row($table_name);
    $id = $fee['id'];
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
        <h1> Timeslot </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#transport">Transport</a></li>
            <li class="breadcrumb-item active">Create Timeslot</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Add and Update Timeslot </h3>
                <div class="box-tools pull-right">
                    <a class='fa fa-plus btn btn-info btn-sm' href='create_timeslot' title='Add Timeslot'> </a>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
                <div class='row'>
                    <div class="col-lg-3 col-sm-6">
                        <form action='update_timeslot' id='update_frm' method='post' enctype='multipart/form-data'>
                            <input type='hidden' value='<?php echo $id; ?>' name='id'>
                            <div class="form-group">
                                <label>Timeslot <span class="text-danger">*</span></label>
                                <input class="form-control" name='timeslot' value='<?php echo $timeslot; ?>' required placeholder="10:00 AM - 10:40 AM">
                            </div>
                            <div class="form-group">
                                <label>Period <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name='period' value='<?php echo $timeslot; ?>' required placeholder="eg:1,2" min="1" max="8">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <?php dropdown($status_list, $status); ?>
                                </select>
                            </div>
                        </form>
                        <button class="btn btn-primary" id='update_btn'> Save </button>
                    </div>

                    <div class="col-lg-9">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th> # </th>
                                        <th> Timeslot</th>
                                        <th> Period</th>
                                        <th> Status </th>
                                        <th> Operation </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $i = 1;
                                    $res = get_all($table_name);
                                    foreach ($res['data'] as $row) {
                                        extract($row);
                                        echo "<tr><td>";
                                        echo   $i . "</td>";
                                        echo "<td>" . $timeslot . "</td>";
                                        echo "<td>" . $period . "</td>";
                                        echo "<td>" . $status . "</td>";
                                    ?>
                                        <td>
                                            <?php echo btn_edit('create_timeslot', $id); ?>
                                            <?php echo btn_delete($table_name, $id); ?>
                                        </td>
                                    <?php
                                        $i++;
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