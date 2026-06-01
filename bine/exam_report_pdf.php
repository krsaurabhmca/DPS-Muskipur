<?php
require('fpdf/fpdf.php');
require_once('required/function.php');
date_default_timezone_set('Asia/Kolkata');

class PDF extends FPDF {
    function Header() {
        global $full_name, $inst_address1, $inst_address2, $inst_managed_by, $aff_no, 
               $inst_contact, $inst_email, $inst_url, $session_list, $db_name;

        $this->Image('images/cbse.png', 15, 10, 25);
        $this->Image('../assets/images/fav.png', 170, 10, 25);

        $this->SetY(10);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 8, strtoupper($full_name), 0, 1, 'C');

        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, "$inst_address1", 0, 1, 'C');
        $this->Cell(0, 5, "$inst_address2", 0, 1, 'C');
        $this->Cell(0, 5, "Managed By: $inst_managed_by", 0, 1, 'C');
        $this->Cell(0, 5, "AFFILIATED TO CBSE, NEW DELHI UPTO 10+2 | AFFILIATION NO. $aff_no", 0, 1, 'C');
        $this->Cell(0, 5, "$inst_contact | $inst_email | $inst_url", 0, 1, 'C');

        $examTitle = strtoupper($_REQUEST['exam_name']) . " EXAMINATION " . strtoupper($session_list[$db_name]);
        $this->SetFillColor(82, 82, 82);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, $examTitle, 0, 1, 'C', true);
        // $this->Ln(6);
        $this->SetTextColor(0);
    }

    // function Footer() {
    //     $this->SetY(-15);
    //     $this->SetFont('Arial', 'I', 8);
    //     $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    // }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

extract($_POST);
$exam_name = $_REQUEST['exam_name'];
$student = get_data('student', $_REQUEST['student_id'])['data'];
$sid = $student['id'];
$gtotal = 0;

$psign = 'images/sign.jpg';

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, strtoupper($student['student_name']), 0, 1, 'C');
$pdf->Ln(2);

$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(245, 245, 245);
$pdf->Cell(190, 25, '', 1, 1, 'L', true);
$pdf->SetY($pdf->GetY() - 20);
$pdf->SetX(12);

$pdf->Cell(40, 6, "Roll No.:", 0, 0);
$pdf->Cell(50, 6, $student['student_roll'], 0, 1);
$pdf->Cell(40, 6, "Class & Section:", 0, 0);
$pdf->Cell(50, 6, $student['student_class'] . " - " . $student['student_section'], 0, 1);
$pdf->Cell(40, 6, "Student ID:", 0, 0);
$pdf->Cell(50, 6, $student['student_admission'], 0, 1);

$pdf->SetXY(110, $pdf->GetY() - 18);
$pdf->Cell(40, 6, "Father's Name:", 0, 0);
$pdf->Cell(50, 6, $student['student_father'], 0, 1);
$pdf->SetX(110);
$pdf->Cell(40, 6, "Mother's Name:", 0, 0);
$pdf->Cell(50, 6, $student['student_mother'], 0, 1);
$pdf->SetX(110);
$pdf->Cell(40, 6, "Date of Birth:", 0, 0);
$pdf->Cell(50, 6, date('d-M-Y', strtotime($student['date_of_birth'])), 0, 1);
$pdf->Ln(6);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(82, 82, 82);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(60, 8, 'Subject', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'NB (5)', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'SEA (5)', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Marks (40)', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Total', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Grade', 1, 1, 'C', true);
$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 10);

$sub_list = subject_list($student['student_class']);
$graph = [['Subject', 'Marks']];

foreach ($sub_list as $subject_id) {
    $subject_name = get_data('subject', $subject_id, 'subject_name')['data'];
    $subject_col = get_data('subject', $subject_id, 'subject_column')['data'];
    $marks = get_marks($student['student_admission'], $exam_name, $subject_col);
    $total = $marks['pt'] + $marks['nb'] + $marks['se'] + $marks['mo'];
    $gtotal += $total;

    $pdf->Cell(60, 8, $subject_name, 1);
    $pdf->Cell(25, 8, $marks['nb'], 1, 0, 'C');
    $pdf->Cell(25, 8, $marks['se'], 1, 0, 'C');
    $pdf->Cell(35, 8, $marks['mo'], 1, 0, 'C');
    $pdf->Cell(25, 8, $total, 1, 0, 'C');
    $pdf->Cell(20, 8, grade($total * 2), 1, 1, 'C');

    $graph[] = [$subject_name, (int)$total];
}
$pdf->Ln(6);

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell(0, 8, 'Additional Subjects', 1, 1, 'C', true);
$pdf->SetFont('Arial', '', 10);

