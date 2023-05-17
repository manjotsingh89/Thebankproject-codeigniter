// DEPENDENCIES
// 1: jQuery


$(document).on('click', '#start-review', function() {
	$.Incipit('show');
	var postData = {
		"employeeId": $(this).attr('data-employee-id'),
	};

	$.ajax({
		url: 'core/reviews/create-review.php',
		type: 'POST',
		data: postData,
		success: (response) => {
			$.Incipit('hide');
			response = $.parseJSON(response);
			if (response.status) {
				window.location.href = `reviews.php?reviewId=${response.reviewId}`;
			}
			else {
				alertify.error(response.message);
			}
		},
		error: () => {
			$.Incipit('hide');
			alertify.error('Something went wrong');
		}
	});
});

$(window).on('load', function() {
	setTimeout(() => {
		$('#review-instructions-modal').modal('show');
	}, 1000);
});


var currentReviewTab = 0;
$(document).ready(function() {
	$(".review-tab").css('height', $(window).height()-((35/100)*$(window).height()));
	$("#create-kpi-form").validate({
		errorClass: 'text-danger border-danger',
		errorPlacement: function(error, element) {}
	});
	$(".review-tab").hide();
	for (var i = $(".review-tab").length - 1; i >= 0; i--) {
		$("#review-step-icons").append(`<span class="review-step"></span>`);
	}

	if (window.location.pathname.match(/reviews.php/)) {
		displayReviewTab(currentReviewTab);
		getProjects();
		getKpis();
		getDuties();
	}
});

function displayReviewTab(index) {
	let tabs = $(".review-tab");
	$(tabs[index]).fadeIn();

	if (index == 0) {
		$("#review-form #prevBtn").prop('disabled', true);
	} else {
		$("#review-form #prevBtn").prop('disabled', false);
	}
	
	if (index == (tabs.length - 1)) {
		let txt = $("#nextBtn").attr("data-text");
		if (txt == 'd-none') {
			$("#nextBtn").addClass(txt);
			$("#nextBtn").parent().append(`<div id="empty-div"></div>`);
		} else {
			document.getElementById("nextBtn").innerHTML = $("#nextBtn").attr("data-text");
		}
	} else {
		$("#nextBtn").removeClass("d-none");
		$("#nextBtn").parent().find("#empty-div").remove();
		document.getElementById("nextBtn").innerHTML = "Next";
	}

	fixStepIndicator(index);
}

