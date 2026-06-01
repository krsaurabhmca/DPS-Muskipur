/* Apprise Js By OfferPlant*/

//=======AUTO LOGOUT AFTER 2 Min of Inactivity ====//
var base_url = 'https://dpsmushkipur.com/bine/';
var mobiles = [];
var timeSinceLastMove = 0;
$(document).on('mousemove , keyup', function () {
	timeSinceLastMove = 0;
});

function checkTime() {
	timeSinceLastMove++;
	console.log(timeSinceLastMove);
	if (timeSinceLastMove > 5 * 60) {
		autologout();
	}
	else {
		setTimeout(checkTime, 10000);
	}
}


//=============CANCEL INVOICE=================//
$(".cancel_invoice_btn").on('click', function () {
	const student_id = $(this).attr("data-id");
	const inv_id = $(this).attr("data-inv");
    var remarks = confirm("Do you really want to Cancel this invoice?");
  //alert(remarks); 
  if(remarks ==true)
  {
	$.ajax({
		url: "required/master_process?task=cancel_invoice_receipt",
		type: "POST",
		data: {
			'student_id': student_id,
			'inv_id': inv_id
		},
		success: function (data) {
			console.log(data);
			var obj = JSON.parse(data);
			if (obj.url != null) {
				bootbox.alert(obj.msg, function () {
					window.location.replace(obj.url);
				});
			}
			else {
				$.notify(obj.msg, obj.status);
			}
		}
	});
  }
});

$(".payment_mode").on('click', function(){
  var id = $(this).data('id');
  var pay_mode = $(this).data('mode');
  var remarks = confirm("Do you really want to change payment mode?");
  //alert(remarks); 
  if(remarks ==true)
  {
     $.ajax({
       method:'post',
       url:'required/master_process.php?task=payment_mode',
       data:{
       receipt_id: id,
       payment_mode:pay_mode
         },
      beforeSend:function(){
       //console.log(id+remarks);
      },
      success:function(res){
              var obj = JSON.parse(res);
        $.notify(obj.msg,obj.status);
       } 
       
      });
  }
 });

//======================UPLOAD PROFILE=========//
$('#e_pic').change(function () {
	$("#uploadProfile").submit();
});

$("#uploadProfile").on('submit', (function (e) {
	e.preventDefault();
	$.ajax({
		url: "required/master_process?task=uploadProfile",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		success: function (data) {
			var obj = JSON.parse(data);
			$("#profile").val(obj.id);
			$("#displayProfile").html("<img src='required/upload/" + obj.id + "' width='100px' height='100px' class='img-thumbnail'>");
			$.notify(obj.msg, obj.status);
		},
		error: function () { }
	});
}));

//======================UPLOAD AADHAR=========//
$('#e_aadhar_profile').change(function () {
	$("#uploadAadhar").submit();
});

$("#uploadAadhar").on('submit', (function (e) {
	e.preventDefault();
	$.ajax({
		url: "required/master_process?task=uploadAadhar",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		success: function (data) {
			var obj = JSON.parse(data);
			$("#aadhar").val(obj.id);
			$("#displayAadhar").html("<img src='required/upload/" + obj.id + "' width='100px' height='100px' class='img-thumbnail'>");
			$.notify(obj.msg, obj.status);
		},
		error: function () { }
	});
}));

//======================UPLOAD DL PROOF=========//
$('#e_dl_proof').change(function () {
	$("#uploadDL").submit();
});

$("#uploadDL").on('submit', (function (e) {
	e.preventDefault();
	$.ajax({
		url: "required/master_process?task=uploadDL",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		success: function (data) {
			var obj = JSON.parse(data);
			$("#dl").val(obj.id);
			$("#displayDL").html("<img src='required/upload/" + obj.id + "' width='100px' height='100px' class='img-thumbnail'>");
			$.notify(obj.msg, obj.status);
		},
		error: function () { }
	});
}));

//========================UPLOAD LEAVE APPLICATION=======================
$('#leave_app').change(function () {
	$("#uploadLeaveApp").submit();
});

$("#uploadLeaveApp").on('submit', (function (e) {
	e.preventDefault();
	$.ajax({
		url: "required/master_process?task=uploadLeaveApp",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		success: function (data) {
			var obj = JSON.parse(data);
			$("#leave").val(obj.id);
			$("#displayLeaveApp").html("<img src='required/upload/" + obj.id + "' width='100px' height='100px' class='img-thumbnail'>");
			$.notify(obj.msg, obj.status);
		},
		error: function () { }
	});
}));


//======================UPLOAD HOMEWORK===================
$('#homework').change(function () {
	$("#uploadHomework").submit();
});

$("#uploadHomework").on('submit', (function (e) {
	e.preventDefault();
	$.ajax({
		url: "required/master_process?task=uploadHomework",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		success: function (data) {
			var obj = JSON.parse(data);
			$("#targetimg").val(obj.id);
			$("#displayHW").html("<img src='required/upload/" + obj.id + "' width='100px' height='100px' class='img-thumbnail'>");
			$.notify(obj.msg, obj.status);
		},
		error: function () { }
	});
}));

//=====Query BUTTON =========//

function fetch_data(task, param = null) {
	var api_url = "required/master_process.php?task=" + task;
	var ajax = $.ajax({
		url: api_url,
		method: 'POST',
		dataType: "json",
		data: param,
		async: false,
		cache: false,
	});
	var data = $.parseJSON(ajax.responseText);
	return data; // .staus .url .id	
}
function addspace(input) {
	var newtstr = input.replace("_", " &nbsp; ");
	var words = newtstr.split(' ');
	var CapitalizedWords = [];
	words.forEach(element => {
		CapitalizedWords.push(element[0].toUpperCase() + element.slice(1, element.length));
	});
	return CapitalizedWords.join('');
}

function objtable(mydata) {
	var table = $('<table class="table" width="100%" >');
	var tblHeader = "<tr>";
	for (var k in mydata[0]) tblHeader += "<th>" + addspace(k) + "</th>";
	tblHeader += "</tr>";
	$(tblHeader).appendTo(table);
	$.each(mydata, function (index, value) {
		var TableRow = "<tr>";
		$.each(value, function (key, val) {
			TableRow += "<td>" + val + "</td>";
		});
		TableRow += "</tr>";
		$(table).append(TableRow);
	});
	return ($(table));
}

