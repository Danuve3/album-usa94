<?php

namespace App\Enums;

enum TradeItemDirection: string
{
    case Offered = 'offered';
    case Requested = 'requested';
}
