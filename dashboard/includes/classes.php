<?php

session_start();
/**
 * Created on: 30th September 2016
 * Created by: David NIWEWE
 *
 */
require 'rb.php';
require 'config.php';
require 'sectionFormat.php';
//require_once "../../vendor/autoload.php";
require 'SMTP.php';
require 'PHPMailer.php';
$connection = new connection();
R::setup("pgsql:host=$connection->host;dbname=$connection->db", $connection->db_user, $connection->pass_phrase);

//to allow underscores in table creation
R::ext('xdispense', function ($type) {
    return R::getRedBean()->dispense($type);
});

$main = new main();

class UIfeeders {

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
    public function comboBuilder($content, $defValue, $defDisplay) {
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
    public function feedModal($subject, $occurenceId) {
        $subjectObj = new subject();
        $subjectId = $subjectObj->getId($subject);
        $component = new main();
        $component->subjectOccurenceId = $occurenceId;
        $component->subjectTitle = $subject;
        $component->formBuilder($subjectId, "update");
    }

    /**
     * <h1>isDataTypeTable</h1>
     * <p>Verifies if datatype is table</p>
     * @param String $dataType The data type to be verified
     */
    public function isDataTypeTable($dataType) {
        $isTable = false;
        $mainObj = new main();
        $schema = $mainObj->dbname;
        if (isset($dataType)) {
            try {
                $tableList = R::getAll("SELECT table_name FROM information_schema.tables
                WHERE table_catalog = '$schema' AND table_schema   = 'public'");
                if (count($tableList) > 0) {
                    for ($count = 0; $count < count($tableList); $count++) {
                        if ($tableList[$count]['table_name'] == $dataType) {
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
    public function isDataTypeColumn($dataType) {
        $isColumn = false;
        $mainObj = new main();
        $schema = $mainObj->dbname;
        if (isset($dataType)) {
            try {
                $columnList = R::getAll("SELECT column_name
                                    FROM information_schema.columns
                                    WHERE table_catalog = '$schema'
                                    AND table_schema   = 'public'");
                if (count($columnList) > 0) {
                    for ($count = 0; $count < count($columnList); $count++) {
                        if ($columnList[$count]['column_name'] == $dataType) {
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
    public function isDataTypeDefault($dataType) {
        $isDefault = false;
        $dataType = strtolower($dataType);
        if (isset($dataType) &&
                ($dataType == "text" || $dataType == "numeric" || $dataType == "date") || $dataType == "file" || $dataType == "unique text" || $dataType == "long text" || $dataType == "password") {
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
class main extends UIfeeders {

    public $status;
    public $appName = APP_NAME;
    public $author = APP_AUTHOR;

    public function __construct() {
        $connection = new connection();
        $this->dbname = $connection->db;
    }

    /**
     * <h1>feedbackFormat</h1>
     * <p>This method is to format for performed action</p>
     * @param Integer $status The status of the message
     * @param String $text the message to be displayed on the screen
     */
    public function feedbackFormat($status, $text) {
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

    public function displayMessageTable($header, $message, $action) {
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
    public function displayTable($header, $body, $action) {
        /*
         * start table
         */
        echo "<div class='panel-body'>";
        echo "<div class='table-responsive'>";
        echo "<table class='details-table-view display table table-striped table-bordered table-hover' style='width: 100%; cellspacing: 0;'>";

        /*
         * display headers
         */
        echo "<thead>";
        for ($count = 0; $count < count($header); $count++) {
            $headerTitle = str_replace("_", " ", $header[$count]);
            echo "<th>" . $headerTitle . "</th>";
        }
        //by default show the action
        if (!isset($action) || $action == true) {
            echo '<th>Action</th>';
        }
        echo "</thead>";
        /*
         * table body
         */
        echo "<tbody>";
        for ($row = 0; null !== $body && $row < count($body); $row++) { //row
            echo "<tr>";
            for ($col = 1; $col <= count($header); $col++) {
                echo "<td>" . $body[$row][$col] . "</td>";
            }
            //action
            if (!isset($action) || $action == true) {
                $this->tableAction($body[$row][0]);
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
    private function tableAction($rowId) {
        echo "<td>" .
        "<a class='open-UpdateItemDialog btn btn-info' data-toggle='modal' data-target='#editModal' title='Edit' data-table_data='$rowId'>
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
    public function makeLinks($action) {
        try {
            $userObj = new user();
            if (isset($_SESSION["username"]) && $userObj->getUserType($_SESSION["username"]) == "author") {
                $subjects = R::getAll("SELECT id,title,type FROM subject WHERE type='single' OR type='container'");
            } else {
                $subjects = R::getAll("SELECT id,title,type FROM subject WHERE type='single' OR type='container'");
            }
            if (count($subjects) > 0) {
                for ($count = 0; null !== $subjects && $count < count($subjects); $count++) {
                    $subjectId = $subjects[$count]['id'];
                    $subjectTitle = $subjects[$count]['title'];
                    $user = new user();
                    if ($user->isUserAllowed($action, $subjectTitle)) {
                        $subjectTitle = str_replace("_", " ", $subjectTitle);
                        echo "<li><a href='" . $action . "_details.php?article=$subjectId'>" . $subjectTitle . "</a></li>";
                    }
                }
            }
        } catch (Exception $e) {
            error_log("MAIN(makeLinks):" . $e);
        }
    }

    /**
     * <h1>header</h1>
     * <p>This is the method to display the header of the page</p>
     * @param Int $subject The ID of the subject to refer to.
     */
    public function header($subject) {
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
     * creating tabs
     *
     */
    public function tabBuilder($subjectTitle) {
        if (isset($subjectTitle)) {
            $subjectObj = new subject();
            $tabsList = $subjectObj->getChildSubjectList($subjectTitle);
            $this->tabCreator($tabsList);
        }
    }

    /**
     * Creating tabs
     *
     */
    private function tabCreator($tabsList) {
        if (null !== $tabsList && count($tabsList) > 0) {
            for ($counter = 0; $counter < count($tabsList); $counter++) {
                $isActive = "";
                if ($counter == 0) {
                    $isActive = "active";
                }
                $tabTitle = str_replace("_", " ", $tabsList[$counter]['title']);
                echo '<li class="$isActive">';
                echo '<a href="#' . $tabsList[$counter]['title'] . '" data-toggle="tab">' . $tabTitle . '</a>';
                echo '</li>';
            }
        }
    }

    /**
     * <h1>formBuilder</h1>
     * <p>This form is the build the form input</p>
     * @param Integer $subjectId This the ID of the subject being viewed
     * @param String $caller The calling environment
     */
    public function formBuilder($subjectId, $caller) {

        $title = "";
        try {

            if (!empty($subjectId)) {
                $subjectId = $subjectId;
                $userObj = new user();
                $userType = $userObj->getUserType($_SESSION['username']);
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
    private function formInterface($subject, $subjectId, $caller) {
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
                echo "<input type='submit' class='btn btn-dark' id='save-$subjectId' onclick='saveArticle(this);' name='action' value='Save'>";
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
    private function inputGenerator($id, $name, $type, $caller) {
        if (isset($this->subjectOccurenceId)) {
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
                case 'unique text':
                    $input = "<input type='text' required name='$name' id='$caller" . "_" . "$name' class='form-control' $holder='$value'>";
                    break;
                case 'password':
                    $input = "<input type='password' required name='$name' id='$caller" . "_" . "$name' class='form-control' $holder='$value'>";
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

    private function referentialDataInputGenerator($id, $name, $type, $caller) {
        $input = "";
        $optionString = "";
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
                        $optionString = "<option>No choice available</option>";
                    }
                    for ($counter = 0; $counter < count($referenceValues); $counter++) {
                        $optionString = $optionString . "<option value='" . $referenceValues[$counter] . "'>" . $referenceValues[$counter] . "</option>";
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
    private function getValue($col) {
        $value = "Not set";
        try {
            $occurenceId = $this->subjectOccurenceId;
            $subject = str_replace(" ", "_", $this->subjectTitle);
            $value = R::getCell("SELECT DISTINCT $col FROM $subject WHERE id='$occurenceId'");
        } catch (Exception $e) {
            error_log("MAIN[getValue]:" . $e);
        }
        return $value;
    }

    //BUILDING THE SELECT
    public function fetchBuilder($table, $columnList) {
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
        $userObj = new user();
        $sql = "SELECT id," . $query . " FROM " . $table;
        //executing the query
        try {
            $values = R::getAll($sql);
            //building the table content
            $rows = array();
            for ($count = 0; $count < count($values); $count++) { //feed row
                $columns = array();
                $columns[0] = $table . "-" . $values[$count]['id'];
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
    public function getTables($showCombo) {
        $tableList = null;
        try {
            $mainObj = new main();
            $schema = $mainObj->dbname;
            $tableList = R::getAll("SELECT table_name FROM information_schema.tables WHERE table_catalog = '$schema' AND table_schema   = 'public'");
            if (null !== $showCombo && $showCombo == true) {
                $this->comboBuilder($tableList, "table_name", "table_name");
            }
        } catch (Exception $e) {
            error_log("ERROR(main:getTables)" . $e);
        }
        return $tableList;
    }

    /**
     * <h1>getTableColumns</h1>
     * <p>
     * This function returns the list of all columns belonging to the specified table.
     * </p>
     * @param String $tableName The name of the table to be specified
     */
    public function getTableColumns($tableName) {
        $columnList = null;
        try {
            $dbname = $this->dbname;
            if (!$this->isDataTypeTable($tableName) && isset($_SESSION['ref_data_type']) && !$this->isDataTypeColumn($tableName)) {
                $tableName = $_SESSION['ref_data_type'];
            } else if (isset($tableName) && ($this->isDataTypeTable($tableName) && !$this->isDataTypeColumn($tableName))) {
                $_SESSION['ref_data_type'] = $tableName;
            } else if (isset($tableName) && (!$this->isDataTypeTable($tableName) && $this->isDataTypeColumn($tableName))) {
                $_SESSION['ref_data_value'] = $columnName = $tableName;
            }
            if (isset($columnName) && $this->isDataTypeColumn($columnName) && isset($_SESSION['ref_data_type'])) {
                $columnList[0] = array("column_name" => $_SESSION['ref_data_type'] . "|" . $_SESSION['ref_data_value'], "column_type" => $_SESSION['ref_data_type'] . " " . $_SESSION['ref_data_value']);
            } else {
                $_SESSION['ref_data_type'] = $tableName;
                $columns = R::getAll("SELECT column_name
                                            FROM information_schema.columns
                                            WHERE table_catalog = '$dbname'
                                            AND table_schema   = 'public' AND table_name='$tableName'");
                for ($counter = 0; $counter < count($columns); $counter++) {
                    $columnList[$counter] = array("column_name" => $columns[$counter]['column_name'], "column_type" => $_SESSION['ref_data_type'] . " " . $columns[$counter]['column_name']);
                }
            }
            $this->comboBuilder($columnList, "column_name", "column_type");
        } catch (Exception $exc) {
            error_log("ERROR(main:getTableColumns)" . $exc);
        }
    }

    /**
     * isTableExisting
     * Verifies if the tables is available and returns false or true
     * @param String  $tableName The name of the table you want to check
     */
    public function isTableExisting($tableName) {
        $tableExists = false;
        if (isset($tableName)) {
            $tableList = $this->getTables(false);
            //verify if table is dropped
            for ($counter = 0; null !== $tableList && $counter < count($tableList); $counter++) {
                if ($tableList[$counter]['table_name'] == $tableName) {
                    $tableExists = TRUE;
                    break;
                }
            }
        } else {
            error_log("ERROR(isTableExisting): unable to verify no table specified");
        }
        return $tableExists;
    }

    /*
     * validating the numbers
     */

    public function standardize($phone) {
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
class user extends main {

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

    public function __construct() {
        $this->count();
    }

    /**
     * <h1>fetch</h1>
     * <p>Counting the user of the system</p>
     */
    public function count() {
        $users = [];
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $loggedInType = $this->getUserType($username);
            if ($loggedInType == "administrator") {
                try {
                    $users = R::getAll("SELECT type FROM credentials JOIN user_profile ON credentials.user=user_profile.id");
                    $this->count = count($users);
                } catch (Exception $e) {
                    error_log("ERROR(USER:COUNT):" . $e);
                }
            } else {
                try {

                    if ($loggedInType != null && $loggedInType != "initial" && $loggedInType != "author") {
                        $type = R::getCell("SELECT DISTINCT id FROM role WHERE title='$loggedInType'");
                        $users = R::getAll("SELECT type FROM credentials JOIN user_profile ON credentials.user=user_profile.id WHERE type='$type'");
                        $this->count = count($users);
                    } else if ($loggedInType == "initial") {
                        $users = R::getAll("SELECT * FROM user_profile");
                        $this->count = count($users);
                    } else if ($loggedInType == "author") {
                        $users = R::getAll("SELECT * FROM user_profile");
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
    public function userList() {
        $header = array('No', 'Names', 'Email', 'Tel', 'Category');
        try {
            $type = $_SESSION["type"];
            if ($type === null) {
                error_log("ERROR:internal:Invalid user type");
            } else {
                if (isset($type)) {
                    if ($type == 0) {
                        $users = R::getAll("SELECT user_profile.id,fname,lname,oname,email,phone,credentials.user,credentials.username,credentials.type FROM user_profile INNER JOIN credentials ON user_profile.id=credentials.user");
                    } else {
                        $users = R::getAll("SELECT user_profile.id,fname,lname,oname,email,phone,credentials.user,credentials.username,credentials.type FROM user_profile INNER JOIN credentials ON user_profile.id=credentials.user WHERE type='$type'");
                    }
                }
                if (count($users) == 0) {
                    $this->displayTable($header, null, null);
                } else {
                    $tableContent = array();
                    for ($row = 0; $row < count($users); $row++) {
                        $rowNumber = $row + 1;
                        $username = $users[$row]['id'];
                        $names = $users[$row]['fname'] . " " . $users[$row]['lname'];
                        $email = $users[$row]['email'];
                        $tel = $users[$row]['phone'];
                        $type = $this->getUserType($users[$row]['username']);
                        $tableContent[$row] = array($username, $rowNumber, $names, $email, $tel, $type);
                    }
                    $this->displayTable($header, $tableContent, null);
                }
            }
        } catch (Exception $e) {
            error_log("UNABLE TO LOAD LIST OF USERS" . $e);
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
    public function add($fname, $lname, $oname, $email, $tel, $address, $username, $password, $type) {
        $isCreated = false;
        if ($this->isUsernameValid($username)) {
            //saving user credentials
            try {
                //saving user details
                $user_details = R::xdispense("user_profile");
                $user_details->fname = $fname;
                $user_details->lname = $lname;
                $user_details->oname = $oname;
                $user_details->email = $email;
                $user_details->phone = $tel;
                $user_details->address = $address;
                $user_id = R::store($user_details);
                if (isset($user_id)) {
                    $isCreated = $this->addCredentials($user_id, $username, $password, $type);
                }
            } catch (Exception $e) {
                $this->status = $this->feedbackFormat(0, "User not added!" . $e);
            }
        } else {
            $this->status = $this->feedbackFormat(0, "Username already exists!");
        }
        return $isCreated;
    }

    private function addCredentials($id, $username, $password, $type) {
        $isCreated = false;
        try {
            $user_credentials = R::dispense("credentials");
            $user_credentials->user = $id;
            $user_credentials->username = $username;
            $user_credentials->password = md5($password);
            $user_credentials->type = $type;
            $user_credentials->last_log = date("d-m-Y h:m:s");
            $user_credentials->status = 1;
            $cred_id = R::store($user_credentials);
            if (isset($cred_id)) {
                $this->status = $this->feedbackFormat(1, "User successfully added!");
                $isCreated = true;
            } else {
                R::exec("DELETE FROM user_profile WHERE id='$id'");
                $this->status = $this->feedbackFormat(0, "User not added!");
            }
        } catch (Exception $e) {
            $this->status = $this->feedbackFormat(0, "Error occured saving credentials" . $e);
        }
        return $isCreated;
    }

    public function initialGrant($userEmail, $passcode) {
        $initial = 1;
        if (null !== getenv("ADDAX_AUTHOR") && null !== getenv("ADDAX_PASSCODE")) {
            if ($userEmail === getenv("ADDAX_AUTHOR") && $passcode === getenv("ADDAX_PASSCODE")) {
                $initial = 0;
            }
        }
        return $initial;
    }

    /**
     * <h1>isValid</h1>
     * <p>This function validates id the username is valid for registration</p>
     * @param $username The user name to validate.
     * @return Boolean
     */
    public function isUsernameValid($username) {
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
    public function isEmailValid($email) {
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
                error_log("UNABLE TO VALIDATE EMAIL:" . $e);
                $status = false;
                $this->status = "Error checking the email" . $exc;
            }
        }
        return $status;
    }

    //evaluating logged in user
    private function evalLoggedUser($id, $u) {
        //getting the logged in user information
        try {
            $logged_user = R::getRow("SELECT id FROM credentials WHERE user_id = {$id} AND username ='{$u}'  AND user_status='1'");
            if (isset($logged_user)) {
                return true;
            }
        } catch (Exception $e) {
            error_log("UNABLE TO VERIFY A USER:" . $e);
            return false;
        }
    }

    /**
     * <h1>checkLogin</h1>
     * <p>This function verifies if the user is logged in</p>
     * @return Boolean
     */
    public function checkLogin() {
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
        }
        return $user_ok;
    }

    /**
     * <h1>login</h1>
     * <p>This is the function to login the user</p>
     * @param $username the username of the user
     * @param $password the password of the user
     */
    public function login($username, $password) {
        $password = md5($password);
        try {
            $user = R::getRow("SELECT credentials.user,user_profile.id,username,password,type FROM credentials JOIN user_profile ON credentials.user=user_profile.id WHERE username='$username' AND password='$password'");
            if (isset($user)) {
                $_SESSION['user_id'] = $db_id = $user['id'];
                $_SESSION['username'] = $db_username = $user['username'];
                $_SESSION['type'] = $db_type = $user['type'];
                setcookie("user_id", $db_id, time() + 60, "/", "", "", true);
                setcookie("username", $db_username, time() + 60, "/", "", "", true);
                setcookie("type", $db_type, time() + 60, "/", "", "", true);
                // UPDATE THEIR "LASTLOGIN" FIELDS
                try {
                    $log_time = date("Y-m-d h:m:s");
                    R::exec("UPDATE credentials SET last_log = '$log_time' WHERE id = '$db_id'");
                } catch (Exception $e) {
                    error_log("ERROR: Unable to update login information" . $e);
                }
                $this->status = $this->feedbackFormat(1, "Authentication verified");
                //header("location:../views/home.php");
            } else {
                $this->status = $this->feedbackFormat(0, "Authentication not verified");
            }
        } catch (Exception $exc) {
            error_log("LOGIN ERROR:" . $exc);
            $this->status = $this->feedbackFormat(0, "Login error");
        }
        die($this->status);
    }

    /**
     * Return the user type of the logged in user
     */
    public function getUserType($username) {
        $userType = null;
        if ($username) {
            try {
                $type_id = R::getCell("SELECT DISTINCT type FROM credentials WHERE username = '$username'");
                if ($type_id != null && $type_id > 1) {
                    $userType = R::getCell("SELECT DISTINCT title FROM role WHERE id='$type_id'");
                } else if ($type_id == 0) {
                    $userType = "author";
                } else if ($type_id == 1) {
                    $userType = "initial";
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
    public function isUserAllowed($toDo, $subject) {
        $isAllowed = false;
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $userType = $this->getUserType($username);
            if (isset($userType)) {
                if ($userType === "author") {
                    $isAllowed = true;
                } else {
                    try {
                        $userPrivilege = R::getRow("SELECT writing,reading FROM privilege WHERE subject='$subject' AND role='$userType'");

                        if (null !== $userPrivilege) {
                            if ($toDo == 'add' && $userPrivilege['writing'] == "allowed") {
                                $isAllowed = true;
                            }
                            if ($toDo == 'view' && $userPrivilege['reading'] == "allowed") {
                                $isAllowed = true;
                            }
                        }
                    } catch (Exception $e) {
                        error_log("isuserAllowed:" . $e);
                    }
                }
            }
        }
        return $isAllowed;
    }

    public function listRoleInOption() {
        try {
            $userType = $this->getUserType($_SESSION['username']);
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
     * @param Int $username The user id
     * @return Array Returns an array that contains all user information.
     */
    public function getUserDetails($username) {
        $user = null;
        try {
            $user = R::getRow("SELECT user_profile.id,fname,lname,oname,email,phone,address,credentials.user,credentials.type,credentials.username FROM user_profile INNER JOIN credentials ON user_profile.id=credentials.user WHERE user_profile.id='$username'");
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

class subject extends main {

    public $status = "";
    public $subjectId = null;
    public $title = "";
    public $attributes = [];

    //adding a new content
    public function add($subjTitle, $type, $subjAttrNumber, $subjAttributes, $subjCommenting, $subjLikes, $subjDisplayViews) {

        if ($this->isValid($subjTitle) && $subjTitle != 'subject') {
            try {
                $subject = R::dispense("subject");
                $subject->title = $subjTitle;
                $subject->type = $type;
                $subject->createdOn = date("d-m-Y h:m:s");
                $subject->createdBy = $_SESSION['username'];
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
                        error_log("UNABLE TO CREATE CONTENT" . $e);
                    }
                    $this->status = $this->feedbackFormat(0, "ERROR: article could not be created.");
                } else {
                    $this->status = $this->feedbackFormat(1, "Subject added successfully");
                }
            } catch (Exception $e) {
                $this->status = $this->feedbackFormat(0, "ERROR: subject not added");
                error_log("UNABLE TO CREATE CONTENT" . $e);
            }
        } else {
            $this->status = $this->feedbackFormat(0, "ERROR: Title already exists");
        }
    }

    /**
     * Adding the subject attributes.
     */
    private function createAttributes($attributes) {
        $isCreated = false;
        if (isset($this->subjectId)) {
            try {
                for ($counter = 0; $counter < count($attributes); $counter++) {
                    $attribute = R::dispense("attribute");
                    $attribute->subject = $this->subjectId;
                    $attribute->name = $attributes[$counter]["name"];
                    $attribute->data_type = $attributes[$counter]["type"];
                    $attribute->is_null = $attributes[$counter]["is_null"];
                    $attribute->is_unique = $attributes[$counter]["is_unique"];
                    $attribute->has_ref = $hasRef = $attributes[$counter]["has_ref"];
                    $attributeId = R::store($attribute);
                    $savedAttribute = R::getRow("SELECT * FROM attribute WHERE id='$attributeId'");
                    if ((null !== $savedAttribute && $hasRef == false) || (null !== $savedAttribute && $hasRef == true && $this->createReference($attributeId, $attributes[$counter]["reference"]))) {
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
    public function createReference($attributeId, $referenceName) {
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
    public function readReference($attrId) {
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
    public function getId($title) {
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
    public function isValid($title) {
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
    public function getAttributes($subject) {
        $response = array();
        try {
            $attributeList = R::getAll("SELECT id,name,data_type,is_null,is_unique FROM attribute WHERE subject='$subject'");
            for ($counter = 0; $counter < count($attributeList); $counter++) {
                $attrName = str_replace(" ", "_", $attributeList[$counter]["name"]);
                $attrType = $attributeList[$counter]["data_type"];
                $attrIsNull = $attributeList[$counter]["is_null"];
                $attrIsUnique = $attributeList[$counter]["is_unique"];
                $response[$counter] = array("id" => $attributeList[$counter]["id"],
                    "name" => $attrName,
                    "type" => $attrType,
                    "is_null" => $attrIsNull,
                    "is_unique" => $attrIsUnique);
            }
        } catch (Exception $e) {
            error_log("ERROR (getAttributes): " . $e);
        }
        return $response;
    }

    //GET LIST OF REGISTERED SUBJECTS
    public function getList() {
        $header = array("Title", "Created by", "Created on", "Last update");
        $tablecontent = null;
        try {
            $subjectList = R::getAll("SELECT id,title,created_by,created_on,last_update FROM subject ORDER BY created_on DESC ");
            for ($count = 0; $count < count($subjectList); $count++) {
                $title = $subjectList[$count]['title'];
                $createdBy = $subjectList[$count]['created_by'];
                $createdOn = $subjectList[$count]['created_on'];
                $lastUpdate = $subjectList[$count]['last_update'];
                $tableActionTag = "subject-" . $subjectList[$count]['id'];
                $tablecontent[$count] = array(0 => $tableActionTag, 1 => $title, 2 => $createdBy, 3 => $createdOn, 4 => $lastUpdate);
            }
            $this->displayTable($header, $tablecontent, null);
        } catch (Exception $e) {
            error_log("ERROR (getList):" . $e);
        }
    }

    /**
     * delete
     * This method deleted the subject specified
     * @param String The title of the subject.
     */
    public function delete($subjectId) {
        $isDeleted = false;
        if (isset($subjectId)) {
            try {
                $subjectTitle = R::getCell("SELECT title FROM subject WHERE id='$subjectId'");
                if (isset($subjectTitle)) {
                    R::exec("DROP TABLE $subjectTitle");
                    if ($this->isTableExisting($subjectTitle) === false) {
                        $attributeList = R::getAll("SELECT id,name,has_ref FROM attribute WHERE subject='$subjectId'");
                        for ($counter = 0; null !== $attributeList && $counter < count($attributeList); $counter++) {
                            $attributeId = $attributeList[$counter]['id'];
                            if ($attributeList[$counter]['has_ref'] == true) {
                                R::exec("DELETE FROM reference WHERE attribute='$attributeId'");
                                R::exec("DELETE FROM attribute WHERE id='$attributeId'");
                            } else {
                                R::exec("DELETE FROM attribute WHERE id='$attributeId'");
                            }
                        }
                        R::exec("DELETE FROM subject WHERE id='$subjectId'");
                        $subjectId = $this->getId($subjectTitle);
                        if (!isset($subjectId)) {
                            $isDeleted = true;
                            $this->status = $this->feedbackFormat(1, "Subject deleted successfully");
                        } else {
                            $this->status = $this->feedbackFormat(0, "Unable to delete subject");
                        }
                    } else {
                        error_log("Unable to drop table $subjectTitle");
                        $this->status = $this->feedbackFormat(0, "Unable to delete the subject (the table can't be dropped)");
                    }
                }
            } catch (Exception $e) {
                error_log("SUBJECT:DELETE" . $e);
                $this->status = $this->feedbackFormat(0, "Error occured");
            }
        } else {
            $this->status = $this->feedbackFormat(0, "Subject not specified");
            error_log("Unable to read the ID of the subject title specified.");
        }
        die($this->status);
        return $isDeleted;
    }

    public function loadContainerCombo() {
        try {
            $parentCombo = R::getCol("SELECT title FROM subject WHERE type='container'");
            for ($counter = 0; null !== $parentCombo && $counter < count($parentCombo); $counter++) {
                echo "<option value='$parentCombo[$counter]'>$parentCombo[$counter]</option>";
            }
        } catch (Exception $exc) {
            error_log("ERROR:loadParentCombo" . $exc);
        }
    }

    public function getSubjectType($articleId) {
        $type = null;
        if (isset($articleId)) {
            try {
                $type = R::getCell("SELECT type FROM subject WHERE id='$articleId'");
            } catch (Exception $exc) {
                error_log("ERROR:getSubjectType" . $exc);
            }
        }
        return $type;
    }

    public function getChildSubjectList($parentName) {
        $childList = null;
        try {
            $childList = R::getAll("SELECT id,title FROM subject WHERE type='$parentName'");
        } catch (Exception $exc) {
            error_log("ERROR:getChildSubjectList:" . $exc);
        }
        return $childList;
    }

}

/**
 * THE CONTENT CLASS
 */
class content extends main {

    public $status = "";

    //register a new article
    public function register($subjectTitle, $attributes) {
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
    public function add($content, $values, $attributes) {
        try {
            $content = str_replace(" ", "_", $content);
            $article = R::xdispense($content);
            for ($counter = 0; $counter < count($attributes); $counter++) {
                $attribute = str_replace(" ", "_", $attributes[$counter]['name']);
                $value = $values[$counter];
                $article->$attribute = $value;
            }
            $articleId = R::store($article);
            $savedArticle = R::getRow("SELECT * FROM $content WHERE id='$articleId'");
            if (null !== $savedArticle) {
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
    public function getList($subjectId) {
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

    /**
     * delete
     * This function deletes the specified instance in the table
     * @param $content the name of the table holding the value to be deleted
     * @param $instanceId The id of the value to be deleted
     */
    public function delete($content, $instanceId) {
        $isDeleted = false;
        if (isset($content) && isset($instanceId)) {
            try {
                R::exec("DELETE FROM $content WHERE id='$instanceId'");
                //verify if deleted
                $deletedId = R::getCell("SELECT id FROM $content WHERE id='$instanceId'");
                if (isset($deletedId)) {
                    error_log("ERROR(delete): Failed to delete instance($instanceId) from $content");
                    $this->status = $this->feedbackFormat(0, "Failed to delete the subject");
                } else {
                    $isDeleted = true;
                    $this->status = $this->feedbackFormat(1, "Successfully deleted!");
                }
            } catch (Exception $exc) {
                error_log("ERROR(delete)" . $exc);
                $this->status = $this->feedbackFormat(0, "Error occured deleting the content");
            }
        } else {
            error_log("ERROR(delete):Missing input");
            $this->status = $this->feedbackFormat(0, "Missing inputs");
        }
        die($this->status);
        return $isDeleted;
    }

    /**
     * update
     * This function is to update the content of a given table
     * @param $instanceId the ID of the occurence to be update
     * @param $content the table to be updated
     * @param $values the values to modify
     * @param $attributes columns to be updated
     */
    public function update($instanceId, $content, $values, $attributes) {
        $response = null;
        if (isset($instanceId) && isset($content) && isset($values) && isset($attributes)) {
            try {
                $updateTime = date("d-m-Y h:m:s");
                //keep last update time records
                $content = str_replace(" ", "_", $content);
                R::exec("UPDATE subject SET last_update='$updateTime' WHERE title='$content' ");
                $sqlUpdateString = "";
                for ($counter = 0; $counter < count($attributes); $counter++) {
                    $attribute = str_replace(" ", "_", $attributes[$counter]['name']);
                    $value = $values[$counter];
                    if (isset($value)) {
                        if ($counter == 0) {
                            $sqlUpdateString = $attribute . "='" . $value . "'";
                        } else {
                            $sqlUpdateString = $sqlUpdateString . "," . $attribute . "='" . $value . "'";
                        }
                    }
                }
                R::exec("UPDATE $content SET $sqlUpdateString WHERE id='$instanceId'");
                $response = $this->feedbackFormat(1, "Updated successfully");
            } catch (Exception $exc) {
                error_log("ERROR(update): " . $exc);
                $response = $this->feedbackFormat(0, "Error occured updating " . str_replace("_", " ", $content));
            }
        } else {
            error_log("ERROR(update):Missing inputs");
            $response = $this->feedbackFormat(0, "Missing inputs");
        }
        return $response;
    }

    //adding a comment
    public function comment() {
        
    }

}

/**
 * <h1>message</h1>
 * <p>This is the class to handle the communication through the system</p>
 *
 */
class message extends main {

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

    public function __construct() {
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
    public function send($sender, $email, $message) {
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
        try {
            $messageQR = R::dispense("message");
            $messageQR->sender = $sender;
            $messageQR->email = $email;
            $messageQR->message = $message;
            $messageQR->receiver = getenv("ADDAX_AUTHOR");
            $messageQR->created_on = date("Y-m-d h:m:s");
            $messageQR->status = 0;
            R::store($messageQR);
            $this->status = $this->feedbackFormat(1, "Message sent successfully!");
        } catch (Exception $e) {
            $this->status = $this->feedbackFormat(0, "Unable to post message!");
            error_log("ERROR(web:postContactMessage)" + $e);
        }
        die($this->status);
    }

    /**
     * <h1>count</h1>
     * <p>This is the method count message.</p>
     */
    public function count() {
        $userObj = new user();
        $username = $_SESSION['username'];
        $message = ["sent" => 0, "received" => 0, "not read" => 0];
        try {
            $notRead = R::getAll("SELECT id,sender,message,created_on FROM message WHERE receiver='$username' AND status='0'");
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
    public function receive() {
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
    public function read($messageId) {

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

    private function alertDisplayFormat($messageDetails) {
        echo '<li>
                    <a href="' . $messageDetails['link'] . '">
                        <div class="msg-img"><div class="online off"></div><img class="img-circle" src="../images/noimage-team.png" alt=""></div>
                        <p class="msg-name">' . $messageDetails['sender'] . '</p>
                        <p class="msg-text">' . $messageDetails['message'] . '</p>
                        <p class="msg-time"></p>
                    </a>
                </li>';
    }

    private function fetch() {
        $userObj = new user();
        $username = $_SESSION['username'];
        $userType = $userObj->getUserType($username);
        try {
            $notRead = R::getAll("SELECT id,sender,message,created_on,status FROM message WHERE receiver='$username' AND status='0'");
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
            $received = R::getAll("SELECT id,sender,message,created_on,status FROM message WHERE receiver='$userType' OR receiver='$username' ");
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

    public function alert() {
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

    /**
     * <h1>send</h1>
     * <p>This is the method to send an email </p>
     */
    public function sendEmail($reciever) {
        $isSent = false;
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->From = "davejuelz@gmail.com";
        $mail->FromName = "David NIWEWE";
        $mail->addAddress($reciever);
        $mail->addReplyTo("davejuelz@gmail.com", "Reply");
        $mail->isHTML(true);
        $mail->Subject = "Addax verify email";
        $mail->Body = "Your email has been verified login <a href='https://addax.herokuapp.com/dashboard'>here</a>";
        try {
            $isSent = $mail->Send();
            if ($isSent != true) {
                error_log("ERROR: unable to send email." . $mail->ErrorInfo);
            }
        } catch (Exception $exc) {
            error_log("ERROR(sendEmail)" . $exc);
        }
    }

}

/**
 * <h1>notification</h1>
 * <p>This class is to handle notification</p>
 */
class notification extends main {

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

    public function __construct() {
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
    private function alertDisplayFormat($notificationDetails) {
        echo ' <li>
                    <a href="' . $notificationDetails['link'] . '">
                        <div class="task-icon badge badge-success"><i class="icon-pin"></i></div>
                        <span class="badge badge-roundless badge-default pull-right">' . $notificationDetails['time'] . '</span>
                        <p class="task-details">' . $notificationDetails['description'] . '.</p>
                    </a>
                </li>';
    }

    public function alert() {
        $userObj = new user();
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $userType = $userObj->getUserType($username);
            try {
                $userTypeCode = R::getCell("SELECT DISTINCT type FROM credentials WHERE user='$username' LIMIT 1");
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
                $notificationPNP = R::getAll("SELECT id,title,description,created_on FROM notification WHERE privacy='2' AND dedicated='$username' ORDER BY created_on DESC");
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
    }

    /**
     * <h1>notify</h1>
     * <p>This method is to notify about recent activity</p>
     * @param
     */
    public function add($notificationDetails) {
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
    public function count() {
        $userObj = new user();
        $username = $_SESSION['username'];
        $userType = $userObj->getUserType($username);
        try {
            /*
             * Getting the user type
             */
            $userTypeCode = R::getCell("SELECT DISTINCT type FROM credentials WHERE user='$username' LIMIT 1");
            /*
             * Counting notifications dedicated to user types
             */
            $notificationUL = R::getAll("SELECT DISTINCT id FROM notification WHERE privacy='1' AND dedicated='$userTypeCode' AND status='0'");
            /*
             * Counting notification dedicated to the logged in user
             */
            $notificationPNP = R::getAll("SELECT DISTINCT id FROM notification WHERE privacy='2' AND dedicated='$username' AND status='0'");
            $this->count = count($notificationUL) + count($notificationPNP);
            if ($this->count > 0) {
                $this->head = "You have " . $this->count . " notifications!";
            }
        } catch (Exception $e) {
            error_log("NOTIFICATION(count):" . $e);
        }
    }

    public function fetch() {
        $userObj = new user();
        $username = $_SESSION['username'];
        $userType = $userObj->getUserType($username);
        $details = [];
        try {
            $userTypeCode = R::getCell("SELECT DISTINCT type FROM credentials WHERE user='$username' LIMIT 1");
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
            $notificationPNP = R::getAll("SELECT id,title,description,created_on FROM notification WHERE privacy='2' AND dedicated='$username' ORDER BY created_on DESC");
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
    public function read($messageId) {
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
    public function receive() {
        $notified = $this->notified;
        if (count($notified) > 0) {
            $this->displayMessageTable(null, $notified, null);
        } else {
            //TODO: Add no data to display format
        }
    }

}

//the  sms class
class sms extends main {

    public $status = "";

    //sending the sms
    public function send($recipient, $subject, $message) {
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
    private function serviceCaller($message, $phone, $sender) {
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

    public function history($user, $caller) {
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
    public function counter($criteria, $user) {
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

    public function stockBalance($user) {
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
class dashboard {

    /**
     * Setting the values in the dashboard
     */
    public $title = [];
    public $number = [];

    public function __construct() {
        $this->populate();
    }

    /**
     * <h1>populate</h1>
     * <p>Populating the values to be displayed in the dashboard</p>
     */
    public function populate() {
        $userObj = new user();
        $messageObj = new message();
        $notificationObj = new notification();
        $userObj = new user();
        if (isset($_SESSION['username'])) {
            $userType = $userObj->getUserType($_SESSION['username']);
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

class web extends main {

    public $status = "";

    /**
     * <h1>showContent</h1>
     * <p>This method is to show the specified content of the web.</p>
     */
    public function showContent($title, $formatType, $attributes) {
        $subject = new subject();
        $format = new sectionFormat();
        if (isset($title) && isset($formatType) && isset($attributes)) {
            $content = new content();
            for ($count = 0; $count < count($attributes); $count++) {
                $attributList[$count] = ["name" => $attributes[$count]];
            }
            $contentList = $this->fetchBuilder($title, $attributList);
            for ($outer = 0; null !== $contentList && $outer < count($contentList); $outer++) {
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

class Sender {

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

    private function sms__unicode($message) {
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
    public function __construct($host, $port, $username, $password, $sender, $message, $mobile, $msgtype, $dlr) {
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

    private function send_hex() {
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
    private function send_sms_curl() {
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
    private function send_sms() {
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

    public function Submit() {
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

class file_handler extends main {

    public $status = "";
    public $fileId = "";
    public $filePath = "";

    /**
     * <h1>upload</h1>
     * Uploading the and image
     * @param $file the name of the image to be uploaded
     * @param $category The category in which the image can be described in
     */
    public function upload($file) {
        $isUploaded = false;
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
                error_log("ERROR(upload):No file was attached");
            } else if ($file["error"] != 0) {
                $this->status = $this->feedbackFormat(0, $upload_errors[$file["error"]]);
                error_log("ERROR(upload)" . $upload_errors[$file["error"]]);
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
                        if (isset($fileId)) {
                            error_log("file id" . $fileId);
                            $isUploaded = true;
                        }
                        $this->filePath = "../images/uploaded/" . $taget_file;
                        $this->status = json_encode(array('id' => $fileId, 'type' => 'success', 'text' => "Upload successful", 'path' => $path));
                    } catch (Exception $e) {
                        $this->status = $this->feedbackFormat(0, "Image not added");
                        error_log($e);
                    }
                } else {
                    $this->status = $this->feedbackFormat(0, "Failed to add file");
                    error_log("ERROR(upload):Failed to add file");
                }
            }
        } else {
            $this->status = $this->feedbackFormat(0, "The File is not an image.");
            error_log("ERROR(upload):The File is not an image.");
        }
        return $isUploaded;
    }

}

class validation extends main {

    /**
     * Checking the uniqueness of the value to be entered.
     */
    public function isUnique($tableName, $columnName, $value) {
        $isUnique = true;
        try {
            $tableList = $this->getTables(false);
            if (null !== $tableList) {
                for ($counter = 0; $counter < count($tableList); $counter++) {
                    if ($tableList[$counter]['table_name'] == $tableName) {
                        $colValue = R::getCell("SELECT DISTINCT $columnName FROM $tableName WHERE $columnName='$value'");
                        if (isset($colValue) && $colValue == $value) {
                            $isUnique = false;
                        }
                        break;
                    }
                }
            }
        } catch (Exception $exc) {
            error_log("ERROR:isUnique:" . $exc);
            $isUnique = false;
        }
        return $isUnique;
    }

    /**
     * Checking if the email entered is valid.
     */
    public function isValidEmail($email) {
        $isValid = true;
        if (null === $email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $isValid = false;
        }
        return $isValid;
    }

    public function isActionValid($action) {
        $user = new user();
        $isValid = false;
        //TODO: remove the delete_subject
        if (!empty($action) && ($action == "Login" || $action == "sign_up") || $action == "delete_subject" || $action == "send_message") {
            $isValid = true;
        } else if ($user->checkLogin()) {
            $isValid = true;
        }
        return $isValid;
    }

}
