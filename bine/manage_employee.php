<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Manage Employee <span class="badge badge-success badge-sm p-2">NEW</span></h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Employee</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Manage Employee </h3>
                <div class="box-tools pull-right">
                    <a class='fa fa-plus btn btn-success btn-sm' title='New Employee' href='add_employee'> </a>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">
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
                                                        <th> # </th>
                                                        <th> Name </th>
                                                        <th> Mobile</th>
                                                        <th> Email </th>
                                                        <th> Qualification</th>
                                                        <th> Role</th>
                                                        <th> Status</th>
                                                        <th> Operation </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php
                                                    $i = 1;
                                                    $sql = "SELECT * from employee where status NOT IN('BLOCK','AUTO')";
                                                    $res = direct_sql($sql);
                                                    if ($res['count'] > 0) {
                                                        foreach ($res['data'] as $row) {
                                                            $id = $row['id'];
                                                            echo "<tr>";
                                                            echo "<td>" . $i . "</td>";
                                                            echo "<td>" . $row['name'] . "</td>";
                                                            echo "<td>" . $row['mobile'] . "</td>";
                                                            echo "<td>" . $row['email'] . "</td>";
                                                            echo "<td>" . $row['qualification'] . "</td>";
                                                            echo "<td>" . $row['category'] . "</td>";
                                                            echo "<td>" . $row['status'] . "</td>";
                                                    ?>
                                                            <td>
                                                                <a href='add_employee.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
                                                                <span class='deletbtn btn btn-danger btn-sm' data-table='employee' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
                                                            </td>
                                                    <?php
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
            </div>
        </div>
    </div>
</section>
</div>
<?php require_once('required/footer2.php'); ?>