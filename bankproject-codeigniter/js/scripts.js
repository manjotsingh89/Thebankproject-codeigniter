// * Start Bootstrap - Simple Sidebar v6.0.3 (https://startbootstrap.com/template/simple-sidebar)
// * Copyright 2013-2021 Start Bootstrap
// * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-simple-sidebar/blob/master/LICENSE)
// Scripts
// 
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

window.addEventListener('DOMContentLoaded', event => {
    //loading script
    $('body').IncipitInit({
        icon : "solid-snake",
        note : true,
        noteCustom: "Please wait..",
        logo : false,
        logoSrc :'img/logo_sm.png',
        material: false,
        quote: false
    });

    $("tbody").find("tr").addClass("align-middle");

    $(document).on('click', "#create-interview", function(event) {
        event.preventDefault();
        $.Incipit('show');
        let application_id = $(this).attr('data-id');

        $.ajax({
            url: $(this).attr('href'),
            type: 'POST',
            data: {"ApplicationID": application_id, "action": "create_interview"},
            success: (response) => {
                $.Incipit('hide');
                response = $.parseJSON(response);
                if (response.status) {
                    alertify.success("Interview Created Successfully");
                    document.location.href = response.location;
                } else {
                    alertify.error(response.message);
                }
            },
            error: () => {
                $.Incipit('hide');
                alertify.error("Something went wrong while creating interview");
            }
        });
    });

    $(document).on('click', "#hire-btn", function(event) {
        let OfferAcceptDate = $("#hire-form input[name=OfferAcceptDate]").val();
        console.log(OfferAcceptDate);
        if (OfferAcceptDate == null || OfferAcceptDate == '') {
            alertify.error("Please select Offer Accept date.");
            return;
        }
        let ApplicationID = $("#hire-form input[name=ApplicationID]").val();
        let url = $("#hire-form").attr('action');

        $.Incipit('show');
        $.ajax({
            url: url,
            type: 'POST',
            data: {"ApplicationID": ApplicationID, "action": 'hire', "OfferAcceptDate": OfferAcceptDate},
            success: (response) => {
                $.Incipit('hide');
                response = $.parseJSON(response);
                if (response.status) {
                    alertify.success(response.message);
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                } else {
                    alertify.error(response.message);
                }
            },
            error: () => {
                $.Incipit('hide');
                alertify.error("Something went wrong while hiring");
            }
        });
    });

    $(document).on('click', ".hiring", function(event) {
        event.preventDefault();
        let action = $(this).data('action');
        let application_id = $(this).data('id');
        let button = $(this);
        let interview_button = $("#create-interview");

        if (action == '_hire') {
            $("#HireModal input[name=ApplicationID]").val(application_id);
            $("#HireModal .hiring").attr('data-id', application_id);
            $("#HireModal").modal('show');
            return;
        }

        $.Incipit('show');
        $.ajax({
            url: $(this).attr('href'),
            type: 'POST',
            data: {"ApplicationID": application_id, "action": action},
            success: (response) => {
                $.Incipit('hide');
                response = $.parseJSON(response);
                if (response.status) {
                    alertify.success(response.message);
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                } else {
                    alertify.error(response.message);
                }
            },
            error: () => {
                $.Incipit('hide');
                alertify.error("Something went wrong while hiring");
            }
        });
    });


    activeLink = $("div#sidebar a.active").attr("href");
    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
            document.body.classList.toggle('sb-sidenav-toggled');
        }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

    //employee list datatables
    // $('#table-employees').DataTable( {
    //     "order": [[ 0, "desc" ]],
    //     bLengthChange: false,
    //     bFilter: false,
    //     responsive: true
    // });

    //employee list TR link
    $('#table-employees, #table-interviews, #table-jobs, #table-reviews').on('click', 'tbody > tr', function ()
    {
        var link = $(this).attr("data-link");
        window.location.href = link;
    });

    //applications list datatables
    // $('#table-applications').DataTable( {
    //     "order": [[ 0, "desc" ]],
    //     bLengthChange: false,
    //     bFilter: false,
    //     responsive: true
    // });

    //set active tab onload
    $("div#sidebar a[href='"+localStorage.getItem('activeLink')+"']").addClass("active");

    //set active link onclick
    $("div#sidebar a.list-group-item").click(function(){
        var clickedLink = $(this).attr("href");
        setActiveLink($(this), clickedLink);

    });

    //employee details previous and next
    var sections = $("div#forms-controller section");
    $("div#forms-controller section").each(function(e) {
        if (e != 0)
            $(this).hide();
    });

    $.validator.addMethod(
        'correctPhone',
        function (value, element) {
            let ret = false;
            $(".phone:visible").each(function(index, input){
                if (input.name == element.name) {
                    ret = iti[index].isValidNumber();
                    return;
                }
            });
            if (element.name == "SpouseTelephoneNumber" && element.value == "") {ret = true}
            return ret;
        },
        'Invalid input value.'
    );

    function fixStepIndicator(index) {
        $(".application-step").removeClass("active");
        let tabs = $(".application-step");
        $(tabs[index]).addClass("active");
    }
    window.Validator = '';
    $("#next-step").click(function(){
        $("#application-error-box").hide();
        var activePos = $("div#forms-controller section:visible").attr("data-position");
        var formObj   = new Object;
        var table     = $("div#forms-controller section:visible").attr("data-table");
        var type      = $(this).attr("data-type");

        //save record each step
        let form = '#form-employee-'+activePos;

        let rules = {
                TelephoneNumber: {
                    required: true,
                    correctPhone: true,
                },
                SpouseTelephoneNumber: {
                    correctPhone: true,
                },
            };

        if ($(form).find('.AttendedFrom').length > 0) {
            $(form).find('.AttendedFrom').each(function(index, element){
                rules[element.name] = {required: true};
            });
        }

        if ($(form).find('.AttendedTo').length > 0) {
            $(form).find('.AttendedTo').each(function(index, element){
                rules[element.name] = {required: true};
            });
        }

        if ($(form).find('.EmploymentFrom').length > 0) {
            $(form).find('.EmploymentFrom').each(function(index, element){
                rules[element.name] = {required: true};
            });
        }

        if ($(form).find('.EmploymentTo').length > 0) {
            $(form).find('.EmploymentTo').each(function(index, element){
                rules[element.name] = {required: true};
            });
        }

        if (Validator != '') {
            Validator.destroy();
        }

        Validator = $(form).validate({
            errorClass: 'text-danger border-danger',
            errorPlacement: function(error, element) {
                if (element.attr("type") == "checkbox") {
                } else if (element.attr("type") == "tel") {
                    let ele = $(element).parent();
                    error.insertAfter(ele);
                } else {
                    error.insertAfter(element);
                }
            },
            rules: rules
        });

        if (!$(form).valid()) {
            if (activePos == 8) {
                // alertify.error("Declaration required");
                $("#application-error-box").html("Declaration required").show();
            } else {
                $("#application-error-box").html("Please fill in all required fields").show();
                // alertify.error("Kindly fill up the form correctly.");
            }
            return false;
        }
        
        $(document).find(".phone:visible").each(function(index, element){
            $(this).val(iti[index].getNumber());
        })
        if(activePos == "8") {
            
            alertify.confirm('Confirm Application', 'Are you sure you want to submit Application?', function(){
                $.Incipit('show');
                updateEmployee(table, form, true, activePos, type);
                window.location.href = "thank-you.php"
            }, function(){});
        } else {
            $.Incipit('show');
            updateEmployee(table, form, true, activePos, type);

            //this is for the few employee details in educational details section
            if(activePos == 2){
                updateEmployee("Applications", "#form-employee-2half", false, activePos, type);
            }

            return false;
        }
    });

    $("#previous-step").click(function(){

        $.Incipit('show');
        $("#application-error-box").hide();
        $("#next-step").attr("disabled", false);
        $("#next-step").html("Next");

        if ($("div#forms-controller section:visible").prev().length != 0)
            $("div#forms-controller section:visible").prev().show().next().hide();
        else {
            $("div#forms-controller section:visible").hide();
            $("div#forms-controller section:last").show();
        }

        if(!$("div#forms-controller section:visible").prev().attr("data-position")){
            $(this).attr("disabled", true);
        }else{
            
        }

        //hide the loading screen
        $.Incipit('hide');


        return false;
    });
    //end of employee details previous and next

    //interview previous and next
    var sections = $("div#interview-controller section");
    $("div#interview-controller section").each(function(e) {
        if (e != 0)
            $(this).hide();
    });

    $("#int-next-step").click(function(){
        var activePos = $("div#interview-controller section:visible").attr("data-position");
        var form = '#form-int-'+activePos;


        if(activePos ==  13){
            $(this).html("Finalize");
        }else{
            $(this).html("Next");
        }

        $.Incipit('show');

        updateInterview(form, true, activePos);

        return false;
    });

    $("#int-previous-step").click(function(){

        $.Incipit('show');

        $("#int-next-step").attr("disabled", false);

        if ($("div#interview-controller section:visible").prev().length != 0)
            $("div#interview-controller section:visible").prev().show().next().hide();
        else {
            $("div#interview-controller section:visible").hide();
            $("div#interview-controller section:last").show();
        }

        if(!$("div#interview-controller section:visible").prev().attr("data-position")){
            $(this).attr("disabled", true);
        }else{
            
        }

        var activePos = $("div#interview-controller section:visible").attr("data-position");
        getPreviousInterviews(activePos);


        return false;
    });
    //end of interviews previous and next


    $("#save-employee").click(function(){
        $.Incipit('show');
        updateEmployee("Employees", $("#form-employee-details"), false, 1, "update", true);
    })

    $("#delete-employee").click(function(event){
        event.preventDefault();
        $.Incipit('show');
        
        $.ajax({
            url: 'core/delete-employee.php',
            type: 'DELETE',
            dataType: 'json',
            data: {"EmployeeID": $(this).attr('data-id')},
            success: (response) => {
                if (response.status) {
                    alertify.success("Employee Delted");
                    document.location.href = 'index.php';
                } else {
                    alertify.error(response.message);
                }
            },
        });
    });

    $(document).on('click', '.confirm-delete', function(){
        var link = $(this).attr("data-link");
        alertify.confirm('Confirm Delete', 'Are you sure you want to delete?',
            function(){        
                window.location.href = link;
            }, function(){
                console.log("Reject");
        });
    });

    $(document).on('click', "#save-job", function(event){
        event.preventDefault();
        // let description = $("input[name=JobDescription]").val();
        let sales = $("input[name=JobSales]:checked").val() || 0;

        $.ajax({
            url: 'core/update-job.php',
            type: 'POST',
            data: {'JobID': $("#JobID").val(), 'value': [sales], 'column': ['JobSales']},
            success: (response) => {
                response = $.parseJSON(response);
                if (response.status) {
                    // $("#JobDescription").html(description);
                    // $("#JobDescription").show();
                    // $("input[name=JobDescription]").hide();
                    alertify.success(response.message);
                } else {
                    $("#JobDescription").show();
                    $("input[name=JobDescription]").hide();
                    alertify.error(response.message);
                }
            }
        });
    });

    //JOBS


    $("#copy-job").click(function(){
        var id = $(this).attr("data-id");
        alertify.confirm('Confirm Delete', 'Are you sure you want to copy this job?',
            function(){
                window.location.href = "core/save-jobs.php?act=copy&id="+id;
            }, function(){
                console.log("Reject");
        });
    });


    //INTERVIEWS
    $('#interview-instruction-modal').modal('show');
    
    $("#Q47").on('change', function(){
        var x = 0;
        for(var q = 48; q <= 51; q++){
            if($(this).is(':checked')){
                console.log($("span.duties").eq(x).html());
                $("#Q"+q).val($("span.duties").eq(x).html());
            }   else{
                $("#Q"+q).val('');

            }
            x++;
        }
    });

    //job requirements
    $("#CategoryFilter").change(function(){
        var val = $(this).val();

        $("tr.tr-cat").fadeOut(100, function(){
            if(val == ""){
                $("tr.tr-cat").fadeIn();
            }else{
                $("tr.cat_"+val).fadeIn();
            }
        });

        console.log(val)
    });

    var oldValue = "";

    $(document).on("focus", "input.editable", function(){
        oldValue = $(this).val();
    });

    //editable fields
    $(document).on('blur', 'input.editable', function(){
        autoUpdate(this);
    });

    $(document).on('change', "select.editable", function(){
        autoUpdate(this)
    });
    window.unload = function(e) {
      return 'Are you sure you want to leave this page?  You will lose any unsaved data.';
    };

    function autoUpdate(e){
        var idField = $(e).attr("data-idfield");
        var id = $(e).attr("data-id");
        var table = $(e).attr("data-table");
        var field = $(e).attr("data-field");
        var value = $(e).val();

        if(oldValue != value){
            // send the formobject to the backend
            $.ajax({url: "core/auto-update.php",
                type: "POST",
                dataType: 'json',
                data: "submit=autosave&table="+table+"&id="+id+"&field="+field+"&value="+value+"&idfield="+idField,
                success: function(result){
                    if(result == 200){
                        alertify.success("Update successful");
                    }
                }
            });
        }
    }


    //supporting methods
    function setActiveLink(e, link){
        localStorage.setItem('activeLink', link);
        $("div#sidebar a.list-group-item").removeClass("active");
        e.addClass("active");
    }

    $(document).on('submit', '#form-employee-1', function(event){
        event.preventDefault();
        let rules = {
            TelephoneNumber: {
                required: true,
                correctPhone: true,
            },
            SpouseTelephoneNumber: {
                correctPhone: true,
            },
        };

        if (Validator != '') {
            Validator.destroy();
        }

        Validator = $("#form-employee-1").validate({
            errorClass: 'text-danger border-danger',
            errorPlacement: function(error, element) {
                if (element.attr("type") == "checkbox") {
                } else if (element.attr("type") == "tel") {
                    let ele = $(element).parent();
                    error.insertAfter(ele);
                } else {
                    error.insertAfter(element);
                }
            },
            rules: rules
        });

        if (!$("#form-employee-1").valid()) {
            return false;
        }
        let table = $(this).parent().attr('data-table');
        $(document).find(".phone").each(function(index, element){
            $(this).val(iti[index].getNumber());
        })

        $.Incipit('show');
        updateEmployee(table, "#form-employee-1", false, 1, 'update', true);
    });

    $(document).on('submit', '#form-employee-2half, #form-employee-5, #form-employee-6', function(event){
        event.preventDefault();
        let form = "#"+$(this).attr('id');
        if (Validator != '') {
            Validator.destroy();
        }

        Validator = $(form).validate();

        if (!$(form).valid()) {
            return false;
        }
        let table = "Applications";

        $.Incipit('show');
        updateEmployee(table, form, false, 1, 'update', true);
    });

    $(document).on('submit', '#form-employee-3', function(event){
        event.preventDefault();
        let form = "#form-employee-3";
        let rules = {};
        if (Validator != '') {
            Validator.destroy();
        }

        Validator = $(form).validate({
            errorClass: 'text-danger border-danger',
            errorPlacement: function(error, element) {
                if (element.attr("type") == "checkbox") {
                } else if (element.attr("type") == "tel") {
                    let ele = $(element).parent();
                    error.insertAfter(ele);
                } else {
                    error.insertAfter(element);
                }
            },
            rules: rules
        });

        if (!$(form).valid()) {
            return false;
        }
        let table = $(this).parent().attr('data-table');

        $.Incipit('show');
        updateEmployee(table, form, false, 1, 'update', true);
    });

    $(document).on('submit', '#form-employee-4', function(event){
        event.preventDefault();
        let form = "#form-employee-4";
        let rules = {};
        
        if ($(form).find('.EmploymentFrom').length > 0) {
            $(form).find('.EmploymentFrom').each(function(index, element){
                rules[element.name] = {required: true};
            });
        }

        if ($(form).find('.EmploymentTo').length > 0) {
            $(form).find('.EmploymentTo').each(function(index, element){
                rules[element.name] = {required: true};
            });
        }

        if (Validator != '') {
            Validator.destroy();
        }

        Validator = $(form).validate({
            errorClass: 'text-danger border-danger',
            errorPlacement: function(error, element) {
                if (element.attr("type") == "checkbox") {
                } else if (element.attr("type") == "tel") {
                    let ele = $(element).parent();
                    error.insertAfter(ele);
                } else {
                    error.insertAfter(element);
                }
            },
            rules: rules
        });

        if (!$(form).valid()) {
            return false;
        }
        let table = $(this).parent().attr('data-table');

        $.Incipit('show');
        updateEmployee(table, form, false, 1, 'update', true);
    });

    $(document).on('submit', '#form-employee-2', function(event){
        event.preventDefault();
        let rules = {};
        let form = "#form-employee-2";
        if ($(form).find('.AttendedFrom').length > 0) {
            $(form).find('.AttendedFrom').each(function(index, element){
                rules[element.name] = {required: true};
            });
        }

        if ($(form).find('.AttendedTo').length > 0) {
            $(form).find('.AttendedTo').each(function(index, element){
                rules[element.name] = {required: true};
            });
        }

        if (Validator != '') {
            Validator.destroy();
        }

        Validator = $(form).validate({
            errorClass: 'text-danger border-danger',
            errorPlacement: function(error, element) {
                if (element.attr("type") == "checkbox") {
                } else if (element.attr("type") == "tel") {
                    let ele = $(element).parent();
                    error.insertAfter(ele);
                } else {
                    error.insertAfter(element);
                }
            },
            rules: rules
        });

        if (!$(form).valid()) {
            return false;
        }
        let table = $(this).parent().attr('data-table');

        $.Incipit('show');
        updateEmployee(table, form, false, 1, 'update', true);
    });

    $(document).on('submit', '#form-employee-7', function(event){
        event.preventDefault();
        let form = "#"+$(this).attr('id');
        let rules = {};
        if (Validator != '') {
            Validator.destroy();
        }

        Validator = $(form).validate({
            errorClass: 'text-danger border-danger',
            errorPlacement: function(error, element) {
                if (element.attr("type") == "checkbox") {
                } else if (element.attr("type") == "tel") {
                    let ele = $(element).parent();
                    error.insertAfter(ele);
                } else {
                    error.insertAfter(element);
                }
            },
            rules: rules
        });

        if (!$(form).valid()) {
            return false;
        }
        let table = $(this).parent().attr('data-table');
        $(document).find(".phone").each(function(index, element){
            $(this).val(iti[index].getNumber());
        })

        $.Incipit('show');
        updateEmployee(table, form, false, 1, 'update', true);
    });

    function updateEmployee(table, form, next = true, pos, action, emp = false){

        // send the formobject to the backend
        $.ajax({url: "core/save-employee.php",
            type: "POST",
            dataType: 'json',
            data: $(form).serialize()+"&submit="+action+"&table="+table,
            success: (response) => {
                $.Incipit('hide');
                if (response.status) {
                    if (action == "update") {
                        alertify.success(response.message);
                    }
                    if(action == "new"){
                        $("#next-step").attr("data-type", "update");
                    }
                    if(emp === false){
                        if(next){
                            //set previous and next buttons behavior
                            fixStepIndicator(pos);
                            if(pos >= 7){$("#next-step").html("Submit Application")}
                            $("#previous-step").attr("disabled", false);

                            if ($("div#forms-controller section:visible").next().length != 0)
                                $("div#forms-controller section:visible").next().show().prev().hide();
                            else {
                                $("div#forms-controller section:visible").hide();
                                $("div#forms-controller section:first").show();
                                $("div#forms-controller section:first").show();
                            }

                            if(!$("div#forms-controller section:visible").next().attr("data-position")){
                                $(this).attr("disabled", true);
                            }else{
                                $(this).attr("disabled", false);
                            }
                            runPhone();
                        }
                    }
                    
                    let html = `
                    <input type="hidden" name="EmployeeID" value="${response.employee_id}">
                    <input type="hidden" name="ApplicationID" value="${response.application_id}">
                    `;
                    $("body").prepend(html);
                } else {
                    if ($("#application-error-box").length == 0){
                        alertify.error(response.message);
                    } else {
                        $("#application-error-box").html(response.message).show();
                    }
                }

                
                //hide the loading screen
        }});        
    }

    if($("#interview-controller").length > 0){
        getPreviousInterviews(1);
    }

    $(".btn-hire").click(function(){
        var link = $(this).attr("data-link");
        alertify.confirm('Confirm Delete', 'Are you sure you want to hire this applicant?',
            function(){        
                window.location.href = link;
            }, function(){
                console.log("Reject");
        });
    });

    function updateInterview(form, next = true, pos){
        var table = pos == 11 ? 'InterviewRequirementScores' : 'Interviews';
        var iID   = $("#InterviewID").val();
        // send the formobject to the backend
        $.ajax({url: "core/save-interview.php",
            type: "POST",
            dataType: 'json',
            data: $(form).serialize()+"&submit=update&table="+table+"&iID="+iID+"&position="+pos,
            success: function(result){
                if(next){
                    //set previous and next buttons behavior
                    $("#int-previous-step").attr("disabled", false);

                    if ($("div#interview-controller section:visible").next().length != 0)
                        $("div#interview-controller section:visible").next().show().prev().hide();
                    else {
                        $("div#interview-controller section:visible").hide();
                        $("div#interview-controller section:first").show();
                        $("div#interview-controller section:first").show();
                    }

                    if(!$("div#interview-controller section:visible").next().attr("data-position")){
                        $(this).attr("disabled", true);
                    }else{
                        $(this).attr("disabled", false);
                    }

                    if(pos ==  14){
                        alertify.alert('Thank you.', 'Interview Finished. You can now close the window.', function(){ window.location.href = 'index.php' });
                    }

                    getPreviousInterviews(parseInt(pos) + 1);
                }
        }});
    }

    function getPreviousInterviews(position){
        $("div#answers").html("<h3>Loading previous answers...</h3>");

        var interview_id = $("#InterviewID").val();
        var application_id = $("#ApplicationID").val();
        $("#answers").html("");

        $.ajax({url: "core/interview-answers.php",
            type: "POST",
            dataType: 'html',
            data: {"ApplicationID": application_id, "CurrentInterviewID": interview_id, "Position": position},
            success: function(result){
                $("div#answers").html(result);

                $('.collapse').collapse();
            
                //hide the loading screen
                $.Incipit('hide');
        }});  
    }

    var employeeSearchTimeout = null;
    $(document).ready(function(){
        if ($("#search-employee-input").length > 0) {
            if ($("#search-employee-input").val()) {
                let status = $("#status-filter").val();
                getEmployees($("#search-employee-input"), status);
            }
        }

        if ($(".select-interviewer").length > 0) {
            $(".select-interviewer").select2({
                placeholder: "Select interviewers",
                allowClear: true,
                dropdownParent: $('#ApplicationApproveModalBody'),
            });
        }

        if ($(document).find('input[data-type="currency"]').length > 0) {
            $(document).find('input[data-type="currency"]').each((i, element) => {
                formatCurrency($(element), "blur");
            });
        }
    });

    $(document).on('change input', '#search-employee-input, #status-filter', function() {
        let status = $("#status-filter").val();
        let query_string = $("#search-employee-input").val();
        getEmployees(query_string, status);
    });

    $(document).on('submit', "#add-new-employee-form", function(event){
        event.preventDefault();
        $.Incipit('show');
        console.log($("#add-new-employee-form").serialize());
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: (response) => {
                $.Incipit('hide');
                if (response.status) {
                    let employee = response.employee;
                    let row = `<tr data-link="employee-details.php?id=${employee.EmployeeID}">
                        <td>${employee.EmployeeID}</td>
                        <td>${employee.FirstName + ' ' + employee.LastName}</td>
                        <td>${employee.Email}</td>
                        <td>${employee.TelephoneNumber}</td>
                        <td><a href="job-details.php?id=${employee.JobID}">${employee.JobTitleName}</a></td>
                        <td><button class='btn btn-${employee.StatusColor} btn-sm txt-white btn-sm-rounded'>${employee.Status}</button></td>
                        <td><a href="employee-details.php?id=${employee.EmployeeID}" class='btn btn-default'><i class='fas fa-angle-right'></i></a></td>
                    </tr>`;

                    $("#employees-list-container").prepend(row);
                    $("#AddEmployeeModal").modal('hide');
                    alertify.success(response.message);
                } else {
                    alertify.error(response.message);
                }
            },
        });
    });

    $(document).on('click', '.sortable-e', function() {
        $('.sortable-e i').removeClass('active');
        $(this).find('i').addClass('active');
        column = $(this).data('column');
        order = $(this).attr('data-order');

        if (order === 'ASC') {
            $(this).find('i').removeClass('arrow-down').addClass('arrow-up');
        } else {
            $(this).find('i').removeClass('arrow-up').addClass('arrow-down');
        }
        
        let status = $("#status-filter").val();
        let query_string = $("#search-employee-input").val();
        getEmployees(query_string, status, column, order);
        $(this).attr('data-order', order === 'ASC' ? 'DESC' : 'ASC');
    });

    function getEmployees(query_string, status, column = 'EmployeeID', order = 'ASC') {
        if (employeeSearchTimeout) {
            clearTimeout(employeeSearchTimeout);
        }

        employeeSearchTimeout = setTimeout(function(){
            $("#employees-list-container").hide();
            $("#employees-list-loader").show();
            $.ajax({
                url: "core/get-employees.php",
                type: "post",
                data: {"query_string": query_string, "status": status, "column": column, "order": order},
                success: (response) => {
                    let employees = $.parseJSON(response);
                    $("#employees-list-container").html('');
                    if (employees.length > 0) {
                        employees.forEach((employee) => {
                            let row = `<tr data-link="employee-details.php?id=${employee.EmployeeID}">
                                <td>${employee.EmployeeID}</td>
                                <td>${employee.FirstName + ' ' + employee.LastName}</td>
                                <td>${employee.Email}</td>
                                <td>${employee.TelephoneNumber}</td>
                                <td><a href="job-details.php?id=${employee.JobID}">${employee.JobTitleName}</a></td>
                                <td><button class='btn btn-${employee.StatusColor} btn-sm txt-white btn-sm-rounded'>${employee.Status}</button></td>
                                <td><a href="employee-details.php?id=${employee.EmployeeID}" class='btn btn-default'><i class='fas fa-angle-right'></i></a></td>
                            </tr>`;

                            $("#employees-list-container").append(row);
                        });
                    } else {
                        $("#employees-list-container").html('<div class="text-warning p-3"><span>No employees found</span></div>');
                    }
                    $("#employees-list-loader").hide();
                    $("#employees-list-container").show();
                },
            });
        }, 500);
    }

    $(document).on("change", "#CurrentSupervisorSelect", function(){
        setEmployeeAttribute($(this).attr('data-employee-id'), "CurrentSupervisorID", $(this).val());
    });

    $(document).on("change", "#FilledByEmpID", function(){
        setJobAttribute($(this).val(), "FilledByEmpID", $(this).attr('data-employee-id'));
    });

    $(document).on("change", "#employee-general-detail input[name=NextReviewDate]", function(){
        setEmployeeAttribute($(this).attr('data-employee-id'), "NextReviewDate", $(this).val());
    });

    $(document).on("change", "#EmployeeStatusSelect", function(){
        setEmployeeAttribute($(this).attr('data-employee-id'), "EmployeeStatusID", $(this).val());
    });

    function setEmployeeNextReviewDate(element) {
        setEmployeeAttribute($(element).attr('data-employee-id'), "NextReviewDate", $(element).val());
    }

    function setEmployeeAttribute(EmployeeID, column, value) {
        $.Incipit('show');
        $.ajax({
            url: 'core/employees/set-employee-attribute.php',
            type: 'post',
            data: {"EmployeeID": EmployeeID, "column": column, "value": value},
            success: (response) => {
                $.Incipit('hide');
                response = $.parseJSON(response);
                if (response.status) {
                    alertify.success(response.message);
                }
                else {
                    alertify.error(response.message);
                }
            },
            error: () => {
                $.Incipit('hide');
                alertify.error('Something went wrong')
            }
        });
    }

    function setJobAttribute(JobID, column, value) {
        $.Incipit('show');
        $.ajax({
            url: 'core/update-job.php',
            type: "POST",
            data: {"JobID": JobID, "column": column, "value": value},
            success: (response) => {
                $.Incipit('hide');
                response = $.parseJSON(response);
                if (response.status) {
                    alertify.success(response.message);
                    if (column == "JobStatusID" && value != '2') {
                        $("#create-interview").hide();
                        $("#hire-btn").hide();
                    } else if (column == "JobStatusID") {
                        $("#create-interview").show();
                        $("#hire-btn").show();
                    }

                    if (column == 'FilledByEmpID') {
                        $("select[name=FilledByEmpID]").find('option').prop('disabled', true);
                        $("select[name=JobStatusID]").find('option').prop('disabled', true);
                        $("select[name=JobStatusID]").find('option').prop('selected', false);
                        $("select[name=JobStatusID]").find('option[value=3]').prop('selected', true);
                        $("#create-interview").hide();
                        $("#hire-btn").hide();
                    }
                }
                else {
                    alertify.error(response.message);
                }
            },
            error: () => {
                $.Incipit('hide');
                alertify.error('Something went wrong')
            }
        });
    }

    $("#select-job-status").on("change", function(){
        setJobAttribute($(this).attr("data-job-id"), $(this).attr("name"), $(this).val());
    });

    $("#select-job-filled-by").on("change", function(){
        if ($(this).val() == "") {return}
        setJobAttribute($(this).attr("data-job-id"), $(this).attr("name"), $(this).val());
    });

    $(document).on("click", ".delete-job-requirement", function(event){
        event.preventDefault();
        let requirement_id = $(this).attr("data-requirement-id");
        $("tr[id=" + requirement_id + "]").css('opacity', '0.5');

        $.ajax({
            url: 'core/delete-job-requirement.php',
            type: "POST",
            data: {"JobRequirementID": requirement_id},
            success: (response) => {
                $.Incipit('hide');
                response = $.parseJSON(response);
                if (response.status) {
                    $(document).find("tr[id=" + requirement_id + "]").remove();
                    $(document).find(`#${response.requirement_row_id}`).show();
                    alertify.success(response.message);
                }
                else {
                    $(document).find("tr[id=" + requirement_id + "]").css('opacity', '1');
                    alertify.error(response.message);
                }
            },
            error: () => {
                alertify.error('Something went wrong')
            }
        });
    });

    $(document).on('submit', '.create-job-requirement', function (event) {
        event.preventDefault();
        $.Incipit('show');
        let formData = new FormData($(this)[0]);
        let table = $(this).attr("data-table");
        formData.append("table", table);

        $.ajax({
            url: "core/create-job-requirement.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                response = $.parseJSON(response);
                $.Incipit('hide');
                if (!response.status) {
                    alertify.error(response.message);
                    return;
                }

                $(`#table-${table.toLowerCase()}`).find("tbody").prepend(response.requirement);
                $(`#table-requirements-${table}`).find("tbody").prepend(response.job_requirement);
            },
            error: () => {
                $.Incipit('hide');
                alertify.error("Something went wrong");
            }
        });
    });

    $(document).on('click', ".add-job-requirement-btn", function(){
        $.Incipit('show');
        let jobID = $(this).attr('data-job-id');
        let column = $(this).attr('data-column');
        let value = $(this).attr('data-value');

        $.ajax({
            url: "core/add-job-requirement.php",
            type: "POST",
            data: {"column": column, "value": value, "JobID": jobID},
            success: (response) => {
                response = $.parseJSON(response);
                $.Incipit('hide');
                if (!response.status) {
                    alertify.error(response.message);
                    return;
                }

                $(`#${column}-${value}`).hide();
                $(`#table-requirements-${response.table}`).find("tbody").prepend(response.job_requirement);
            },
            error: () => {
                $.Incipit('hide');
                alertify.error("Something went wrong");
            }
        });
    });

    $(".job-requirement-alt").prop('readonly', true);
    $(document).on('focus', ".job-requirement-alt", function() {
        $(this).prop('readonly', false);
        editableValue = $(this).val();
    });

    $(document).on('focusout', ".job-requirement-alt", function(){
        $(this).prop('readonly', true);
        if ($(this).prop('tagName').toLowerCase() != 'select') {
            if (editableValue != $(this).val()) {
                updateJobRequirement($(this).parent().parent().attr('id'), $(this).attr('name'), $(this).val())
            }
        }
    });

    $(document).on('change', ".job-requirement-alt", function(){
        if ($(this).prop('tagName').toLowerCase() == 'select') {
            if (editableValue != $(this).val()) {
                updateJobRequirement($(this).parent().parent().attr('id'), $(this).attr('name'), $(this).val())
            }
        }
    });

    function updateJobRequirement(JobRequirementID, column, value) {
        $.ajax({
            url: 'core/update-job-requirement.php',
            type: 'post',
            data: {"JobRequirementID": JobRequirementID, "column": column, "value": value},
            success: (response) => {
                response = $.parseJSON(response);
                if (!response.status) {
                    alertify.error(response.message);
                }
            },
            error: () => {
                alertify.error('Something went wrong');
            }
        });
    }

    window.editableValue = '';
    
    $("#add-new-employee-form").validate({
        errorClass: 'text-danger border-danger',
        rules: {
            Email: {
                required: true,
                email: true,
                remote: {
                    url: "core/employee-exists.php",
                    type: 'post',
                    data: {
                        Email: function() {
                           return $( "#add-new-employee-form input[name=Email]" ).val();
                        }
                    }
                }
            }
        },
        messages: {
            Email: {
                remote: "Email already exists.",
            }
        }
    });
});