//=====INSERT BUTTON =========//
$(document).on('click', "#insert_btn", function (event) {
	$("#insert_frm").validate();
	if ($("#insert_frm").valid()) {
		var task = $("#insert_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#insert_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': base_url + 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				console.log(data);
				//alert(data);
				var obj = JSON.parse(data);
				$('#insert_frm')[0].reset();
				if ($('#uploadForm').length != 0) {
					$('#uploadForm')[0].reset();
				}
				//$.notify(obj.msg, obj.status);

				$("#insert_btn").html("Save Details");
				$("#insert_btn").removeAttr("disabled");
				if (obj.url != null) {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
			}

		});
	}
});

//============= INVENTORY CLOSE INVOICE ========//
$("#close_invoice").click(function () {
	$("#invoice_frm").validate();

	if ($("#invoice_frm").valid()) {
		var task = $("#invoice_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#invoice_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				//alert(data);
				console.log(data);
				var obj = JSON.parse(data);
				//$('#update_frm')[0].reset();

				$("#close_invoice").html("Save Details");
				$("#close_invoice").removeAttr("disabled");
				if (obj.url != null) {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
			}

		});
	}
});

//=====UPDATE BUTTON =========//
$(document).on("click","#update_btn",function () {
	$("#update_frm").validate();

	if ($("#update_frm").valid()) {
		var task = $("#update_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#update_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': base_url + 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				//alert(data);
				console.log(data);
				var obj = JSON.parse(data);
				//$('#update_frm')[0].reset();

				$("#update_btn").html("Save Details");
				$("#update_btn").removeAttr("disabled");
				if (obj.url != null && obj.status =='success') {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
			}

		});
	}
});
$("#cat_btn").click(function () {
	$("#cat_frm").validate();

	if ($("#cat_frm").valid()) {
		var task = $("#cat_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#cat_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				//alert(data);
				console.log(data);
				var obj = JSON.parse(data);
				//$('#update_frm')[0].reset();

				$("#cat_btn").html("Save Details");
				$("#cat_btn").removeAttr("disabled");
				if (obj.url != null) {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
			}

		});
	}
});
$("#assign_btn").click(function () {
	$("#assign_frm").validate();

	if ($("#assign_frm").valid()) {
		var task = $("#assign_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#assign_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				//alert(data);
				console.log(data);
				var obj = JSON.parse(data);
				//$('#update_frm')[0].reset();

				$("#assign_btn").html("Save Details");
				$("#assign_btn").removeAttr("disabled");
				if (obj.url != null) {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
			}

		});
	}
});
$("#change_btn").click(function () {
	$("#change_frm").validate();

	if ($("#change_frm").valid()) {
		var task = $("#change_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#change_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				//alert(data);
				console.log(data);
				var obj = JSON.parse(data);
				//$('#update_frm')[0].reset();

				$("#change_btn").html("Save Details");
				$("#change_btn").removeAttr("disabled");
				if (obj.url != null) {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
			}

		});
	}
});
$("#vehicle_type_btn").click(function () {
	$("#vehicle_type_frm").validate();

	if ($("#vehicle_type_frm").valid()) {
		var task = $("#vehicle_type_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#vehicle_type_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				//alert(data);
				console.log(data);
				var obj = JSON.parse(data);
				//$('#update_frm')[0].reset();

				$("#vehicle_type_btn").html("Save Details");
				$("#vehicle_type_btn").removeAttr("disabled");
				if (obj.url != null) {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
			}

		});
	}
});
$("#leave_btn").click(function () {
	$("#leave_frm").validate();

	if ($("#leave_frm").valid()) {
		var task = $("#leave_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#leave_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				//alert(data);
				console.log(data);
				var obj = JSON.parse(data);
				//$('#update_frm')[0].reset();

				$("#leave_btn").html("Save Details");
				$("#leave_btn").removeAttr("disabled");
				if (obj.url != null) {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
			}
		});
	}
});
$("#add_item_btn").click(function () {
	$("#add_item_frm").validate();

	if ($("#add_item_frm").valid()) {
		var task = $("#add_item_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#add_item_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				//alert(data);
				console.log(data);
				var obj = JSON.parse(data);
				//$('#update_frm')[0].reset();

				$("#add_item_btn").html("Save Details");
				$("#add_item_btn").removeAttr("disabled");
				if (obj.url != null) {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
			}
		});
	}
});
//=====DELETE BUTTON =========//
$(document).on('click', '.delete_btn', function () {
	var del_row = $($(this).closest("tr"));
	var id = $(this).attr("data-id");
	var table = $(this).attr("data-table");
	var pkey = $(this).attr("data-pkey");
	bootbox.confirm({
		message: "Do you really want to delete this?",
		buttons:
		{
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function (result) {
			if (result == true) {
				$.ajax({
					'type': 'POST',
					'url': 'required/master_process?task=master_delete',
					'data': { 'id': id, 'table': table, 'pkey': pkey },
					success: function (data) {
						console.log(data);
						var obj = JSON.parse(data);
						$.notify(obj.msg, obj.status);
						del_row.hide(500);
					}
				});
			}
		}
	});
});



//=====SET Fee BUTTON =========//
$(".set_fee_btn").on('click', function () {

	var del_row = $($(this).closest("tr"));
	var fee_value = $(this).closest("tr").find(".fee_value").val();
	var sid = $(this).closest("tr").find(".fee_value").attr("data-id");
	if (fee_value == "" || fee_value == null) {
		$.notify("Sorry Fee Amount Can't be Empty", "info");
	}
	else {
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=set_fee',
			'data': { 'student_id': sid, 'course_fee': fee_value },
			success: function (data) {
				//alert(data);
				var obj = JSON.parse(data);
				$.notify(obj.msg, obj.status);
				del_row.css("background", "lightgreen");
			}
		});
	}

});

