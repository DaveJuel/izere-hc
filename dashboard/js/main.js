//this is the function to notify
function notifier(status, text, holder) {
    /*
     *0=failure
     *1=success
     *2=pending
     * */
    if (status == 0) {
        holder.innerHTML = "<span class='alert alert-danger'>" + text + "</span>";
    } else if (status == 1) {
        holder.innerHTML = "<span class='alert alert-success'>" + text + "</span>";
    } else if (status == 2) {
        holder.innerHTML = "<span class='alert alert-info'><span class='fa fa-spinner fa-pulse'></span>" + text + "</span>";
    } else {
        holder.innerHTML = "<span class='alert alert-info'>" + text + "</span>";
    }
}


//adding attribute form
function addAttribute(obj) {
    var attributeNumber = obj.value;
    //display input fields
    var container = document.getElementById("attributes");
    //show the attribute division
    container.style.visibility = "visible";
    // Clear previous contents of the container
    while (container.hasChildNodes()) {
        container.removeChild(container.lastChild);
    }
    for (i = 0; i < attributeNumber; i++) {
        //CREATING THE ELEMENTS
        //name of the attribute
        var name = document.createElement("input");
        name.type = "text";
        name.id = "attr_name" + i;
        name.name = "attr_name" + i;
        name.className = "form-control";
        name.placeholder = "Attribute name";

        //type of the attribute        
        var attrType = document.createElement("select");
        attrType.id = "attr_type" + i;
        attrType.name = "attr_type" + i;
        attrType.onchange = "loadComboBox(this)";
        attrType.innerHTML = "<option value=''>-- Select type --</option>" +
                "<option value='text'>Text</option>" +
                "<option value='numeric'>Numeric</option>" +
                "<option value='date'>Date</option>" +
                "<option value='file'>File</option>" +
                "<option value='long text'>Long text</option>" +
                "<option value='password'>Password</option>" +
                "<option value='select'>Select from</option>";
        attrType.className = "form-control";
        attrType.style = "margin-left:15px;margin-bottom:2px";
        attrType.setAttribute("onchange", "loadComboBox(this)");

        //creating the label for the nullable selection
        var nullLabel = document.createElement("label");
        nullLabel.innerHTML = "N";
        nullLabel.className = "control-label";
        nullLabel.style = "margin-left:15px;margin-bottom:2px";

        //creating radio buttons
        var radioLabelTrue = document.createElement("label");
        radioLabelTrue.className = "checkbox-inline";
        radioLabelTrue.innerHTML = "<input type='radio' name='attr_nullable" + i + "' value='true'>True";

        var radioLabelFalse = document.createElement("label");
        radioLabelFalse.className = "checkbox-inline";
        radioLabelFalse.innerHTML = "<input type='radio' name='attr_nullable" + i + "' value='false'>False";

        //creating the label for the uniqueness selection
        var uniqueLabel = document.createElement("label");
        uniqueLabel.innerHTML = "U";
        uniqueLabel.className = "control-label";
        uniqueLabel.style = "margin-left:15px;margin-bottom:2px";

        //creating radio buttons
        var radioLabelUniqueTrue = document.createElement("label");
        radioLabelUniqueTrue.className = "checkbox-inline";
        radioLabelUniqueTrue.innerHTML = "<input type='radio' name='attr_uniqueness" + i + "' value='true'>True";

        var radioLabelUniqueFalse = document.createElement("label");
        radioLabelUniqueFalse.className = "checkbox-inline";
        radioLabelUniqueFalse.innerHTML = "<input type='radio' name='attr_uniqueness" + i + "' value='false'>False";


        //displaying the elements
        container.appendChild(name);
        container.appendChild(attrType);
        container.appendChild(nullLabel);
        container.appendChild(radioLabelTrue);
        container.appendChild(radioLabelFalse);
        container.appendChild(uniqueLabel);
        container.appendChild(radioLabelUniqueTrue);
        container.appendChild(radioLabelUniqueFalse);
        //append line break
        container.appendChild(document.createElement("br"));
    }
}


/**
 * load combo box
 */
