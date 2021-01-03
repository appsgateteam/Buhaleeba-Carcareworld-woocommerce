<?php

/**
 * default
 * on phpmailer init
 */
add_action('phpmailer_init', function ($phpmailer) {
    $phpmailer->IsSMTP();
    $phpmailer->Host = 'smtp.office365.com'; // for example, smtp.mailtrap.io
    $phpmailer->Port = 587; // set the appropriate port: 465, 2525, etc.
    $phpmailer->Username = 'onlineorders@thecarcareworld.com'; // your SMTP username
    $phpmailer->Password = '0n1i@rd%%'; // your SMTP password
    $phpmailer->SMTPAuth = true;
    //$phpmailer->SMTPDebug = 3;
    $phpmailer->SMTPSecure = 'tls'; // preferable but optional

    // error message to be logged 
    //$message = json_encode($phpmailer);
    //ysgLogInFile($message);
    ysgLogInFile(11);
}, PHP_INT_MAX - 2, 1);

//
add_filter('wp_mail_from', function ($email) {
    return 'onlineorders@thecarcareworld.com';
}, PHP_INT_MAX - 2, 1);

add_filter('wp_mail_from_name', function ($name) {
    return 'The Car Care World';
}, PHP_INT_MAX - 2, 1);


/**
 * update email headers of wpforms when its contact us
 * 
 */
add_filter('wpforms_email_headers', function ($headers, $f) {
    $id = isset($f->form_data['id']) ? $f->form_data['id'] : "";
    if (!empty($id) && $id == 6897) {
        $headers .= "ygType: 1\r\n";
    }
    return $headers;
}, 10, 3);


add_filter('wp_mail', function ($mail) {

    if (preg_match('/ygType/', $mail['headers'])) {
        //strpos($mail, "ygType: 1") == false
        add_action('phpmailer_init', function ($phpmailer) {
            $phpmailer->IsSMTP();
            $phpmailer->Host = 'smtp.office365.com'; // for example, smtp.mailtrap.io
            $phpmailer->Port = 587; // set the appropriate port: 465, 2525, etc.
            $phpmailer->Username = 'cs@thecarcareworld.com'; // your SMTP username
            $phpmailer->Password = 'C@6crew0%%'; // your SMTP password
            $phpmailer->SMTPAuth = true;
            //$phpmailer->SMTPDebug = 3;
            $phpmailer->SMTPSecure = 'tls'; // preferable but optional

            // error message to be logged 
            //$message = json_encode($phpmailer);
            //ysgLogInFile($message);
            //ysgLogInFile(12);
        }, PHP_INT_MAX, 1);

        //
        add_filter('wp_mail_from', function ($email) {
            return 'cs@thecarcareworld.com';
        }, PHP_INT_MAX, 1);
    } else {
        //ysgLogInFile(json_encode($mail['headers']));
    }
    //exit;
    return $mail;
});
