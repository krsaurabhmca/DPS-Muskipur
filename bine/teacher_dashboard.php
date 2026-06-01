<?php 
require_once('required/header.php'); 
require_once('required/menu.php'); 

$teacher_id = $_SESSION['user_id'];

// Get current date time greeting
$hour = date('H');
if ($hour < 12) {
    $greeting = "Good morning";
} else if ($hour < 17) {
    $greeting = "Good afternoon";
} else {
    $greeting = "Good evening";
}

// Query assignments
$q = "SELECT ta.id, ta.student_class, ta.student_section, ta.subject_id, s.subject_name, s.subject_column 
      FROM teacher_assignment ta 
      LEFT JOIN subject s ON ta.subject_id = s.id 
      WHERE ta.user_id = '$teacher_id' AND ta.status = 'ACTIVE'
      ORDER BY ta.student_class ASC, ta.student_section ASC";
$assigned_res = direct_sql($q);

// Calculate stats
$total_classes = 0;
$total_students = 0;
$distinct_combos = array();
$distinct_subjects = array();

if ($assigned_res['count'] > 0) {
    foreach ($assigned_res['data'] as $row) {
        $combo = $row['student_class'] . "|" . $row['student_section'];
        if (!in_array($combo, $distinct_combos)) {
            $distinct_combos[] = $combo;
            $total_students += studentcount($row['student_class'], $row['student_section']);
        }
        if (!in_array($row['subject_name'], $distinct_subjects)) {
            $distinct_subjects[] = $row['subject_name'];
        }
    }
    $total_classes = count($distinct_combos);
}
?>

<style>
    .welcome-banner {
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        color: #fff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(253, 160, 133, 0.2);
        margin-bottom: 25px;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.18);
        transition: all 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px 0 rgba(31, 38, 135, 0.1);
    }
    .stat-icon {
        font-size: 2.5rem;
        color: #fda085;
        opacity: 0.8;
    }
    .action-btn {
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        border: none;
        color: white;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    .action-btn:hover {
        opacity: 0.9;
        transform: scale(1.02);
        color: white;
        box-shadow: 0 4px 15px rgba(253, 160, 133, 0.3);
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Teacher Dashboard</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><i class="fa fa-dashboard"></i> Dashboard</li>
        </ol>
    </section>

    <section class="content">
        <!-- Welcome Banner -->
        <div class="welcome-banner">
            <h2 style="font-weight: 700; margin: 0;"><?php echo $greeting; ?>, <?php echo strtoupper($user_name); ?>!</h2>
            <p style="margin-top: 10px; font-size: 1.1rem; opacity: 0.9;">Welcome to your personalized portal. You can view your active assignments and enter student marks directly from this panel.</p>
        </div>

        <!-- Metrics Row -->
        <div class="row">
            <!-- Assigned Classes Card -->
            <div class="col-md-4 col-sm-6 col-12 mb-4">
                <div class="box box-body glass-card d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h3 class="no-margin" style="font-weight: 700; color: #2d3748;"><?php echo $total_classes; ?></h3>
                        <p class="text-muted no-margin" style="font-weight: 500; font-size: 0.95rem;">Assigned Classes & Sections</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- Total Students Card -->
            <div class="col-md-4 col-sm-6 col-12 mb-4">
                <div class="box box-body glass-card d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h3 class="no-margin" style="font-weight: 700; color: #2d3748;"><?php echo $total_students; ?></h3>
                        <p class="text-muted no-margin" style="font-weight: 500; font-size: 0.95rem;">Total Active Students</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                </div>
            </div>

            <!-- Total Subjects Card -->
            <div class="col-md-4 col-sm-6 col-12 mb-4">
                <div class="box box-body glass-card d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h3 class="no-margin" style="font-weight: 700; color: #2d3748;"><?php echo count($distinct_subjects); ?></h3>
                        <p class="text-muted no-margin" style="font-weight: 500; font-size: 0.95rem;">Teaching Subjects</p>
                    </div>
                    <div class="stat-icon">
                        <i class="fa fa-book"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignments Grid -->
        <div class="box box-default glass-card mt-3">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-weight: 600; color: #2c3e50;"><i class="fa fa-list mr-1" style="color: #fda085;"></i> Your Active Subject Combinations</h3>
            </div>
            
            <div class="box-body">
                <div class="row">
                    <?php 
                    if ($assigned_res['count'] > 0) {
                        foreach ($assigned_res['data'] as $row) {
                            $scount = studentcount($row['student_class'], $row['student_section']);
                            // Build parameters to directly initialize marks sheet
                            $val = $row['student_class'] . "|" . $row['student_section'] . "|" . $row['subject_id'] . "|" . $row['subject_column'] . "|" . $row['subject_name'];
                    ?>
                        <div class="col-lg-4 col-md-6 col-12 mb-4">
                            <div class="box box-body glass-card border p-4 d-flex flex-column justify-content-between" style="height: 100%; border-top: 4px solid #fda085 !important;">
                                <div>
                                    <span class="badge badge-warning text-dark font-weight-bold mb-2" style="font-size: 0.8rem; padding: 4px 8px;">
                                        Class <?php echo $row['student_class'] . " - " . $row['student_section']; ?>
                                    </span>
                                    <h4 style="font-weight: 700; color: #2d3748; margin-top: 5px;"><?php echo $row['subject_name']; ?></h4>
                                    <p class="text-muted" style="font-size: 0.9rem;"><i class="fa fa-users mr-1"></i> <?php echo $scount; ?> Students enrolled</p>
                                </div>
                                <div class="mt-4">
                                    <a href="teacher_marks_entry.php?assignment=<?php echo urlencode($val); ?>" class="btn btn-block action-btn py-2">
                                        <i class="fa fa-edit mr-1"></i> Enter Marks
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php 
                        }
                    } else {
                    ?>
                        <div class="col-12 text-center py-5">
                            <i class="fa fa-info-circle fa-3x text-warning mb-3"></i>
                            <h4 style="font-weight: 600; color: #4a5568;">No Subject Combinations Assigned</h4>
                            <p class="text-muted" style="max-width: 450px; margin: 0 auto;">You have not been assigned any teaching subjects yet. Please contact the administrator to assign classes, sections, and subjects.</p>
                        </div>
                    <?php 
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once('required/footer2.php'); ?>
