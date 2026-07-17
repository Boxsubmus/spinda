<?php

namespace App\Enum;

enum SteamAuthResult: string
{
    case SUCCESS = 'success';
    case NO_ACCOUNT = 'no_account';
    case INVALID_TICKET = 'invalid_ticket';
    case USERNAME_TAKEN = 'username_taken';
    case USERNAME_INVALID = 'username_invalid';
}