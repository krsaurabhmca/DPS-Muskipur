<?php
require_once('required/function.php');
if (isset($_GET['link'])) {
    $link = decode($_GET['link']);
    // print_r($link);
    $stu_id = $link['student_id'];
    $inv_id = $link['inv_id'];
    $res = get_all('distribute_item', '*', array('student_id' => $stu_id, 'inv_id' => $inv_id));
    if ($res['count'] > 0) {
        foreach ($res['data'] as $detail) {
            $inv_no = $detail['inv_no'];
            $created_at = $detail['created_at'];
            $name = get_data('student', $detail['student_id'], 'student_name')['data'];
            $class = get_data('student', $detail['student_id'], 'student_class')['data'];
            $section = get_data('student', $detail['student_id'], 'student_section')['data'];
            $student_type = get_data('student', $detail['student_id'], 'student_type')['data'];
            $roll = get_data('student', $detail['student_id'], 'student_roll')['data'];
        }
    }
?>


    <title>Invoice No. <?php echo $inv_no; ?> </title>
    <script type="text/javascript" src="js/towords.js"></script>
    <style>
        body {
            font-family: calibri, arial, time new roman;
            font-size: 10px;
            padding: 0px;
            margin: 0px;
        }

        .cancel,
        .CANCEL {
            text-decoration: line-through;
            color: red;
        }

        .success,
        .SUCCESS {
            text-decoration: line-through;
            color: green;
        }

        td {
            font-weight: 300;
            font-size: 14px;
            padding: 5px 8px;
        }

        td .head {
            font-size: 11px;
        }

        .btn {
            border: solid 1px #ddd;
            padding: 4px;
            margin: 10px;
            background: #f5f5f5;
            text-decoration: none;
            color: #222;
            text-transform: uppercase;
            font-weight: 800;
        }

        .no-print {
            padding: 15px 50px;
            width: 450px;
        }

        .name {
            display: inline;
            font-size: 16px;
            line-height: 24px;
            font-weight: 800;
        }

        @media print {
            .btn {
                display: none;
            }
        }

        @media print {
            @page {
                size: landscape
            }
        }
    </style>
    <?php if ($_SESSION['user_id'] <> '') { ?>
        <div class='no-print'>
            <a href='manage_distribute_item' class='btn' accesskey='n'> New Distribute Item (Use Alt+N) </a>
            <a href='' class='btn' onClick='window.print()'> PRINT (Use Ctrl +P) </a>
        </div>
        <table border='1' rules='all' cellpadding='5px' width='450px' align='right'>
        <?php } else { ?>
            <br> <br>
            <table border='1' rules='all' cellpadding='5px' width='70%' align='center'>
            <?php } ?>

            <thead>
                <tr>
                    <td colspan='5' align='center' class='head'>
                        <img align='left' src='images/logo.png' height='80px'>
                        <span style='font-size:18px;font-weight:600;'> <?php echo $full_name; ?> </span><br>
                        (Affiliated to CBSE, New Delhi upto 10+2) <br>
                        <b>Affiliation No. : <?php echo $aff_no; ?> School No. : <?php echo $school_code; ?></b><br>
                        <?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
                        Contact No.: <?php echo $inst_contact; ?> <br> Email : <?php echo $inst_email; ?>, Website : <?php echo $inst_url; ?>
                    </td>
                </tr>
                <tr bgcolor='#f5f5f5'>
                    <td colspan='3'> Invoice No. : <b><?php echo $inv_no; ?></b></td>
                    <td align='right' colspan='2'><?php echo date('d-M-Y h:i A', strtotime($created_at)); ?></td>
                </tr>
                <!-- <tr>
                            <td colspan='5' style='text-align:center'>
                                <b> Parents Copy : Fee Details of <?php //echo add_space(str_replace(',', ', ', $receipt['paid_month'])); 
                                                                    ?></b>
                            </td>
                        </tr> -->
            </thead>
            <tbody>
                <tr>
                    <td colspan='2'>Student Name </td>
                    <td colspan='3'><?php echo strtoupper($name); ?></td>
                </tr>
                <tr>
                    <td> Class </td>
                    <td colspan='3'><?php echo $class; ?>-<?php echo $section; ?> (<?php echo $student_type; ?>)</td>
                    <td>Roll No. : <?php echo $roll; ?></td>
                </tr>
                <tr>
                    <td colspan='5' height='140px' valign='top'>
                        <table width='100%' border='0' rules='none'>
                            <tr border='1'>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Total Price</th>
                            </tr>
                            <tr>
                                <?php
                                $q = 0;
                                $t = 0;
                                if ($res['count'] > 0) {
                                    foreach ($res['data'] as $detail) {
                                        $q += $detail['qty'];
                                        $t += $detail['amount'];

                                ?>
                                        <td align="center"><?php echo get_data('inventory_item', $detail['item_id'], 'name')['data']; ?></td>
                                        <td align="center"><?php echo $detail['qty']; ?></td>
                                        <td align="center"><?php echo $detail['rate']; ?></td>
                                        <td align="center"><?php echo $detail['amount']; ?></td>
                            </tr>
                    <?php }
                                } ?>
                    <tr>
                        <td colspan="4">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align='right'> Total
                        </td>
                        <td align='center'><?php echo floatval($t); ?></td>
                    </tr>

                    <tr>
                        <td colspan='5' style='text-transform:capitalize'>
                            <hr>
                            <script>
                                var words = toWords(<?php echo floatval($t); ?>);
                                document.write("<div class='t'><b>In Words : </b>" + words + " rupees only </div>");
                            </script>
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center">
                            <?php if ($detail['status'] == "CANCELLED") { ?>
                                <p class="cancel"><?php echo $detail['status'] ?></p>
                            <?php } ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan='2'> Issued by : <?php echo get_data('user', get_data('receipt', $rid, 'created_by')['data'], 'user_name')['data']; ?>
                        </td>
                        <td colspan='3' align='right'>
                            Authorised Signatory
                        </td>
                    </tr>
                        </table>
                        <br>
                    <?php
                }
                    ?>