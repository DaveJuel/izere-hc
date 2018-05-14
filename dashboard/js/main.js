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
            "<option value='select'>Select from</option>";
        attrType.className = "form-control";
        attrType.style = "margin-left:15px;margin-bottom:2px";
        attrType.setAttribute("onchange", "loadComboBox(this)");

        //creating the label for the nullable selection
        var nullLabel = document.createElement("label");
        nullLabel.innerHTML = "Nullable";
        nullLabel.className = "control-label";
        nullLabel.style = "margin-left:15px;margin-bottom:2px";

        //creating radio buttons
        var radioLabelTrue = document.createElement("label");
        radioLabelTrue.className = "checkbox-inline";
        radioLabelTrue.innerHTML = "<input type='radio' name='attr_nullable" + i + "' value='true'>True";

        var radioLabelFalse = document.createElement("label");
        radioLabelFalse.className = "checkbox-inline";
        radioLabelFalse.innerHTML = "<input type='radio' name='attr_nullable" + i + "' value='false'>False";

        //displaying the elements
        container.appendChild(name);
        container.appendChild(attrType);
        container.appendChild(nullLabel);
        container.appendChild(radioLabelTrue);
        container.appendChild(radioLabelFalse);
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
            dataType != "numeric" &&
            dataType != "date" &&
            dataType != "file" &&
            dataType != "long text" &&
            dataType != "select" &&
            dataType != "none")) {
        isTable = true;
    }
    return isTable;
}
//feed combo box
function feedComboBox() {

}

//loading the interface
function loader() {

}
//feed modal
function feedModal() {
    var instance = document.getElementById("instance_value").value;
    var field = document.getElementById("field_value").value;
    var trigger = document.getElementById("btn_trigger");
    document.getElementById("deleteModal_body").innerHTML = "Loading...";
    if (instance != null && field != null) {
        var xmlhttp = new XMLHttpRequest;
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.status == 200 && xmlhttp.readyState == 4) {
                var response = xmlhttp.responseText;
                document.getElementById("deleteModal_body").innerHTML = response;
            }
        };
        xmlhttp.open("GET", "../includes/interface.php?action=feed_modal&instance=" + instance + "&field=" + field, true);
        xmlhttp.send();
    } else {
        //disable button 
        trigger.disabled(true);
    }

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