//=====STATUS BUTTON =========//
$(".status_btn").on('click', function () {
	var data_status = $(this).attr('data-status');
	var all_student = [];
	$('input[class="chk"]:checked').each(function () {
		all_student.push($(this).attr('value'));
	});
	var ct = all_student.length;
	if (ct >= 1) {
		bootbox.confirm({
			message: "Do you really want to " + data_status + " selected (" + ct + ") student ?",
			buttons:
			{
				confirm: {
					label: 'Yes',
					className: 'btn-success btn-sm'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger btn-sm'
				}
			},
			callback: function (result) {
				if (result == true) {
					$.ajax({
						'type': 'POST',
						'url': 'required/master_process?task=update_status',
						'data': { 'data_status': data_status, 'sid': all_student },
						success: function (data) {
							//alert(data);
							//var obj = JSON.parse(data);
							$.notify(ct + " Student(s) " + data_status + " Succesfully", "success");
							location.reload();
						}
					});
				}
			}
		});
	} else {
		$.notify("Sorry ! No Student Selected ", "info");
	}
});


//=====ACTIVE / BLOCK BUTTON =========//
$(".active_block").on('click', function () {
	var data_table = $(this).attr('data-table');
	var data_status = $(this).attr('data-status');
	var data_pkey = $(this).attr('data-pkey');
	var all_id = [];
	$('input[class="chk"]:checked').each(function () {
		all_id.push($(this).attr('value'));
	});
	var ct = all_id.length;
	if (ct >= 1) {
		bootbox.confirm({
			message: "Do you really want to " + data_status + " selected (" + ct + ") records ?",
			buttons:
			{
				confirm: {
					label: 'Yes',
					className: 'btn-success btn-sm'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger btn-sm'
				}
			},
			callback: function (result) {
				if (result == true) {
					$.ajax({
						'type': 'POST',
						'url': 'required/master_process?task=active_block',
						'data': { 'table': data_table, 'status': data_status, 'id': all_id, 'pkey': data_pkey },
						success: function (data) {
							console.log(data);
							//var obj = JSON.parse(data);
							$.notify(ct + " records(s) " + data_status + " Successfully", "success");
							//location.reload();
						}
					});
				}
			}
		});
	} else {
		$.notify("Sorry ! No Record Selected ", "info");
	}
});


//=====ATTENDANCE BUTTON =========//
$("#att_btn").on('click', function () {
	//var data  =$("#att_frm").serialize();
	var att_date = $("#att_date").val();
	abs_student = [];
	$('input[class="chk"]:unchecked').each(function () {
		// let student_class = $(this).attr('data-class');
		abs_student.push($(this).attr('value'));
	});
	all_student = [];
	$('input[class="chk"]:checked').each(function () {
		// let student_class = $(this).attr('data-class');
		all_student.push($(this).attr('value'));
	});
	console.log(all_student);
	var ct = all_student.length;
	if (ct >= 1) {
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=make_att',
			'data': { 'att_date': att_date, 'sel_id': all_student, 'unsel_id': abs_student },
			success: function (data) {
				console.log(data);
				$.notify(ct + " Student(s) Succesfully Marked as Present", "success");
			}
		});
	}
	else {
		$.notify("Sorry ! No Student Selected ", "info");
	}
});
$("#present_btn").on('click', function () {
	//var data  =$("#att_frm").serialize();
	var att_date = $("#att_date").val();
	abs_employee = [];
	$('input[class="chk"]:unchecked').each(function () {
		abs_employee.push($(this).attr('value'));
	});
	all_employee = [];
	$('input[class="chk"]:checked').each(function () {
		all_employee.push($(this).attr('value'));
	});
	var ct = all_employee.length;
	if (ct >= 1) {
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=make_emp_att',
			'data': { 'att_date': att_date, 'sel_id': all_employee, 'unsel_id': abs_employee },
			success: function (data) {
				//alert(data);
				console.log(data);
				//var obj = JSON.parse(data);
				$.notify(ct + " Employee(s) Succesfully Marked as Present", "success");
				//location.reload();
			}
		});
	}
	else {
		$.notify("Sorry ! No Employee Selected ", "info");
	}
});
$("#abs_btn").on('click', function () {
	//var data  =$("#att_frm").serialize();
	var att_date = $("#att_date").val();
	emp_type = [];
	present_employee = [];
	$('input[class="chk"]:unchecked').each(function () {
		present_employee.push($(this).attr('value'));
	});
	all_employee = [];
	$('input[class="chk"]:checked').each(function () {
		all_employee.push($(this).attr('value'));
	});
	var ct = all_employee.length;
	if (ct >= 1) {
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=make_emp_abs',
			'data': { 'att_date': att_date, 'sel_id': all_employee, 'unsel_id': present_employee },
			success: function (data) {
				//alert(data);
				console.log(data);
				//var obj = JSON.parse(data);
				$.notify(ct + " Employee(s) Succesfully Marked as Absent", "success");
				//location.reload();
			}
		});
	}
	else {
		$.notify("Sorry ! No Employee Selected ", "info");
	}
});
//=====BLOCK BUTTON =========//
$(".block_btn").on('click', function () {
	var del_row = $($(this).closest("tr"));
	var id = $(this).attr("data-id");
	var table = $(this).attr("data-table");
	var pkey = $(this).attr("data-pkey");
	bootbox.confirm({
		message: "Do you really want to BLOCK this?",
		buttons:
		{
			confirm: {
				label: 'Yes',
				className: 'btn-info'
			},
			cancel: {
				label: 'No',
				className: 'btn-warning'
			}
		},
		callback: function (result) {
			if (result == true) {
				$.ajax({
					'type': 'POST',
					'url': 'required/master_process?task=master_block',
					'data': { 'id': id, 'table': table, 'pkey': pkey },
					success: function (data) {
						//alert(data);
						var obj = JSON.parse(data);
						$.notify(obj.msg, obj.status);
						del_row.hide(500);
					}
				});
			}
		}
	});
});

//=====BLOCK USER =========//
$(".block_user").on('click', function () {
	var del_row = $($(this).closest("tr"));
	var id = $(this).attr("data-id");
	var st = $(this).attr("data-status");
	bootbox.confirm({
		message: "Do you really want to " + st + "  this User Account?",
		buttons:
		{
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function (result) {
			if (result == true) {
				$.ajax({
					'type': 'POST',
					'url': 'required/master_process?task=block_user',
					'data': { 'id': id, 'data_status': st },
					success: function (data) {
						//alert(data);
						var obj = JSON.parse(data);
						$.notify(obj.msg, obj.status);
						//del_row.hide(500); 
						location.reload();
					}
				});
			}
		}
	});
});

//========= LOGIN BUTTON ===========//
$("#login_btn").click(function () {
	$("#login_frm").validate();
	if ($("#login_frm").valid()) {
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#login_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=verify_login',
			'data': data,
			success: function (data) {
				var obj = JSON.parse(data);
				if (obj.status.trim() == 'success') {
					$.notify("Login Success...", obj.status);
					window.location = obj.url;
				}
				else {
					$.notify("Sorry Some Thing Went Wrong", "error");
					$("#login_frm")[0].reset();
					$("#login_btn").html("Secure Login");
					$("#login_btn").attr("disabled", false);
				}
			}

		});
	}
});

