<?php require_once('required/header.php'); ?>
<?php require_once('required/menu.php'); ?>
<style>
    .displayBadge {
        display: none;
        text-align: center;
        color: #fff;
        font-weight: 600;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Change Password </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#user">user</a></li>
            <li class="breadcrumb-item active">Chanage Password </li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Always Use Strong Password </h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <div class='row justify-content-center'>
                    <div class="col-md-4 col-md-offset-4">
                        <form action='change_password' id='update_frm' method='post' role="form">
                            <div class="form-group">
                                <label>Current Password</label>
                                <input class="form-control" type='password' id='current_password' required>

                            </div>

                            <div class="form-group">
                                <label>New Password</label>
                                <input class="form-control" type='password' id='new_password' required minlength='5'>
                                <span id="StrengthDisp" class="badge displayBadge badge-light text-light float-right mt-2 p-1">Weak</span>
                            </div>

                            <div class="form-group">
                                <label>Confirm Password <span id='matched' class='badge badge-light'> </span> </label>
                                <input class="form-control" id='repeat_password' type='password' required minlength='5'>

                            </div>
                        </form>
                        <input type="button" class="btn btn-dark btn-block" id='change_password' value='Change Password'>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
<?php require_once('required/footer2.php'); ?>


<script>
    $(document).on('keyup', "#repeat_password", function() {
        var a = $("#new_password").val();
        var b = $("#repeat_password").val();
        if (a == b) {
            $("#matched").html("<b class ='text-success mt-1'> Matched </b>");
            $("#change_password").attr("disabled", false);
        } else {
            $("#matched").html("<b class ='text-danger mt-1'> Not Matched </b>");
            $("#change_password").attr("disabled", true);
        }
    });
    // timeout before a callback is called

    let timeout;

    // traversing the DOM and getting the input and span using their IDs

    let password = document.getElementById('new_password')
    let strengthBadge = document.getElementById('StrengthDisp')

    // The strong and weak password Regex pattern checker

    let strongPassword = new RegExp('(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})')
    let mediumPassword = new RegExp('((?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{6,}))|((?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9])(?=.{8,}))')

    function StrengthChecker(PasswordParameter) {
        // We then change the badge's color and text based on the password strength

        if (strongPassword.test(PasswordParameter)) {
            strengthBadge.style.backgroundColor = "green"
            strengthBadge.textContent = 'Strong'
        } else if (mediumPassword.test(PasswordParameter)) {
            strengthBadge.style.backgroundColor = 'skyblue'
            strengthBadge.textContent = 'Medium'
        } else {
            strengthBadge.style.backgroundColor = 'orangered'
            strengthBadge.textContent = 'Weak'
        }
    }

    // Adding an input event listener when a user types to the  password input 

    password.addEventListener("input", () => {

        //The badge is hidden by default, so we show it

        strengthBadge.style.display = 'block'
        clearTimeout(timeout);

        //We then call the StrengChecker function as a callback then pass the typed password to it

        timeout = setTimeout(() => StrengthChecker(password.value), 500);

        //Incase a user clears the text, the badge is hidden again

        if (password.value.length !== 0) {
            strengthBadge.style.display != 'block'
        } else {
            strengthBadge.style.display = 'none'
        }
    });
</script>