$extra_list = extra_list($student['student_class']);
foreach ($extra_list as $extra_id) {
    $exta_subject_name = get_data('subject', $extra_id, 'subject_name')['data'];
    $subject_col = get_data('subject', $extra_id, 'subject_column')['data'];
    $extra  = get_marks($student['student_admission'], $exam_name, $subject_col);
    $nb = $extra['nb'];
    $se = $extra['se'];
    $mo = $extra['mo'];
    $total = $nb + $se + $mo;

    $pdf->Cell(60, 8, $exta_subject_name, 1);
    $pdf->Cell(25, 8, $nb, 1, 0, 'C');
    $pdf->Cell(25, 8, $se, 1, 0, 'C');
    $pdf->Cell(35, 8, $mo, 1, 0, 'C');
    $pdf->Cell(25, 8, $total, 1, 0, 'C');
    $pdf->Cell(20, 8, grade($total * 2), 1, 1, 'C');
}
$pdf->Ln(6);

$gper = number_format(($gtotal * 2) / count($sub_list), 2);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(82, 82, 82);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(60, 8, "Total Marks: $gtotal", 1, 0, 'C', true);
$pdf->Cell(65, 8, "Percentage: $gper%", 1, 0, 'C', true);
$pdf->Cell(65, 8, "Grade: " . grade($gper), 1, 1, 'C', true);
$pdf->SetTextColor(0);
$pdf->Ln(1);

$yBefore = $pdf->GetY();

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(82, 82, 82);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(50, 8, 'Co-Scholastic Areas', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Grade', 1, 1, 'C', true);
$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 10);

$co_scholastic_static = [
    ['Work Education', 'NA'],
    ['Art Education', 'NA'],
    ['Health & Physical Education', 'NA'],
    ['Discipline', 'NA']
];
foreach ($co_scholastic_static as $row) {
    $pdf->Cell(50, 8, $row[0], 1);
    $pdf->Cell(30, 8, $row[1], 1, 1, 'C');
}

$pdf->Ln(4);

$graph_labels = [];
$graph_data = [];
foreach ($graph as $idx => $row) {
    if ($idx === 0) continue;
    $graph_labels[] = $row[0];
    $graph_data[] = (int)$row[1];
}

$chartConfig = [
    'type' => 'bar',
    'data' => [
        'labels' => $graph_labels,
        'datasets' => [[
            'label' => 'Marks',
            'data' => $graph_data,
            'backgroundColor' => 'rgba(54, 162, 235, 0.6)'
        ]]
    ],
    'options' => [
        'title' => [
            'display' => true,
            'text' => 'Performance Report'
        ],
        'legend' => [
            'display' => false
        ],
        'scales' => [
            'yAxes' => [[
                'ticks' => ['beginAtZero' => true]
            ]]
        ]
    ]
];

$chartUrl = 'https://quickchart.io/chart?c=' . urlencode(json_encode($chartConfig));
$imgData = file_get_contents($chartUrl);
if ($imgData === false) {
    die('Error fetching chart image from QuickChart');
}

$tempImagePath = sys_get_temp_dir() . '/chart.png';
file_put_contents($tempImagePath, $imgData);

$pdf->Image($tempImagePath, 110, $yBefore, 90, 50);

$pdf->Ln(8);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 6, "8 Point Grading Scale : A1(91-100), A2(81-90), B1(71-80), B2(61-70), C1(51-60), C2(41-50), D(33-40), E(0-32)",'LTR','1','C');
$pdf->Cell(0, 6, "MO - Marks Obtained | NB - Note Book | SEA - Subject Enrichment Activity | HY - Half Yearly",'LRB','1','C');

$pdf->Cell(140, 20, 'Remarks : ', 1, 0, 'L');
$pdf->Cell(0, 20, 'Attendance', 1, 1, 'L');


$pdf->SetY(290);
$pdf->Cell(140, 2, 'Class Teacher', 0, 0, 'L');
$pdf->Cell(50, 2, 'Signature of Principal', 0, 1, 'C');
$pdf->Image($psign, 160, 265 , 30, 20);

$pdf->Output('D',$student['student_name'].' '.$_REQUEST['exam_name'].'.pdf');
