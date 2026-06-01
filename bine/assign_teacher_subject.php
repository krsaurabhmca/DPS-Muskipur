<?php
require_once('required/header.php');
require_once('required/menu.php');

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

    .badge-combo {
        font-size: 0.85rem;
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 500;
    }

    .custom-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Assign Class/Subject to Teacher</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Assign Teacher</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Assignment Form -->
            <div class="col-lg-4 col-md-5 col-12">
                <div class="box box-default glass-card">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="font-weight: 600; color: #2c3e50;">New Assignment</h3>
                    </div>

                    <div class="box-body">
                        <form id="add_item_frm" action="assign_teacher_subject" method="post">
                            <!-- Teacher Dropdown -->
                            <div class="form-group">
                                <label style="font-weight: 500; color: #4a5568;">Select Teacher</label>
                                <select class="form-control" name="user_id" required>
                                    <option value="">-- Choose Teacher --</option>
                                    <?php
                                    $teachers = get_all('user', '*', array('user_type' => 'STAFF', 'status' => 'ACTIVE'), 'full_name ASC');
                                    if ($teachers['count'] > 0) {
                                        foreach ($teachers['data'] as $t) {
                                            echo "<option value='" . $t['id'] . "'>" . $t['full_name'] . " (" . $t['user_name'] . ")</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Class Dropdown -->
                            <div class="form-group">
                                <label style="font-weight: 500; color: #4a5568;">Select Class</label>
                                <select class="form-control" name="student_class" required>
                                    <option value="">-- Choose Class --</option>
                                    <?php dropdown($class_list); ?>
                                </select>
                            </div>

                            <!-- Section Dropdown -->
                            <div class="form-group">
                                <label style="font-weight: 500; color: #4a5568;">Select Section</label>
                                <select class="form-control" name="student_section" required>
                                    <option value="">-- Choose Section --</option>
                                    <?php dropdown($section_list); ?>
                                </select>
                            </div>

                            <!-- Subject Dropdown -->
                            <div class="form-group">
                                <label style="font-weight: 500; color: #4a5568;">Select Subject</label>
                                <select class="form-control" name="subject_id" required>
                                    <option value="">-- Choose Subject --</option>
                                    <?php
                                    $subjects = get_all('subject', '*', array('status' => 'ACTIVE'), 'subject_name ASC');
                                    if ($subjects['count'] > 0) {
                                        foreach ($subjects['data'] as $sub) {
                                            echo "<option value='" . $sub['id'] . "'>" . $sub['subject_name'] . " [" . $sub['student_class'] . "]</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mt-4">
                                <button type="button" id="add_item_btn"
                                    class="btn btn-warning btn-block font-weight-bold shadow-sm"
                                    style="border-radius: 6px;">
                                    <i class="fa fa-link mr-1"></i> Assign Subject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Assignments List -->
            <div class="col-lg-8 col-md-7 col-12">
                <div class="box box-default glass-card">
                    <div class="box-header with-border">
                        <h3 class="box-title" style="font-weight: 600; color: #2c3e50;">Active Assignments</h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table custom-table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Teacher Name</th>
                                        <th class="text-center">Class - Section</th>
                                        <th>Subject</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q = "SELECT ta.id, ta.student_class, ta.student_section, u.full_name, s.subject_name 
                                          FROM teacher_assignment ta 
                                          LEFT JOIN user u ON ta.user_id = u.id 
                                          LEFT JOIN subject s ON ta.subject_id = s.id 
                                          WHERE ta.status = 'ACTIVE' 
                                          ORDER BY u.full_name ASC, ta.student_class ASC";
                                    $assignments = direct_sql($q);
                                    if ($assignments['count'] > 0) {
                                        foreach ($assignments['data'] as $row) {
                                            ?>
                                            <tr>
                                                <td style="font-weight: 500; color: #2d3748;"><?php echo $row['full_name']; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-warning badge-combo text-dark">
                                                        <?php echo $row['student_class'] . " - " . $row['student_section']; ?>
                                                    </span>
                                                </td>
                                                <td style="font-weight: 500; color: #4a5568;">
                                                    <?php echo $row['subject_name']; ?></td>
                                                <td class="text-right">
                                                    <button class="btn btn-danger btn-sm delete_btn shadow-sm"
                                                        style="border-radius: 6px;" data-table="teacher_assignment"
                                                        data-id="<?php echo $row['id']; ?>" data-pkey="id">
                                                        <i class="fa fa-trash mr-1"></i> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center text-muted p-4'>No assignments found. Make your first assignment using the form on the left.</td></tr>";
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