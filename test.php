<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    function __autoload($class_name)
    {
        require_once('classes/' . $class_name . '.php');
    }

    $publicHash = ''; // your Public Key
    $privateHash = ''; //your Private Key

    $api = new MmpAPIClient($publicHash, $privateHash, realpath(dirname(__FILE__)) . '/ssl/mmprocrmapi.crt');

    $map = [
        'MemberContactDetails_firstName' => 'firstName',
        'MemberContactDetails_lastName' => 'lastName',
        'MemberContactDetails_gender' => 'gender',
        'MemberMatchingData_matchGender' => 'matchGender',
        'MemberContactDetails_dob' => 'dob',
        'MemberContactDetails_email' => 'email',
        'memberStatus' => 1,

    ];

    $mapFiles = [
        'MemberContactDetails_photo' => 'photo',
        'MemberPhoto_extraPhoto2' => 'photo2'
    ];

    $data = [];

    if (isset($_FILES)) {
        foreach ($mapFiles as $key => $val) {
            if (isset($_FILES[$val])) {
                $data[$key] = base64_encode(file_get_contents($_FILES[$val]['tmp_name']));
            }
        }
    }

    foreach ($map as $key => $val) {
        if (isset($_POST[$val])) {
            $data[$key] = $_POST[$val];
        }
    }
    $response = $api->call('POST', 'members/create', ['data' => $data]);
} else {
    $response = null;
}
?>
<html>
<head>
    <title>Api test</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>-->

    <script>
        $(function () {
            $("#dob").datepicker();
        });
    </script>
</head>
<body>
<div class="container" style="margin-top:20px">
    <?php if ($response !== null): ?>
        <div class="row">
            <div class="col-md-12">
                <h3>API Response:</h3>

                <p><b>Status:</b> <?php var_export($response->status); ?></p>
                <b>Data:</b>
                <pre><?php var_dump($response); ?></pre>
            </div>
        </div>
    <?php endif; ?>


    <form class="form-horizontal" method="POST" enctype="multipart/form-data">
        <fieldset>

            <!-- Form Name -->
            <legend>Match Maker CRM - API Test Form</legend>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="firstName">First Name</label>

                <div class="col-md-4">
                    <input id="firstName" name="firstName" placeholder="First name" class="form-control input-md"
                           required="" type="text">

                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="lastName">Last Name</label>

                <div class="col-md-4">
                    <input id="lastName" name="lastName" placeholder="Last Name" class="form-control input-md"
                           required="" type="text">

                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="email">E-mail</label>

                <div class="col-md-4">
                    <input id="email" name="email" placeholder="Email" class="form-control input-md" required=""
                           type="text">

                </div>
            </div>

            <!-- Multiple Radios (inline) -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="gender">Gender</label>

                <div class="col-md-4">
                    <label class="radio-inline" for="gender-0">
                        <input name="gender" id="gender-0" value="1" checked="checked" type="radio">
                        Male
                    </label>
                    <label class="radio-inline" for="gender-1">
                        <input name="gender" id="gender-1" value="2" type="radio">
                        Female
                    </label>
                </div>
            </div>

            <!-- Multiple Radios -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="matchGender">Match Gender</label>

                <div class="col-md-4">
                    <div class="radio">
                        <label for="matchGender-0">
                            <input name="matchGender" id="matchGender-0" value="0" checked="checked" type="radio">
                            Male
                        </label>
                    </div>
                    <div class="radio">
                        <label for="matchGender-1">
                            <input name="matchGender" id="matchGender-1" value="1" type="radio">
                            Female
                        </label>
                    </div>
                    <div class="radio">
                        <label for="matchGender-2">
                            <input name="matchGender" id="matchGender-2" value="2" type="radio">
                            Both
                        </label>
                    </div>
                </div>
            </div>

            <!-- File Button -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="photo">Photo</label>

                <div class="col-md-4">
                    <input id="photo" name="photo" class="input-file" type="file">
                </div>
            </div>

            <!-- File Button -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="photo2">Additional Photo</label>

                <div class="col-md-4">
                    <input id="photo2" name="photo2" class="input-file" type="file">
                </div>
            </div>

            <!-- Select Basic -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="memberStatus">Status</label>

                <div class="col-md-4">
                    <select id="memberStatus" name="memberStatus" class="form-control">
                        <option value="1">Lead</option>
                        <option value="2">Client</option>
                    </select>
                </div>
            </div>

            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="dob">Date of Birth</label>

                <div class="col-md-4">
                    <input id="dob" name="dob" placeholder="Date of birth" class="form-control input-md" required=""
                           type="text">
                </div>
            </div>

            <!-- Button -->
            <div class="form-group">
                <label class="col-md-4 control-label" for=""></label>

                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>

        </fieldset>
    </form>
</div>
</body>
</html>