$(window).on('load', function() {
 setTimeout(() => {
    if ($('#application-instructions-modal').length > 0) {
        $('#application-instructions-modal').modal('show');
        runPhone();
    }
 }, 1000);
});

window.iti = [];
var ip = $("meta[name=ip]").attr('content');

function runPhone(){
    $(document).find(".phone:visible").each(function(index, element){
        iti[index] = intlTelInput(element, {
          utilsScript: "build/js/utils.js",
          separateDialCode: true,
          initialCountry: "auto",
          geoIpLookup: function(success, failure) {
            $.ajax({
                url: "http://www.geoplugin.net/json.gp?ip="+ip,
                type: "GET",
                dataType: 'json',
                success: function(resp){
                    var countryCode = (resp && resp.geoplugin_countryCode) ? resp.geoplugin_countryCode : "gb";
                    success(countryCode);
                },
                error: function(){
                    success("gb");
                }
            });
          },
        });
    });
}

$("#add-reference").click(function(){
    let count = $(".reference-row").length;
    let newRow = `
    <tr class="reference-row">
        <td>
            <input type="text" name="ReferenceName[]" class="form-control" placeholder="Enter name">
            <input type="hidden" name="ApplicationReferencesID[]" class="form-control">
        </td>
        <td>
            <textarea type="text" name="Association[]" class="form-control" placeholder="Organization where you worked with this person"></textarea>
        </td>
        <td><input type="email" name="ReferenceEmail[]" class="form-control" placeholder="Email"></td>
        <td><input type="tel" name="ReferenceMobile[]" class="form-control phone"></td>
        <td><input type="number" name="YearsKnown[]" class="form-control" placeholder="Years known" min="0"></td>
    </tr>`;
    $(newRow).appendTo("#tbl-reference");
    $(".reference-row").last().find("input").val("");
    let element = $(".reference-row").last().find("input[type=tel]");
    let inst = intlTelInput(element[0], {
      utilsScript: "build/js/utils.js",
      separateDialCode: true,
      initialCountry: "auto",
      geoIpLookup: function(success, failure) {
        $.ajax({
            url: "http://www.geoplugin.net/json.gp?ip="+ip,
            type: "GET",
            dataType: 'json',
            success: function(resp){
                var countryCode = (resp && resp.geoplugin_countryCode) ? resp.geoplugin_countryCode : "gb";
                success(countryCode);
            },
            error: function(){
                success("gb");
            }
        });
      },
    });

    iti.push(inst);
});