function nextPrevReviewTab(index) {
	var tabs = $(".review-tab");

	$("#review-form").validate({
		errorClass: 'text-danger border-danger',
    	errorPlacement: function(error, element) {
            if (element.attr("type") == "radio" || element.attr("type") == "number" || element.attr("name") == "Benchmark[]") {
                // error.insertAfter(element.parent().parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

	if (index == 1 && !$("#review-form").valid()) {
		return false;
	}

	if (index == 1 && currentReviewTab+1 >= tabs.length) {
		$("#review-form").submit();
		return false;
	}

	$(tabs[currentReviewTab]).hide();
	currentReviewTab = currentReviewTab + index;

	displayReviewTab(currentReviewTab);
}

function fixStepIndicator(index) {
	$(".review-step").removeClass("active");
	let tabs = $(".review-step");
	$(tabs[index]).addClass("active");
}

function toggleProjectTarget(id) {
	$(`textarea[data-id=project-target-${id}]`).toggle();
}

function removeJobReqProject(id) {
	$(`#job-req-project-${id}`).remove();
	$(`#modal-projects-list`).find(`button[data-id=${id}]`).prop('disabled', false);
}

function removeBenchmark(element) {
	$(element).parent().parent().parent().parent().remove();
	orderBenchmarks();
}

function removeJobReqKpi(id) {
	$(`#job-req-kpi-${id}`).remove();
	$(`#modal-kpis-list`).find(`button[data-id=${id}]`).prop('disabled', false);
}

function removeJobReqDuty(id) {
	$(`#job-req-duty-${id}`).remove();
	$(`#modal-duties-list`).find(`button[data-id=${id}]`).prop('disabled', false);
}

function getProjects() {
	$.ajax({
		url: "core/projects/get-all-projects.php",
		type: "GET",
		success: (response) => {
			response = $.parseJSON(response);
			response.forEach((project) => {
				let disabled = '';
				if ($(`#job-req-project-${project.ProjectID}`).length > 0) {disabled = 'disabled'}
				let html = `<tr>
					<td>${project.ProjectID}</td>
					<td>${project.ProjectName}</td>
					<td><button ${disabled} id="add-project-requirement" data-id="${project.ProjectID}" onclick="addReviewProjectRequirement('${project.ProjectID}', '${project.ProjectName}', this)" class="btn btn-sm btn-primary">Select</button></td>
				</tr>`;
				$("#modal-projects-list").append(html);
			});
		}
	});
}

function getDuties() {
	$.ajax({
		url: "core/reviews/get-all-duties.php",
		type: "GET",
		success: (response) => {
			response = $.parseJSON(response);
			response.forEach((duty) => {
				let disabled = '';
				if ($(`#job-req-duty-${duty.DutyID}`).length > 0) {disabled = 'disabled'}
				let html = `<tr>
					<td>${duty.DutyID}</td>
					<td>${duty.DutyName}</td>
					<td><button ${disabled} id="add-duty-requirement" data-id="${duty.DutyID}" onclick="addReviewDutyRequirement('${duty.DutyID}', '${duty.DutyName}', this)" class="btn btn-sm btn-primary">Select</button></td>
				</tr>`;
				$("#modal-duties-list").append(html);
			});
		}
	});
}

function getKpis() {
	$.ajax({
		url: "core/kpis/get-all-kpis.php",
		type: "GET",
		success: (response) => {
			kpis = $.parseJSON(response);
			kpis.forEach((kpi) => {
				let disabled = '';
				if ($(`#job-req-kpi-${kpi.KPIID}`).length > 0) {disabled = 'disabled'}
				let html = `<tr>
					<td>${kpi.KPIID}</td>
					<td>${kpi.KPITitle}</td>
					<td>${kpi.CategoryName}</td>
					<td><button ${disabled} id="add-kpi-requirement" data-id="${kpi.KPIID}" onclick="addReviewKpiRequirement('${kpi.KPIID}', '${kpi.KPITitle}', this)" class="btn btn-sm btn-primary">Select</button></td>
				</tr>`;
				$("#modal-kpis-list").append(html);
			});
		}
	});
}

$("#create-project-form").submit(function(event){
	event.preventDefault();
	$.Incipit('show');

	$.ajax({
		url: $(this).attr('action'),
		type: $(this).attr('method'),
		data: $(this).serialize(),
		success: function (response) {
			$.Incipit('hide');
			response = $.parseJSON(response);
			if (response.status) {
				alertify.success('Project Created Successfully');
				let project = response.project;
				let html = `<tr>
					<td>${project.ProjectID}</td>
					<td>${project.ProjectName}</td>
					<td><button disabled id="add-project-requirement" data-id="${project.ProjectID}" onclick="addReviewProjectRequirement('${project.ProjectID}', '${project.ProjectName}', this)" class="btn btn-sm btn-primary">Select</button></td>
				</tr>`;
				$("#modal-projects-list").prepend(html);
				addReviewProjectRequirement(project.ProjectID, project.ProjectName);
			}
			else {
				alertify.error(response.message);
			}
		}
	});
});

$("#create-kpi-form").submit(function(event){
	event.preventDefault();

	if (!$(this).valid()) { return; }
	$.Incipit('show');

	$.ajax({
		url: $(this).attr('action'),
		type: $(this).attr('method'),
		data: $(this).serialize(),
		success: function (response) {
			$.Incipit('hide');
			response = $.parseJSON(response);
			if (response.status) {
				alertify.success('KPI Created Successfully');
				let kpi = response.kpi;
				let html = `<tr>
					<td>${kpi.KPIID}</td>
					<td>${kpi.KPITitle}</td>
					<td>${kpi.CategoryName}</td>
					<td><button disabled id="add-kpi-requirement" data-id="${kpi.KPIID}" onclick="addReviewKpiRequirement('${kpi.KPIID}', '${kpi.KPITitle}', this)" class="btn btn-sm btn-primary">Select</button></td>
				</tr>`;
				$("#modal-kpis-list").prepend(html);
				addReviewKpiRequirement(kpi.KPIID, kpi.KPITitle);
			}
			else {
				alertify.error(response.message);
			}
		}
	});
});

$("#create-duty-form").submit(function(event){
	event.preventDefault();

	if (!$(this).valid()) { return; }
	$.Incipit('show');

	$.ajax({
		url: $(this).attr('action'),
		type: $(this).attr('method'),
		data: $(this).serialize(),
		success: function (response) {
			$.Incipit('hide');
			response = $.parseJSON(response);
			if (response.status) {
				alertify.success('KPI Created Successfully');
				let duty = response.duty;
				let html = `<tr>
					<td>${duty.DutyID}</td>
					<td>${duty.DutyName}</td>
					<td><button disabled id="add-duty-requirement" data-id="${duty.DutyID}" onclick="addReviewDutyRequirement('${duty.DutyID}', '${duty.DutyName}', this)" class="btn btn-sm btn-primary">Select</button></td>
				</tr>`;
				$("#modal-duties-list").prepend(html);
				addReviewDutyRequirement(duty.DutyID, duty.DutyName);
			}
			else {
				alertify.error(response.message);
			}
		}
	});
});



function addReviewKpiRequirement(KPIID, KPITitle, btn = null) {
	if (btn) {$(btn).prop('disabled', true)};
	let html = `
	<div class="row mb-1" class="review-form-kpi" id="job-req-kpi-${KPIID}">
		<div class="col-12 input-group">
			<input type="hidden" name="KPIID[]" value="${KPIID}">
			<input readonly type="text" class="form-control" name="KPITitle[]" value="${KPITitle}">
			<div class="input-group-append">
				<input type="number" name="KPITargetNum[]" class="form-control rounded-0" required min="0" placeholder="Target">
			</div>
			<div class="input-group-append">
				<select name="KPITargetFreq[]" class="form-control rounded-0" required>
					<option value="1">Day</option>
					<option value="2">Month</option>
					<option value="3">Quarter</option>
					<option value="4">Year</option>
					<option value="5">Next Review</option>
				</select>
			</div>
			<div class="input-group-append">
				<span class="input-group-text btn btn-danger rounded-0" onclick="removeJobReqKpi(${KPIID})"><i class="fas fa-multiply"></i></span>
			</div>
		</div>
	</div>
	`;

	$("#review-form-kpis").append(html);
}

function addReviewProjectRequirement(ProjectID, ProjectName, btn = null) {
	if (btn) {$(btn).prop('disabled', true)};
	let html = `
	<div class="row mb-2" id="job-req-project-${ProjectID}">
		<div class="form-group">
			<div class="col-12 input-group">
				<input type="hidden" name="ProjectID[]" value="${ProjectID}">
				<input type="text" readonly class="form-control rounded-0" name="ProjectName[]" value="${ProjectName}">
				<div class="input-group-append">
					<input type="number" name="ProjectQuarter[]" required class="form-control rounded-0" min="1" max="4" placeholder="Quarter" >
				</div>
				<div class="input-group-append">
					<input type="number" name="ProjectYear[]" required class="form-control rounded-0" placeholder="Year" min="2000">
				</div>
				<div class="input-group-append">
					<span class="input-group-text btn btn-danger rounded-0" onclick="removeJobReqProject(${ProjectID})"><i class="fas fa-multiply"></i></span>
				</div>
			</div>
			<div>
				<textarea name="ProjectTarget[]" data-id="project-target-${ProjectID}" class="form-control rounded-0" placeholder="Write Quantifiable target"></textarea>
			</div>
		</div>
	</div>
	`;

	$("#review-form-projects").append(html);
}

function addReviewDutyRequirement(DutyID, DutyName, btn = null) {
	if (btn) {$(btn).prop('disabled', true)};
	let html = `
	<div class="row mb-1" class="review-form-duty" id="job-req-duty-${DutyID}">
		<div class="col-12 input-group">
			<input type="hidden" name="DutyID[]" value="${DutyID}">
			<input readonly type="text" class="form-control" name="DutyName[]" value="${DutyName}">
			<?php if (!$completed) { ?>
			<div class="input-group-append">
				<span class="input-group-text btn btn-danger rounded-0" onclick="removeJobReqDuty(${DutyID})"><i class="fas fa-multiply"></i></span>
			</div>
			<?php } ?>
		</div>
	</div>
	`;

	$("#review-form-duties").append(html);
}

function addBenchmark() {
	let html = `
	<div class="row mb-1 p-1 bg-white" class="review-form-benchmark">
		<div class="col-9">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text btn btn-danger rounded-0" onclick="removeBenchmark(this)"><i class="fas fa-multiply"></i></span>
				</div>
				<input type="text" class="form-control" name="Benchmark[]" placeholder="Salary benchmark" required>
			</div>
		</div>
		<div class="col-3 d-flex justify-content-between align-items-center benchmark-score-div">
			<input type="radio" value="1" required>
			<input type="radio" value="2" required>
			<input type="radio" value="3" required>
			<input type="radio" value="4" required>
			<input type="radio" value="5" required>
		</div>
	</div>
	`;

	$("#review-form-benchmarks").append(html);
	orderBenchmarks();
}

function orderBenchmarks() {
	$.each($('.benchmark-score-div'), function(i, val) { 
		$(val).find("input").attr("name", "score-benchmark-" + i);
	});
}

$("#review-form").submit(function(event){
	event.preventDefault();
	$.Incipit('show');
	var formData = new FormData(document.getElementById("review-form"));
	formData.append('ReviewSubmissionDate', $("input[name=ReviewSubmissionDate]").val());
	formData.append('ReviewCompletionDate', $("input[name=ReviewCompletionDate]").val());
	formData.append('ReviewAction', $("#review-form").attr('data-type'));
	formData.append('ReviewType', $("#review-form").attr('data-review-type'));

	$.ajax({
		url: $("#review-form").attr('action'),
		type: $("#review-form").attr('method'),
		data: formData,
		processData: false,
		contentType: false,
		success: function (response) {
			$.Incipit('hide');
			response = $.parseJSON(response);

			if (response.status) {
				alertify.success(response.message);
				setTimeout(function(){
					document.location.href = "employee-details.php?id=" + response.EmployeeID;
				}, 1000);
			}
			else {
				alertify.error(response.message);
			}
		}
	});
});





