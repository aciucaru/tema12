<?php

require_once __DIR__ . '/../utils/logging.inc.php';

function loginSession(Client $clientLogat)
{
    static $logger = new Logger(__FILE__);
    $logger->log('loginSession: inceput rularea');

    if($clientLogat != null)
    {
        $_SESSION['username'] = $clientLogat->username;
        $_SESSION['email'] = $clientLogat->email;
        header('Location: index.php');

        $logger->log("loginSession: sesiune initializata: username: $clientLogat->username, email: $clientLogat->email");
    }
    else
        $logger->log('loginSession: argumentul pasat este nul');
}

function logoutSession()
{
    static $logger = new Logger(__FILE__);

    $logger->log('logOut: inceput rularea');

    session_start();
    session_unset();
    session_destroy();

    header('Location: ../login.php');
}

?>