//========= LOGIN As BUTTON ===========//
$(".login_as").click(function () {

	var user_name = $(this).attr("data-id");
	var user_pass = $(this).attr("data-code");
	var data = {
		'user_name': user_name,
		'user_pass': user_pass
	}
	$.ajax({
		'type': 'POST',
		'url': 'required/master_process?task=login_as',
		'data': data,
		success: function (data) {
			//alert(data);
			var obj = JSON.parse(data);

			if (obj.status.trim() == 'success') {
				$.notify("Login Success...", obj.status);
				window.location = 'client_index';
			}
			else {
				$.notify("Sorry Some Thing Went Wrong", "error");
				$("#login_frm")[0].reset();
				$("#login_btn").html("Secure Login");
				$("#login_btn").attr("disabled", false);
			}
		}

	});
});

//===========UPLOAD IMAGES ==============//
$('#uploadimg').change(function () {
	$("#uploadForm").submit();
});

$("#uploadForm").on('submit', (function (e) {
	e.preventDefault();
	if ($('#update_btn').length != 0) {
		$("#insert_btn").attr("disabled", true);
	}
	$.ajax({
		url: "required/master_process?task=upload",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		success: function (data) {
			console.log(data);
			//alert(data);
			var obj = JSON.parse(data);
			$("#targetimg").val(obj.id);
			$("#display").html("<img src='required/upload/" + obj.id + "' width='140px' height='160px' >");
			$.notify(obj.msg, obj.status);
			$("#update_btn").attr("disabled", false);
			$("#uploadmodal").hide();
		},
		error: function () {

		}
	});
}));

//===========UPLOAD ID PROOF ==============//
$('#upload_id_proof').change(function () {
	$("#id_proof").submit();
});

$("#id_proof").on('submit', (function (e) {
	e.preventDefault();
	$.ajax({
		url: "required/master_process?task=upload",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		success: function (data) {
			var obj = JSON.parse(data);
			//alert(data);
			$("#target_id_proof").val(obj.id);
			$("#student_id_display").html("<img src='upload/" + obj.id + "' width='100px' height='100px' class='img-thumbnail'>");
			$.notify(obj.msg, obj.status);
		},
		error: function () { }
	});
}));


//===========UPLOAD EDUCATIONAL PROOF ==============//
$('#upload_edu_proof').change(function () {
	$("#edu_proof").submit();
});

$("#edu_proof").on('submit', (function (e) {
	e.preventDefault();
	$.ajax({
		url: "required/master_process?task=upload",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData: false,
		success: function (data) {
			var obj = JSON.parse(data);
			//alert(data);
			$("#target_edu_proof").val(obj.id);
			$("#student_edu_display").html("<img src='upload/" + obj.id + "' width='100px' height='100px' class='img-thumbnail'>");
			$.notify(obj.msg, obj.status);
		},
		error: function () { }
	});
}));




//=========SELECT ALL CHECK BOX WITH SAME NAME =======//
// function selectAll(source) 
// {
// checkboxes = document.getElementsByName('sel_id[]');
// for(var i in checkboxes)
// checkboxes[i].checked = source.checked;
// }
function selectall(c = 'fee_month') {
	var x = $("input[name=" + c + "\\[\\]]");
	//var x = $("input[name=fee_month\\[\\]]");
	//$("input[name='fee_month[]']").each(function(){ 
	//$("input[name='"+x+"']").each(function(){ 
	x.each(function () {
		$(this).prop("checked", !$(this).prop("checked"));
	});
}

function selectcheck(c = 'fee_month') {
	var x = $("input[name=" + c + "\\[\\]]");
	//var x = $("input[name=fee_month\\[\\]]");
	//$("input[name='fee_month[]']").each(function(){ 
	//$("input[name='"+x+"']").each(function(){ 
	x.each(function () {
		$(this).prop("checked", !$(this).prop("checked"));
	});
}

function ajax_call(url, data, target) {
	var data = this.value;
	$.ajax({
		'type': 'POST',
		'url': url,
		'data': data,
		success: function (data) {
			//var obj = JSON.parse(data);
			$(target).show();
			$(target).html(data);
		}
	});
}

//===========LOGOUT WITH CONFIRAMTION ==========//
function logout() {
	bootbox.confirm({
		message: "You you really want to logout ?",
		buttons:
		{
			confirm: {
				label: '<i class="fa fa-check"></i> Logout',
				className: 'btn-success'
			},
			cancel: {
				label: '<i class="fa fa-times"></i> Cancel',
				className: 'btn-danger'
			}
		},
		callback: function (result) {
			if (result == true) {
				$.ajax({
					type: 'POST',
					url: 'required/master_process?task=logout',
					data: { 'rtype': 'AJAX' },
					success: function (data) {
						console.log(data);
						var obj = JSON.parse(data);
						window.location = 'login';
						$.notify(obj.msg, obj.status);
					}
				});
			}
		}
	});
}

function autologout() {
	$.ajax({
		'type': 'POST',
		'url': 'required/master_process?task=logout',
		success: function (data) {
			//alert(data);
			var obj = JSON.parse(data);

			window.location = 'login';
			$.notify(obj.msg, obj.status);
		}
	});
}
//===========ADD SINGLE DATA ===========//
$("#add_btn").click(function () {
	var msg = $(this).attr('data-msg');
	var table = $(this).attr('data-table');
	var col = $(this).attr('data-col');
	bootbox.prompt(msg, function (udata) {

		if (udata) {
			var tdata = { "table": table, 'col': col, 'value': udata };
			$.ajax({
				'type': 'POST',
				'url': 'required/master_process?task=add_data',
				'data': tdata,
				success: function (data) {
					////alert(data);
					var obj = JSON.parse(data);
					$.notify(obj.msg, obj.status);
				}
			});
		}
	});
});

