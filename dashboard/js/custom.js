//passing values to the edit modal
$('#editModal').on('show.bs.modal', function(e) {
    var tableData = $(e.relatedTarget).data('table_data');
    $(e.currentTarget).find('input[name="table_data"]').val(tableData); 
    feedEditModal();
});

//Passing the id of the instance to be deleted
$(document).on("click", ".open-DeleteItemDialog", function () {
    var instanceId = $(this).data('table_data');
    $(".modal-body #instance-id").val(instanceId);
});

/**
 * 
 */
function feedEditModal() {
    var instanceId = document.getElementById("instance-id");   
    var field=document.getElementById("field");
    var url = "../includes/interface.php?action=feed_modal&caller=site&instance=" + instanceId+"&field="+field;
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          //response = JSON.parse(xmlhttp.responseText);
//            //read response
//            if (response != null) {
//                viewFname.innerHTML= response.profile.fname+" "+response.profile.lname;
//            } else {
//               // alert("Something went wrong :(");
//            }
        }        
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
    }
