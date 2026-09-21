<?php

namespace App\Enums;

enum TrackingEventName: string
{
    case ViewContent = 'ViewContent';
    case AddToCart = 'AddToCart';
    case InitiateCheckout = 'InitiateCheckout';
    case Purchase = 'Purchase';
}