//======FORGET PASSWORD USING PROMPT BOX =======/
$("#forget_password").click(function () {
	bootbox.prompt("Enter a valid Username ", function (str) {
		if (str) {
			$.ajax({
				'type': 'POST',
				'url': 'required/master_process?task=forget_password',
				'data': 'user_name=' + str,
				success: function (data) {
					//alert(data);
					var obj = JSON.parse(data);
					$.notify(obj.msg, obj.status);
				}
			});
		}
	});
});


//======Change PASSWORD of Logged In User =======/
$("#change_password").click(function () {
	$(this).attr("disabled", true);
	$(this).html("Please Wait...");
	$("#update_frm").validate();

	if ($("#update_frm").valid()) {
		var cp = $("#current_password").val();
		var np = $("#new_password").val();
		var rp = $("#repeat_password").val();
		if (np != rp) {
			$.notify("New password and Repeat password Not matched", "error");

		}
		else {
			$.ajax({
				'type': 'POST',
				'url': 'required/master_process?task=change_password',
				'data': 'new_password=' + np + '&current_password=' + cp,
				success: function (data) {
					////alert(data);
					var obj = JSON.parse(data);
					if (obj.status.trim() == 'success') {
						$.notify("Password Changed Succesfully", obj.status);
						$("#update_frm")[0].reset();
						logout();
					}
					else {
						$.notify("Sorry! Unable to Chanage Password ", "error");
						$("#update_frm")[0].reset();
						$("#change_password").attr("disabled", false);
					}
				}
			});
		}
	}

});



//======ADD NEW COURSE TYPE PROMPT BOX =======/
$("#add_course_type").click(function () {
	bootbox.prompt("Enter Course Type name ", function (data) {
		if (data) {
			$.ajax({
				'type': 'POST',
				'url': 'required/master_process?task=add_course_type',
				'data': 'course_type=' + data,
				success: function (data) {
					////alert(data);
					var obj = JSON.parse(data);
					$.notify(obj.msg, obj.status);
				}
			});
		}
	});
});


function populate(frm, data) {
	//$("#edit_modal").show();
	$.each(data, function (key, value) {
		var ctrl = $('[name=' + key + ']', frm);
		switch (ctrl.prop("type")) {
			case "radio": case "checkbox":
				ctrl.each(function () {
					if ($(this).attr('value') == value) $(this).attr("checked", value);
				});
				break;
			case "select":
				$("option", ctrl).each(function () {
					if (this.value == value) { this.selected = true; }
				});
				break;
			default:
				ctrl.val(value);
		}
	});
}

function json2table(selector, myList) {
	var columns = addAllColumnHeaders(myList, selector);

	for (var i = 0; i < myList.length; i++) {
		var row$ = $('<tr/>');
		for (var colIndex = 0; colIndex < columns.length; colIndex++) {
			var cellValue = myList[i][columns[colIndex]];
			if (cellValue == null) cellValue = "";
			row$.append($('<td/>').html(cellValue));
		}
		$(selector).append(row$);
	}
}

function addAllColumnHeaders(myList, selector) {
	var columnSet = [];
	var headerTr$ = $('<tr/>');

	for (var i = 0; i < myList.length; i++) {
		var rowHash = myList[i];
		for (var key in rowHash) {
			if ($.inArray(key, columnSet) == -1) {
				columnSet.push(key);
				headerTr$.append($('<th/>').html(key));
			}
		}
	}
	$(selector + ' thead').append(headerTr$);

	return columnSet;
}

// === Dynamic District Section From State ==/
function getdistrict(val) {
	//alert(val);
	$.ajax({
		type: "GET",
		url: "required/master_process.php?task=get_dist",
		data: 'state_code=' + val,
		success: function (data) {
			//console.log(data);
			$("#district_list").html(data);
		}
	});
}


//======SEND SMS ANY TIME =======/
$("#send_sms").click(function () {

	var mobile = $("#mobile2").val();
	var sms = $("#msg_text2").val();
	var template_id = $("#sms_id2").val();
	//var allmobile = mobile.replace(/\s/g/, "");
	var allmobile = mobile.replace(/[^0-9.]/g, "");
	var ct = allmobile.length;
	//alert(ct);
	if ((ct % 10) != 0 || ct < 10) {
		$.notify("Sorry! Invalid Mobile Number", "error");
	}
	else if (sms == '') {
		$.notify("SMS can't be blank", "info");
	}
	else {
		$("#send_sms").html("Sending...");
		$("#send_sms").attr("disabled", true);
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=send_sms',
			'data': { 'mobile': mobile, 'sms': sms, 'template_id': template_id },
			success: function (data) {
				console.log(data);
				$("#send_sms").html("Sent Successfully");
				$("#send_sms").attr("disabled", false);
				var obj = JSON.parse(data);
				$.notify(obj.msg, obj.status);
				$("#sms_frm")[0].reset();
			}
		});
	}
});

/*============== GROUP SMS ==============*/
$("#group_sms").click(function () {

	var mobile = $("#msg_no").children("option:selected").val();
	var section = $("#student_section").children("option:selected").val();
	var message = $("#msg_text").val();
	var unicode = $("#unicode1").prop('checked');
	var template_id = $("#sms_id").val();
	if (mobile == '') {
		$.notify("Sorry! SMS Group Not Selected ", "error");
	}
	else if (message == '') {
		$.notify("SMS can't be blank", "info");
	}
	else {
		$("#group_sms").html("Please Wait While Sending...");
		$("#group_sms").attr("disabled", true);
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=group_sms',
			'data': { 'msg_no': mobile, 'section': section, 'msg_text': message, 'unicode': unicode, 'template_id': template_id },
			beforeSend: function () {
				//console.log(mobile + message );
			},
			success: function (data) {
				console.log(data);
				// var obj = JSON.parse(data);
				// $("#group_sms").html("SMS SENT SUCCESSFULLY");
				// $.notify(obj.msg, obj.status);
				$("#group_sms").html("SEND SMS TO ALL");
				$("#group_sms").attr("disabled", false);
				
				
				// $("#group_frm")[0].reset();
			}
		});
	}
});
//=====INSERT Item in Table =========//
$("#add_item_btn").on('click', function () {
	$("#item_frm").validate();


	if ($("#item_frm").valid()) {
		var task = $("#item_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#item_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				var obj = JSON.parse(data);
				if (obj.url != null) {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
				$("#add_item_btn").html("Add Item");
				$("#add_item_btn").removeAttr("disabled");
			}

		});
	}
});


