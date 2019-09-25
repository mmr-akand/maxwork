<?php

namespace App\Enumarations;

class ApiErrorCodes
{
    public static $VALIDATION_ERROR = '100';
    public static $INVALID_LOGIN = '101';
    public static $BLOCKED_USER = '102';
    public static $UNVERIFIED_PHONE = '103';
    public static $PANEL_MISMATCH = '104';
    public static $PHONE_ALREADY_TAKEN = '105';
    public static $PASSWORD_MISMATCH_AT_REG = '106';
    public static $INACTIVE_BLOCKED_DELETED_USER = '107';
    public static $INCOMOPLETE_PROFILE = '108';
    public static $ACCESS_DENIED = '109';
    public static $INACTIVE_USER = '110';
}