$(document).on("click", "#add-new-school", function(){

    let count = $(".educational-details-row").length;
    let newRow = $(".educational-details-row").first().clone();
    newRow.find("input[type=radio]").attr("name", "IsGraduated_"+count);
    newRow.find(".AttendedFrom").attr("name", `AttendedFrom_${count}`);
    newRow.find(".AttendedTo").attr("name", `AttendedTo_${count}`);
    newRow.appendTo("#educational-details-table");
    $(".educational-details-row").last().find("input:not([type=radio])").val("");
    reassignRadioNames(".educational-details-row");
});

$(document).on("click", "#add-emphistory", function(){
    
    let count = $(".employee-history-row").length;
    let newRow = $(".employee-history-row").first().clone();
    newRow.find(".EmploymentFrom").attr("name", `EmploymentFrom_${count}`);
    newRow.find(".EmploymentTo").attr("name", `EmploymentTo_${count}`);
    newRow.appendTo("#tbl-emphistory");
    $(".employee-history-row").last().find("input:not([type=radio])").val("");
    reassignRadioNames(".employee-history-row");
});

function reassignRadioNames(className){
    $(className).each(function(index, element){
        $(element).find("input[type=radio]").attr("name", "IsGraduated_"+index);
    });

    $(className).each(function(index, element){
        $(element).find(".AttendedFrom").attr("name", `AttendedFrom_${index}`);
        $(element).find(".AttendedTo").attr("name", `AttendedTo_${index}`);
    });

    $(className).each(function(index, element){
        $(element).find(".EmploymentFrom").attr("name", `EmploymentFrom_${index}`);
        $(element).find(".EmploymentTo").attr("name", `EmploymentTo_${index}`);
    });

    if ($(className).length > 1) {
        $(className).find(".remove-row-btn").show();
    } else {
        $(className).find(".remove-row-btn").hide();
    }
}

