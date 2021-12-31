<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeoLocation;

class MapController extends Controller
{
    public function addMap()
    {

        return view('add_location');
    }

    public function mapLocation()
    {

        $route_data = GeoLocation::all();

        return view('map_location', compact('route_data'));
    }

    public function mapRouteView($routeId = null)
    {
        $location = GeoLocation::where('id', $routeId)->first();

        $route_data = GeoLocation::all();

        return view('single_view_location', compact('location', 'route_data'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'route_name' => 'required',
            'start_location' => 'required',
            'end_location' => 'required',
        ]);
        try {
            $distance = $this->measureDistance($request);
            $request['distance'] = $distance;
            $location = GeoLocation::create($request->all());

            return redirect()->route('map.view', $location->id);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'data' => 0
            ]);
        }
    }

    //algorithm for kilometer measurement
    public function measureDistance($request, $unit = 'K')
    {
        $theta = $request->start_longitude - $request->end_longitude;
        $dist = sin(deg2rad($request->start_latitude)) * sin(deg2rad($request->end_latitude)) + cos(deg2rad($request->start_latitude)) * cos(deg2rad($request->end_latitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function get_route_map_info(Request $request)
    {

        $route_id = $request->id;
        $location = GeoLocation::where('id', $route_id)->first();

        return response()->json([
            'status' => !empty($location) ? 'success' : 'error',
            'msg' => !empty($location) ? 'Data Found' : 'Something went wrong',
            'data' => !empty($location) ? $location : []
        ]);

    }
}
