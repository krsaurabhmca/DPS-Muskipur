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

// Fetch selfie attendance timesheet for the month
$att_sql = "
    SELECT 
        emp_id,
        DAY(att_date) as day_num,
        created_at,
        checkout_time
    FROM selfie_attendance
    WHERE att_date LIKE '$month_year_str-%'
";
$att_res = direct_sql($att_sql);

// Build matrix: [emp_id][day] = ['in' => time, 'out' => time, 'dur_min' => int]
$att_matrix = [];
foreach (($att_res['data'] ?? []) as $row) {
    $in_ts  = $row['created_at']    ? strtotime($row['created_at'])    : null;
    $out_ts = $row['checkout_time'] ? strtotime($row['checkout_time']) : null;
    $dur    = ($in_ts && $out_ts)   ? round(($out_ts - $in_ts) / 60)  : 0;

    $att_matrix[$row['emp_id']][$row['day_num']] = [
        'in'      => $in_ts  ? date('H:i', $in_ts)  : '—',
        'out'     => $out_ts ? date('H:i', $out_ts) : '—',
        'dur_min' => $dur
    ];
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-clock-o text-info"></i> Monthly Timesheet Report
            <small>GPS Selfie Daily Check-In / Check-Out</small>
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Staff Attendance</li>
            <li class="breadcrumb-item active">Timesheet</li>
        </ol>
    </section>

    <style>
        .ts-cell { text-align: center; font-size: 10px; padding: 2px 3px !important; border-left: 1px dashed #eee; line-height: 1.3; }
        .ts-cell .in  { color: #00a65a; display: block; }
        .ts-cell .out { color: #dd4b39; display: block; }
        .ts-cell .dur { color: #333; font-weight: bold; background: #f4f4f4; border-radius: 2px;
                        padding: 1px 3px; display: inline-block; margin-top: 2px; font-size: 9px; }
        .ts-empty { color: #ccc; text-align: center; }
        .freeze-col { position: sticky; left: 0; background: #fff; z-index: 10; border-right: 2px solid #ddd; }
        .table-responsive { overflow-x: auto; }
        .stats-col { background: #f9f9f9; font-weight: bold; text-align: center; vertical-align: middle !important; font-size: 13px; }

        @media print {
            @page { size: landscape; margin: 5mm; }
            body, html { margin: 0 !important; padding: 0 !important; }
            .content-wrapper, .main-footer, .wrapper { margin-left: 0 !important; min-height: 0 !important; padding: 0 !important; }
            .main-header, .main-sidebar, form, .btn, .content-header, .breadcrumb { display: none !important; }
            .box { border: none !important; box-shadow: none !important; margin: 0 !important; }
            .table-responsive { overflow: visible !important; width: 100% !important; }
            .table { font-size: 7px !important; border-collapse: collapse !important; }
            .table th, .table td { padding: 1px !important; border: 1px solid #ddd !important; white-space: nowrap !important; }
            .freeze-col { position: static !important; }
            .ts-cell { font-size: 7px !important; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            body { zoom: 55%; }
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
                        <a href="staff_attendance_report_matrix.php?month=<?= $selected_month ?>&year=<?= $selected_year ?>"
                            class="btn btn-success">
                            <i class="fa fa-th"></i> Matrix
                        </a>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?> — GPS Timesheet
                </h3>
                <div class="box-tools pull-right">
                    <small class="text-muted">
                        <span style="color:#00a65a;">▲ In</span> &nbsp;
                        <span style="color:#dd4b39;">▼ Out</span> &nbsp;
                        <span style="background:#f4f4f4;padding:1px 4px;border-radius:3px;">Duration</span>
                    </small>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr class="bg-navy text-white">
                                <th class="freeze-col" style="min-width:150px;vertical-align:middle;">Staff Name</th>
                                <?php for ($d = 1; $d <= $days_in_month; $d++):
                                    $dow    = date('D', mktime(0, 0, 0, $selected_month, $d, $selected_year));
                                    $is_sun = ($dow === 'Sun');
                                ?>
                                    <th style="text-align:center;padding:3px;min-width:46px;font-size:10px;<?= $is_sun ? 'background:#e8e8e8;color:#999;' : '' ?>">
                                        <?= $d ?><br><small><?= $dow ?></small>
                                    </th>
                                <?php endfor; ?>
                                <th class="stats-col text-blue" style="min-width:65px;">Total Hrs</th>
                                <th class="stats-col" style="min-width:40px;">Days</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($staff_list as $staff):
                            $eid           = $staff['id'];
                            $total_minutes = 0;
                            $days_present  = 0;
                        ?>
                            <tr>
                                <td class="freeze-col" style="white-space:nowrap;vertical-align:middle;">
                                    <strong><?= htmlspecialchars($staff['e_name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($staff['designation']) ?></small>
                                </td>
                                <?php for ($d = 1; $d <= $days_in_month; $d++):
                                    $data = $att_matrix[$eid][$d] ?? null;
                                    if ($data) {
                                        $total_minutes += $data['dur_min'];
                                        if ($data['in'] !== '—') $days_present++;

                                        $dur_h   = floor($data['dur_min'] / 60);
                                        $dur_m   = $data['dur_min'] % 60;
                                        $dur_str = $data['dur_min'] > 0 ? "{$dur_h}h{$dur_m}m" : '·';

                                        echo "<td class='ts-cell'>";
                                        echo "<span class='in'>{$data['in']}</span>";
                                        echo "<span class='out'>{$data['out']}</span>";
                                        echo "<span class='dur'>{$dur_str}</span>";
                                        echo "</td>";
                                    } else {
                                        echo "<td class='ts-cell ts-empty'>·</td>";
                                    }
                                endfor;

                                $total_h = floor($total_minutes / 60);
                                $total_m = $total_minutes % 60;
                                ?>
                                <td class="stats-col text-blue"><?= $total_h ?>h <?= $total_m ?>m</td>
                                <td class="stats-col"><?= $days_present ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($staff_list)): ?>
                            <tr>
                                <td colspan="<?= $days_in_month + 3 ?>" class="text-center text-muted">No staff found.</td>
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