$(document).on('click', '.remove-row-btn', function(){
    let row = $(this).closest("tr");
    let className = row.attr('class');
    className = className.split(/(\s+)/).filter( function(e) { return e.trim().length > 0; } );
    console.log(className[0]);
    row.remove();
    reassignRadioNames("."+className[0]);
});

$(document).on('change', 'input[name=NationalServiceApplicable]', function(){
    if ($(this).val() == '1') {
        $("#form-employee-3").show();
        $("#form-employee-3").find("input[name=Applicable]").val(1);
    } else {
        $("#form-employee-3").trigger("reset");
        $("#form-employee-3").hide();
        $("#form-employee-3").find("input[name=Applicable]").val(0);
    }
});

$(document).on('submit', '#salary-form', function(event){
    event.preventDefault();
    $.Incipit('show');
    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        dataType: 'json',
        data: $(this).serialize(),
        success: (response) => {
            $.Incipit('hide');
            if (response.status) {
                alertify.success(response.message);
            } else {
                alertify.error(response.message);
            }
        },
        error: () => {
            $.Incipit('hide');
            alertify.error("Something went wrong");
        }
    });
});

$(document).on('click', 'button.app-status', function(){
    let status = $(this).data('status');
    let id = $(this).data('id');
    let it = $("input[name=Interviewers-type]:checked").val();
    $("#ApplicationApproveModal input[name=ApplicationID]").val(id);

    if (status == 'approve') {
        if (it == 'custom') {
            $("#select-interviewer-box").show();
        } else {
            $("#select-interviewer-box").hide();
        }
        $("#ApplicationApproveModal").modal('show');
    }
});

