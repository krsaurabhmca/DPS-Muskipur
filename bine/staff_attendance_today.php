<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// selfie_attendance.emp_id = employee.id
$emp_sql  = "SELECT id, e_name, e_category AS department, designation FROM employee WHERE status NOT IN('BLOCK','AUTO') ORDER BY e_name ASC";
$emp_res  = direct_sql($emp_sql);
$all_staff      = $emp_res['data'] ?? [];
$total_teachers = $emp_res['count'] ?? 0;

// Selfie attendance for selected date — keyed by emp_id (= instructor.id)
$att_sql = "
    SELECT emp_id, latitude, longitude, selfie_file, created_at,
           checkout_latitude, checkout_longitude, checkout_file, checkout_time
    FROM selfie_attendance
    WHERE att_date = '$selected_date'
";
$att_rows = [];
foreach ((direct_sql($att_sql)['data'] ?? []) as $row) {
    $att_rows[$row['emp_id']] = $row;
}

// employee_att grid fallback — emp_id also = instructor.id
$col_name = 'd_' . date('j', strtotime($selected_date));
$mvalue   = remove_space(date('M_Y', strtotime($selected_date)));
$grid_map = [];
foreach ((direct_sql("SELECT emp_id, $col_name as day_status FROM employee_att WHERE att_month = '$mvalue'")['data'] ?? []) as $g) {
    $grid_map[$g['emp_id']] = $g['day_status'];
}

