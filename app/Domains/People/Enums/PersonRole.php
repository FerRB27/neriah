<?php

namespace App\Domains\People\Enums;

enum PersonRole: string
{
    case Administrator = 'administrator';
    case Maker = 'maker';
    case Seller = 'seller';
    case Distributor = 'distributor';
    case Viewer = 'viewer';
}