$(document).on('change', 'input[name=Interviewers-type]', function(){
    let it = $("input[name=Interviewers-type]:checked").val();
    if (it == 'custom') {
        $("#select-interviewer-box").show();
    } else {
        $("#select-interviewer-box").hide();
    }
});

$(document).on('click', '#approve-application-btn', function(){
    let it = $("input[name=Interviewers-type]:checked").val();
    console.log(it);
    if (it == 'custom' && $("#approve-app-form select").val().length < 3) {
        alertify.error("Please select at least 3 Interviewers");
        return;
    }

    $("#approve-app-form").submit();
});






$(document).on('click', '#applications-tab', function(){
    let EmployeeID = $(this).data('id');
    getEmployeeApplications(EmployeeID);
});

$(document).on('click', '#reviews-tab', function(){
    let EmployeeID = $(this).data('id');
    getEmployeeReviews(EmployeeID);
});

$(document).on('click', '#interviews-tab', function(){
    let EmployeeID = $(this).data('id');
    getEmployeeInterviews(EmployeeID);
});

$(document).on('click', '#notes-tab', function(){
    let EmployeeID = $(this).data('id');
    getEmployeeNotes(EmployeeID);
});

function getEmployeeNotes(EmployeeID, column = 'EmployeeNoteID', order = 'ASC') {
    $("#table-notes tbody").html('');
    $("#employee-notes-loader").show();
    $.ajax({
        url: `views/employee-notes.php?EmployeeID=${EmployeeID}&column=${column}&order=${order}`,
        type: 'GET',
        success: function(response){
            $("#employee-notes-loader").hide();
            $("#table-notes tbody").html(response);
        }
    });
}