$total_present = $total_half_day = 0;
foreach ($all_staff as $s) {
    $eid = $s['id'];
    if (isset($att_rows[$eid])) {
        $att_rows[$eid]['checkout_time'] ? $total_present++ : $total_half_day++;
    } elseif (in_array($grid_map[$eid] ?? '', ['P', 'L'])) {
        $total_present++;
    }
}
$total_absent = max(0, $total_teachers - $total_present - $total_half_day);
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-calendar-check-o text-primary"></i> Daily Staff Attendance
            <small>GPS Selfie Verified — <?= date('d M Y', strtotime($selected_date)) ?></small>
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Staff Attendance</li>
            <li class="breadcrumb-item active">Today</li>
        </ol>
    </section>

    <style>
        .att-photo { cursor: pointer; transition: transform 0.2s; }
        .att-photo:hover { transform: scale(1.1); }
    </style>

    <section class="content">
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Staff</span>
                        <span class="info-box-number"><?= $total_teachers ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Full Day (GPS)</span>
                        <span class="info-box-number"><?= $total_present ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-adjust"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Checked-In Only</span>
                        <span class="info-box-number"><?= $total_half_day ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-red"><i class="fa fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Not Marked</span>
                        <span class="info-box-number"><?= $total_absent ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-top:5px;">
                            Attendance — <?= date('d M, Y (D)', strtotime($selected_date)) ?>
                        </h3>
                        <div class="box-tools pull-right" style="display:flex;gap:8px;">
                            <form method="GET" style="margin:0;">
                                <input type="date" name="date" class="form-control input-sm"
                                    value="<?= htmlspecialchars($selected_date) ?>"
                                    max="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
                            </form>
                            <button onclick="location.reload()" class="btn btn-default btn-sm">
                                <i class="fa fa-refresh"></i> Refresh
                            </button>
                            <a href="staff_attendance_history.php" class="btn btn-info btn-sm">
                                <i class="fa fa-history"></i> History
                            </a>
                            <a href="staff_attendance_report_matrix.php" class="btn btn-success btn-sm">
                                <i class="fa fa-th"></i> Matrix
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="today_att_table">
                                <thead>
                                    <tr class="bg-primary">
                                        <th>#</th>
                                        <th>Staff Name</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                        <th>In Photo</th>
                                        <th>Check-In</th>
                                        <th>In GPS</th>
                                        <th>Out Photo</th>
                                        <th>Check-Out</th>
                                        <th>Out GPS</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $sr = 0;
                                foreach ($all_staff as $staff):
                                    $sr++;
                                    $eid         = $staff['id'];
                                    $sa          = $att_rows[$eid] ?? null;
                                    $grid_status = $grid_map[$eid] ?? '';

                                    if ($sa) {
                                        $status_label = $sa['checkout_time']
                                            ? '<span class="label label-success">Full Day</span>'
                                            : '<span class="label label-warning">Checked-In</span>';
                                    } elseif ($grid_status === 'P') {
                                        $status_label = '<span class="label label-success">Present</span>';
                                    } elseif ($grid_status === 'L') {
                                        $status_label = '<span class="label label-info">Leave</span>';
                                    } elseif ($grid_status === 'A') {
                                        $status_label = '<span class="label label-danger">Absent</span>';
                                    } else {
                                        $status_label = '<span class="label label-default">Not Marked</span>';
                                    }
                                ?>
                                    <tr>
                                        <td><?= $sr ?></td>
                                        <td><strong><?= htmlspecialchars($staff['e_name']) ?> (ID: <?= $eid ?>)</strong></td>
                                        <td><?= htmlspecialchars($staff['designation'] ?? '') ?></td>
                                        <td>
                                            <?php if ($sa && $sa['selfie_file']): ?>
                                                <img src="upload/<?= $sa['selfie_file'] ?>" width="50" height="50"
                                                    class="img-thumbnail att-photo"
                                                    style="object-fit:cover;border-radius:50%;"
                                                    onclick="viewImage('upload/<?= $sa['selfie_file'] ?>', 'Check-In — <?= htmlspecialchars($staff['e_name']) ?>')">
                                            <?php else: echo '—'; endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($sa && $sa['created_at']): ?>
                                                <i class="fa fa-sign-in text-success"></i>
                                                <?= date('h:i A', strtotime($sa['created_at'])) ?>
                                            <?php else: echo '—'; endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($sa && $sa['latitude']): ?>
                                                <a href="https://maps.google.com/?q=<?= $sa['latitude'] ?>,<?= $sa['longitude'] ?>"
                                                    target="_blank" class="btn btn-xs btn-default">
                                                    <i class="fa fa-map-marker text-danger"></i>
                                                    <?= number_format((float)$sa['latitude'], 4) ?>,<?= number_format((float)$sa['longitude'], 4) ?>
                                                </a>
                                            <?php else: echo '—'; endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($sa && $sa['checkout_file']): ?>
                                                <img src="upload/<?= $sa['checkout_file'] ?>" width="50" height="50"
                                                    class="img-thumbnail att-photo"
                                                    style="object-fit:cover;border-radius:50%;"
                                                    onclick="viewImage('upload/<?= $sa['checkout_file'] ?>', 'Check-Out — <?= htmlspecialchars($staff['name']) ?>')">
                                            <?php else: echo '—'; endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($sa && $sa['checkout_time']): ?>
                                                <i class="fa fa-sign-out text-danger"></i>
                                                <?= date('h:i A', strtotime($sa['checkout_time'])) ?>
                                            <?php elseif ($sa): ?>
                                                <span class="text-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                            <?php else: echo '—'; endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($sa && $sa['checkout_latitude']): ?>
                                                <a href="https://maps.google.com/?q=<?= $sa['checkout_latitude'] ?>,<?= $sa['checkout_longitude'] ?>"
                                                    target="_blank" class="btn btn-xs btn-default">
                                                    <i class="fa fa-map-marker text-success"></i>
                                                    <?= number_format((float)$sa['checkout_latitude'], 4) ?>,<?= number_format((float)$sa['checkout_longitude'], 4) ?>
                                                </a>
                                            <?php else: echo '<span class="label label-default">Not Marked</span>'; endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($sa && $sa['created_at'] && $sa['checkout_time']) {
                                                $in = strtotime($sa['created_at']);
                                                $out = strtotime($sa['checkout_time']);
                                                $diff = max(0, $out - $in);
                                                $h = floor($diff / 3600);
                                                $m = floor(($diff % 3600) / 60);
                                                echo sprintf("%02dh %02dm", $h, $m);
                                            } else {
                                                echo '—';
                                            }
                                            ?>
                                        </td>
                                        <td><?= $status_label ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function viewImage(url, title) {
        Swal.fire({
            title: title, imageUrl: url, imageAlt: title,
            width: 500, imageWidth: '100%',
            showCloseButton: true, showConfirmButton: false,
            padding: '0 0 20px 0', background: '#fff', backdrop: 'rgba(0,0,0,0.8)'
        });
    }
    $(document).ready(function () {
        $('#today_att_table').DataTable({ order: [[0, 'asc']], pageLength: 50, responsive: true });
    });
</script>
<?php require_once('required/footer.php'); ?>