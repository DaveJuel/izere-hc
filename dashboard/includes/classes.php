<?php

session_start();
/**
 * Created on: 30th September 2016
 * Created by: David NIWEWE
 *
 */
/*
 * TODO: You need to replace username and password by your specific db credentials
 */
require 'rb.php';
require 'config.php';
require 'sectionFormat.php';
$connection = new connection();
R::setup("mysql:host=$connection->host;dbname=$connection->db", "$connection->db_user", "$connection->pass_phrase");
R::ext('xdispense', function ($type) {
    return R::getRedBean()->dispense($type);
});
$main = new main();
class UIfeeders
{

    public $instance;
    public $field;

    /**
     * <h1>comboBuilder</h1>
     * <p>
     * This method is to generate a combo box for select input.
     * </p>
     * @param Array $content The content to display in the array.
     * @param String $defValue The value to hold
     * @param String $defDisplay The value to display
     */
    public function comboBuilder($content, $defValue, $defDisplay)
    {
        if (count($content) > 1) {
            echo "<option>-- Select " . strtolower(str_replace("_", " ", $defValue)) . "--</option>";
        }
        for ($count = 0; $count < count($content); $count++) {
            $value = $content[$count][$defValue];
            $display = $content[$count][$defDisplay];
            echo "<option value='$value' >$display</option>";
        }
        echo "<option value='none'>None</option>";
    }

    /**
     * <h1>feedModal</h1>
     * <p>This method is to generate the form for editing content</p>
     * @param String $instance The instance to edit
     * @param String $field Description
     */
    public function feedModal($instance, $subject)
    {
        $this->instance = $instance;
        $this->field = $subject;
        $component = new main();
        $component->formBuilder($subject, "update");
    }

