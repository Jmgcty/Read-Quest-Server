<?php

function isValidEmail(string $email)
{
    $isEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
    return $isEmail ? true : false;
}
