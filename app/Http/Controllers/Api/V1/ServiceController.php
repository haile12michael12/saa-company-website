<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::where('status', 1)->paginate($request->get('per_page', 15));
        return response()->json($services);
    }

    public function show(Service $service)
    {
        return response()->json($service);
    }
}