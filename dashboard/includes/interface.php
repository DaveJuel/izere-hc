<?php

//Interface
require 'classes.php';
$user = new user();
$subject = new subject();
$content = new content();
$web = new web();
$dashboard = new dashboard();
$message = new message();
$notification = new notification();
$smsKey = new sms();
$fileHandler = new file_handler();
$validation = new validation();
$action = null;
//getting caller details
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    if (!$validation->isActionValid($action)) {
        error_log("ERROR: unable to validate action ($action)");
        $notification->status = $notification->feedbackFormat(0, "Error occured");
        die($notification->status);
    }
}
switch ($action) {
    //admin functionalities
    case 'Login':
        //getting the required values to log in
        $username = $_REQUEST['log_username'];
        $password = $_REQUEST['log_password'];
        $user->login($username, $password);
        break;
    case 'Unlock':
        //getting the required values to log in
        $password = $_REQUEST['password'];
        $response = $user->login($_SESSION['username'], $password);
        break;
    case 'sign_up':
        $valid = true;
        $firstName = $_REQUEST['register_fname'];
        $lastName = $_REQUEST['register_lname'];
        $email = $_REQUEST['register_email'];
        $password = $_REQUEST['register_password'];
        $cpassword = $_REQUEST['confirm_password'];
        if (empty($firstName) || empty($password) || empty($email)) {
            $user->status = $user->feedbackFormat(0, "Missing input!");
            $valid = false;
        }
        if (empty($email)) {
            $valid = false;
            $user->status = $user->feedbackFormat(0, "Missing email!");
        } else {
            // check if e-mail is valid
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user->status = $user->feedbackFormat(0, "Invalid email format");
                $valid = false;
            }
        }
        if ($user->isUsernameValid($email) == false) {
            $valid = false;
            $user->status = $user->feedbackFormat(0, "Email already registered");
        }
        if ($cpassword != $password) {
            $user->status = $user->feedbackFormat(0, "Password do not match!");
            $valid = false;
        }
        /**
         * XXX: Uncomment the if below to send email verification.
         */
        // if($message->sendEmail($email)==false){
        //     $valid = false;
        //     $user->status = $user->feedbackFormat(0, "Unable to verify email");
        // }

        if ($valid) {
            $userType = $user->initialGrant($email, $password);
            $isCreated = $user->add($firstName, $lastName, "", $email, "", "", $email, $password, $userType);
            if ($isCreated == true) {
                $user->status = $user->feedbackFormat(1, "Account created successfully, check your email (" . $email . ") to verify!");
            } else {
                $user->status = $user->feedbackFormat(0, "Unable to create account");
            }
        }
        die($user->status);
        break;
    case 'add_user':
        $valid = true;
        $fname = $_REQUEST['fname'];
        $lname = $_REQUEST['lname'];
        $oname = $_REQUEST['oname'];
        $tel = $_REQUEST['phone'];
        $email = $_REQUEST['email'];
        $address = $_REQUEST['address'];
        $password = $_REQUEST['password'];
        $cpassword = $_REQUEST['confirm_password'];
        $username = $_REQUEST['username'];
        $type = $_REQUEST['user_type'];
        if (empty($fname) || empty($lname) || empty($email)) {
            $user->status = $user->feedbackFormat(0, "Missing input!");
            $valid = false;
        }
        if (empty($username)) {
            $user->status = $user->feedbackFormat(0, "Missing username!");
            $valid = false;
        }
        if ($cpassword != $password) {
            $user->status = $user->feedbackFormat(0, "Password do not match!");
            $valid = false;
        }
        if (empty($type)) {
            $user->status = $user->feedbackFormat(0, "User type not defined!");
            $valid = false;
        }
        if ($valid) {
            $user->add($fname, $lname, $oname, $email, $tel, $address, $username, $password, $type);
        }
        die($user->status);
        break;
    case 'Add subject':
        $title = $_REQUEST['subject_title'];
        $type = $_REQUEST['subject_type'];
        $attrNumber = $_REQUEST['subject_count_attr'];
        $attrString = "";
        if (isset($title) && isset($attrNumber) && isset($_REQUEST['attr_name0']) && $type !== 0) {
            //looping throughout the attributes
            $attributes = array();
            for ($count = 0; $count < $attrNumber; $count++) {
                $attrName = $_REQUEST['attr_name' . $count];
                $attrType = $_REQUEST['attr_type' . $count];
                $attrNullable = $_REQUEST['attr_nullable' . $count];
                $attrUniqueness = $_REQUEST['attr_uniqueness' . $count];
                $attribute_desc = null;
                if ($subject->isDataTypeDefault($attrType)) {
                    $attribute_desc = array(
                        'name' => $attrName,
                        'type' => $attrType,
                        'is_null' => $attrNullable,
                        'is_unique' => $attrUniqueness,
                        'has_ref' => false,
                        'reference' => null);
                } else if (!$subject->isDataTypeDefault($attrType) && !$subject->isDataTypeTable($attrType)) {
                    $attrDetails = explode("|", $attrType);
                    if (isset($attrDetails[0]) && isset($attrDetails[1]) && $subject->isDataTypeTable($attrDetails[0]) && $subject->isDataTypeColumn($attrDetails[1])) {
                        $attribute_desc = array(
                            'name' => $attrName,
                            'type' => $attrDetails[0],
                            'is_null' => $attrNullable,
                            'is_unique' => $attrUniqueness,
                            'has_ref' => true,
                            'reference' => $attrDetails[1]);
                    } else {
                        $subject->status = $subject->feedbackFormat(0, "ERROR: Failure to read data types");
                        return;
                    }
                } else {
                    $subject->status = $subject->feedbackFormat(0, "ERROR: Invalid data types");
                    return;
                }
                array_push($attributes, $attribute_desc);
            }
            $commenting = $_REQUEST['subject_commenting'];
            $likes = $_REQUEST['subject_likes'];
            $displayViews = $_REQUEST['subject_display_views'];
            $subject->add($title, $type, $attrNumber, $attributes, $commenting, $likes, $displayViews);
        } else {
            $subject->status = $subject->feedbackFormat(0, "Fill all required fields!");
        }
        break;
    case 'delete_instance':
        $contentName = str_replace(" ", "_", $_REQUEST['content']);
        $instanceId = $_REQUEST['instance_id'];
        if ($contentName == "subject") {
            $subject->delete($instanceId);
        } else {
            $content->delete($contentName, $instanceId);
        }
        break;
    case 'save':
        $isDataValid = true;
        $validationError = "";
        $values = array();
        $articleId = $_REQUEST['data-set'];
        $attributes = $subject->getAttributes($articleId);
        if (count($attributes) > 0) {
            //getting form values
            for ($count = 0; $count < count($attributes); $count++) {
                $attributeName = str_replace("_", " ", $attributes[$count]['name']);
                $nullProperty = $attributes[$count]['is_null'];
                $uniqueProperty = $attributes[$count]['is_unique'];
                if ($attributes[$count]['type'] == "file") {
                    $file = $_FILES[$attributes[$count]['name']];
                    $isUploaded = $fileHandler->upload($file);
                    if ($isUploaded != true) {
                        die($fileHandler->status);
                    } else {
                        $values[$count] = $fileHandler->filePath;
                    }
                } else {
                    $values[$count] = $_REQUEST[$attributes[$count]['name']];
                }
                if ($nullProperty == "false" && empty($values[$count])) {
                    $isDataValid = false;
                    $main->status = $main->feedbackFormat(0, $attributeName . " is required");
                    die($main->status);
                } else if ($uniqueProperty == "true" && $validation->isUnique($main->header($articleId), $attributes[$count]['name'], $values[$count]) != true) {
                    $isDataValid = false;
                    $main->status = $main->feedbackFormat(0, $attributeName . " must be unique, and " . $_REQUEST[$attributes[$count]['name']] . " is already saved");
                    die($main->status);
                }
            }
            if ($isDataValid == true) {
                $main->status = $content->add($main->header($articleId), $values, $attributes);
            }
        } else {
            $main->status = $main->feedbackFormat(0, "ERROR: Form data not fetched!");
        }
        die($main->status);
        break;
    case 'update':
        $isDataValid = true;
        $validationError = "";
        $values = array();
        $subjectTitle = $_REQUEST['subject_title'];
        $occurenceId = $_REQUEST['occurence_id'];
        $subjectId = $subject->getId($subjectTitle);
        $attributes = $subject->getAttributes($subjectId);
        if (count($attributes) > 0) {
            //getting form values
            for ($count = 0; $count < count($attributes); $count++) {
                $attributeName = str_replace("_", " ", $attributes[$count]['name']);
                $nullProperty = $attributes[$count]['is_null'];
                $uniqueProperty = $attributes[$count]['is_unique'];
                if ($nullProperty == "false" && empty($_REQUEST[$attributes[$count]['name']]) && $attributes[$count]['type'] !== "file") {
                    $isDataValid = false;
                    $main->status = $main->feedbackFormat(0, $attributeName . " is required");
                    die($main->status);
                } else {
                    if ($attributes[$count]['type'] == "file") {
                        $values[$count] = NULL;
                    } else {
                        $values[$count] = $_REQUEST[$attributes[$count]['name']];
                    }
                }
            }
            if ($isDataValid == true) {
                $main->status = $content->update($occurenceId, $main->header($subjectId), $values, $attributes);
            }
        } else {
            $main->status = $main->feedbackFormat(0, "ERROR: Form data not fetched!");
        }
        die($main->status);
        break;
    case 'send_message':
        $sender = $_REQUEST['name'];
        $email = $_REQUEST['email'];
        $messageTXT = $_REQUEST['message'];
        $message->send($sender, $email, $messageTXT);
        break;
    case 'Send':
        $recipient = $_REQUEST['send_sms_recipient'];
        $subject = $_REQUEST['send_sms_subject'];
        $messageTXT = $_REQUEST['send_sms_message'];
        $smsKey->send($recipient, $subject, $messageTXT);
        break;
    //UI callers
    case 'combo_tables':
        $main->getTables(true);
        break;
    case 'combo_table_columns':
        if (isset($_REQUEST["table_name"])) {
            $main->getTableColumns($_REQUEST["table_name"]);
        }
        break;
    case 'is_data_type_table':
        if (isset($_REQUEST['data_type'])) {
            echo $main->isDataTypeTable($_REQUEST['data_type']);
        }
        break;
    case 'feed_modal':
        $occurenceId = $_REQUEST['occurence_id'];
        $subject = $_REQUEST['subject'];
        $main->feedModal($subject, $occurenceId);
        break;
    case 'add_file':
        $file = $_FILES['image'];
        $result = $fileHandler->upload($file);
        die($result);
        break;
    case 'get_article_attributes':
        if (isset($_REQUEST['data-set'])) {
            $articleId = $_REQUEST['data-set'];
            if (is_numeric($articleId)) {
                $attributeList = $subject->getAttributes($articleId);
            } else {
                $articleId = $subject->getId($articleId);
                $attributeList = $subject->getAttributes($articleId);
            }
            if (count($attributeList) > 0) {
                $main->status = json_encode(array('type' => 'success', 'attributes' => $attributeList));
            } else {
                $main->status = $main->feedbackFormat(0, "Unable to get attributes");
            }
            die($main->status);
        } else {
            $main->status = $main->feedbackFormat(0, "Empty article reference id");
            die($main->status);
        }
        break;
    default:
        break;
}
unset($action);