//==== Create HTML TO PDF ============//
function createpdf(file) {
	var pdf = new jsPDF('p', 'pt', 'letter');
	// source can be HTML-formatted string, or a reference
	// to an actual DOM element from which the text will be scraped.
	source = $('#content')[0];

	// we support special element handlers. Register them with jQuery-style 
	// ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
	// There is no support for any other type of selectors 
	// (class, of compound) at this time.
	specialElementHandlers = {
		// element with id of "bypass" - jQuery style selector
		'#bypassme': function (element, renderer) {
			// true = "handled elsewhere, bypass text extraction"
			return true
		}
	};
	margins = {
		top: 80,
		bottom: 60,
		left: 40,
		width: 522
	};
	// all coords and widths are in jsPDF instance's declared units
	// 'inches' in this case
	pdf.fromHTML(
		source, // HTML string or DOM elem ref.
		margins.left, // x coord
		margins.top, { // y coord
		'width': margins.width, // max width of content on PDF
		'elementHandlers': specialElementHandlers
	},

		function (dispose) {
			// dispose: object with X, Y of the last line add to the PDF 
			//          this allow the insertion of new lines after html
			pdf.save(file + '.pdf');
		}, margins
	);
}


function exportxls() {
	var tab_text = "<table border='1px'><tr>";
	var textRange; var j = 0;
	tab = document.getElementById('data_tbl'); // id of table

	for (j = 0; j < tab.rows.length; j++) {
		tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
		//tab_text=tab_text+"</tr>";
	}

	tab_text = tab_text + "</table>";
	// tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
	tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
	tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

	var ua = window.navigator.userAgent;
	var msie = ua.indexOf("MSIE ");

	if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
	{
		txtArea1.document.open("txt/html", "replace");
		txtArea1.document.write(tab_text);
		txtArea1.document.close();
		txtArea1.focus();
		sa = txtArea1.document.execCommand("SaveAs", true, "export.xls");
	}
	else                 //other browser not tested on IE 11
		sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

	return (sa);
}


//=====INSERT Item in Table =========//
$("#course_id").on('change', function () {

	var id = $(this).val();
	var data = 'id=' + id;
	$.ajax({
		'type': 'POST',
		'url': 'required/master_process?task=get_course',
		'data': data,
		success: function (data) {
			//console.log(data);
			var obj = JSON.parse(data);
			$("#course_data").css('display', 'inline');
			$("#course_data").html(obj.data.course_duration + " " + obj.data.course_unit);

		}

	});
});

//=====INSERT Item in Table =========//
$("#center_id").on('change', function () {

	var id = $(this).val();
	var data = 'id=' + id;
	$.ajax({
		'type': 'POST',
		'url': 'required/master_process?task=get_wallet',
		'data': data,
		success: function (data) {
			//console.log(data);
			var obj = JSON.parse(data);
			$("#wallet_amount").css('display', 'inline');
			$("#wallet_amount").html(obj.center_wallet);

		}

	});
});


//=====INSERT Item in Table =========//
$('body').on('click', "#add_wallet", function () {
	$("#wallet_frm").validate();

	if ($("#wallet_frm").valid()) {
		var task = $("#wallet_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#wallet_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=add_to_wallet',
			'data': data,
			success: function (data) {
				//alert(data);
				var obj = JSON.parse(data);
				if (obj.url != null) {
					bootbox.alert(obj.msg, function () {
						window.location.replace(obj.url);
					});
				}
				else {
					$.notify(obj.msg, obj.status);
				}
			}
		});
	}
});


//=====Attendance BUTTON =========//
$("#add_to_att").on('click', function () {
	var all_student = [];
	$('input[class="chk"]:checked').each(function () {
		all_student.push($(this).attr('value'));
	});
	var ct = all_student.length;
	if (ct >= 1) {
		bootbox.confirm({
			message: "Do you really want to add selected (" + ct + ") student to attendance list ?",
			buttons:
			{
				confirm: {
					label: 'Yes',
					className: 'btn-success btn-sm'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger btn-sm'
				}
			},
			callback: function (result) {
				if (result == true) {
					$.ajax({
						'type': 'POST',
						'url': 'required/master_process?task=add_to_att',
						'data': { 'sel_id': all_student },
						success: function (data) {
							console.log(data);
							$.notify(ct + " Student(s) Successfully Added to Attendance List", "success");

						}
					});
				}
			}
		});
	}
	else {
		$.notify("Sorry ! No Student Selected ", "danger");
	}
});

/*==============ADD / REMOVE QUESTION ==========*/

//=====ACTIVE / BLOCK BUTTON =========//
$(".add_remove_question").on('click', function () {
	var data_task = $(this).attr('data-task');
	var data_status = $(this).attr('data-status');
	var set_id = $(this).attr('data-set');
	var all_id = [];
	$('input[class="chk"]:checked').each(function () {
		all_id.push($(this).attr('value'));
	});
	var ct = all_id.length;
	if (ct >= 1) {
		bootbox.confirm({
			message: "Do you really want to " + data_status + " selected (" + ct + ") records ?",
			buttons:
			{
				confirm: {
					label: 'Yes',
					className: 'btn-success btn-sm'
				},
				cancel: {
					label: 'No',
					className: 'btn-danger btn-sm'
				}
			},
			callback: function (result) {
				if (result == true) {
					$.ajax({
						'type': 'POST',
						'url': 'required/master_process?task=' + data_task,
						'data': { 'all_id': all_id, 'set_id': set_id },
						success: function (data) {
							console.log(data);
							//var obj = JSON.parse(data);
							$.notify(ct + " question(s) " + data_status + " Successfully", "success");
							location.reload();
						}
					});
				}
			}
		});
	} else {
		$.notify("Sorry ! No Record Selected ", "info");
	}
});