function getEmployeeReviews(EmployeeID, column = 'ReviewID', order = 'ASC') {
    $("#table-my-reviews tbody").html('');
    $("#table-reviews tbody").html('');
    $("#employee-reviews-loader").show();
    $("#employee-my-reviews-loader").show();
    $.ajax({
        url: `views/employee-reviews.php?EmployeeID=${EmployeeID}&column=${column}&order=${order}`,
        type: 'GET',
        dataType: 'json',
        success: function(response){
            $("#employee-reviews-loader").hide();
            $("#employee-my-reviews-loader").hide();
            $("#table-reviews tbody").html(response.reviews);
            $("#table-my-reviews tbody").html(response.my_reviews);
        }
    });
}

function getEmployeeApplications(EmployeeID, column = 'ApplicationID', order = 'ASC') {
    $("#table-applications tbody").html('');
    $("#employee-applications-loader").show();
    $.ajax({
        url: `views/employee-applications.php?EmployeeID=${EmployeeID}&column=${column}&order=${order}`,
        type: 'GET',
        success: function(response){
            $("#employee-applications-loader").hide();
            $("#table-applications tbody").html(response);
        }
    });
}

function getApplications(column = 'ApplicationID', order = 'ASC') {
    $("#table-my-interviews tbody").html('');
    $("#my-interviews-loader").show();
    $.ajax({
        url: `views/my-interviews.php?column=${column}&order=${order}`,
        type: 'GET',
        success: function(response){
            $("#my-interviews-loader").hide();
            $("#table-my-interviews tbody").html(response);
        }
    });
}

