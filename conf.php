<?php
// Site Information

use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Html;

$conf['site_name'] = "The Good Bookstore Kenya";
$conf['site_url'] = 'http://localhost/iap-configurations';
$conf['admin_email'] = 'austin.maina@strathmore.edu';

// Database Configuration
$conf['db_type'] = 'pdo';
$conf['db_host'] = 'localhost';
$conf['db_user'] = 'root';
$conf['db_pass'] = '1234';
$conf['db_name'] = 'books';

$conf['signType'] = array(
    "Signin" => "Sign in",
    "Signup" => "Sign Up"
);
// Site Language
$conf['site_lang'] = 'en';

//Email Configuration
$conf['mail_type'] = 'smtp'; //Option smtp or mail
$conf['smtp_host'] = 'smtp.gmail.com';
$conf['smtp_user'] = 'austin.maina@strathmore.edu';
$conf['smtp_pass'] = 'akvb jygs fdan upyx';
$conf['smtp_port'] = 465;
$conf['smtp_secure'] = 'ssl';

$mailCnt = [
    'name_from' => 'Austin Maina',
    'email_from' => 'austin.maina@strathmore.edu',
    // 'name_to' => 'Austin Maina',
    // 'email_to' => '',: This has been reconfigured.
    'subject' => 'Connection Verified.',
    'body' => 'This is to test for successful database connectivity'
];

$conn = new mysqli('localhost', 'root', '1234', 'books');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}