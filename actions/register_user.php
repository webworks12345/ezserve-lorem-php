<?php
if (!defined('ACCESS')) die('DIRECT ACCESS NOT ALLOWED');

if (isset($_POST['data'])) {
    // Hash the password before storing it in the database.
    $_POST['data']['password'] = password_hash($_POST['data']['password'], PASSWORD_BCRYPT);

    // Check if usertype is set to "client" or "business owner."
    $allowedUsertypes = ['client', 'business owner'];
    if (in_array($_POST['data']['usertype'], $allowedUsertypes)) {
        if (add_record("users", $_POST['data'])) {
            set_message("Thank you for your registration.", "success");
            // Redirect to the login page after successful registration.
            header("Location: " . SITE_URL . "/?page=login");
            
            exit(); // Make sure to exit after redirection.
        } else {
            set_message("Failed to register.", "danger");
        }
    } else {
        // Handle the case where an invalid usertype was selected.
        set_message("Invalid usertype selected.", "danger");
    }
}

redirect();