function getJobs(column = 'JobID', order = 'ASC') {
    $("#table-jobs tbody").html('');
    $("#jobs-loader").show();
    $.ajax({
        url: `views/jobs.php?column=${column}&order=${order}`,
        type: 'GET',
        success: function(response){
            $("#jobs-loader").hide();
            $("#table-jobs tbody").html(response);
        }
    });
}

function getJobTitles(column = 'JobTitleName', order = 'ASC') {
    $("#table-jobtitles tbody").html('');
    $("#job-titles-loader").show();
    $.ajax({
        url: `views/job-titles.php?column=${column}&order=${order}`,
        type: 'GET',
        success: function(response){
            $("#job-titles-loader").hide();
            $("#table-jobtitles tbody").html(response);
        }
    });
}

function getJobDuties(column = 'DutyID', order = 'ASC') {
    $("#table-duties tbody").html('');
    $("#duties-loader").show();
    $.ajax({
        url: `views/duties.php?column=${column}&order=${order}`,
        type: 'GET',
        success: function(response){
            $("#duties-loader").hide();
            $("#table-duties tbody").html(response);
        }
    });
}
function getJobProjects(column = 'ProjectID', order = 'ASC') {
    $("#table-projects tbody").html('');
    $("#projects-loader").show();
    $.ajax({
        url: `views/projects.php?column=${column}&order=${order}`,
        type: 'GET',
        success: function(response){
            $("#projects-loader").hide();
            $("#table-projects tbody").html(response);
        }
    });
}
function getJobSkills(column = 'SKillID', order = 'ASC') {
    $("#table-skills tbody").html('');
    $("#skills-loader").show();
    $.ajax({
        url: `views/skills.php?column=${column}&order=${order}`,
        type: 'GET',
        success: function(response){
            $("#skills-loader").hide();
            $("#table-skills tbody").html(response);
        }
    });
}
function getJobKPIs(column = 'KPIID', order = 'ASC') {
    $("#table-kpi tbody").html('');
    $("#kpi-loader").show();
    $.ajax({
        url: `views/kpis.php?column=${column}&order=${order}`,
        type: 'GET',
        success: function(response){
            $("#kpi-loader").hide();
            $("#table-kpi tbody").html(response);
        }
    });
}

