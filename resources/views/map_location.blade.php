@extends('master')
@section('title', 'View location')
@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css')}}"/>

{{--    <script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>--}}
    <script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script>
    <script src="https://maps.google.com/maps/api/js?key=AIzaSyDUPClCAvO-EIlmJajX4Sc3bpGgi57-LnE&libraries=places"
            type="text/javascript"></script>
    <script src="https://maps.google.com/maps/api/js?key=AIzaSyDUPClCAvO-EIlmJajX4Sc3bpGgi57-LnE"
            type="text/javascript"></script>
@endsection
@section('content')
    <button style="float: right;" class="Mapbtn btn btn-sm btn-success" onclick="goBack(`{{ url('/') }}`)"> Back</button>

    <div class="container">
        <div class="mt-5"></div>
        <div class="row">
            <label class="col-md-2 col-sm-2 col-2" for="route_name"> Route name </label>
            <div class="col-md-5 col-sm-5 col-5 SelectDiv">
                <select class="form-control shadow-none" name="route_name" id="route_name"
                        onchange="getRouteInfo()">
                    <option value="" disabled selected> Select location</option>
                    @foreach($route_data as $item)
                        <option value="{{ $item->id }}"> {{$item->route_name }}</option>@endforeach
                </select>
            </div>
        </div>
        <div class="mt-5"></div>
        <div data-role="page" id="map_page">
            <div data-role="content">

                <div class="" style="padding:1em;">
                    <div id="map_canvas" style="height:300px;"></div>
                    <br>

                    <div data-role="fieldcontain" class="d-none">
                        <label for="from">From</label>
                        <input type="text" id="from" value=""/>
                    </div>
                    <div data-role="fieldcontain" class="d-none">
                        <label for="to">To </label>
                        <input type="text" id="to" value=""/>
                    </div>
                    <input type="hidden" id="start_latitude" name="start_latitude" value="23.810332">
                    <input type="hidden" id="end_longitude" name="end_longitude" value="90.4125181">

                    <table style="width: 100%;">
                        <tr>
                            <td> Start Location</td>
                            <td>: <span id="from_text">  </span></td>
                        </tr>
                        <tr>
                            <td> End Location</td>
                            <td>: <span id="to_text">  </span></td>
                        </tr>
                    </table>
                    <br>

                    <div data-role="fieldcontain">
                        <label for="mode" class="select">Transportation method </label>
                        <select class="TransportationMethod" name="select-choice-0" id="mode">
                            <option value="DRIVING">Driving</option>
                            <option value="WALKING">Walking</option>
                        </select>
                    </div>
                    <a class="btn btn-sm btn-primary mt-2 ml-5" data-icon="search" data-role="button" href="javascript:void(0)" id="submit">Get directions</a>
                </div>
                <div id="results" style="display:none;">
                    <h3> &nbsp; Route Details Information </h3>
                    <div style="padding-left: 15px;" id="directions"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("pageinit", "#map_page", function () {
            initialize();
            calculateRoute();
        });

        $(document).on('click', '#submit', function (e) {
            e.preventDefault();
            calculateRoute();
        });

        var directionDisplay,
            directionsService = new google.maps.DirectionsService(),
            map;

        function initialize() {
            var lat = $("#start_latitude").val();
            var long = $("#end_longitude").val();

            directionsDisplay = new google.maps.DirectionsRenderer();
            var mapCenter = new google.maps.LatLng(lat, long);

            var myOptions = {
                zoom: 10,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: mapCenter
            }

            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
            directionsDisplay.setMap(map);
            directionsDisplay.setPanel(document.getElementById("directions"));
        }

        function calculateRoute() {
            var selectedMode = $("#mode").val(),
                start = $("#from").val(),
                end = $("#to").val();

            if (start == '' || end == '') {
                // cannot calculate route
                $("#results").hide();
                return;
            } else {
                var request = {
                    origin: start,
                    destination: end,
                    travelMode: google.maps.DirectionsTravelMode[selectedMode]
                };

                directionsService.route(request, function (response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);
                        $("#results").show();
                        /*
                            var myRoute = response.routes[0].legs[0];
                            for (var i = 0; i < myRoute.steps.length; i++) {
                                alert(myRoute.steps[i].instructions);
                            }
                        */
                    } else {
                        $("#results").hide();
                    }
                });

            }
        }

        //  select2
        $(document).ready(function () {
            if ($(".select2").length > 0) {
                $(".select2").select2();
            }
        });


        // getRouteInfo
        function getRouteInfo() {
            id = $("#route_name").val();
            $.ajax({
                url: "{{ route('get_route_map_info') }}",
                type: "POST",
                data: {
                    id: id
                },
                success: function (response) {

                    $("#from").val(response.data.start_location);
                    $("#to").val(response.data.end_location);

                    $("#from_text").html(response.data.start_location);
                    $("#to_text").html(response.data.end_location);

                    $("#start_latitude").val(response.data.start_latitude);
                    $("#end_longitude").val(response.data.end_longitude);

                    initialize();
                    calculateRoute();

                }
            })
        }
    </script>

@endsection