$("#finish_set").on('click', function () {
	var set_id = $(this).attr('data-set');
	$.ajax({
		'type': 'POST',
		'url': 'required/master_process?task=finish_set',
		'data': { 'set_id': set_id },
		success: function (data) {
			//console.log(data);
			var obj = JSON.parse(data);
			if (obj.url != null) {
				bootbox.alert(obj.msg, function () {
					window.location.replace(obj.url);
				});
			}
			else {
				$.notify(obj.msg, obj.status);
			}
		}
	});
});


//=====Visitor Register =========//
$("#visitor_btn").on('click', function () {
	$("#visitor_frm").validate();

	if ($("#visitor_frm").valid()) {
		var task = $("#visitor_frm").attr('action');
		$(this).attr("disabled", true);
		$(this).html("Please Wait...");
		var data = $("#visitor_frm").serialize();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=' + task,
			'data': data,
			success: function (data) {
				console.log(data);
				//alert(data);
				var obj = JSON.parse(data);
				//$('#visitor_frm')[0].reset();
				$("#visitor_btn").html("Save Details");
				$("#visitor_btn").removeAttr("disabled");
				$("#visitor_btn").css('display', 'none');
				$("#otparea").show();
				$("#quizurl").val(obj.url);

			}

		});
	}
});

$(document).on('click', '#otp_btn', function () {
	var sotp = $("#sotp").val();
	var uotp = $("#uotp").val();
	var quizurl = $("#quizurl").val();

	if (sotp == uotp) {
		window.location.replace(quizurl);
	}
	else {
		$.notify('Invalid OTP', 'error');
	}
});

$("#basic_checkbox_1").on('click', function () {

	$("#address2").val($("#address1").val());
});

/*---------------- BINE CUSTOME BUTTIONS --------------*/


