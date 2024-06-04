<?php
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sende Benutzerdaten an den REST-Service zur Überprüfung
    $login_url = "http://localhost:5000/api/login";
    $login_data = array(
        'username' => $username,
        'password' => $password
    );

    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => json_encode($login_data),
            'header' =>  "Content-Type: application/json\r\n"
        )
    );

    $context = stream_context_create($options);
    $login_result = file_get_contents($login_url, false, $context);
    $login_response = json_decode($login_result, true);

    if ($login_response['success']) {
        // Anmeldung erfolgreich, user_id in der Session speichern
        $_SESSION['user_id'] = $login_response['user_id'];
        header("Location: home.php");
        exit();
    } else {
        // Anmeldung fehlgeschlagen
        header("Location: index.php?error=Invalid username or password");
        exit();
    }
}