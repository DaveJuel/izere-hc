/**
 * Javascript to interact with the back end
 * @author David NIWEWE
 * @version 0.0.1 
 */
/*
 * TODO: Handle all buttons.
 */

//this is the function to notify
function notifier(status, text) {
    /*
     *0=failure
     *1=success
     *2=pending
     * */
    var output;
    if (status == 0) {
        output = "<span class='alert alert-danger'>" + text + "</span>";
    } else if (status == 1) {
        output = "<span class='alert alert-success'>" + text + "</span>";
    } else if (status == 2) {
        output = "<span class='alert alert-info'><span class='fa fa-spinner fa-pulse'></span>" + text + "</span>";
    } else {
        output = "<span class='alert alert-info'>" + text + "</span>";
    }
    document.getElementById("notification").innerHTML = output;
}
/*
 * ============ LOGIN ===============
 */
$("#login-form").on('submit', function (e) {
    e.preventDefault();
    //Get input field values from HTML form
    var username = $("input[name=log_username]").val();
    var password = $("input[name=log_password]").val();

    //Data to be sent to server
    var post_data;
    var output;
    post_data = {
        'action': "Login",
        'log_username': username,
        'log_password': password
    };
    output = '<div class="alert alert-info"><span class="notification-icon"><i class="glyphicon glyphicon-repeat fast-right-spinner" aria-hidden="true"></i></span><span class="notification-text">Logging in...</span></div>';
    $("#notification").html(output);   
    //Ajax post data to server
    $.post('../includes/interface.php', post_data, function (response) {
        //Response server message
        if (response.type == 'error') {
            output = '<div class="alert alert-danger"><span class="notification-icon"><i class="glyphicon glyphicon-warning-sign" aria-hidden="true"></i></span><span class="notification-text"> ' + response.text + '</span></div>';
        } else if (response.type == "success") {
            window.location.href = "home.php";
        } else {
            output = '<div class="alert alert-warning"><span class="notification-icon"><i class="glyphicon glyphicon-question-sign" aria-hidden="true"></i></span><span class="notification-text"> ' + response.text + '</span></div>';
            //If success clear inputs
            $("input, textarea").val('');
            $('select').val('');
            $('select').val('').selectpicker('refresh');
        }
        $("#notification").html(output);
    }, 'json');
});
//END LOGIN-------------------------------

/*
 * ============= UNLOCK ==================
 */
$("#unlock-form").on('submit', function (e) {
    e.preventDefault();
    //Get input field values from HTML form
    var password = $("input[name=password]").val();

    //Data to be sent to server
    var post_data;
    var output;
    post_data = {
        'action': "Unlock",
        'password': password
    };

    //Ajax post data to server
    $.post('../includes/interface.php', post_data, function (response) {
        //Response server message
        if (response.type == 'error') {
            output = '<div class="alert alert-danger"><span class="notification-icon"><i class="glyphicon glyphicon-warning-sign" aria-hidden="true"></i></span><span class="notification-text">' + response.text + '</span></div>';
        } else if (response.type == "success") {
            window.location.href = "home.php";
        } else {
            output = '<div class="alert alert-warning"><span class="notification-icon"><i class="glyphicon glyphicon-question-sign" aria-hidden="true"></i></span><span class="notification-text">' + response.text + '</span></div>';
            //If success clear inputs
            $("input, textarea").val('');
            $('select').val('');
            $('select').val('').selectpicker('refresh');
        }
        $("#notification").html(output);
    }, 'json');
});
//END UNLOCK------------------------

/*
 * ============= ADD USER ==================
 */
