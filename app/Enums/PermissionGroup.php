<?php

namespace App\Enums;

enum PermissionGroup: string{
    case Products = 'products';
    case Categories = 'categories';
    case Discounts = 'discounts';
    case Orders = 'orders';
    case Admins = 'admins';
    case ProductFeedback = 'product-feedback';
}