function getEmployeeInterviews(EmployeeID, column = 'FirstName', order = 'ASC') {
    $("#table-interviews tbody").html('');
    $("#employee-interviews-loader").show();
    $.ajax({
        url: `views/employee-interviews.php?EmployeeID=${EmployeeID}&column=${column}&order=${order}`,
        type: 'GET',
        success: function(response){
            $("#employee-interviews-loader").hide();
            $("#table-interviews tbody").html(response);
        }
    });
}

$(document).on('click', '.sortable', function() {
    $('.sortable i').removeClass('active');
    $(this).find('i').addClass('active');
    let column = $(this).data('column');
    let order = $(this).attr('data-order');
    let table = $(this).closest('table');
    let table_id = table.attr('id');

    if (order === 'ASC') {
        $(this).find('i').removeClass('arrow-down').addClass('arrow-up');
    } else {
        $(this).find('i').removeClass('arrow-up').addClass('arrow-down');
    }

    if (table_id == 'table-interviews') {
        getEmployeeInterviews(table.attr('data-id'), column, order);
    } else if (table_id == "table-applications") {
        getEmployeeApplications(table.data('id'), column, order);
    } else if (table_id == "table-my-reviews" || table_id == "table-reviews" ) {
        getEmployeeReviews(table.data('id'), column, order);
    } else if (table_id == "table-notes") {
        getEmployeeNotes(table.data('id'), column, order);
    } else if (table_id == "table-my-interviews") {
        getApplications(column, order);
    } else if (table_id == "table-jobs") {
        getJobs(column, order);
    } else if (table_id == "table-jobtitles") {
        getJobTitles(column, order);
    } else if (table_id == "table-duties") {
        getJobDuties(column, order);
    } else if (table_id == "table-projects") {
        getJobProjects(column, order);
    } else if (table_id == "table-skills") {
        getJobSkills(column, order);
    } else if (table_id == "table-kpi") {
        getJobKPIs(column, order);
    }
    
    $(this).attr('data-order', order === 'ASC' ? 'DESC' : 'ASC');
});
















// Jquery Dependency

$(document).on({
    keyup: function() {
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
}, "input[data-type='currency']");


function formatNumber(n) {
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}


function formatCurrency(input, blur) {
  // appends $ to value, validates decimal side
  // and puts cursor back in right position.
  
  // get input value
  var input_val = input.val();
  
  // don't validate empty input
  if (input_val === "") { return; }
  
  // original length
  var original_len = input_val.length;

  // initial caret position 
  var caret_pos = input.prop("selectionStart");
    
  // check for decimal
  if (input_val.indexOf(".") >= 0) {

    // get position of first decimal
    // this prevents multiple decimals from
    // being entered
    var decimal_pos = input_val.indexOf(".");

    // split number by decimal point
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);

    // add commas to left side of number
    left_side = formatNumber(left_side);

    // validate right side
    right_side = formatNumber(right_side);
    
    // On blur make sure 2 numbers after decimal
    if (blur === "blur") {
      right_side += "00";
    }
    
    // Limit decimal to only 2 digits
    right_side = right_side.substring(0, 2);

    // join number by .
    input_val = "$" + left_side + "." + right_side;

  } else {
    // no decimal entered
    // add commas to number
    // remove all non-digits
    input_val = formatNumber(input_val);
    input_val = "$" + input_val;
    
    // final formatting
    if (blur === "blur") {
      input_val += ".00";
    }
  }
  
  // send updated string to input
  input.val(input_val);

  // put caret back in the right position
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}