function loadComboBox(obj) {
    var xmlhttp = null;
    var response = null;
    if (obj.value == "select") {
        xmlhttp = new XMLHttpRequest;
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.status == 200 && xmlhttp.readyState == 4) {
                response = xmlhttp.responseText;
                obj.innerHTML = response;
            }
        };
        xmlhttp.open("GET", "../includes/interface.php?action=combo_tables", true);
        xmlhttp.send();
    } else if (isDataTypeTable(obj.value) == true) {
        xmlhttp = new XMLHttpRequest;
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.status == 200 && xmlhttp.readyState == 4) {
                response = xmlhttp.responseText;
                obj.innerHTML = response;
            }
        };
        xmlhttp.open("GET", "../includes/interface.php?action=combo_table_columns&table_name=" + obj.value, true);
        xmlhttp.send();
    } else if (obj.value == "none") {
        obj.innerHTML = "<option value=''>-- Select type --</option>" +
                "<option value='text'>Text</option>" +
                "<option value='unique text'>Unique text</option>" +
                "<option value='numeric'>Numeric</option>" +
                "<option value='date'>Date</option>" +
                "<option value='file'>File</option>" +
                "<option value='long text'>Long text</option>" +
                "<option value='select'>Select from</option>";
    }
}

function isDataTypeTable(dataType) {
    var isTable = false;
    if ((dataType != null) && (dataType != "text" &&
            dataType != "password" &&
            dataType != "numeric" &&
            dataType != "date" &&
            dataType != "file" &&
            dataType != "unique text" &&
            dataType != "long text" &&
            dataType != "select" &&
            dataType != "none")) {
        isTable = true;
    }
    return isTable;
}

//Passing values to the update modal
$(document).on("click", ".open-UpdateItemDialog", function (e) {
    var occurenceCompositeId = $(this).data('table_data');
    var occurenceSplitId = occurenceCompositeId.split("-");
    var subject = occurenceSplitId[0];
    var occurenceId = occurenceSplitId[1];
    $(".modal-body #update-instance-id").val(occurenceCompositeId);
    feedEditModal(subject, occurenceId);
});

//Passing the id of the instance alert(subject,occurenceId);
// to be deleted
$(document).on("click", ".open-DeleteItemDialog", function () {
    var instanceId = $(this).data('table_data');
    $(".modal-body #delete-instance-id").val(instanceId);
});

/**
 * feedEditModal
 * This function is to load the form in the displayed modal
 * @param {String} subject the name of the table to update
 * @param {Integer} occurence_id the id of the instance to be updated  
 */
function feedEditModal(subject, occurence_id) {
    notifier(2, " Loading form", document.getElementById("update-notification"));
    var url = "../includes/interface.php?action=feed_modal&caller=site&subject=" + subject + "&occurence_id=" + occurence_id;
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
            var response = xmlhttp.responseText;
            if (null !== response) {
                document.getElementById("update-notification").innerHTML = "";
                document.getElementById("modal-form-holder").innerHTML = response;
            } else {
                notifier(0, "Internal error", document.getElementById("update-notification"));
            }
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

function uploadList(obj) {

    var file = obj.files[0];
    if (!file) {
        notifier(0, "No file", document.getElementById("upload_status"));
    } else {
        var fd = new FormData();
        fd.append("image", file);
        var aj = new XMLHttpRequest();
        aj.upload.addEventListener("progress", progressHandler, false);
        aj.addEventListener("load", completeHandler, false);
        aj.addEventListener("error", errorHandler, false);
        aj.addEventListener("abort", abortHandler, false);
        aj.open("POST", "../includes/interface.php?action=add_file");
        aj.send(fd);
    }
}


function progressHandler(event) {
    notifier(2, "Uploading ...", document.getElementById("status"));
}

function completeHandler(event) {
    var response = JSON.parse(event.target.responseText);
    if (response.type == "error") {
        notifier(0, response.text, document.getElementById("status"));
    } else if (response.type == "success") {
        document.getElementsByName("image").value = response.filename;
        notifier(1, response.text, document.getElementById("status"));
    } else {
        notifier(3, response.text, document.getElementById("status"));
    }
}

function errorHandler(event) {
    notifier(0, "Upload failed", document.getElementById("status"));
}

function abortHandler(event) {
    notifier(0, "Upload aborted", document.getElementById("upload_status"));
}

