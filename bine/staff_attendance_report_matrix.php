<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>

<?php
$selected_month = $_GET['month'] ?? date('m');
$selected_year  = $_GET['year']  ?? date('Y');
$month_year_str = $selected_year . '-' . str_pad($selected_month, 2, '0', STR_PAD_LEFT);
$days_in_month  = date('t', strtotime($month_year_str . '-01'));

// selfie_attendance.emp_id = employee.id
$users_sql = "SELECT id, e_name, e_category AS department, designation FROM employee WHERE status NOT IN('BLOCK','AUTO') ORDER BY e_name ASC";
$users_res  = direct_sql($users_sql);
$staff_list = $users_res['data'] ?? [];

// Fetch selfie_attendance for the month (checkin rows give P, checkout = full day)
$att_sql = "SELECT emp_id, DAY(att_date) as day_num, created_at, checkout_time
            FROM selfie_attendance
            WHERE att_date LIKE '$month_year_str-%'";
$att_res = direct_sql($att_sql);

// Fetch employee_att grid for same month (for P/A/L from standard grid)
$mvalue   = remove_space(date('M_Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)));
$grid_sql = "SELECT * FROM employee_att WHERE att_month = '$mvalue'";
$grid_res = direct_sql($grid_sql);
$grid_map = [];
foreach (($grid_res['data'] ?? []) as $g) {
    $grid_map[$g['emp_id']] = $g;
}

// Build selfie matrix: [emp_id][day] = 'P' (full) or 'HD' (half-day / no checkout)
$selfie_matrix = [];
foreach (($att_res['data'] ?? []) as $row) {
    $selfie_matrix[$row['emp_id']][$row['day_num']] = $row['checkout_time'] ? 'P' : 'HD';
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-th text-success"></i> Monthly Attendance Matrix
            <small>Status per day — GPS + Standard Grid</small>
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Staff Attendance</li>
            <li class="breadcrumb-item active">Matrix</li>
        </ol>
    </section>

    <style>
        .att-cell { text-align: center; font-weight: bold; font-size: 12px; padding: 5px !important; }
        .st-P  { color: #00a65a; background: #f0fff5; }
        .st-A  { color: #dd4b39; background: #fff5f5; }
        .st-L  { color: #00c0ef; background: #f0faff; }
        .st-HD { color: #f39c12; background: #fffdf0; }
        .st-empty { color: #ddd; }
        .freeze-col { position: sticky; left: 0; background: #fff; z-index: 10; border-right: 2px solid #ddd; }
        .table-responsive { overflow-x: auto; }
        .stats-col { background: #f9f9f9; font-weight: bold; text-align: center; }
        .legend-dot { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 4px; }

        @media print {
            @page { size: landscape; margin: 5mm; }
            body, html { margin: 0 !important; padding: 0 !important; }
            .content-wrapper, .main-footer, .wrapper { margin-left: 0 !important; min-height: 0 !important; padding: 0 !important; }
            .main-header, .main-sidebar, form, .btn, .content-header, .breadcrumb { display: none !important; }
            .box { border: none !important; box-shadow: none !important; margin: 0 !important; }
            .table-responsive { overflow: visible !important; width: 100% !important; }
            .table { font-size: 9px !important; border-collapse: collapse !important; }
            .table th, .table td { padding: 3px 2px !important; border: 1px solid #ddd !important; }
            .freeze-col { position: static !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body { zoom: 70%; }
        }
    </style>

    <section class="content">
        <!-- Filter -->
        <div class="box box-default">
            <div class="box-body">
                <form method="GET" class="row" style="margin-bottom:0;">
                    <div class="col-md-3">
                        <select name="month" class="form-control" onchange="this.form.submit()">
                            <?php for ($m = 1; $m <= 12; $m++):
                                $sel = ($m == $selected_month) ? 'selected' : '';
                                echo "<option value='$m' $sel>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                            endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="year" class="form-control" onchange="this.form.submit()">
                            <?php for ($y = date('Y'); $y >= 2024; $y--):
                                $sel = ($y == $selected_year) ? 'selected' : '';
                                echo "<option value='$y' $sel>$y</option>";
                            endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-7 text-right">
                        <a href="staff_attendance_today.php" class="btn btn-primary">
                            <i class="fa fa-calendar-check-o"></i> Today
                        </a>
                        <a href="staff_attendance_history.php?month=<?= $selected_month ?>&year=<?= $selected_year ?>"
                            class="btn btn-default">
                            <i class="fa fa-history"></i> History
                        </a>
                        <a href="staff_attendance_report_timesheet.php?month=<?= $selected_month ?>&year=<?= $selected_year ?>"
                            class="btn btn-info">
                            <i class="fa fa-clock-o"></i> Timesheet
                        </a>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Legend -->
        <div class="box box-default">
            <div class="box-body" style="padding:8px 16px;">
                <strong>Legend:</strong> &nbsp;
                <span class="legend-dot" style="background:#00a65a;"></span>P = Present (Full Day) &nbsp;
                <span class="legend-dot" style="background:#f39c12;"></span>HD = Half Day (No Check-Out) &nbsp;
                <span class="legend-dot" style="background:#dd4b39;"></span>A = Absent &nbsp;
                <span class="legend-dot" style="background:#00c0ef;"></span>L = Leave &nbsp;
                <span class="legend-dot" style="background:#ddd;"></span>– = No Data
            </div>
        </div>

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?> — Attendance Matrix
                </h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th class="freeze-col" style="min-width:160px;">Staff Name</th>
                                <?php for ($d = 1; $d <= $days_in_month; $d++):
                                    $dow    = date('D', mktime(0, 0, 0, $selected_month, $d, $selected_year));
                                    $is_sun = ($dow === 'Sun');
                                ?>
                                    <th style="text-align:center;padding:4px;min-width:30px;<?= $is_sun ? 'background:#e8e8e8;color:#999;' : '' ?>">
                                        <?= $d ?><br><small style="font-weight:normal;"><?= $dow ?></small>
                                    </th>
                                <?php endfor; ?>
                                <th class="stats-col text-success" title="Present">P</th>
                                <th class="stats-col text-warning" title="Half Day">HD</th>
                                <th class="stats-col text-danger" title="Absent">A</th>
                                <th class="stats-col text-info" title="Leave">L</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($staff_list as $staff):
                            $eid    = $staff['id'];
                            $counts = ['P' => 0, 'HD' => 0, 'A' => 0, 'L' => 0];
                        ?>
                            <tr>
                                <td class="freeze-col" style="white-space:nowrap;">
                                    <strong><?= htmlspecialchars($staff['e_name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($staff['designation']) ?></small>
                                </td>
                                <?php for ($d = 1; $d <= $days_in_month; $d++):
                                    // Priority: selfie_attendance first, then employee_att grid
                                    if (isset($selfie_matrix[$eid][$d])) {
                                        $status = $selfie_matrix[$eid][$d]; // 'P' or 'HD'
                                    } else {
                                        $col_key = 'd_' . $d;
                                        $status  = $grid_map[$eid][$col_key] ?? '-';
                                    }

                                    if ($status !== '-') {
                                        $counts[$status] = ($counts[$status] ?? 0) + 1;
                                    }

                                    if ($status === 'P') $cls = 'st-P';
                                    elseif ($status === 'HD') $cls = 'st-HD';
                                    elseif ($status === 'A') $cls = 'st-A';
                                    elseif ($status === 'L') $cls = 'st-L';
                                    else $cls = 'st-empty';
                                    $label = ($status !== '-') ? $status : '·';
                                ?>
                                    <td class="att-cell <?= $cls ?>"><?= $label ?></td>
                                <?php endfor; ?>
                                <td class="stats-col text-success"><?= $counts['P'] ?></td>
                                <td class="stats-col text-warning"><?= $counts['HD'] ?></td>
                                <td class="stats-col text-danger"><?= $counts['A'] ?></td>
                                <td class="stats-col text-info"><?= $counts['L'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($staff_list)): ?>
                            <tr>
                                <td colspan="<?= $days_in_month + 5 ?>" class="text-center text-muted">No staff found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once('required/footer.php'); ?>