$("#add-user-form").on('submit', function (e) {
    e.preventDefault();
    //Get input field values from HTML form
    var fname = $("input[name=add_user_fname]").val();
    var lname = $("input[name=add_user_lname]").val();
    var oname = $("input[name=add_user_oname]").val();
    var email = $("input[name=add_user_email]").val();
    var phone = $("input[name=add_user_tel]").val();
    var address = $("input[name=add_user_address]").val();
    var userType = $("select[name=add_user_type]").val();
    var username = $("input[name=add_user_username]").val();
    var password = $("input[name=add_user_password]").val();
    var confirmPassword = $("input[name=add_user_password_confirmed]").val();

    //Data to be sent to server
    var post_data;
    var output;
    post_data = {
        'action': "add_user",
        'fname': fname,
        'lname': lname,
        'oname': oname,
        'email': email,
        'phone': phone,
        'address': address,
        'user_type': userType,
        'username': username,
        'password': password,
        'confirm_password': confirmPassword
    };

    //Ajax post data to server
    $.post('../includes/interface.php', post_data, function (response) {
        //Response server message
        if (response.type == 'error') {
            output = '<div class="alert alert-danger"><span class="notification-icon"><i class="glyphicon glyphicon-warning-sign" aria-hidden="true"></i></span><span class="notification-text">' + response.text + '</span></div>';
        } else if (response.type == "success") {
            output = '<div class="alert alert-success"><span class="notification-icon"><i class="glyphicon glyphicon-ok-sign" aria-hidden="true"></i></span><span class="notification-text">' + response.text + '</span></div>';
        } else {
            output = '<div class="alert alert-warning"><span class="notification-icon"><i class="glyphicon glyphicon-question-sign" aria-hidden="true"></i></span><span class="notification-text">' + response.text + '</span></div>';
            //If success clear inputs
            $("input, textarea").val('');
            $('select').val('');
            $('select').val('').selectpicker('refresh');
        }
        $("#notification").html(output);
    }, 'json');
});
//END ADD USER------------------------

//REGISTER
$("#register-form").on("submit", function (e) {
    e.preventDefault();   
    var firstName =  $("input[name=register_fname]").val();
    var lastName =  $("input[name=register_lname]").val();
    var email = $("input[name=register_email]").val();    
    var password = $("input[name=register_password]").val();
    var confirmPassword = $("input[name=confirm_password]").val();
    //Data to be sent to server
    var post_data;
    var output;
    post_data = {
        'action': "sign_up",
        'register_fname': firstName,
        'register_lname': lastName,
        'register_email': email,
        'register_password': password,
        'confirm_password': confirmPassword
    };
    output = '<div class="alert alert-info"><span class="notification-icon"><i class="glyphicon glyphicon-repeat fast-right-spinner" aria-hidden="true"></i></span><span class="notification-text"> Creating account...</span></div>';
    $("#notification").html(output);   
    //Ajax post data to server
    $.post('../includes/interface.php', post_data, function (response) {
        //Response server message
        if (response.type == 'error') {
            output = '<div class="alert alert-danger"><span class="notification-icon"><i class="glyphicon glyphicon-warning-sign" aria-hidden="true"></i></span><span class="notification-text">' + response.text + '</span></div>';
        } else if (response.type == "success") {
            $("input, .form-group").val('');
            output = '<div class="alert alert-success"><span class="notification-icon"><i class="glyphicon glyphicon-ok-sign" aria-hidden="true"></i></span><span class="notification-text">' + response.text + '</span></div>';
        } else {
            output = '<div class="alert alert-warning"><span class="notification-icon"><i class="glyphicon glyphicon-question-sign" aria-hidden="true"></i></span><span class="notification-text">' + response.text + '</span></div>';
            //If success clear inputs
            $("input, textarea").val('');
            $('select').val('');
            $('select').val('').selectpicker('refresh');
        }
        $("#notification").html(output);
    }, 'json');
});
//END REGISTER -------------

//saving the form with ajax
function saveArticle(obj) {
    notifier(2, " Saving...", null);
    var subjectToSave = obj.id;
    var subjectCompositeId = subjectToSave.split("-");
    var articleId = subjectCompositeId[1];
    fetchDataToSave(articleId, "add");
}

/**
 * Updating details of an existing instance of a given subject
 * 
*/
function updateOccurence() {
    notifier(2, "Updating...", document.getElementById("update-notification"));
    var occurenceCompositeDetails = document.getElementById("update-instance-id").value;
    var occurenceSplitDetails = occurenceCompositeDetails.split("-");
    var subjectTitle = occurenceSplitDetails[0];   
    fetchDataToSave(subjectTitle, "update");
    document.getElementById("update-notification").innerHTML="";
}


