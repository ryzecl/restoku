<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case SETTLEMENT = 'settlement';
    case CANCEL = 'cancel';
    case COOKED = 'cooked';
    case SERVED = 'served';
}