    /**
     * <h1>isDataTypeTable</h1>
     * <p>Verifies if datatype is table</p>
     * @param String $dataType The data type to be verified
     */
    public function isDataTypeTable($dataType)
    {
        $isTable = false;
        $mainObj = new main();
        $schema = $mainObj->dbname;
        if (isset($dataType)) {
            try {
                $tableList = R::getAll("SELECT TABLE_NAME
                                FROM INFORMATION_SCHEMA.TABLES
                                WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='$schema'");
                if (count($tableList) > 0) {
                    for ($count = 0; $count < count($tableList); $count++) {
                        if ($tableList[$count]['TABLE_NAME'] == $dataType) {
                            $isTable = true;
                            break;
                        }
                    }
                }
            } catch (Exception $exc) {
                error_log("ERROR(UIFeeders:isDataTypeTable)");
            }
        }
        return $isTable;
    }

    /**
     * <h1>isDataTypeColumn</h1>
     * <p>Verifies if datatype is column</p>
     * @param String $dataType the data type to be verified
     */
    public function isDataTypeColumn($dataType)
    {
        $isColumn = false;
        if (isset($dataType)) {
            try {
                $columnList = R::getAll("SELECT COLUMN_NAME
                                      FROM INFORMATION_SCHEMA.COLUMNS");
                if (count($columnList) > 0) {
                    for ($count = 0; $count < count($columnList); $count++) {
                        if ($columnList[$count]['COLUMN_NAME'] == $dataType) {
                            $isColumn = true;
                            break;
                        }
                    }
                }
            } catch (Exception $exc) {
                error_log("ERROR(UIFeeders:isDataTypeTable)" . $exc);
            }
        }
        return $isColumn;
    }

    /**
     * <h1>isDataTypeDefault</h1>
     * <p>Verifies if data type is valid</p>
     * @param String $dataType the data type to be verified
     */
    public function isDataTypeDefault($dataType)
    {
        $isDefault = false;
        $dataType = strtolower($dataType);
        if (isset($dataType) &&
            ($dataType == "text" || $dataType == "numeric" || $dataType == "date") || $dataType == "file" || $dataType == "long text") {
            $isDefault = true;
        }
        return $isDefault;
    }

}

/**
 * <h1>main</h1>
 * <p>This is the main method with all utilities used by the application.</p>
 * <p>It extends {@link UIfeeders The class that handles UI content}</p>
 */
class main extends UIfeeders
{

    public $status;
    public $appName = APP_NAME;
    public $author = APP_AUTHOR;

    public function __construct()
    {
        $connection = new connection();
        $this->dbname = $connection->db;
    }

    /**
     * <h1>feedbackFormat</h1>
     * <p>This method is to format for performed action</p>
     * @param Integer $status The status of the message
     * @param String $text the message to be displayed on the screen
     */
    public function feedbackFormat($status, $text)
    {
        $feedback = "";
        /*
         * status = 0 => failure
         * status = 1 => success
         * status = 2 => pending
         */
        switch ($status) {
            case 0:
                $feedback = json_encode(array('type' => 'error', 'text' => $text));
                break;
            case 1:
                $feedback = json_encode(array('type' => 'success', 'text' => $text));
                break;
            case 3:
                $feedback = json_encode(array('type' => 'message', 'text' => $text));
                break;
            default:
                $feedback = json_encode(array('type' => 'error', 'text' => "No response found"));
                break;
        }
        return $feedback;
    }

    public function displayMessageTable($header, $message, $action)
    {
        /*
         * Start table
         */
        echo '<div class="col-md-10">
                <div class="mailbox-content">
                    <table class="table">';
        /*
         * Display headers
         */
        echo '<thead>';
        echo ' <tr> <th colspan="1" class="hidden-xs">
                            <span><input type="checkbox" class="check-mail-all"></span>
                    </th>
                    <th class="text-right" colspan="5">
                            <a class="btn btn-default m-r-sm" data-toggle="tooltip" data-placement="top" title="Refresh"><i class="fa fa-refresh"></i></a>
                            <div class="btn-group">
                                <a class="btn btn-default"><i class="fa fa-angle-left"></i></a>
                                <a class="btn btn-default"><i class="fa fa-angle-right"></i></a>
                            </div>
                        </th>
                    </tr>';
        echo '<thead>';
        /*
         * Table content
         */
        echo '<tbody>';
        for ($count = 0; $count < count($message); $count++) {
            $sender = $message[$count]['sender'];
            $content = $message[$count]['message'];
            $time = $message[$count]['created_on'];
            $status = $message[$count]['status'];
            if (isset($action)) {
                $link = "read.php?action=" . $action . "&content=" . $message[$count]['content'] . "&ref=" . $message[$count]['id'];
            } else {
                $link = "read.php?content=" . $message[$count]['content'] . "&ref=" . $message[$count]['id'];
            }

            echo '<tr class="' . $status . '">
                        <td class="hidden-xs">
                            <span><input type="checkbox" class="checkbox-mail"></span>
                        </td>
                        <td class="hidden-xs">
                            <i class="fa fa-star icon-state-warning"></i>
                        </td>
                        <td class="hidden-xs">
                            ' . $sender . '
                        </td>
                        <td>
                            <a href="' . $link . '">' . $content . '</a>
                        </td>
                        <td>
                        </td>
                        <td>
                            ' . $time . '
                        </td>
                    </tr>';
        }
        echo '</tbody>';
        /*
         * end table
         */
        echo '</table>
                </div>
            </div>';
    }

    /**
     * <h1>displayTable</h1>
     * <p>displaying a table</p>
     * @param Array $header Headers of the table
     * @param Array $body Content of the table
     * @param Boolean $action Set to true to activate editing or delete
     */
    public function displayTable($header, $body, $action)
    {
        /*
         * start table
         */
        echo "<div class='panel-body'>";
        echo "<div class='table-responsive'>";
        echo "<table class='display table table-striped table-bordered table-hover' style='width: 100%; cellspacing: 0;' id='example'>";

        /*
         * display headers
         */
        echo "<thead>";
        for ($count = 0; $count < count($header); $count++) {
            echo "<th>" . $header[$count] . "</th>";
        }
        if (!isset($action) || $action == true) {
            echo '<th>Action</th>';
        }
        echo "</thead>";
        /*
         * table body
         */
        echo "<tbody>";
        for ($row = 0; $row < count($body); $row++) { //row
            echo "<tr>";
            for ($col = 1; $col <= count($header); $col++) {
                echo "<td>" . $body[$row][$col] . "</td>";
            }
            //action
            if (!isset($action) || $action == true) {
                $this->tableAction($body[$row][1]);
            }
            echo "</tr>";
        }
        echo "</tbody>";
        /*
         * end table
         */
        echo "</table>";
        echo "</div>";
        echo "</div>";
    }

    /**
     * <h1>tableAction</h1>
     * <p>This method defines the action on each table item.</p>
     * @param Integer $rowId The  id of the item on the table ID
     */
    private function tableAction($rowId)
    {
        echo "<td>" .
            "<a class='btn btn-info' data-toggle='modal' data-target='#editModal' title='Edit' data-table_data='$rowId'>
		 <i class='fa fa-pencil fa-fw'></i>
		</a>  " . "  <a class='open-DeleteItemDialog btn btn-danger' data-toggle='modal' data-target='#deleteModal' title='Remove'  data-table_data='$rowId'>
		<i class='fa fa-remove fa-fw'></i>
		</a>" .
            "</td>";
    }

    /**
     * <h1>makeLinks</h1>
     * <p>This is the method that generates links for the application.</p>
     * @param String $action This is the action assigned to the link.
     */
    public function makeLinks($action)
    {
        try {
            $subjects = R::getAll("SELECT id,title FROM subject ");
            if (count($subjects) > 0) {
                for ($count = 0; $count < count($subjects); $count++) {
                    $subjectId = $subjects[$count]['id'];
                    $subjectTitle = $subjects[$count]['title'];
                    $user = new user();
                    if ($user->isUserAllowed($action, $subjectTitle)) {
                        echo "<li><a href='" . $action . "_article.php?article=$subjectId'>" . $subjectTitle . "</a></li>";
                    }
                }
            }
        } catch (Exception $e) {
            error_log("MAIN(makeLinks):" . $e);
        }
    }

    /**
     * <h1>listCountries</h1>
     * <p>Generating the list of countries</p>
     */
    public function listCountries()
    {
        $countries = array();
        $countries[] = array("code" => "AF", "name" => "Afghanistan", "d_code" => "+93");
        $countries[] = array("code" => "AL", "name" => "Albania", "d_code" => "+355");
        $countries[] = array("code" => "DZ", "name" => "Algeria", "d_code" => "+213");
        $countries[] = array("code" => "AS", "name" => "American Samoa", "d_code" => "+1");
        $countries[] = array("code" => "AD", "name" => "Andorra", "d_code" => "+376");
        $countries[] = array("code" => "AO", "name" => "Angola", "d_code" => "+244");
        $countries[] = array("code" => "AI", "name" => "Anguilla", "d_code" => "+1");
        $countries[] = array("code" => "AG", "name" => "Antigua", "d_code" => "+1");
        $countries[] = array("code" => "AR", "name" => "Argentina", "d_code" => "+54");
        $countries[] = array("code" => "AM", "name" => "Armenia", "d_code" => "+374");
        $countries[] = array("code" => "AW", "name" => "Aruba", "d_code" => "+297");
        $countries[] = array("code" => "AU", "name" => "Australia", "d_code" => "+61");
        $countries[] = array("code" => "AT", "name" => "Austria", "d_code" => "+43");
        $countries[] = array("code" => "AZ", "name" => "Azerbaijan", "d_code" => "+994");
        $countries[] = array("code" => "BH", "name" => "Bahrain", "d_code" => "+973");
        $countries[] = array("code" => "BD", "name" => "Bangladesh", "d_code" => "+880");
        $countries[] = array("code" => "BB", "name" => "Barbados", "d_code" => "+1");
        $countries[] = array("code" => "BY", "name" => "Belarus", "d_code" => "+375");
        $countries[] = array("code" => "BE", "name" => "Belgium", "d_code" => "+32");
        $countries[] = array("code" => "BZ", "name" => "Belize", "d_code" => "+501");
        $countries[] = array("code" => "BJ", "name" => "Benin", "d_code" => "+229");
        $countries[] = array("code" => "BM", "name" => "Bermuda", "d_code" => "+1");
        $countries[] = array("code" => "BT", "name" => "Bhutan", "d_code" => "+975");
        $countries[] = array("code" => "BO", "name" => "Bolivia", "d_code" => "+591");
        $countries[] = array("code" => "BA", "name" => "Bosnia and Herzegovina", "d_code" => "+387");
        $countries[] = array("code" => "BW", "name" => "Botswana", "d_code" => "+267");
        $countries[] = array("code" => "BR", "name" => "Brazil", "d_code" => "+55");
        $countries[] = array("code" => "IO", "name" => "British Indian Ocean Territory", "d_code" => "+246");
        $countries[] = array("code" => "VG", "name" => "British Virgin Islands", "d_code" => "+1");
        $countries[] = array("code" => "BN", "name" => "Brunei", "d_code" => "+673");
        $countries[] = array("code" => "BG", "name" => "Bulgaria", "d_code" => "+359");
        $countries[] = array("code" => "BF", "name" => "Burkina Faso", "d_code" => "+226");
        $countries[] = array("code" => "MM", "name" => "Burma Myanmar", "d_code" => "+95");
        $countries[] = array("code" => "BI", "name" => "Burundi", "d_code" => "+257");
        $countries[] = array("code" => "KH", "name" => "Cambodia", "d_code" => "+855");
        $countries[] = array("code" => "CM", "name" => "Cameroon", "d_code" => "+237");
        $countries[] = array("code" => "CA", "name" => "Canada", "d_code" => "+1");
        $countries[] = array("code" => "CV", "name" => "Cape Verde", "d_code" => "+238");
        $countries[] = array("code" => "KY", "name" => "Cayman Islands", "d_code" => "+1");
        $countries[] = array("code" => "CF", "name" => "Central African Republic", "d_code" => "+236");
        $countries[] = array("code" => "TD", "name" => "Chad", "d_code" => "+235");
        $countries[] = array("code" => "CL", "name" => "Chile", "d_code" => "+56");
        $countries[] = array("code" => "CN", "name" => "China", "d_code" => "+86");
        $countries[] = array("code" => "CO", "name" => "Colombia", "d_code" => "+57");
        $countries[] = array("code" => "KM", "name" => "Comoros", "d_code" => "+269");
        $countries[] = array("code" => "CK", "name" => "Cook Islands", "d_code" => "+682");
        $countries[] = array("code" => "CR", "name" => "Costa Rica", "d_code" => "+506");
        $countries[] = array("code" => "CI", "name" => "Côte d'Ivoire", "d_code" => "+225");
        $countries[] = array("code" => "HR", "name" => "Croatia", "d_code" => "+385");
        $countries[] = array("code" => "CU", "name" => "Cuba", "d_code" => "+53");
        $countries[] = array("code" => "CY", "name" => "Cyprus", "d_code" => "+357");
        $countries[] = array("code" => "CZ", "name" => "Czech Republic", "d_code" => "+420");
        $countries[] = array("code" => "CD", "name" => "Democratic Republic of Congo", "d_code" => "+243");
        $countries[] = array("code" => "DK", "name" => "Denmark", "d_code" => "+45");
        $countries[] = array("code" => "DJ", "name" => "Djibouti", "d_code" => "+253");
        $countries[] = array("code" => "DM", "name" => "Dominica", "d_code" => "+1");
        $countries[] = array("code" => "DO", "name" => "Dominican Republic", "d_code" => "+1");
        $countries[] = array("code" => "EC", "name" => "Ecuador", "d_code" => "+593");
        $countries[] = array("code" => "EG", "name" => "Egypt", "d_code" => "+20");
        $countries[] = array("code" => "SV", "name" => "El Salvador", "d_code" => "+503");
        $countries[] = array("code" => "GQ", "name" => "Equatorial Guinea", "d_code" => "+240");
        $countries[] = array("code" => "ER", "name" => "Eritrea", "d_code" => "+291");
        $countries[] = array("code" => "EE", "name" => "Estonia", "d_code" => "+372");
        $countries[] = array("code" => "ET", "name" => "Ethiopia", "d_code" => "+251");
        $countries[] = array("code" => "FK", "name" => "Falkland Islands", "d_code" => "+500");
        $countries[] = array("code" => "FO", "name" => "Faroe Islands", "d_code" => "+298");
        $countries[] = array("code" => "FM", "name" => "Federated States of Micronesia", "d_code" => "+691");
        $countries[] = array("code" => "FJ", "name" => "Fiji", "d_code" => "+679");
        $countries[] = array("code" => "FI", "name" => "Finland", "d_code" => "+358");
        $countries[] = array("code" => "FR", "name" => "France", "d_code" => "+33");
        $countries[] = array("code" => "GF", "name" => "French Guiana", "d_code" => "+594");
        $countries[] = array("code" => "PF", "name" => "French Polynesia", "d_code" => "+689");
        $countries[] = array("code" => "GA", "name" => "Gabon", "d_code" => "+241");
        $countries[] = array("code" => "GE", "name" => "Georgia", "d_code" => "+995");
        $countries[] = array("code" => "DE", "name" => "Germany", "d_code" => "+49");
        $countries[] = array("code" => "GH", "name" => "Ghana", "d_code" => "+233");
        $countries[] = array("code" => "GI", "name" => "Gibraltar", "d_code" => "+350");
        $countries[] = array("code" => "GR", "name" => "Greece", "d_code" => "+30");
        $countries[] = array("code" => "GL", "name" => "Greenland", "d_code" => "+299");
        $countries[] = array("code" => "GD", "name" => "Grenada", "d_code" => "+1");
        $countries[] = array("code" => "GP", "name" => "Guadeloupe", "d_code" => "+590");
        $countries[] = array("code" => "GU", "name" => "Guam", "d_code" => "+1");
        $countries[] = array("code" => "GT", "name" => "Guatemala", "d_code" => "+502");
        $countries[] = array("code" => "GN", "name" => "Guinea", "d_code" => "+224");
        $countries[] = array("code" => "GW", "name" => "Guinea-Bissau", "d_code" => "+245");
        $countries[] = array("code" => "GY", "name" => "Guyana", "d_code" => "+592");
        $countries[] = array("code" => "HT", "name" => "Haiti", "d_code" => "+509");
        $countries[] = array("code" => "HN", "name" => "Honduras", "d_code" => "+504");
        $countries[] = array("code" => "HK", "name" => "Hong Kong", "d_code" => "+852");
        $countries[] = array("code" => "HU", "name" => "Hungary", "d_code" => "+36");
        $countries[] = array("code" => "IS", "name" => "Iceland", "d_code" => "+354");
        $countries[] = array("code" => "IN", "name" => "India", "d_code" => "+91");
        $countries[] = array("code" => "ID", "name" => "Indonesia", "d_code" => "+62");
        $countries[] = array("code" => "IR", "name" => "Iran", "d_code" => "+98");
        $countries[] = array("code" => "IQ", "name" => "Iraq", "d_code" => "+964");
        $countries[] = array("code" => "IE", "name" => "Ireland", "d_code" => "+353");
        $countries[] = array("code" => "IL", "name" => "Israel", "d_code" => "+972");
        $countries[] = array("code" => "IT", "name" => "Italy", "d_code" => "+39");
        $countries[] = array("code" => "JM", "name" => "Jamaica", "d_code" => "+1");
        $countries[] = array("code" => "JP", "name" => "Japan", "d_code" => "+81");
        $countries[] = array("code" => "JO", "name" => "Jordan", "d_code" => "+962");
        $countries[] = array("code" => "KZ", "name" => "Kazakhstan", "d_code" => "+7");
        $countries[] = array("code" => "KE", "name" => "Kenya", "d_code" => "+254");
        $countries[] = array("code" => "KI", "name" => "Kiribati", "d_code" => "+686");
        $countries[] = array("code" => "XK", "name" => "Kosovo", "d_code" => "+381");
        $countries[] = array("code" => "KW", "name" => "Kuwait", "d_code" => "+965");
        $countries[] = array("code" => "KG", "name" => "Kyrgyzstan", "d_code" => "+996");
        $countries[] = array("code" => "LA", "name" => "Laos", "d_code" => "+856");
        $countries[] = array("code" => "LV", "name" => "Latvia", "d_code" => "+371");
        $countries[] = array("code" => "LB", "name" => "Lebanon", "d_code" => "+961");
        $countries[] = array("code" => "LS", "name" => "Lesotho", "d_code" => "+266");
        $countries[] = array("code" => "LR", "name" => "Liberia", "d_code" => "+231");
        $countries[] = array("code" => "LY", "name" => "Libya", "d_code" => "+218");
        $countries[] = array("code" => "LI", "name" => "Liechtenstein", "d_code" => "+423");
        $countries[] = array("code" => "LT", "name" => "Lithuania", "d_code" => "+370");
        $countries[] = array("code" => "LU", "name" => "Luxembourg", "d_code" => "+352");
        $countries[] = array("code" => "MO", "name" => "Macau", "d_code" => "+853");
        $countries[] = array("code" => "MK", "name" => "Macedonia", "d_code" => "+389");
        $countries[] = array("code" => "MG", "name" => "Madagascar", "d_code" => "+261");
        $countries[] = array("code" => "MW", "name" => "Malawi", "d_code" => "+265");
        $countries[] = array("code" => "MY", "name" => "Malaysia", "d_code" => "+60");
        $countries[] = array("code" => "MV", "name" => "Maldives", "d_code" => "+960");
        $countries[] = array("code" => "ML", "name" => "Mali", "d_code" => "+223");
        $countries[] = array("code" => "MT", "name" => "Malta", "d_code" => "+356");
        $countries[] = array("code" => "MH", "name" => "Marshall Islands", "d_code" => "+692");
        $countries[] = array("code" => "MQ", "name" => "Martinique", "d_code" => "+596");
        $countries[] = array("code" => "MR", "name" => "Mauritania", "d_code" => "+222");
        $countries[] = array("code" => "MU", "name" => "Mauritius", "d_code" => "+230");
        $countries[] = array("code" => "YT", "name" => "Mayotte", "d_code" => "+262");
        $countries[] = array("code" => "MX", "name" => "Mexico", "d_code" => "+52");
        $countries[] = array("code" => "MD", "name" => "Moldova", "d_code" => "+373");
        $countries[] = array("code" => "MC", "name" => "Monaco", "d_code" => "+377");
        $countries[] = array("code" => "MN", "name" => "Mongolia", "d_code" => "+976");
        $countries[] = array("code" => "ME", "name" => "Montenegro", "d_code" => "+382");
        $countries[] = array("code" => "MS", "name" => "Montserrat", "d_code" => "+1");
        $countries[] = array("code" => "MA", "name" => "Morocco", "d_code" => "+212");
        $countries[] = array("code" => "MZ", "name" => "Mozambique", "d_code" => "+258");
        $countries[] = array("code" => "NA", "name" => "Namibia", "d_code" => "+264");
        $countries[] = array("code" => "NR", "name" => "Nauru", "d_code" => "+674");
        $countries[] = array("code" => "NP", "name" => "Nepal", "d_code" => "+977");
        $countries[] = array("code" => "NL", "name" => "Netherlands", "d_code" => "+31");
        $countries[] = array("code" => "AN", "name" => "Netherlands Antilles", "d_code" => "+599");
        $countries[] = array("code" => "NC", "name" => "New Caledonia", "d_code" => "+687");
        $countries[] = array("code" => "NZ", "name" => "New Zealand", "d_code" => "+64");
        $countries[] = array("code" => "NI", "name" => "Nicaragua", "d_code" => "+505");
        $countries[] = array("code" => "NE", "name" => "Niger", "d_code" => "+227");
        $countries[] = array("code" => "NG", "name" => "Nigeria", "d_code" => "+234");
        $countries[] = array("code" => "NU", "name" => "Niue", "d_code" => "+683");
        $countries[] = array("code" => "NF", "name" => "Norfolk Island", "d_code" => "+672");
        $countries[] = array("code" => "KP", "name" => "North Korea", "d_code" => "+850");
        $countries[] = array("code" => "MP", "name" => "Northern Mariana Islands", "d_code" => "+1");
        $countries[] = array("code" => "NO", "name" => "Norway", "d_code" => "+47");
        $countries[] = array("code" => "OM", "name" => "Oman", "d_code" => "+968");
        $countries[] = array("code" => "PK", "name" => "Pakistan", "d_code" => "+92");
        $countries[] = array("code" => "PW", "name" => "Palau", "d_code" => "+680");
        $countries[] = array("code" => "PS", "name" => "Palestine", "d_code" => "+970");
        $countries[] = array("code" => "PA", "name" => "Panama", "d_code" => "+507");
        $countries[] = array("code" => "PG", "name" => "Papua New Guinea", "d_code" => "+675");
        $countries[] = array("code" => "PY", "name" => "Paraguay", "d_code" => "+595");
        $countries[] = array("code" => "PE", "name" => "Peru", "d_code" => "+51");
        $countries[] = array("code" => "PH", "name" => "Philippines", "d_code" => "+63");
        $countries[] = array("code" => "PL", "name" => "Poland", "d_code" => "+48");
        $countries[] = array("code" => "PT", "name" => "Portugal", "d_code" => "+351");
        $countries[] = array("code" => "PR", "name" => "Puerto Rico", "d_code" => "+1");
        $countries[] = array("code" => "QA", "name" => "Qatar", "d_code" => "+974");
        $countries[] = array("code" => "CG", "name" => "Republic of the Congo", "d_code" => "+242");
        $countries[] = array("code" => "RE", "name" => "Réunion", "d_code" => "+262");
        $countries[] = array("code" => "RO", "name" => "Romania", "d_code" => "+40");
        $countries[] = array("code" => "RU", "name" => "Russia", "d_code" => "+7");
        $countries[] = array("code" => "RW", "name" => "Rwanda", "d_code" => "+250");
        $countries[] = array("code" => "BL", "name" => "Saint Barthélemy", "d_code" => "+590");
        $countries[] = array("code" => "SH", "name" => "Saint Helena", "d_code" => "+290");
        $countries[] = array("code" => "KN", "name" => "Saint Kitts and Nevis", "d_code" => "+1");
        $countries[] = array("code" => "MF", "name" => "Saint Martin", "d_code" => "+590");
        $countries[] = array("code" => "PM", "name" => "Saint Pierre and Miquelon", "d_code" => "+508");
        $countries[] = array("code" => "VC", "name" => "Saint Vincent and the Grenadines", "d_code" => "+1");
        $countries[] = array("code" => "WS", "name" => "Samoa", "d_code" => "+685");
        $countries[] = array("code" => "SM", "name" => "San Marino", "d_code" => "+378");
        $countries[] = array("code" => "ST", "name" => "São Tomé and Príncipe", "d_code" => "+239");
        $countries[] = array("code" => "SA", "name" => "Saudi Arabia", "d_code" => "+966");
        $countries[] = array("code" => "SN", "name" => "Senegal", "d_code" => "+221");
        $countries[] = array("code" => "RS", "name" => "Serbia", "d_code" => "+381");
        $countries[] = array("code" => "SC", "name" => "Seychelles", "d_code" => "+248");
        $countries[] = array("code" => "SL", "name" => "Sierra Leone", "d_code" => "+232");
        $countries[] = array("code" => "SG", "name" => "Singapore", "d_code" => "+65");
        $countries[] = array("code" => "SK", "name" => "Slovakia", "d_code" => "+421");
        $countries[] = array("code" => "SI", "name" => "Slovenia", "d_code" => "+386");
        $countries[] = array("code" => "SB", "name" => "Solomon Islands", "d_code" => "+677");
        $countries[] = array("code" => "SO", "name" => "Somalia", "d_code" => "+252");
        $countries[] = array("code" => "ZA", "name" => "South Africa", "d_code" => "+27");
        $countries[] = array("code" => "KR", "name" => "South Korea", "d_code" => "+82");
        $countries[] = array("code" => "ES", "name" => "Spain", "d_code" => "+34");
        $countries[] = array("code" => "LK", "name" => "Sri Lanka", "d_code" => "+94");
        $countries[] = array("code" => "LC", "name" => "St. Lucia", "d_code" => "+1");
        $countries[] = array("code" => "SD", "name" => "Sudan", "d_code" => "+249");
        $countries[] = array("code" => "SR", "name" => "Suriname", "d_code" => "+597");
        $countries[] = array("code" => "SZ", "name" => "Swaziland", "d_code" => "+268");
        $countries[] = array("code" => "SE", "name" => "Sweden", "d_code" => "+46");
        $countries[] = array("code" => "CH", "name" => "Switzerland", "d_code" => "+41");
        $countries[] = array("code" => "SY", "name" => "Syria", "d_code" => "+963");
        $countries[] = array("code" => "TW", "name" => "Taiwan", "d_code" => "+886");
        $countries[] = array("code" => "TJ", "name" => "Tajikistan", "d_code" => "+992");
        $countries[] = array("code" => "TZ", "name" => "Tanzania", "d_code" => "+255");
        $countries[] = array("code" => "TH", "name" => "Thailand", "d_code" => "+66");
        $countries[] = array("code" => "BS", "name" => "The Bahamas", "d_code" => "+1");
        $countries[] = array("code" => "GM", "name" => "The Gambia", "d_code" => "+220");
        $countries[] = array("code" => "TL", "name" => "Timor-Leste", "d_code" => "+670");
        $countries[] = array("code" => "TG", "name" => "Togo", "d_code" => "+228");
        $countries[] = array("code" => "TK", "name" => "Tokelau", "d_code" => "+690");
        $countries[] = array("code" => "TO", "name" => "Tonga", "d_code" => "+676");
        $countries[] = array("code" => "TT", "name" => "Trinidad and Tobago", "d_code" => "+1");
        $countries[] = array("code" => "TN", "name" => "Tunisia", "d_code" => "+216");
        $countries[] = array("code" => "TR", "name" => "Turkey", "d_code" => "+90");
        $countries[] = array("code" => "TM", "name" => "Turkmenistan", "d_code" => "+993");
        $countries[] = array("code" => "TC", "name" => "Turks and Caicos Islands", "d_code" => "+1");
        $countries[] = array("code" => "TV", "name" => "Tuvalu", "d_code" => "+688");
        $countries[] = array("code" => "UG", "name" => "Uganda", "d_code" => "+256");
        $countries[] = array("code" => "UA", "name" => "Ukraine", "d_code" => "+380");
        $countries[] = array("code" => "AE", "name" => "United Arab Emirates", "d_code" => "+971");
        $countries[] = array("code" => "GB", "name" => "United Kingdom", "d_code" => "+44");
        $countries[] = array("code" => "US", "name" => "United States", "d_code" => "+1");
        $countries[] = array("code" => "UY", "name" => "Uruguay", "d_code" => "+598");
        $countries[] = array("code" => "VI", "name" => "US Virgin Islands", "d_code" => "+1");
        $countries[] = array("code" => "UZ", "name" => "Uzbekistan", "d_code" => "+998");
        $countries[] = array("code" => "VU", "name" => "Vanuatu", "d_code" => "+678");
        $countries[] = array("code" => "VA", "name" => "Vatican City", "d_code" => "+39");
        $countries[] = array("code" => "VE", "name" => "Venezuela", "d_code" => "+58");
        $countries[] = array("code" => "VN", "name" => "Vietnam", "d_code" => "+84");
        $countries[] = array("code" => "WF", "name" => "Wallis and Futuna", "d_code" => "+681");
        $countries[] = array("code" => "YE", "name" => "Yemen", "d_code" => "+967");
        $countries[] = array("code" => "ZM", "name" => "Zambia", "d_code" => "+260");
        $countries[] = array("code" => "ZW", "name" => "Zimbabwe", "d_code" => "+263");
        for ($i = 0; $i < count($countries); $i++) {
            echo "<option value='" . $countries[$i]["d_code"] . "|" . $countries[$i]["name"] . "'>" . $countries[$i]["name"] . "</option>";
        }
    }

    /**
     * <h1>header</h1>
     * <p>This is the method to display the header of the page</p>
     * @param Int $subject The ID of the subject to refer to.
     */
    public function header($subject)
    {
        $head = "";
        try {
            $subject = $subject;
            $subjectDetails = R::getAll("SELECT title FROM subject WHERE id='$subject'");
            if (count($subjectDetails) > 0) {
                $head = $subjectDetails[0]['title'];
            } else {
                $head = "New article" . count($subjectDetails) . $subject;
            }
        } catch (Exception $e) {
            error_log("MAIN[header]:" . $e);
        }
        return $head;
    }

    /**
     * <h1>formBuilder</h1>
     * <p>This form is the build the form input</p>
     * @param Integer $subjectId This the ID of the subject being viewed
     * @param String $caller The calling environment
     */
    public function formBuilder($subjectId, $caller)
    {

        $title = "";
        try {
            $subjectId = $subjectId;
            $subject = R::getAll("SELECT title,attr_number FROM subject WHERE id='$subjectId'");
            if (count($subject) > 0) {
                if (!$this->formInterface($subject, $subjectId, $caller)) {
                    $this->status = $this->feedbackFormat(0, "ERROR: form can not be built!");
                    error_log("ERROR: -> CLASS:main FUNCTION:formBuilder ---- formInterface failure");
                }
            } else {
                $this->status = $this->feedbackFormat(0, "ERROR: form can not be built!");
                error_log("ERROR: -> CLASS:main FUNCTION:formBuilder ---- no subject available");
            }
        } catch (Exception $e) {
            error_log("ERROR: -> CLASS:main FUNCTION:formBuilder ---- " . $e);
        }
    }

    /**
     * <h1>formInterface</h1>
     * making the form structure
     */
    private function formInterface($subject, $subjectId, $caller)
    {
        $built = false;
        $attrNumber = $subject[0]['attr_number'];
        $subjectObj = new subject();
        $attributes = $subjectObj->getAttributes($subjectId);
        if ($attrNumber == count($attributes)) {
            echo "<form role='form' method='post' onsubmit='return false;'>";
            echo "<div class='form-group'>";
            echo "<input type='hidden'  name='article' id='article_id' value='$subjectId'>";
            echo '';
            echo "</div>";
            for ($counter = 0; $counter < count($attributes); $counter++) {
                $attrName = $attributes[$counter]["name"];
                $attrType = $attributes[$counter]["type"];
                echo "<div class='form-group'>";
                $this->inputGenerator($attributes[$counter]["id"], $attrName, $attrType, $caller);
                echo "</div>";
                $built = true;
            }
            if ($caller == "add") {
                echo "<div class='form-group'>";
                echo "<input type='submit' class='btn btn-dark' id='save-article' onclick='saveArticle();' name='action' value='Save'>";
                echo "</div>";
            }
            echo "</form>";
        } else {
            error_log("ERROR: -> CLASS:main FUNCTION:formInterface ---- Attributes number not matching");
        }
        return $built;
    }

    /**
     * <h1>inputGenerator</h1>
     * <p>Generates the input for attributes with default datatypes</p>
     * @param String $name The name of the attribute
     */
    private function inputGenerator($id, $name, $type, $caller)
    {
        if (isset($this->instance)) {
            $value = $this->getValue($name);
            $holder = "value";
        } else {
            $value = "Insert value...";
            $holder = "placeholder";
        }
        $title = "<span class='input-group-addon'>" . str_replace("_", " ", $name) . "</span>";
        $input = "";
        if ($this->isDataTypeDefault($type)) {
            switch ($type) {
                case 'text':
                    $input = "<input type='text' name='$name' id='$caller" . "_" . "$name' class='form-control' $holder='$value'>";
                    break;
                case 'numeric':
                    $input = "<input type='number' name='$name' id='$caller" . "_" . "$name' class='form-control' $holder='$value'>";
                    break;
                case 'date':
                    $input = "<input type='date' name='$name' id='$caller" . "_" . "$name' class='form-control'$holder='$value'>";
                    break;
                case 'file':
                    $input = "<input type='file' id='$caller" . "_" . "$name' class='form-control' $holder='$value'>";
                    break;
                case 'long text':
                    $input = "<textarea class='form-control' id='$caller" . "_" . "$name' name='$name'>$value</textarea>";
                    break;
            }
        } else {
            $input = $this->referentialDataInputGenerator($id, $name, $type, $caller);
        }
        $formInput = $title . $input;
        echo "<div class='input-group'>" . $formInput . "</div>";
    }

    private function referentialDataInputGenerator($id, $name, $type, $caller)
    {
        $input = "";
        if (isset($id) && isset($name) && isset($type)) {
            $startCombo = "<select type='date' name='$name' id='$caller" . "_" . "$name' class='form-control'>";
            $subjectObj = new subject();
            $reference = $subjectObj->readReference($id);
            if (isset($reference) && $subjectObj->isDataTypeTable($type)) {
                try {
                    $referenceValues = R::getCol("SELECT " . $reference . " FROM " . $type);
                    if (count($referenceValues) > 0) {
                        $optionString = "<option >Select $reference</option>";
                    } else {
                        $optionString = "";
                    }
                    for ($counter = 0; $counter < count($referenceValues); $counter++) {
                        $optionString = $optionString . "<option value=" . $referenceValues[$counter] . ">" . $referenceValues[$counter] . "</option>";
                    }
                } catch (Exception $exc) {
                    error_log("ERROR(referentialDataInputGenerator):" . $e);
                }
            }
            $endCombo = "</select>";
            $input = $startCombo . $optionString . $endCombo;
        }
        return $input;
    }

    /**
     * <h1>feedFormValues</h1>
     * <p>This method is to set values to feed the built form.</p>
     */
    private function getValue($col)
    {
        $value = "Not set";
        try {
            $instance = $this->instance;
            $field = $this->field;
            $value = R::getCell("SELECT DISTINCT $col FROM $field WHERE id='$instance'");
        } catch (Exception $e) {
            error_log("MAIN[getValue]:" . $e);
        }
        return $value;
    }

    //BUILDING THE SELECT
    public function fetchBuilder($table, $columnList)
    {
        $result = null;
        $query = "";
        //building the syntax
        for ($count = 0; $count < count($columnList); $count++) {
            if ($count == 0) {
                $query = str_replace(" ", "_", $columnList[$count]['name']);
            } else {
                $query = $query . "," . str_replace(" ", "_", $columnList[$count]['name']);
            }
        }

        $sql = "SELECT id," . $query . " FROM " . $table;
        //executing the query
        try {
            $values = R::getAll($sql);
            //building the table content
            $rows = array();
            for ($count = 0; $count < count($values); $count++) { //feed row
                $columns = array();
                $columns[0] = $values[$count]['id'];
                for ($inner = 1; $inner <= count($columnList); $inner++) { //feed column
                    $columns[$inner] = $values[$count][str_replace(" ", "_", $columnList[$inner - 1]['name'])];
                }
                $rows[$count] = $columns;
            }
            //get the result
            if (count($rows) != 0) {
                $result = $rows;
            }
        } catch (Exception $e) {
            error_log("ERROR(fetchBuilder):" . $e);
        }
        return $result;
    }

    //loading the list of tables
    public function getTables()
    {
        $schema = $this->dbname;
        try {
            $tables = R::getAll("SELECT TABLE_NAME
                                FROM INFORMATION_SCHEMA.TABLES
                                WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA='$schema'");
            $this->comboBuilder($tables, "TABLE_NAME", "TABLE_NAME");
        } catch (Exception $e) {
            error_log("ERROR(main:getTables)" . $e);
        }
    }

    /**
     * <h1>getTableColumns</h1>
     * <p>
     * This function returns the list of all columns belonging to the specified table.
     * </p>
     * @param String $tableName The name of the table to be specified
     */
    public function getTableColumns($tableName)
    {
        $columnList = null;
        try {
            if (!$this->isDataTypeTable($tableName) && isset($_SESSION['ref_data_type']) && !$this->isDataTypeColumn($tableName)) {
                $tableName = $_SESSION['ref_data_type'];
            } else if (isset($tableName) && ($this->isDataTypeTable($tableName) && !$this->isDataTypeColumn($tableName))) {
                $_SESSION['ref_data_type'] = $tableName;
            } else if (isset($tableName) && (!$this->isDataTypeTable($tableName) && $this->isDataTypeColumn($tableName))) {
                $_SESSION['ref_data_value'] = $columnName = $tableName;
            }
            if (privilege_grantisset($columnName) && $this->isDataTypeColumn($columnName) && isset($_SESSION['ref_data_type'])) {
                $columns = R::getAll("SELECT DISTINCT COLUMN_NAME
                                      FROM INFORMATION_SCHEMA.COLUMNS
                                      WHERE COLUMN_NAME='$columnName'");
                $columnList[0] = array("COLUMN_NAME" => $_SESSION['ref_data_type'] . "|" . $columns[0]['COLUMN_NAME'], "COLUMN_TYPE" => $_SESSION['ref_data_type'] . " " . $columns[0]['COLUMN_NAME']);
            } else {
                $_SESSION['ref_data_type'] = $tableName;
                $columns = R::getAll("SELECT COLUMN_NAME
                                      FROM INFORMATION_SCHEMA.COLUMNS
                                      WHERE TABLE_NAME='$tableName'");
                for ($counter = 0; $counter < count($columns); $counter++) {
                    $columnList[$counter] = array("COLUMN_NAME" => $columns[$counter]['COLUMN_NAME'], "COLUMN_TYPE" => $_SESSION['ref_data_type'] . " " . $columns[$counter]['COLUMN_NAME']);
                }
            }
            $this->comboBuilder($columnList, "COLUMN_NAME", "COLUMN_TYPE");
        } catch (Exception $exc) {
            error_log("ERROR(main:getTableColumns)" . $exc);
        }
    }

    /*
     * validating the numbers
     */

    public function standardize($phone)
    {
        if (strlen($phone) == 10) {
            $phone = "25" . $phone;
        } else if (strlen($phone) == 9) {
            $phone = "250" . $phone;
        } else if (strlen($phone) == 12) {
            $phone = $phone;
        } else {
            $phone = "Failed to build";
        }
        return $phone;
    }

}

//user object
class user extends main
{

    public $fname;
    public $lname;
    public $username;
    public $email;
    public $phone;
    public $address;
    public $status = "";
    public $loggedIn = null;
    public $toVerify = null;
    public $count;
    public $userlist = [];

    public function __construct()
    {
        $this->count();
    }

    /**
     * <h1>fetch</h1>
     * <p>Counting the user of the system</p>
     */
    public function count()
    {
        $users = [];
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $loggedInType = $this->getUserType($userId);
            if ($loggedInType == "administrator") {
                try {
                    $users = R::getAll("SELECT * FROM credentials");
                    $this->count = count($users);
                } catch (Exception $e) {
                    error_log("ERROR(USER:COUNT):" . $e);
                }
            } else {
                try {
                    if ($loggedInType != null) {
                        $type = R::getCell("SELECT DISTINCT id FROM role WHERE title='$loggedInType'");
                        $users = R::getAll("SELECT * FROM credentials WHERE type='$type'");
                        $this->count = count($users);
                    } else {
                        $this->count = "N/A";
                    }

                } catch (Exception $e) {
                    error_log("ERROR(USER:COUNT):" . $e);
                }
            }
        }
    }

    //getting the user
    public function userList($type)
    {
        $header = array('No', 'Names', 'Email', 'Tel', 'Category');
        try {
            if (isset($type)) {
                $users = R::getAll("SELECT u.id,u.fname,u.lname,u.oname,u.email,u.phone,c.user,c.type FROM user AS u JOIN credentials AS c WHERE u.id=c.user AND c.type='$type'");
            } else {
                $users = R::getAll("SELECT u.id,u.fname,u.lname,u.oname,u.email,u.phone,c.user,c.type FROM user AS u JOIN credentials AS c WHERE u.id=c.user");
            }
            if (count($users) == 0) {
                $this->displayTable($header, null, null);
            } else {
                $tableContent = array();
                for ($row = 0; $row < count($users); $row++) {
                    $rowNumber = $row + 1;
                    $userId = $users[$row]['id'];
                    $names = $users[$row]['fname'] . " " . $users[$row]['lname'];
                    $email = $users[$row]['email'];
                    $tel = $users[$row]['phone'];
                    $type = $this->getUserType($users[$row]['id']);
                    $tableContent[$row] = array($userId, $rowNumber, $names, $email, $tel, $type);
                }
                $this->displayTable($header, $tableContent, null);
            }
        } catch (Exception $e) {
            $this->status = $this->feedbackFormat(0, "Error loading user list");
        }
    }

    /**
     * <h1>add</h1>
     * <p>Adding the user</p>
     * @param $fname the name of the user
     * @param $lname the last name of the user
     * @param $oname Other name of the user
     */
    public function add($fname, $lname, $oname, $email, $tel, $address, $username, $password, $type)
    {
        if ($this->isValid($username)) {
            //saving user credentials
            try {
                //saving user details
                $user_details = R::dispense("user");
                $user_details->fname = $fname;
                $user_details->lname = $lname;
                $user_details->oname = $oname;
                $user_details->email = $email;
                $user_details->phone = $tel;
                $user_details->address = $address;
                $userId = R::store($user_details);
                $this->addCredentials($userId, $username, $password, $type);
            } catch (Exception $e) {
                $this->status = $this->feedbackFormat(0, "User not added!" . $e);
            }
        } else {
            $this->status = $this->feedbackFormat(0, "Username already exists!");
        }
    }

    private function addCredentials($id, $username, $password, $type)
    {
        try {
            $user_credentials = R::dispense("credentials");
            $user_credentials->user = $id;
            $user_credentials->username = $username;
            $user_credentials->password = md5($password);
            $user_credentials->type = $type;
            $user_credentials->last_log = date("d-m-Y h:m:s");
            $user_credentials->status = 1;
            R::store($user_credentials);
            $this->status = $this->feedbackFormat(1, "User successfully added!");
        } catch (Exception $e) {
            $this->status = $this->feedbackFormat(0, "Error occured saving credentials" . $e);
        }
    }

    /**
     *<h1>isValid</h1>
     *<p>This function validates id the username is valid for registration</p>
     * @param $username The user name to validate.
     * @return Boolean
     */
    public function isUsernameValid($username)
    {
        $status = true;
        try {
            $check = R::getCol("SELECT id FROM credentials WHERE username='$username'");
            if (sizeof($check) != 0) {
                $status = false;
                $this->status = "Username already exists";
            }
        } catch (Exception $e) {
            $status = false;
            $this->status = "Error checking username." . $e;
        }
        return $status;
    }

    /**
     * Validates the email of the user registering
     * @param $email the email to verify.
     * @return boolean true if email is valid
     */
    public function isEmailValid($email)
    {
        $status = true;
        if (strpos($email, "@") === false || strpos($email, ".")) {
            $status = false;
            $this->status = "Invalid email" . $email;
        } else {
            try {
                $check = R::getCol("SELECT id FROM credentials WHERE username='$username'");
                if (sizeof($check) != 0) {
                    $status = false;
                    $this->status = "Email already exists" . $email;
                }
            } catch (Exception $exc) {
                $status = false;
                $this->status = "Error checking the email" . $exc;
            }
        }
        return $status;
    }
    //evaluating logged in user
    private function evalLoggedUser($id, $u)
    {
        //getting the logged in user information
        try {
            $logged_user = R::getRow("SELECT id FROM credentials WHERE user_id = {$id} AND username ='{$u}'  AND user_status='1' LIMIT 1");
            if (isset($logged_user)) {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * <h1>checkLogin</h1>
     * <p>This function verifies if the user is logged in</p>
     * @return Boolean
     */
    public function checkLogin()
    {
        $user_ok = false;
        $user_id = "";
        $log_usename = "";
        if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
            $user_id = preg_replace('#[^0-9]#', '', $_SESSION['user_id']);
            $log_usename = preg_replace('#[^a-z0-9]#i', '', $_SESSION['username']);
            // Verify the user
            $user_ok = $this->evalLoggedUser($user_id, $log_usename);
        } else if (isset($_COOKIE["user_id"]) && isset($_COOKIE["username"])) {
            $_SESSION['user_id'] = preg_replace('#[^0-9]#', '', $_COOKIE['user_id']);
            $_SESSION['username'] = preg_replace('#[^a-z0-9]#i', '', $_COOKIE['username']);
            $user_id = preg_replace('#[^0-9]#', '', $_SESSION['user_id']);
            $log_usename = preg_replace('#[^a-z0-9]#i', '', $_SESSION['username']);
            // Verify the user
            $user_ok = $this->evalLoggedUser($user_id, $log_usename);
            if ($user_ok == true) {
                // Update their lastlogin datetime field
                R::exec("UPDATE credentials SET last_login = now() WHERE user_id = '$user_id' LIMIT 1");
            }
        }
        return $user_ok;
    }

    /**
     *<h1>login</h1>
     *<p>This is the function to login the user</p>
     * @param $username the username of the user
     * @param $password the password of the user
     */
    public function login($username, $password)
    {
        $password = md5($password);
        try {
            $user = R::getRow("SELECT id,username,type FROM credentials WHERE username='$username' AND password='$password'");
            if (isset($user)) {
                // CREATE THEIR SESSIONS AND COOKIES
                $_SESSION['user_id'] = $db_id = $user['id'];
                $_SESSION['username'] = $db_username = $user['username'];
                $_SESSION['type'] = $db_type = $user['type'];
                setcookie("user_id", $db_id, time() + 60, "/", "", "", true);
                setcookie("username", $db_username, time() + 60, "/", "", "", true);
                setcookie("type", $db_type, time() + 60, "/", "", "", true);
                // UPDATE THEIR "LASTLOGIN" FIELDS
                try {
                    R::exec("UPDATE credentials SET last_login = now() WHERE id = '$db_id' LIMIT 1");
                } catch (Exception $e) {
                    error_log("ERROR: Unable to login" . $e);
                }
                $this->status = $this->feedbackFormat(1, "Authentication verified");
                //header("location:../views/home.php");
            } else {
                $this->status = $this->feedbackFormat(0, "Authentication not verified");
            }
        } catch (Exception $e) {
            $this->status = $this->feedbackFormat(0, "Login error");
        }
        die($this->status);
    }

    /**
     * Return the user type of the logged in user
     */
    public function getUserType($user_id)
    {
        $userType = null;
        if ($user_id) {
            try {
                $type_id = R::getCell("SELECT DISTINCT type FROM credentials WHERE user = '$user_id'");
                if ($type_id != null) {
                    $userType = R::getCell("SELECT DISTINCT title FROM role WHERE id='$type_id'");
                }
            } catch (Exception $e) {
                error_log("USER[getUserType]:" . $e);
            }
        }
        return $userType;
    }

    /**
     * This function checks if the user is allowed to do the subject.
     * @param $toDo What the user needs
     */
    public function isUserAllowed($toDo, $subject)
    {
        $isAllowed = false;
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $userType = $this->getUserType($userId);
            if (isset($userType)) {
                try {
                    $userPrivilege = R::getRow("SELECT writing,reading FROM privilege WHERE subject='$subject' AND role='$userType'");
                    if ($toDo == 'add' && $userPrivilege['writing'] == "allowed") {
                        $isAllowed = true;
                    }
                    if ($toDo == 'view' && $userPrivilege['reading'] == "allowed") {
                        $isAllowed = true;
                    }
                } catch (Exception $e) {
                    error_log("isuserAllowed:" . $e);
                }
            }
        }
        return $isAllowed;
    }

    public function listRoleInOption()
    {
        try {
            $roleDetails = R::getAll("SELECT id,title FROM role");
            for ($counter = 0; $counter < count($roleDetails); $counter++) {
                echo "<option name='add_user_type' value=" . $roleDetails[$counter]['id'] . ">" . $roleDetails[$counter]['title'] . "</option>";
            }
        } catch (Exception $e) {
            error_log("Unable to read list of roles.");
        }
    }
    /**
     * <h1>getUserDetails</h1>
     * <p>This method is to fetch information of the user.</p>
     * @param Int $userId The user id
     * @return Array Returns an array that contains all user information.
     */
    public function getUserDetails($userId)
    {
        $user = null;
        try {
            $user = R::getRow("SELECT u.id,u.fname,u.lname,u.address,u.oname,u.email,u.phone,c.user,c.username,c.type FROM user AS u JOIN credentials AS c WHERE u.id=c.user AND u.id='$userId'");
            $this->fname = $user['fname'];
            $this->lname = $user['lname'];
            $this->username = $user['username'];
            $this->email = $user['email'];
            $this->phone = $user['phone'];
            $this->address = $user['address'];
        } catch (Exception $e) {
            error_log("USER(getUserDetails):" . $e);
        }
        return $user;
    }

}

/*
 * THE SUBJECT CLASS
 * */

class subject extends main
{

    public $status = "";
    public $subjectId = null;
    public $title = "";
    public $attributes = [];

    //adding a new content
    public function add($subjTitle, $subjAttrNumber, $subjAttributes, $subjCommenting, $subjLikes, $subjDisplayViews)
    {

        if ($this->isValid($subjTitle) && $subjTitle != 'subject') {
            try {
                $subject = R::dispense("subject");
                $subject->title = $subjTitle;
                $subject->createdOn = date("d-m-Y h:m:s");
                $subject->createdBy = $_SESSION['user_id'];
                $subject->lastUpdate = date("d-m-Y h:m:s");
                $subject->attrNumber = $subjAttrNumber;
                $subject->enable_commenting = $subjCommenting;
                $subject->enable_liking = $subjLikes;
                $subject->enable_display_views = $subjDisplayViews;
                $this->subjectId = $subjectId = R::store($subject);
                /*
                 * Creating the attributes associated with the subject
                 */
                $article = new content();
                if (!($article->register($subjTitle, $subjAttributes)) || !($this->createAttributes($subjAttributes))) {
                    try {
                        R::exec("DELETE FROM subject WHERE id='$subjectId'");
                    } catch (Exception $e) {
                        $this->status = $this->feedbackFormat(0, "ERROR: undefined");
                    }
                    $this->status = $this->feedbackFormat(0, "ERROR: article could not be created.");
                } else {
                    $this->status = $this->feedbackFormat(1, "Subject added successfully");
                }
            } catch (Exception $e) {
                $this->status = $this->feedbackFormat(0, "ERROR: subject not added");
            }
        } else {
            $this->status = $this->feedbackFormat(0, "ERROR: Title already exists");
        }
    }

    /**
     * Adding the subject attributes.
     */
    private function createAttributes($attributes)
    {
        $isCreated = false;
        if (isset($this->subjectId)) {
            try {
                for ($counter = 0; $counter < count($attributes); $counter++) {
                    $attribute = R::dispense("attribute");
                    $attribute->subject = $this->subjectId;
                    $attribute->name = $attributes[$counter]["name"];
                    $attribute->data_type = $attributes[$counter]["type"];
                    $attribute->has_ref = $hasRef = $attributes[$counter]["has_ref"];
                    $attributeId = R::store($attribute);
                    if ((isset($attributeId) && $hasRef == false) || (isset($attributeId) && $hasRef == true && $this->createReference($attributeId, $attributes[$counter]["reference"]))) {
                        $isCreated = true;
                    }
                }
            } catch (Exception $exc) {
                error_log("ERROR: subject(createAttributes)" . $exc);
            }
        }
        return $isCreated;
    }

    /**
     * <h1>createReference</h1>
     * <p>Adding references to attributes</p>
     * @param Integer $attributeId The ID of the attribute creating the reference
     * @param String $referenceName The name of reference
     */
    public function createReference($attributeId, $referenceName)
    {
        $isCreated = false;
        if (isset($attributeId)) {
            try {
                $reference = R::dispense("reference");
                $reference->attribute = $attributeId;
                $reference->name = $referenceName;
                $referenceId = R::store($reference);
                if (isset($referenceId)) {
                    $isCreated = true;
                }
            } catch (Exception $e) {
                error_log("ERROR: subject(createReference)" . $e);
            }
        }
        return $isCreated;
    }

    /**
     * <h1>readReference</h1>
     * <p>This method is to read the references of the specified attributes</p>
     * @param Integer $attrId The id of the attribute.
     */
    public function readReference($attrId)
    {
        $reference = null;
        if (isset($attrId)) {
            try {
                $reference = R::getCell("SELECT DISTINCT name FROM reference WHERE attribute='$attrId'");
            } catch (Exception $exc) {
                error_log("ERROR: subject(readReference)" . $exc);
            }
        }
        return $reference;
    }

    //checking the existence of a subject
    public function getId($title)
    {
        $id = null;
        try {
            $id = R::getCell("SELECT id FROM subject WHERE title='$title'");
        } catch (Exception $e) {
            $id = null;
            $this->status = "Error getting the ID." . $e;
        }
        return $id;
    }

    //checking the existence of a subject
    public function isValid($title)
    {
        $status = true;
        try {
            $check = R::getCol("SELECT id FROM subject WHERE title='$title'");
            if (sizeof($check) != 0) {
                $status = false;
            }
        } catch (Exception $e) {
            $status = false;
            $this->status = "Error validating subject title " . $e;
        }
        return $status;
    }

    /**
     * returns the attributes of a given subject
     */
    public function getAttributes($subject)
    {
        $response = array();
        try {
            $attributeList = R::getAll("SELECT id,name,data_type FROM attribute WHERE subject='$subject'");
            for ($counter = 0; $counter < count($attributeList); $counter++) {
                $attrName = str_replace(" ", "_", $attributeList[$counter]["name"]);
                $attrType = $attributeList[$counter]["data_type"];
                $response[$counter] = array("id" => $attributeList[$counter]["id"], "name" => $attrName, "type" => $attrType);
            }
        } catch (Exception $e) {
            error_log("ERROR (getAttributes): " . $e);
        }
        return $response;
    }

    //GET LIST OF REGISTERED SUBJECTS
    public function getList()
    {
        $header = array("Title", "Created by", "Created on", "Last update");
        $tablecontent = null;
        try {
            $subjectList = R::getAll("SELECT title,created_by,created_on,last_update FROM subject ORDER BY created_on DESC ");
            for ($count = 0; $count < count($subjectList); $count++) {
                $title = $subjectList[$count]['title'];
                //TODO: Get the creator name
                $createdBy = $subjectList[$count]['created_by'];
                $createdOn = $subjectList[$count]['created_on'];
                $lastUpdate = $subjectList[$count]['last_update'];
                $tablecontent[$count] = array(1 => $title, 2 => $createdBy, 3 => $createdOn, 4 => $lastUpdate);
            }
            $this->displayTable($header, $tablecontent, null);
        } catch (Exception $e) {
            error_log("ERROR (getList):" . $e);
        }
    }

    /**
     * delete
     * This method deleted the subject specified
     */
    public function delete($subjectId)
    {
        if (isset($subjectId)) {
            try {
                $subjectTitle = R::getCell("SELECT title FROM subject WHERE id='$subjectId'");
                if (isset($subjectTitle)) {
                    $tableDropped = R::exec("DROP TABLE $subjectTitle");
                }

                if ($tableDropped) {
                    R::exec("DELETE FROM subject WHERE id='$subjectId'");
                }

                $subjectId = $this->getId($subjectTitle);
                if (!isset($subjectId)) {
                    $this->status = $this->feedbackFormat(1, "Subject deleted successfully");
                } else {
                    $this->status = $this->feedbackFormat(0, "Unable to delete subject");
                }
            } catch (Exception $e) {
                error_log("SUBJECT:DELETE" . $e);
                $this->status = $this->feedbackFormat(0, "Error occured");
            }
        } else {
            $this->status = $this->feedbackFormat(0, "Subject not specified");
        }
        die($this->status);
    }

}

/**
 * THE CONTENT CLASS
 */
class content extends main
{

    public $status = "";

    //register a new article
    public function register($subjectTitle, $attributes)
    {
        $status = false;
        try {
            $subjectTitle = str_replace(" ", "_", $subjectTitle);
            $article = R::xdispense($subjectTitle);
            for ($counter = 0; $counter < count($attributes); $counter++) {
                $attribute = str_replace(" ", "_", $attributes[$counter]['name']);
                if ($attributes[$counter]['type'] == 'text') {
                    $article->$attribute = "dummy text";
                } else if ($attributes[$counter]['type'] == 'numeric') {
                    $article->$attribute = 12356789;
                } else if ($attributes[$counter]['type'] == 'date') {
                    $article->$attribute = date("d-m-Y");
                } else {
                    $article->$attribute = "dummy text";
                }
            }
            $articleId = R::store($article);
            //delete dummy values
            try {
                R::exec("DELETE FROM " . $subjectTitle . " WHERE id='$articleId'");
            } catch (Exception $e) {
                error_log("ERROR(article:Register): " . $e);
                $this->status = $this->feedbackFormat(0, "ERROR(Register): " . $e);
            }
            $status = true;
        } catch (Exception $e) {
            error_log("ERROR(article:Register): " . $e);
        }
        return $status;
    }

    //adding a new article content
    public function add($content, $values, $attributes)
    {
        try {
            $article = R::xdispense($content);
            for ($counter = 0; $counter < count($attributes); $counter++) {
                $attribute = str_replace(" ", "_", $attributes[$counter]['name']);
                $value = $values[$counter];
                $article->$attribute = $value;
            }
            $articleId = R::store($article);
            if (isset($articleId)) {
                $response = $this->feedbackFormat(1, "Saved succefully");
            } else {
                $response = $this->feedbackFormat(0, "Unknown error!");
            }
        } catch (Exception $e) {
            $response = $this->feedbackFormat(0, "Article not added!");
            error_log("ERROR (add article):" . $e);
        }
        return $response;
    }

    /**
     * <h1>getList</h1>
     * <p>This function is to return the list of articles in table view.</p>
     * @param Integer $subjectId The ID of the subject in consideration.
     */
    public function getList($subjectId)
    {
        /*
         * initializing the function
         */
        $subjectObj = new subject();
        $articleTitle = $this->header($subjectId);
        $attributes = $subjectObj->getAttributes($subjectId);
        $list = $this->fetchBuilder($articleTitle, $attributes);
        /*
         * Preparing values to display in the table
         */
        $attrNameList = array();
        for ($counter = 0; $counter < count($attributes); $counter++) {
            $attrNameList[$counter] = $attributes[$counter]['name'];
        }
        /*
         * Displaying the table
         */
        if (count($attrNameList) > 0) {
            $this->displayTable($attrNameList, $list, null);
        }
    }

    //editting an article
    public function edit()
    {

    }

    //adding a comment
    public function comment()
    {

    }

}

/**
 * <h1>message</h1>
 * <p>This is the class to handle the communication through the system</p>
 *
 */
class message extends main
{

    public $count = 0;
    public $head = "No new message.";
    public $notRead = [];
    public $sent = [];
    public $received = [];
    public $sender;
    public $email;
    public $message;
    public $receiver;
    public $createdOn;

    public function __construct()
    {
        $user = new user();
        $this->notRead = [];
        $this->sent = [];
        $this->received = [];
        if ($user->checkLogin()) {
            $this->count();
            $this->fetch();
        }
    }

    /**
     * <h1>send</h1>
     * <p>This is the method to send messages through the system</p>
     */
    public function send($sender, $email, $message)
    {
        $user = new user();
        $fullname = explode(" ", $sender);
        if (isset($fullname[0]) && isset($fullname[1])) {
            $fname = $fullname[0];
            $lname = $fullname[1];
        } else {
            $lname = $fname = $sender;
        }
        if (!isset($sender)) {
            $this->status = $this->feedbackFormat(0, "Missing sender name!");
            die($this->status);
        }
        if (!isset($email)) {
            $this->status = $this->feedbackFormat(0, "Missing sender email!");
            die($this->status);
        }
        if (!isset($message)) {
            $this->status = $this->feedbackFormat(0, "You need to type your message");
            die($this->status);
        }
        /*
         * Create user before sending message
         */
        //$user->add($fname, $lname, $oname, $email, $tel, $address, $username, $password, $type, $age, $gender);
        $user_id = $user->add($fname, $lname, null, $email, null, null, $email, $lname, 4, $age);
        try {
            $messageQR = R::dispense("message");
            $messageQR->user = $user_id;
            $messageQR->sender = $sender;
            $messageQR->email = $email;
            $messageQR->message = $message;
            $messageQR->receiver = 0;
            $messageQR->created_on = date("Y-m-d h:m:s");
            $messageQR->status = 0;
            R::store($messageQR);
            $this->status = $this->feedbackFormat(1, "Message sent successfully!");
        } catch (Exception $e) {
            $this->status = $this->feedbackFormat(0, "Unable to post message!");
            error_log("ERROR(web:postContactMessage)"+$e);
        }
        die($this->status);
    }

    /**
     * <h1>count</h1>
     * <p>This is the method count message.</p>
     */
    public function count()
    {
        $userObj = new user();
        $userId = $_SESSION['user_id'];
        $userType = $userObj->getUserType($userId);
        $message = ["sent" => 0, "received" => 0, "not read" => 0];
        try {
            $notRead = R::getAll("SELECT id,sender,message,created_on FROM message WHERE receiver='$userType' AND status='0'");
            $this->count = count($notRead);
            if ($this->count > 0) {
                $this->head = "You have " . $this->count . " messages";
            }
        } catch (Exception $e) {
            error_log("MESSAGE(count):" . $e);
        }
        return $message;
    }

    /**
     * <h1>receive</h1>
     * <p>This is the method to display received messages</p>
     */
    public function receive()
    {
        $received = $this->received;
        if (count($received) > 0) {
            $this->displayMessageTable(null, $received, "read");
        } else {
            //TODO: Add no data to display format
        }
    }

    /**
     * <h1>read</h1>
     * <p>This function is to read the content of the message</p>
     */
    public function read($messageId)
    {

        $received = $this->received;
        for ($count = 0; $count < count($received); $count++) {
            if ($messageId == $received[$count]['id']) {
                $this->sender = $received[$count]['sender'];
                $this->message = $received[$count]['message'];
                $this->createdOn = $received[$count]['created_on'];
                /*
                 * Change message status
                 */
                try {
                    R::exec("UPDATE message SET status='1' WHERE id='$messageId'");
                } catch (Exception $e) {
                    error_log("MESSAGE(read):" . $e);
                }
                break;
            }
        }
    }

    private function alertDisplayFormat($messageDetails)
    {
        echo '<li>
                    <a href="' . $messageDetails['link'] . '">
                        <div class="msg-img"><div class="online off"></div><img class="img-circle" src="../images/noimage-team.png" alt=""></div>
                        <p class="msg-name">' . $messageDetails['sender'] . '</p>
                        <p class="msg-text">' . $messageDetails['message'] . '</p>
                        <p class="msg-time"></p>
                    </a>
                </li>';
    }

    private function fetch()
    {
        $userObj = new user();
        $userId = $_SESSION['user_id'];
        $userType = $userObj->getUserType($userId);
        try {
            //$notRead = R::getAll("SELECT id,sender,message,created_on FROM message WHERE receiver='$userType' AND status='0'");
            $notRead = R::getAll("SELECT id,sender,message,created_on,status FROM message WHERE receiver='$userId' OR receiver='$userType' AND status='0'");
            if (count($notRead) > 0) {
                for ($countNR = 0; $countNR < count($notRead); $countNR++) {
                    $details[$countNR] = [
                        "id" => $notRead[$countNR]['id'],
                        "sender" => $notRead[$countNR]['sender'],
                        "message" => $notRead[$countNR]['message'],
                        "created_on" => $notRead[$countNR]['created_on'],
                        "status" => "unread",
                        "content" => "message",
                    ];
                }
                $this->notRead = $details;
            }
            $received = R::getAll("SELECT id,sender,message,created_on,status FROM message WHERE receiver='$userType' OR receiver='$userId' ");
            if (count($received) > 0) {
                for ($count = 0; $count < count($received); $count++) {
                    if ($received[$count]['status'] == 0) {
                        $status = "unread";
                    } else {
                        $status = "read";
                    }
                    $details[$count] = [
                        "id" => $received[$count]['id'],
                        "sender" => $received[$count]['sender'],
                        "message" => $received[$count]['message'],
                        "created_on" => $received[$count]['created_on'],
                        "status" => $status,
                        "content" => "message",
                    ];
                }
                $this->received = $details;
            }
        } catch (Exception $e) {
            error_log("MESSAGE(fetchMessage):" . $e);
        }
    }

    public function alert()
    {
        try {
            $notRead = $this->notRead;
            for ($count = 0; $count < count($notRead); $count++) {
                $link = "read.php?action=read&content=" . $notRead[$count]['content'] . "&ref=" . $notRead[$count]['id'];
                $details = [
                    "content" => "message",
                    "id" => $notRead[$count]['id'],
                    "sender" => $notRead[$count]['sender'],
                    "message" => $notRead[$count]['message'],
                    "link" => $link,
                    "time" => "",
                ];
                $this->alertDisplayFormat($details);
            }
        } catch (Exception $e) {
            error_log("MESSAGE(count):" . $e);
        }
    }

}

/**
 * <h1>notification</h1>
 * <p>This class is to handle notification</p>
 */
class notification extends main
{

    /**
     * To count the number of notifications
     */
    public $count = 0;
    public $head = "Nothing to notify";
    public $checked = [];
    public $notified = [];
    public $title;
    public $content;
    public $createdOn;

    public function __construct()
    {
        $user = new user();
        if ($user->checkLogin()) {
            $this->count();
            $this->fetch();
        }
    }

    /**
     * <h1>alertDisplayFormat</h1>
     * <p>This method is the build the format of an alert</p>
     */
    private function alertDisplayFormat($notificationDetails)
    {
        echo ' <li>
                    <a href="' . $notificationDetails['link'] . '">
                        <div class="task-icon badge badge-success"><i class="icon-pin"></i></div>
                        <span class="badge badge-roundless badge-default pull-right">' . $notificationDetails['time'] . '</span>
                        <p class="task-details">' . $notificationDetails['description'] . '.</p>
                    </a>
                </li>';
    }

    public function alert()
    {
        $userObj = new user();
        $userId = $_SESSION['user_id'];
        $userType = $userObj->getUserType($userId);
        try {
            $userTypeCode = R::getCell("SELECT DISTINCT type FROM credentials WHERE user='$userId' LIMIT 1");
            $notificationUL = R::getAll("SELECT id,title,description,created_on FROM notification WHERE privacy='1' AND dedicated='$userTypeCode' ORDER BY created_on DESC");
            for ($countUL = 0; $countUL < count($notificationUL); $countUL++) {
                $link = "read.php?action=read&content=notification&ref=" . $notificationUL[$countUL]['id'];
                $details = [
                    "description" => $notificationUL[$countUL]['description'],
                    "link" => $link,
                    "time" => "",
                ];
                $this->alertDisplayFormat($details);
            }
            $notificationPNP = R::getAll("SELECT id,title,description,created_on FROM notification WHERE privacy='2' AND dedicated='$userId' ORDER BY created_on DESC");
            for ($countPNP = 0; $countPNP < count($notificationPNP); $countPNP++) {
                $link = "read.php?action=read&content=notification&ref=" . $notificationPNP[$countPNP]['id'];
                $details = [
                    "description" => $notificationPNP[$countPNP]['description'],
                    "link" => $link,
                    "time" => "",
                ];
                $this->alertDisplayFormat($details);
            }
        } catch (Exception $e) {
            error_log("NOTIFICATION(alert):" . $e);
        }
    }

    /**
     * <h1>notify</h1>
     * <p>This method is to notify about recent activity</p>
     * @param
     */
    public function add($notificationDetails)
    {
        if (isset($notificationDetails) && count($notificationDetails) > 0) {
            //get all values
            $notification = R::dispense("notification");
            $notification->title = $notificationDetails["title"];
            $notification->description = $notificationDetails["description"];
            $notification->privacy = $notificationDetails["privacy"];
            $notification->dedicated = $notificationDetails["dedicated"];
            $notification->status = $notificationDetails["status"];
            $notification->created_by = $notificationDetails["created_by"];
            $notification->created_on = date("Y-m-d h:m:s");
            $notification->category = $notificationDetails["category"];
            $notification->last_update_on = "Not set";
            R::store($notification);
        } else {
            $this->feedbackFormat(0, "Notification not sent");
        }
    }

    /**
     * <h1>count</h1>
     * <p>This method is to count the number of notification</p>
     */
    public function count()
    {
        $userObj = new user();
        $userId = $_SESSION['user_id'];
        $userType = $userObj->getUserType($userId);
        try {
            /*
             * Getting the user type
             */
            $userTypeCode = R::getCell("SELECT DISTINCT type FROM credentials WHERE user='$userId' LIMIT 1");
            /*
             * Counting notifications dedicated to user types
             */
            $notificationUL = R::getAll("SELECT DISTINCT id FROM notification WHERE privacy='1' AND dedicated='$userTypeCode' AND status='0'");
            /*
             * Counting notification dedicated to the logged in user
             */
            $notificationPNP = R::getAll("SELECT DISTINCT id FROM notification WHERE privacy='2' AND dedicated='$userId' AND status='0'");
            $this->count = count($notificationUL) + count($notificationPNP);
            if ($this->count > 0) {
                $this->head = "You have " . $this->count . " notifications!";
            }
        } catch (Exception $e) {
            error_log("NOTIFICATION(count):" . $e);
        }
    }

    public function fetch()
    {
        $userObj = new user();
        $userId = $_SESSION['user_id'];
        $userType = $userObj->getUserType($userId);
        $details = [];
        try {
            $userTypeCode = R::getCell("SELECT DISTINCT type FROM credentials WHERE user='$userId' LIMIT 1");
            $notificationUL = R::getAll("SELECT id,title,description,created_on,status FROM notification WHERE privacy='1' AND dedicated='$userTypeCode' ORDER BY created_on DESC");
            for ($countUL = 0; $countUL < count($notificationUL); $countUL++) {
                if ($notificationUL[$countUL]['status'] == 0) {
                    $status = "unread";
                } else {
                    $status = "read";
                }
                $details[$countUL] = [
                    "id" => $notificationUL[$countUL]['id'],
                    "sender" => $notificationUL[$countUL]['title'],
                    "message" => $notificationUL[$countUL]['description'],
                    "created_on" => $notificationUL[$countUL]['created_on'],
                    "status" => $status,
                    "content" => "notification",
                ];
            }
            $this->checked = $details;
            $notificationPNP = R::getAll("SELECT id,title,description,created_on FROM notification WHERE privacy='2' AND dedicated='$userId' ORDER BY created_on DESC");
            for ($countPNP = 0; $countPNP < count($notificationPNP); $countPNP++) {
                if ($notificationPNP[$countPNP]['status'] == 0) {
                    $status = "unread";
                } else {
                    $status = "read";
                }
                $details[$countPNP] = [
                    "id" => $notificationPNP[$countPNP]['id'],
                    "sender" => $notificationPNP[$countPNP]['title'],
                    "message" => $notificationPNP[$countPNP]['description'],
                    "created_on" => $notificationPNP[$countPNP]['created_on'],
                    "status" => $status,
                    "content" => "notification",
                ];
            }
            $this->notified = $details;
        } catch (Exception $e) {
            error_log("NOTIFICATION()fetch" . $e);
        }
    }

    /**
     * <h1>read</h1>
     * <p>This function is to read the content of the notification</p>
     */
    public function read($messageId)
    {
        $notified = $this->notified;
        for ($count = 0; $count < count($notified); $count++) {
            if ($messageId == $notified[$count]['id']) {
                $this->title = $notified[$count]['sender'];
                $this->content = $notified[$count]['message'];
                $this->createdOn = $notified[$count]['created_on'];
                /*
                 * Change message status
                 */
                try {
                    R::exec("UPDATE notification SET status='1' WHERE id='$messageId'");
                } catch (Exception $e) {
                    error_log("NOTIFICATION(read):" . $e);
                }
                break;
            }
        }
    }

    /**
     * <h1>receive</h1>
     * <p>This is the method to display received notifications</p>
     */
    public function receive()
    {
        $notified = $this->notified;
        if (count($notified) > 0) {
            $this->displayMessageTable(null, $notified, null);
        } else {
            //TODO: Add no data to display format
        }
    }

}

//the  sms class
class sms extends main
{

    public $status = "";

    //sending the sms
    public function send($recipient, $subject, $message)
    {
        $recipients = array();
        $file = null;
        $sent = 0;
        $message = str_replace(" ", "+", $message);
        //getting the recipient type
        if ($recipient == "list") {
            //get the added list
            $user = $this->user;
            try {
                $list = R::getAll("SELECT id,name FROM file WHERE added_by='$user' ORDER BY id DESC LIMIT 1");
                $handler = new file_handler();
                $file = $list[0]['name'];
                $recipients = $handler->readExcel($file);
            } catch (Exception $e) {
                $this->status = $this->feedbackFormat(0, "Error occured");
                error_log($e);
            }
        } else {
            $recipients = explode(";", $recipient);
        }
        //sending messages
        for ($counter = 0; $counter < count($recipients); $counter++) {
            $status = false;
            $number = $this->standardize($recipients[$counter]);
            $userkey = new user;
            $stockInfo = $this->stockBalance($_SESSION['user_id']);
            $balance = $stockInfo['quantity'];
            if ($this->serviceCaller($message, $number, $subject)) {
                $sent = $sent + 1;
                $status = true;
            }
            //record the details
            try {
                $sms = R::dispense("message");
                $sms->user = $_SESSION['user_id'];
                $sms->subject = $subject;
                $sms->sender = $_SESSION['user_id'];
                $sms->content = $message;
                $sms->recipient = $number;
                $sms->type = "sms";
                $sms->sent_on = date("Y-m-d h:m:s");
                $sms->status = $status;
                $sms->file = $file;
                R::store($sms);
            } catch (Exception $e) {
                error_log($e);
            }
        }
        if ($sent < 0) {
            $this->status = $this->feedbackFormat(1, $sent . " Message(s) sent");
        } else {
            $this->status = $this->feedbackFormat(0, "No message sent<span class='fa fa-warning'></span> ");
        }
    }

    //sending message with the http API
    private function serviceCaller($message, $phone, $sender)
    {
        $status = false;
        $send = new Sender("client.rmlconnect.net", "8080", "paradigm", "2hLn4PXn", $sender, $message, $phone, 0, 1);
        $response = $send->Submit();
        $this->status = $response;
        error_log($this->status);
        $response = explode("|", $response);
        $error_code = $response[0];
        if ($error_code == "1701") {
            $status = true;
            $this->status = "Message sent successfully!";
        } else {
            $this->status = "Message not sent";
        }
        return $status;
    }

    public function history($user, $caller)
    {
        $response = array();
        $response['response'] = array();
        try {
            $header = array('No', 'Time', 'Message subject', 'Recipient', 'Status');

            $list = R::getAll("SELECT sent_on,subject,recipient,status FROM message WHERE sender='$user'");
            if (count($list) != 0) {
                $tableContent = array();
                if ($caller == "site") {
                    for ($row = 0; $row < count($list); $row++) {
                        $rowNumber = $row + 1;
                        $time = $list[$row]['sent_on'];
                        $subject = $list[$row]['subject'];
                        $recipient = $list[$row]['recipient'];
                        $status = $list[$row]['status'];
                        if ($status == 0) {
                            $status = "<span class='text-danger'>Failed <i class='fa fa-thumbs-down'></i></span>";
                        } else {
                            $status = "<span class='text-success'>Succeeded <i class='fa fa-thumbs-up'></i></span>";
                        }
                        $tableContent[$row] = array($rowNumber, $time, $subject, $recipient, $status);
                    }
                    $this->displayTable($header, $tableContent, null);
                } else {
                    $result = array("error_code" => 0, "error_txt" => "success", "messages" => $list);
                    array_push($response['response'], $result);
                }
            } else {
                if ($caller == "site") {
                    $this->displayTable($header, null, null);
                } else {
                    $result = array("error_code" => 1, "error_txt" => "no result");
                    array_push($response['response'], $result);
                }
            }
        } catch (Exception $e) {
            error_log($e);
            $this->displayTable($header, null, null);
        }
        return $response;
    }

    //THE COUNTER FUNCTION
    public function counter($criteria, $user)
    {
        $number = 0;
        try {
            if ($criteria == "sent" && !isset($user)) {
                $sql = "SELECT * FROM message WHERE type='sms' AND sent_on > '$this->startTime' AND sent_on<'$this->endTime'";
            } else if ($criteria == "failed" && !isset($user)) {
                $sql = "SELECT * FROM message WHERE type='sms' AND sent_on > '$this->startTime' AND sent_on<'$this->endTime' AND status = '0'";
            } else if ($criteria == "sent" && isset($user)) {
                $sql = "SELECT * FROM message WHERE type='sms'AND sender='$user'";
            } else if ($criteria == "failed" && isset($user)) {
                $sql = "SELECT * FROM message WHERE type='sms' AND sender='$user' AND status = '0'";
            } else if ($criteria == "success" && isset($user)) {
                $sql = "SELECT * FROM message WHERE type='sms' AND sender='$user' AND status = '1'";
            }
            $sms = R::getAll($sql);
            $number = count($sms);
        } catch (Exception $e) {
            error_log($e);
        }
        return $number;
    }

    /*
    CHECK IF STOCK EXISTS
     *      */

    public function stockBalance($user)
    {
        $response = array();
        try {
            $quantity = R::getCell("SELECT quantity FROM stock WHERE client='$user'");
            $response = array("status" => true, "quantity" => $quantity);
        } catch (Exception $e) {
            error_log($e);
            $response = array("status" => false, "quantity" => $quantity);
        }
        return $response;
    }

}

/**
 * <h1>dashboard</h1>
 * <p>This class is to handle the dashboard of the application.</p>
 */
class dashboard
{

    /**
     * Setting the values in the dashboard
     */
    public $title = [];
    public $number = [];

    public function __construct()
    {
        $this->populate();
    }

    /**
     * <h1>populate</h1>
     * <p>Populating the values to be displayed in the dashboard</p>
     */
    public function populate()
    {
        $userObj = new user();
        $messageObj = new message();
        $notificationObj = new notification();
        $userObj = new user();
        if (isset($_SESSION['user_id'])) {
            $userType = $userObj->getUserType($_SESSION['user_id']);
            if ($userType == "administrator") {
                $titleList = ["Users", "Notifications", "Messages", "Log"];
                $countList = [$userObj->count, $notificationObj->count, $messageObj->count, "-"];
            } else {
                $titleList = ["Users", "Notifications", "Messages", "N/A"];
                $countList = [$userObj->count, $notificationObj->count, $messageObj->count, "-"];
            }
            $this->number = $countList;
            $this->title = $titleList;
        }

    }

}

class web extends main
{

    public $status = "";

    /**
     * <h1>showContent</h1>
     * <p>This method is to show the specified content of the web.</p>
     */
    public function showContent($title, $formatType, $attributes)
    {
        $subject = new subject();
        $format = new sectionFormat();
        if (isset($title) && isset($formatType) && isset($attributes)) {
            $content = new content();
            for ($count = 0; $count < count($attributes); $count++) {
                $attributList[$count] = ["name" => $attributes[$count]];
            }
            $contentList = $this->fetchBuilder($title, $attributList);
            for ($outer = 0; $outer < count($contentList); $outer++) {
                $contentItem = $contentList[$outer];
                switch ($formatType) {
                    case 1: //slide
                        $format->showSlider($contentItem[1], $contentItem[2], $contentItem[3]);
                        break;
                    case 2: //features
                        $format->showFeature($contentList);
                        break;
                    case 3:
                        /*
                         * TODO: Add implementation
                         */
                        break;
                }
                if ($formatType == 2) {
                    break;
                }
            }
        } else {
            error_log("ERROR:Missing content specifications");
        }
    }

}

class Sender
{

    public $host;
    public $port;
    /*
     * Username that is to be used for submission
     */
    public $strUserName;
    /*
     * password that is to be used along with username
     */
    public $strPassword;
    /*
     * Sender Id to be used for submitting the message
     */
    public $strSender;
    /*
     * Message content that is to be transmitted
     */
    public $strMessage;
    /*
     * Mobile No is to be transmitted.
     */
    public $strMobile;
    /*
     * What type of the message that is to be sent
     * <ul>
     * <li>0:means plain text</li>
     * <li>1:means flash</li>
     * <li>2:means Unicode (Message content should be in Hex)</li>
     * <li>6:means Unicode Flash (Message content should be in Hex)</li>
     * </ul>
     */
    public $strMessageType;
    /*
     * Require DLR or not
     * <ul>
     * <li>0:means DLR is not Required</li>
     * <li>1:means DLR is Required</li>
     * </ul>
     */
    public $strDlr;

    private function sms__unicode($message)
    {
        $hex1 = '';
        if (function_exists('iconv')) {
            $latin = @iconv('UTF-8', 'ISO-8859-1', $message);
            if (strcmp($latin, $message)) {
                $arr = unpack('H*hex', @iconv('UTF-8', 'UCS-2BE', $message));
                $hex1 = strtoupper($arr['hex']);
            }
            if ($hex1 == '') {
                $hex2 = '';
                $hex = '';
                for ($i = 0; $i < strlen($message); $i++) {
                    $hex = dechex(ord($message[$i]));
                    $len = strlen($hex);
                    $add = 4 - $len;
                    if ($len < 4) {
                        for ($j = 0; $j < $add; $j++) {
                            $hex = "0" . $hex;
                        }
                    }
                    $hex2 .= $hex;
                }
                return $hex2;
            } else {
                return $hex1;
            }
        } else {
            print 'iconv Function Not Exists !';
        }
    }

//Constructor..
    public function __construct($host, $port, $username, $password, $sender, $message, $mobile, $msgtype, $dlr)
    {
        $this->host = $host;
        $this->port = $port;
        $this->strUserName = $username;
        $this->strPassword = $password;
        $this->strSender = $sender;
        $this->strMessage = $message; //URL Encode The Message..
        $this->strMobile = $mobile;
        $this->strMessageType = $msgtype;
        $this->strDlr = $dlr;
    }

    private function send_hex()
    {
        $this->strMessage = $this->sms__unicode(
            $this->strMessage);
        try {
            //Smpp http Url to send sms.
            $live_url = "http://" . $this->host . ":" . $this->port . "/bulksms/bulksms?username=" . $this->strUserName .
            "&password=" . $this->strPassword . "&type=" . $this->strMessageType . "&dlr=" . $this->strDlr . "&destination=" .
            $this->strMobile . "&source=" . $this->strSender . "&message=" . $this->strMessage . "";
            $parse_url = file($live_url);
            echo $parse_url[0];
        } catch (Exception $e) {
            echo 'Message:' . $e->getMessage();
        }
    }

    //send sms with curl
    private function send_sms_curl()
    {
        $response = "";
        //Smpp http Url to send sms.
        $url = "http://" . $this->host . ":" .
        $this->port . "/bulksms/bulksms?username=" . $this->strUserName . "&password=" . $this->strPassword .
        "&type=" . $this->strMessageType . "&dlr=" . $this->strDlr . "&destination=" . $this->strMobile .
        "&source=" . $this->strSender .
        "&message=" . $this->strMessage . "";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $contents = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log("SEND SMS CURL:" . curl_error($ch));
            $contents = '';
        } else {
            curl_close($ch);
        }
        if (!is_string($contents) || !strlen($contents)) {
            $contents = 'Failed to get contents.';
        }
        return $contents;
    }

    //Sending the sms plain
    private function send_sms()
    {
        $this->strMessage = urlencode($this->strMessage);
        try {
//Smpp http Url to send sms.
            $live_url = "http://" . $this->host . ":" .
            $this->port . "/bulksms/bulksms?username=" . $this->strUserName . "&password=" . $this->strPassword .
            "&type=" . $this->strMessageType . "&dlr=" . $this->strDlr . "&destination=" . $this->strMobile .
            "&source=" . $this->strSender .
            "&message=" . $this->strMessage . "";
            $parse_url = file($live_url);
            $response = $parse_url[0];
        } catch (Exception $e) {
            $response = $e->getMessage();
        }
        return $response;
    }

    public function Submit()
    {
        $response = "";
        if ($this->strMessageType == "2" ||
            $this->strMessageType == "6") {
            //Call The Function Of String To HEX.
            $response = $this->send_hex();
        } else {
            $response = $this->send_sms_curl();
        }
        return $response;
    }

}
/*
 * Handling all upload process
 */
class file_handler extends main
{

    public $status = "";
    public $fileId = "";
    public $filePath = "";

    /**
     * <h1>upload</h1>
     * Uploading the and image
     * @param $file the name of the image to be uploaded
     * @param $category The category in which the image can be described in
     */
    public function upload($file)
    {
        //GETTING THE PARAMETERS TO READ
        //PHONE => DEFINE COLUMN TO READ
        $db_file_name = basename($file['name']);
        $ext = explode(".", $db_file_name);
        $fileExt = end($ext);
        if ($fileExt == "jpeg" || $fileExt == "png" || $fileExt == "jpg") {

            $upload_errors = array(
                // http://www.php.net/manual/en/features.file-upload.errors.php

                UPLOAD_ERR_OK => "No errors.",
                UPLOAD_ERR_INI_SIZE => "Larger than upload_max_filesize.",
                UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE.",
                UPLOAD_ERR_PARTIAL => "Partial upload.",
                UPLOAD_ERR_NO_FILE => "No file.",
                UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
                UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
                UPLOAD_ERR_EXTENSION => "File upload stopped by extension.",
            );

            if (!$file || empty($file) || !is_array($file)) {
                $this->status = $this->feedbackFormat(1, "No file was attached");
            } else if ($file["error"] != 0) {
                $this->status = $this->feedbackFormat(0, $upload_errors[$file["error"]]);
            } else if ($file["error"] == 0) {
                $size = $file['size'];
                $type = $file['type'];
                $temp_name = $file['tmp_name'];
                $db_file_name = basename($file['name']);
                $ext = explode(".", $db_file_name);
                $fileExt = end($ext);
                $taget_file = rand(100000000000, 999999999999) . "." . $fileExt;
                $directory = "../../images/uploaded/";
                if (!is_dir($directory)) {
                    mkdir($directory, 0777);
                }
                $path = $directory . $taget_file;
                if (move_uploaded_file($temp_name, $path)) {
                    try {
                        $fileDetails = R::dispense("image");
                        $fileDetails->name = $taget_file;
                        $fileDetails->path = "../images/uploaded/" . $taget_file;
                        $fileDetails->added_on = date("Y-m-d h:m:s");
                        $fileDetails->added_by = $_SESSION['user_id'];
                        $fileDetails->status = false;
                        $fileId = R::store($fileDetails);
                        $this->filePath = "../images/uploaded/" . $taget_file;
                        $this->status = json_encode(array('id' => $fileId, 'type' => 'success', 'text' => "Upload successful", 'path' => $path));
                    } catch (Exception $e) {
                        $this->status = $this->feedbackFormat(0, "Image not added");
                        error_log($e);
                    }
                } else {
                    $this->status = $this->feedbackFormat(0, "Failed to add file");
                }
            }
        } else {
            $this->status = $this->feedbackFormat(0, "The File is not an image.");
        }
        return $this->status;
    }

}
