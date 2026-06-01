<?php 
require_once('required/header.php'); 
require_once('required/menu.php'); 

$teacher_id = $_SESSION['user_id'];
$q = "SELECT ta.id, ta.student_class, ta.student_section, ta.subject_id, s.subject_name, s.subject_column 
      FROM teacher_assignment ta 
      LEFT JOIN subject s ON ta.subject_id = s.id 
      WHERE ta.user_id = '$teacher_id' AND ta.status = 'ACTIVE'";
$assigned_res = direct_sql($q);

$selected_assignment = $_REQUEST['assignment'] ?? null;
$exam_name = $_REQUEST['exam_name'] ?? null;

$class = $section = $subject_id = $subject_column = $subject_name = null;
if ($selected_assignment) {
    list($class, $section, $subject_id, $subject_column, $subject_name) = explode('|', $selected_assignment);
}
?>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.18);
        transition: all 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12);
    }
    .input-mark {
        width: 100px;
        border-radius: 6px;
        border: 1px solid #cbd5e0;
        padding: 6px 12px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .input-mark:focus {
        border-color: #ecc94b;
        box-shadow: 0 0 0 3px rgba(236, 201, 75, 0.3);
        outline: none;
    }
    .badge-sub {
        font-size: 0.9rem;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 500;
    }
    .custom-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        color: #4a5568;
    }
    .student-row {
        transition: all 0.2s ease;
    }
    .student-row:hover {
        background-color: rgba(247, 250, 252, 0.8) !important;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Teacher Marks Entry Portal</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Marks Entry</li>
        </ol>
    </section>

    <section class="content">
        <!-- Configuration Card -->
        <div class="box box-default glass-card mb-4">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-weight: 600; color: #2c3e50;">Select Class & Exam</h3>
            </div>
            
            <div class="box-body">
                <form method="get" action="">
                    <div class="row">
                        <!-- Assignment Dropdown -->
                        <div class="col-md-5 col-12">
                            <div class="form-group">
                                <label style="font-weight: 500; color: #4a5568;">Your Assigned Classes & Subjects</label>
                                <select class="form-control" name="assignment" required>
                                    <option value="">-- Select Class - Section - Subject --</option>
                                    <?php 
                                    if ($assigned_res['count'] > 0) {
                                        foreach ($assigned_res['data'] as $row) {
                                            $val = $row['student_class'] . "|" . $row['student_section'] . "|" . $row['subject_id'] . "|" . $row['subject_column'] . "|" . $row['subject_name'];
                                            $selected = ($selected_assignment == $val) ? 'selected' : '';
                                            echo "<option value='$val' $selected>Class ".$row['student_class']." - Section ".$row['student_section']." - ".$row['subject_name']."</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Exam Dropdown -->
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label style="font-weight: 500; color: #4a5568;">Select Exam Type</label>
                                <select class="form-control" name="exam_name" required>
                                    <option value="">-- Select Exam --</option>
                                    <?php dropdown($exam_list, $exam_name); ?>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-3 col-12 align-self-end">
                            <div class="form-group">
                                <button type="submit" class="btn btn-warning btn-block font-weight-bold shadow-sm" style="height: 40px; border-radius: 6px;">
                                    <i class="fa fa-search mr-1"></i> Load Student Sheet
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php 
        if ($selected_assignment && $exam_name) {
            $students_res = get_all('student', '*', array(
                'student_class' => $class,
                'student_section' => $section,
                'status' => 'ACTIVE'
            ), 'student_roll ASC');
        ?>
            <!-- Marks Entry Sheet -->
            <div class="box box-default glass-card">
                <div class="box-header with-border d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="box-title" style="font-weight: 600; color: #2c3e50; margin-right: 15px;">Marks Entry Sheet</h3>
                        <span class="badge badge-warning badge-sub text-dark mr-2">
                            <i class="fa fa-book mr-1"></i> <?php echo $subject_name; ?>
                        </span>
                        <span class="badge badge-warning badge-sub text-dark mr-2">
                            <i class="fa fa-users mr-1"></i> Class <?php echo $class . " - " . $section; ?>
                        </span>
                        <span class="badge badge-warning badge-sub text-dark">
                            <i class="fa fa-file-text-o mr-1"></i> <?php echo $exam_name; ?>
                        </span>
                    </div>
                </div>

                <div class="box-body">
                    <form id="add_item_frm" action="marks_entry" method="post">
                        <!-- Core contextual metadata needed by marks_entry task in master_process -->
                        <input type="hidden" name="exam_name" value="<?php echo $exam_name; ?>">
                        <input type="hidden" name="subject" value="<?php echo $subject_column; ?>">

                        <div class="table-responsive">
                            <table class="table custom-table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 80px;">Roll No</th>
                                        <th style="width: 150px;">Adm No.</th>
                                        <th>Student Name</th>
                                        <th class="text-center" style="width: 180px;">Note Book (NB)</th>
                                        <th class="text-center" style="width: 180px;">Subject Enrich (SE)</th>
                                        <th class="text-center" style="width: 180px;">Marks Obtained (MO)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($students_res['count'] > 0) {
                                        foreach ($students_res['data'] as $row) {
                                            $marks = get_marks($row['student_admission'], $exam_name, $subject_column);
                                    ?>
                                        <tr class="student-row">
                                            <td class="text-center align-middle" style="font-weight: 600; color: #4a5568;">
                                                <?php echo $row['student_roll']; ?>
                                            </td>
                                            <td class="align-middle text-muted" style="font-size: 0.9rem;">
                                                <?php echo $row['student_admission']; ?>
                                            </td>
                                            <td class="align-middle" style="font-weight: 500; color: #2d3748;">
                                                <?php echo $row['student_name']; ?>
                                            </td>
                                            
                                            <!-- Student IDs needed for array processing in master_process -->
                                            <input type="hidden" name="student_id[]" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="student_admission[]" value="<?php echo $row['student_admission']; ?>">

                                            <!-- Note Book Inputs -->
                                            <td class="text-center align-middle">
                                                <input type="number" step="0.01" min="0" class="input-mark text-center" 
                                                       name="<?php echo $subject_column; ?>_nb[]" 
                                                       value="<?php echo $marks['nb']; ?>" placeholder="0.00">
                                            </td>

                                            <!-- Subject Enrichment Inputs -->
                                            <td class="text-center align-middle">
                                                <input type="number" step="0.01" min="0" class="input-mark text-center" 
                                                       name="<?php echo $subject_column; ?>_se[]" 
                                                       value="<?php echo $marks['se']; ?>" placeholder="0.00">
                                            </td>

                                            <!-- Marks Obtained Inputs -->
                                            <td class="text-center align-middle">
                                                <input type="number" step="0.01" min="0" class="input-mark text-center" 
                                                       name="<?php echo $subject_column; ?>_mo[]" 
                                                       value="<?php echo $marks['mo']; ?>" placeholder="0.00" required>
                                            </td>
                                        </tr>
                                    <?php 
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center text-muted p-4'>No active students found in this class & section.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($students_res['count'] > 0) { ?>
                            <div class="box-footer text-right bg-transparent p-0 mt-4">
                                <button type="button" id="add_item_btn" class="btn btn-warning font-weight-bold shadow-sm px-5 py-2" style="border-radius: 6px;">
                                    <i class="fa fa-save mr-1"></i> Save Marks
                                </button>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        <?php 
        } else if ($assigned_res['count'] == 0) {
        ?>
            <!-- Empty State Card -->
            <div class="box box-default glass-card p-5 text-center">
                <div class="py-5">
                    <i class="fa fa-info-circle fa-4x text-warning mb-3"></i>
                    <h2 class="mb-2" style="font-weight: 600; color: #2c3e50;">No Classes Assigned</h2>
                    <p class="text-muted" style="font-size: 1.1rem; max-width: 500px; margin: 0 auto;">
                        You have not been assigned any class, section, or subject combinations yet. Please contact your school administrator to configure your teaching assignment.
                    </p>
                </div>
            </div>
        <?php 
        } else {
        ?>
            <!-- Selection Prompt Card -->
            <div class="box box-default glass-card p-5 text-center">
                <div class="py-4">
                    <i class="fa fa-hand-o-up fa-3x text-warning mb-3"></i>
                    <h3 style="font-weight: 600; color: #4a5568;">Select Class and Exam</h3>
                    <p class="text-muted">Choose your assigned subject combination and exam from the dropdowns above to begin marks entry.</p>
                </div>
            </div>
        <?php 
        } 
        ?>
    </section>
</div>

<?php require_once('required/footer2.php'); ?>
