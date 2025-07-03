<?php

namespace App\Http\Controllers;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function getByType($type)
    {
        // Assuming you have a Brand model that relates to a 'types' attribute or similar
        // You would query your database for brands of the given type and return them
        // This is just a placeholder example
        $brands = Brand::where('type', $type)->get();

        return response()->json($brands);
    }
}