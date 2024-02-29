<?php

namespace App\Http\Controllers;

use App\Services\DhlService;
use App\Http\Requests\ShippingRequest;

class DhlController extends Controller
{
    public function __construct(private DhlService $dhl)
    {}

    public function makeShipment(ShippingRequest $request)
    {
        $result = $this->dhl->createShipment($request->validated());

        return response($result);
    }
}