//=====DELETE BUTTON =========//
$(document).on('click', '.delete_fee', function () {
	var del_row = $($(this).closest("tr"));
	var id = $(this).attr("data-id");
	bootbox.confirm({
		message: "Do you really want to delete this fee form everywhere?",
		buttons:
		{
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function (result) {
			if (result == true) {
				$.ajax({
					'type': 'POST',
					'url': 'required/master_process?task=delete_fee',
					'data': { 'id': id },
					success: function (data) {
						//alert(data);
						var obj = JSON.parse(data);
						$.notify(obj.msg, obj.status);
						del_row.hide(500);
					}
				});
			}
		}
	});
});

//=====DELETE SUBJECT=========//
$(document).on('click', '.delete_subject', function () {
	var del_row = $($(this).closest("tr"));
	var id = $(this).attr("data-id");
	bootbox.confirm({
		message: "Do you really want to delete Subject this fee form everywhere?",
		buttons:
		{
			confirm: {
				label: 'Yes',
				className: 'btn-success'
			},
			cancel: {
				label: 'No',
				className: 'btn-danger'
			}
		},
		callback: function (result) {
			if (result == true) {
				$.ajax({
					'type': 'POST',
					'url': 'required/master_process?task=delete_subject',
					'data': { 'id': id },
					success: function (data) {
						console.log(data);
						var obj = JSON.parse(data);
						$.notify(obj.msg, obj.status);
						del_row.hide(500);
					}
				});
			}
		}
	});
});

function selectmenu() {
	var nav = document.getElementById('nav'),
		anchor = nav.getElementsByTagName('a'),
		current = window.location.pathname.substring(1); //.split('/')[2];
	//console.log(current);
	$('#nav li a').each(function () {
		if ($(this).attr('href') == current) {
			//console.log($(this).text());
			$(this).parent().addClass('active');
			$(this).closest(".treeview").addClass('active');
		}
	})
}

function navigate() {
	var nav = document.getElementById('nav'),
		anchor = nav.getElementsByTagName('a'),
		current = window.location.pathname.split('/')[2];
	$('#nav li a').each(function () {
		if ($(this).attr('href') == current) {
			//console.log($(this).text());
			$(this).parent().addClass('active');
			$(this).closest(".treeview").addClass('active');
		}

	})
}

if ($("#nav").length > 0) {
	navigate();
}

$("#fee_type").on("change blur select", function () {
	if ($(this).val() == 'FIXED') {
		$("#fee_amount_area").css('display', 'block');
	}
	else {
		$("#fee_amount_area").css('display', 'none');
	}
});


$(document).on('click', "#month_list .fee-month", function () {
	var mlist = new Array();
	$(".fee-month:checked").each(function () {
		mlist.push($(this).val());
	});
	if (mlist.length <= 0) {
		$(".fee-value").each(function () {
			$(this).val(0);
		
		});
		addall();
		$("#fee_month").val('other_month');
	}
	else {
		$(".fee-value").each(function () {
			$(this).val(0);
		});
		var student_id = $("#student_id").val();
		$.ajax({
			'type': 'POST',
			'url': 'required/master_process?task=nmonth_fee',
			'data': { 'student_id': student_id, 'month_list': mlist },
			beforeSend: function () {
				$("#fee_month").val(mlist);
			},
			success: function (data) {
				var obj = JSON.parse(data);
				console.log(obj);
				$.each(obj, function (index, value) {
					$("#" + index).val(value);
				});
				addall();
			}
		});
	}

});


//=====PAY BUTTON =========//
$("#pay_btn").on('click', function (event) {
	if ($("#paid_amount").val() < 0 || $("#paid_amount").val() == '') {
		$.notify("Check Amount to be paid", 'error');
	}
	else {
		$("#pay_frm").validate();
		if ($("#pay_frm").valid()) {
			var task = $("#pay_frm").attr('action');
			$(this).attr("disabled", true);
			$(this).html("Please Wait...");
			var data = $("#pay_frm").serialize();
			$.ajax({
				'type': 'POST',
				'url': 'required/master_process?task=pay_fee',
				'data': data,
				success: function (data) {
					console.log(data);
					//alert(data);
					var obj = JSON.parse(data);
					$('#pay_frm')[0].reset();

					$("#pay_btn").html("<i class='fa fa-save'></i> Make Payment");
					$("#pay_btn").removeAttr("disabled");
					if (obj.url != null) {
						bootbox.alert(obj.msg, function () {
							window.location.replace(obj.url);
						});
					}
					else {
						$.notify("Payment Recorded Successfully", obj.status);
					}
				}

			});
		}
	}
});



$("#uploadarea").on('click', function () {
	var upload_frm = "";
	$(document).add(upload_frm);
	$("#uploadmodal").show();
});

$(".msg_box").on('keyup', function () {
	var ct = $(this).val().length;
	$("#msg_count").html(ct);
	if (ct > 500) {
		$.notify('SMS is too large', 'info');
	}
});



$(".cancelreceipt").on('click', function () {
	var id = $(this).data('id');
	var remarks = prompt("Enter Cause to cancel");
	//alert(remarks); 
	if (remarks == '' || remarks === null) {
		$.notify("Enter A Valid Cause ", "error");
	}
	else {

		$.ajax({
			method: 'post',
			url: 'required/master_process.php?task=cancel_receipt',
			data: {
				receipt_id: id,
				cancel_remarks: remarks
			},
			beforeSend: function () {
				//console.log(id+remarks);
			},
			success: function (res) {
				var obj = JSON.parse(res);
				$.notify(obj.msg, obj.status);
			}

		});
	}
});



$(".cancel_acctxn").on('click', function () {
	var id = $(this).data('id');
	var remarks = prompt("Enter Cause to cancel");
	//alert(remarks); 
	if (remarks == '' || remarks === null) {
		$.notify("Enter A Valid Cause ", "error");
	}
	else {

		$.ajax({
			method: 'post',
			url: 'required/master_process.php?task=cancel_acctxn',
			data: {
				txn_id: id,
				cancel_remarks: remarks
			},
			beforeSend: function () {
				//console.log(id+remarks);
			},
			success: function (res) {
				var obj = JSON.parse(res);
				$.notify(obj.msg, obj.status);
			}

		});
	}
});


$(".cancel_admin_txn").on('click', function () {
	var id = $(this).data('id');
	var remarks = prompt("Enter Cause to cancel");
	//alert(remarks); 
	if (remarks == '' || remarks === null) {
		$.notify("Enter A Valid Cause ", "error");
	}
	else {

		$.ajax({
			method: 'post',
			url: 'required/master_process.php?task=cancel_admin_txn',
			data: {
				txn_id: id,
				cancel_remarks: remarks
			},
			beforeSend: function () {
				//console.log(id+remarks);
			},
			success: function (res) {
				var obj = JSON.parse(res);
				$.notify(obj.msg, obj.status);
			}

		});
	}
});

$(".mobile").on('click', function () {
	var mobile = $(this).text();
	mobiles.push(mobile);
	$("#mobiles").val(mobiles);
	$(this).addClass('badge badge-success');
});

/* ABOUT DEVELOPER POPUP */

$('#appinfo').on('click', function (e) {
	$('#about_app').modal('show');
});

/* Change Enter Button in to TAB */

$('.form-control').keypress(function (e) {
	var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
	if (key == 13) {
		e.preventDefault();
		var inputs = $(this).closest('form').find('.form-control');
		//var inputs = $(this).closest('form').find('input');
		if (inputs.index(this) + 1 == inputs.length) {
			$("#update_btn").trigger('click');
		}
		else {
			inputs.eq(inputs.index(this) + 1).focus();
		}
	}
});

//=====Update Role Checkbox  =========//
$(document).on('change', ".update_role", function() {
    var st = $(this).val();
    var role = $(this).data('role');
    var id = $(this).attr("data-id");
    var table = $(this).attr("data-table");
    var pkey = $(this).attr("data-pkey");
    bootbox.confirm({
        message: "Do you really want to " + st + " the role ?",
        buttons: {
            confirm: {
                label: 'Yes',
                className: 'btn-success'
            },
            cancel: {
                label: 'No',
                className: 'btn-danger'
            }
        },
        callback: function(result) {
            if (result == true) {
                $.ajax({
                    'type': 'POST',
                    'url': 'required/master_process?task=update_role',
                    'data': { 'user_id': id, 'table_name': table, 'role_name': role, 'task': st },
                    success: function(data) {
                        //alert(data);
                        var obj = JSON.parse(data);
                        $.notify(obj.msg, obj.status);
                        //del_row.hide(500); 
                        //location.reload();
                    }
                });
            }
        }
    });
});


// Voice Search  with Vi Class //

$(document).on('focus',".vi", function(){
	var speech = true;
	window.SpeechRecognition = window.SpeechRecognition
					|| window.webkitSpeechRecognition;

	const recognition = new SpeechRecognition();
	recognition.interimResults = true;

	recognition.addEventListener('result', e => {
		const transcript = Array.from(e.results)
			.map(result => result[0])
			.map(result => result.transcript)
			.join('')
//		console.log(transcript);
	    $(this).val(transcript);
	});
	
	if (speech === true) {
		recognition.start();
		recognition.addEventListener('end');
		//recognition.addEventListener('end', recognition.start);
	}
});


/* VIEW DATA IN MODAL */
$('.view_data').on('click', function (e) {
	e.preventDefault();
	$('#view_data').modal('show').find('.modal-title').html($(this).attr('data-title'));
	$('#view_data').modal('show').find('.modal-body').load($(this).attr('data-href'));
});

/* GLOBAL SHORTCUTS */

shortcut.add("F1", function () {
	window.location = 'dashboard';
});
shortcut.add("F2", function () {
	window.location = 'add_student';
});
shortcut.add("F3", function () {
	window.location = 'collection_report';
});
shortcut.add("F4", function () {
	window.location = 'collect_fee';
});
shortcut.add("F5", function () {
	window.location = 'send_sms';
});
shortcut.add("ctrl+s", function () {
	//alert("CTRL +S");
	$("#update_btn").trigger('click');
});

shortcut.add("ctrl+f", function () {
	//alert("CTRL +f");
	$("#search_text").focus();
});

// ===UPDATE NOTICE  VIEWER ==== //

$(".notification").on('click', function () {
	var id = $(this).data('id');
		$.ajax({
			method: 'post',
			url: 'required/master_process.php?task=update_notice_viewer',
			data: {
				notice_id: id,
			},
			success: function (res) {
			    $('"#n'+id+'"').hide(500);
			}
		});
});

// attendance js


//=========SELECT ALL CHECK BOX WITH PRESENT =======//
// function selectAll() {
// 	let radioboxes = $(".chk");
// 	if (radioboxes.prop("checked", true)) {
// 		radioboxes.prop("checked", false);
// 	} else {
// 		radioboxes.prop("checked", true);
// 	}
// }