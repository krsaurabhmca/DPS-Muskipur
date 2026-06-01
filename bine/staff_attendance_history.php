<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
$selected_month = $_GET['month'] ?? date('m');
$selected_year  = $_GET['year']  ?? date('Y');
$selected_emp   = $_GET['emp_id'] ?? '';
$month_year_str = $selected_year . '-' . str_pad($selected_month, 2, '0', STR_PAD_LEFT);

// selfie_attendance.emp_id = employee.id
$staff_sql  = "SELECT id, e_name, e_category AS department, designation FROM employee WHERE status NOT IN('BLOCK','AUTO') ORDER BY e_name ASC";
$staff_list = direct_sql($staff_sql)['data'] ?? [];

// Build filtered history query (LEFT JOIN so records show even if employee deleted)
$emp_filter  = $selected_emp ? "AND sa.emp_id = '" . mysqli_real_escape_string($con, $selected_emp) . "'" : '';
$history_sql = "
    SELECT
        sa.emp_id,
        i.e_name,
        i.e_category AS department,
        i.designation,
        sa.att_date,
        sa.latitude, sa.longitude, sa.selfie_file, sa.created_at,
        sa.checkout_latitude, sa.checkout_longitude, sa.checkout_file, sa.checkout_time
    FROM selfie_attendance sa
    LEFT JOIN employee i ON sa.emp_id = i.id
    WHERE sa.att_date LIKE '$month_year_str-%'
    $emp_filter
    ORDER BY sa.att_date DESC, i.e_name ASC
";
$history_data = direct_sql($history_sql)['data'] ?? [];
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-history text-info"></i> Staff Attendance History
            <small>GPS Selfie Verified — Month-wise records</small>
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Staff Attendance</li>
            <li class="breadcrumb-item active">History</li>
        </ol>
    </section>

    <style>
        .att-photo { cursor: pointer; transition: transform 0.2s; }
        .att-photo:hover { transform: scale(1.1); }
    </style>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-filter"></i> Search Filters</h3>
            </div>
            <div class="box-body">
                <form method="GET" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Month</label>
                            <select name="month" class="form-control">
                                <?php for ($m = 1; $m <= 12; $m++):
                                    $sel = ($m == $selected_month) ? 'selected' : '';
                                    echo "<option value='$m' $sel>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                                endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Year</label>
                            <select name="year" class="form-control">
                                <?php for ($y = date('Y'); $y >= 2024; $y--):
                                    $sel = ($y == $selected_year) ? 'selected' : '';
                                    echo "<option value='$y' $sel>$y</option>";
                                endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Staff Member</label>
                            <select name="emp_id" class="form-control select2">
                                <option value="">— All Staff —</option>
                                <?php foreach ($staff_list as $u):
                                    $sel = ($u['id'] == $selected_emp) ? 'selected' : '';
                                    echo "<option value='{$u['id']}' $sel>{$u['e_name']} ({$u['designation']})</option>";
                                endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-search"></i> Show Records
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Attendance for <strong><?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?></strong>
                    <small class="text-muted">(<?= count($history_data) ?> records)</small>
                </h3>
                <div class="box-tools" style="display:flex;gap:5px;">
                    <a href="staff_attendance_report_matrix.php?month=<?= $selected_month ?>&year=<?= $selected_year ?>"
                        class="btn btn-success btn-sm"><i class="fa fa-th"></i> Matrix</a>
                    <a href="staff_attendance_report_timesheet.php?month=<?= $selected_month ?>&year=<?= $selected_year ?>"
                        class="btn btn-info btn-sm"><i class="fa fa-clock-o"></i> Timesheet</a>
                    <button class="btn btn-default btn-sm" onclick="window.print()">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="history_table">
                        <thead>
                            <tr class="bg-navy">
                                <th>Date</th>
                                <th>Staff Name</th>
                                <th>Designation</th>
                                <th>In Photo</th>
                                <th>Check-In</th>
                                <th>In Coords</th>
                                <th>Out Photo</th>
                                <th>Check-Out</th>
                                <th>Out Coords</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($history_data)): ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted" style="padding:30px;">
                                        <i class="fa fa-calendar-times-o fa-2x"></i>
                                        <p class="mt-2">No GPS selfie records found for this period.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($history_data as $row): ?>
                                    <tr>
                                        <td>
                                            <strong><?= date('d M Y', strtotime($row['att_date'])) ?></strong><br>
                                            <small class="text-muted"><?= date('D', strtotime($row['att_date'])) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($row['e_name'] ?? 'Unknown') ?></td>
                                        <td><?= htmlspecialchars($row['designation'] ?? '') ?></td>
                                        <td>
                                            <?php if ($row['selfie_file']): ?>
                                                <img src="upload/<?= $row['selfie_file'] ?>" width="45" height="45"
                                                    class="img-thumbnail att-photo"
                                                    style="object-fit:cover;border-radius:50%;"
                                                    onclick="viewImage('upload/<?= $row['selfie_file'] ?>', 'Check-In Selfie')">
                                            <?php else: echo '—'; endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['created_at']): ?>
                                                <i class="fa fa-sign-in text-success"></i>
                                                <?= date('h:i A', strtotime($row['created_at'])) ?>
                                            <?php else: echo '—'; endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['latitude']): ?>
                                                <a href="https://maps.google.com/?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>"
                                                    target="_blank" class="btn btn-xs btn-default">
                                                    <i class="fa fa-map-marker text-danger"></i>
                                                    <?= number_format((float)$row['latitude'], 4) ?>,<?= number_format((float)$row['longitude'], 4) ?>
                                                </a>
                                            <?php else: echo '—'; endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['checkout_file']): ?>
                                                <img src="upload/<?= $row['checkout_file'] ?>" width="45" height="45"
                                                    class="img-thumbnail att-photo"
                                                    style="object-fit:cover;border-radius:50%;"
                                                    onclick="viewImage('upload/<?= $row['checkout_file'] ?>', 'Check-Out Selfie')">
                                            <?php else: echo '—'; endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['checkout_time']): ?>
                                                <i class="fa fa-sign-out text-danger"></i>
                                                <?= date('h:i A', strtotime($row['checkout_time'])) ?>
                                            <?php else: ?>
                                                <span class="label label-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['checkout_latitude']): ?>
                                                <a href="https://maps.google.com/?q=<?= $row['checkout_latitude'] ?>,<?= $row['checkout_longitude'] ?>"
                                                    target="_blank" class="btn btn-xs btn-default">
                                                    <i class="fa fa-map-marker text-success"></i>
                                                    <?= number_format((float)$row['checkout_latitude'], 4) ?>,<?= number_format((float)$row['checkout_longitude'], 4) ?>
                                                </a>
                                            <?php else: echo '—'; endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['checkout_time']): ?>
                                                <span class="label label-success">Full Day</span>
                                            <?php elseif ($row['created_at']): ?>
                                                <span class="label label-warning">Half Day</span>
                                            <?php else: ?>
                                                <span class="label label-default">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
        $('#history_table').DataTable({
            order: [[0, 'desc']], pageLength: 50, responsive: true,
            dom: 'Bfrtip', buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
        $('.select2').select2();
    });
</script>
<?php require_once('required/footer.php'); ?>