/**
 * Calling the delete instance action
 * 
*/
function deleteOccurence() {
    var occurenceCompositeDetails = document.getElementById("delete-instance-id").value;
    var occurenceSplitDetails = occurenceCompositeDetails.split("-");
    var subjectTitle = occurenceSplitDetails[0];
    var occurenceId = occurenceSplitDetails[1];
    var dataToPost = "action=delete_instance&content=" + subjectTitle + "&instance_id=" + occurenceId;
    notifier(2, "deleting ...", document.getElementById("delete-notification"));
    postData(dataToPost);
    document.getElementById("delete-notification").innerHTML="";
}


function fetchDataToSave(articleId, action) {
    var dataToPost;
    if (null!== articleId&& null!== action) {
        //get attributes
        var attributeList = null;
        if (null !== articleId) {
            var http = new XMLHttpRequest();
            var url = "../includes/interface.php";
            var params = "action=get_article_attributes&data-set=" + articleId;
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function () { //Call a function when the state changes.
                if (http.readyState === 4 && http.status === 200) {
                    var response = JSON.parse(http.responseText);
                    if (response.type === "success") {
                        var hasFile = false;
                        attributeList = response.attributes;
                        /*
                        Need to make the action reflecting to the form generated
                        */                        
                        if(action==="update"){
                            var occurenceCompositeDetails = document.getElementById("update-instance-id").value;
                            var occurenceSplitDetails = occurenceCompositeDetails.split("-");
                            var subjectTitle = occurenceSplitDetails[0];
                            var occurenceId = occurenceSplitDetails[1];
                            dataToPost = "action=" + action;
                            dataToPost = dataToPost + "&subject_title=" + articleId+"&occurence_id="+occurenceId;
                        }else{
                            dataToPost = "action=save";
                            dataToPost = dataToPost + "&data-set=" + articleId;
                        }                        
                        var fileObject;
                        for (var count = 0; count < attributeList.length; count++) {
                            var dataType = attributeList[count].type;
                            var dataTitle = attributeList[count].name;
                            if (dataType === "file") {
                                hasFile = true;
                                fileObject = document.getElementById(action + "_" + dataTitle).files[0];
                            } else { //save other input types 
                                dataToPost = dataToPost + "&" + dataTitle + "=" + document.getElementById(action + "_" + dataTitle).value;
                            }
                        }
                        if (hasFile) {
                            uploadData(fileObject, dataToPost);
                        } else {
                            postData(dataToPost);
                        }
                    } else {
                        notifier(0, "Unable to read attributes", null);
                    }
                }
            }
            http.send(params);            
        }
    }
}


function postData(formData) {
    console.log("PARAMS: " + formData);
    var http = new XMLHttpRequest();
    var url = "../includes/interface.php";
    http.open("POST", url, true);
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    http.onreadystatechange = function () { //Call a function when the state changes.
        if (http.readyState == 4 && http.status == 200) {
            var response = JSON.parse(http.responseText);
            if (response.type == "success") {
                document.getElementsByTagName('input').value = "";
                document.getElementsByTagName('textarea').value = "";
                notifier(1, response.text);
            } else if(response.type == "error"){
                notifier(0, response.text);
            }else{
                notifier(0, "Error occured saving");
            }
        }
    }
    http.send(formData);
}

function uploadData(fileObj, params) {
    if (params != null && fileObj != null) {
        var fd = new FormData();
        fd.append("image", fileObj);
        var http = new XMLHttpRequest();
        http.upload.addEventListener("progress", progressHandler, false);
        http.addEventListener("load", completeHandler, false);
        http.addEventListener("error", errorHandler, false);
        http.addEventListener("abort", abortHandler, false);
        http.open("POST", "../includes/interface.php?" + params);
        http.send(fd);
    }
}


function progressHandler(event) {
    notifier(2, "Uploading ...");
}

function completeHandler(event) {
    var response = JSON.parse(event.target.responseText);
    if (response.type == "error") {
        notifier(0, response.text);
    } else if (response.type == "success") {
        notifier(1, response.text);
    } else {
        notifier(3, response.text);
    }
}

function errorHandler(event) {
    notifier(0, "Upload failed");
}

function abortHandler(event) {
    notifier(0, "Upload aborted");
}