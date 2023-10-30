<?php

namespace App\Enums;

enum EventSearchMode: string
{
    case START = 'start';
    case END = 'end';
    case ANY = 'any';
}
