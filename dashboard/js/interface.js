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
$("register-form").on("submit", function (e) {
    e.preventDefault();
    var fname = $("input[name=register_fname]").val();
    var lname = $("input[name=register_lname]").val();
    var email = $("input[name=register_email]").val();
    var username = $("input[name=register_username]").val();
    var password = $("input[name=register_password]").val();
    var confirmPassword = $("input[name=confirm_password]").val();

    //Data to be sent to server
    var post_data;
    var output;
    post_data = {
        'action': "add_user",
        'fname': fname,
        'lname': lname,
        'oname': oname,
        'email': email,
        'phone': "",
        'address': "",
        'user_type': 0,
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
//END REGISTER -------------

/*========== SAVE FORM ==========*/
$(".save-article").on('click', function (e) {
    e.preventDefault();
    notifier(2, "Saving...");
    var form_data = {};
    var get_attr_param = {};
    var articleId;
    //getting the article id
    articleId = document.getElementById("article_id").value;
    if (articleId != null) {
        var data_for_get_article_attributes;
        data_for_get_article_attributes = {
            'action': "get_article_attributes",
            'article_id': articleId
        };
        $.post('../includes/interface.php', data_for_get_article_attributes, function (response_get_attributes) {
            if (response_get_attributes.type == 'success') {
                //getting the list of attributes related to the article id
                attributeList = response_get_attributes.attributes;
                form_data["action"] = "save";
                form_data["article"] = articleId;
                //fetch form content
                for (var count = 0; count < attributeList.length; count++) {
                    var dataType = attributeList[count].type;
                    var dataTitle = attributeList[count].name;
                    if (dataType == "file") { //upload file

                    } else { //save other input types
                        alert("dataTitle--" + dataTitle + " value---" + document.getElementById("attribute_" + dataTitle).value);
                        form_data[dataTitle] = document.getElementById("attribute_" + dataTitle).value;
                    }
                }
                //save form data 
                if (form_data != null && form_data.length > 0) {
                    $.post('../includes/interface.php', form_data, function (response) {
                        //Response server message
                        if (response.type == 'error') {
                            output = '<div class="alert alert-danger"><span class="notification-icon"><i class="glyphicon glyphicon-warning-sign" aria-hidden="true"></i></span><span class="notification-text">' + response_save.text + '</span></div>';
                        } else if (response.type == "success") {
                            output = '<div class="alert alert-success"><span class="notification-icon"><i class="glyphicon glyphicon-ok-sign" aria-hidden="true"></i></span><span class="notification-text">' + response_save.text + '</span></div>';
                        } else {
                            output = '<div class="alert alert-warning"><span class="notification-icon"><i class="glyphicon glyphicon-question-sign" aria-hidden="true"></i></span><span class="notification-text">' + response_save.text + '</span></div>';
                            //If success clear inputs
                            $("input, textarea").val('');
                            $('select').val('');
                            $('select').val('').selectpicker('refresh');
                        }
                        $("#notification").html(output);
                    }, 'json');
                }
            }
        }, 'json');
    }
});

function fetchDataToSave(articleId) {
    var dataToPost;
    if (articleId != null) {
        //get attributes
        var attributeList = null;
        if (articleId != null) {
            var http = new XMLHttpRequest();
            var url = "../includes/interface.php";
            var params = "action=get_article_attributes&article_id=" + articleId;
            http.open("POST", url, true);
            http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            http.onreadystatechange = function () { //Call a function when the state changes.
                if (http.readyState == 4 && http.status == 200) {
                    var response = JSON.parse(http.responseText);
                    if (response.type == "success") {
                        var hasFile = false;
                        attributeList = response.attributes;
                        dataToPost = "action=save";
                        dataToPost = dataToPost + "&article=" + articleId;
                        var fileObject;
                        for (var count = 0; count < attributeList.length; count++) {
                            var dataType = attributeList[count].type;
                            var dataTitle = attributeList[count].name;
                            if (dataType == "file") {
                                hasFile = true;
                                fileObject = document.getElementById("add_" + dataTitle).files[0];
                            } else { //save other input types 
                                dataToPost = dataToPost + "&" + dataTitle + "=" + document.getElementById("add_" + dataTitle).value;
                            }
                        }
                        if (hasFile) {
                            uploadData(fileObject, dataToPost);
                        } else {
                            postData(dataToPost);
                        }
                    } else {
                        notifier(0, "Unable to read attributes");
                    }
                }
            }
            http.send(params);
        }
    }
}

//saving the form with ajax
function saveArticle() {
    //get the article id
    notifier(2, " Saving...");
    var articleId = document.getElementById("article_id").value;
    fetchDataToSave(articleId);
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
            } else {
                notifier(0, "Failed to save");
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