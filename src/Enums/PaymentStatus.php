<?php

namespace Casper\Paymera\Enums;

enum PaymentStatus: string
{
    case Pending  = 'P';
    case Accepted = 'A';
    case Failed   = 'F';
    case Canceled = 'C';
}
