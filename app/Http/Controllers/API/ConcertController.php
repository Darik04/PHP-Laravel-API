<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ConcertModel;
use App\Models\ShowModel;
use App\Models\LocationModel;
use App\Models\ReservationModel;
use App\Models\ReservationTokenModel;
use App\Models\AddressModel;
use App\Models\TicketModel;

use App\Http\Requests\ReservationStoreRequest;
use App\Http\Requests\BookingStoreRequest;

use App\Http\Resources\ShowResource;

//Notes
//8 rows fixed
class ConcertController extends Controller
{
    public static $rows = [
        [
            "id" => 1,
            "name" => "Stall 01"
        ],
        [
            "id" => 2,
            "name" => "Stall 02"
        ],
        [
            "id" => 3,
            "name" => "Stall 03"
        ],
        [
            "id" => 4,
            "name" => "Stall 04"
        ],
        [
            "id" => 5,
            "name" => "Stall 05"
        ],
        [
            "id" => 6,
            "name" => "Stall 06"
        ],
        [
            "id" => 7,
            "name" => "Terrace 01"
        ],
        [
            "id" => 8,
            "name" => "Terrace 02"
        ],
    ];



    public function getConcerts(){
        $allConcerts = ConcertModel::get();
        $jsonConcerts = [];
        foreach($allConcerts as $concert){
            $shows = ShowModel::where('concert', $concert['id'])->get();
            $concert['shows'] = $shows;
            $concert['location'] = LocationModel::find($concert['location']);
            $jsonConcerts[] = $concert;
        }
        return response()->json(["concerts" => $jsonConcerts], 200);
    }

    public function getConcert($id){
        $concert = ConcertModel::find($id);
        if($concert == null){
            return response()->json(["error" => "A concert with this ID does not exist"], 404);
        }
        $shows = ShowModel::where('concert', $concert['id'])->get();
        $concert['shows'] = $shows;
        $concert['location'] = LocationModel::find($concert['location']);
        return response()->json(["concert" => $concert], 200);
    }

    public function getSeating($concertId, $showId){
        $concert = ConcertModel::find($concertId);
        $show = ShowModel::find($showId);
        if($concert == null || $show == null){
            return response()->json(["error" => "A concert or show with this ID does not exist"], 404);
        }
        $reservationToken = ReservationTokenModel::where('show', $showId)->get();
        
        $jsonReservation = [];
        $result = [];

        foreach($reservationToken as $resToken){
            $reservation = ReservationModel::where('reservation_token', $resToken['id'])->get();
            $resToken['reservation'] = $reservation;
            $jsonReservation[] = $resToken;
        }
        
        foreach(ConcertController::$rows as $row){
            $unavailableList = [];
            foreach($jsonReservation as $jsonToken){
                foreach($jsonToken['reservation'] as $reservationModel){
                    if($reservationModel['row'] == $row['id']){
                        $unavailableList[] = $reservationModel['seat'];
                    }
                }
            }
            $result[] = [
                "id" => $row['id'],
                "name" => $row['name'],
                "seats" => [
                    "total" => 50,
                    "unavailable" => $unavailableList
                ]
            ];
        }

        return response()->json(["rows" => $result], 200);
    }





    public function reservation(ReservationStoreRequest $request, $concertId, $showId){
        if(ReservationTokenModel::where('token', $request->all()['reservation_token'])->first() != null){
            return response()->json(["error" => "Invalid reservation token"], 403);
        }
        $concert = ConcertModel::find($concertId);
        $show = ShowModel::find($showId);
        if($concert == null || $show == null){
            return response()->json(["error" => "A concert or show with this ID does not exist"], 404);
        }

        $newReservationToken = ReservationTokenModel::create([
            "token" => $request->all()['reservation_token'],
            "duration" => $request->all()['duration'],
            "show" => $showId
        ]);
        foreach($request->all()['reservations'] as $reservation){
            ReservationModel::create([
                "row" => $reservation['row'],
                "seat" => $reservation['seat'],
                "reservation_token" => $newReservationToken['id']
            ]);
        }
        $currenTime = Carbon::now();
        $currenTime->add($request->all()['duration'], 'second');
        return response()->json([
            "reserved" => true,
            "reservation_token" => $request->all()['reservation_token'],
            "reserved_until" => $currenTime,
        ], 201);
    }




    public function booking(BookingStoreRequest $request, $concertId, $showId){
        $reservationToken = ReservationTokenModel::where('token', $request->all()['reservation_token'])->first();
        if($reservationToken == null){
            return response()->json(["error" => "Unauthorized"], 401);
        }
        $createdTime = Carbon::now();
        $untilTime = Carbon::parse($reservationToken['created_at']);
        $untilTime->add($reservationToken['duration'], 'second');
        if($createdTime > $untilTime){
            return response()->json(["error" => "Token is no longer valid"], 403);
        }
        $concert = ConcertModel::find($concertId);
        $show = ShowModel::find($showId);
        if($concert == null || $show == null){
            return response()->json(["error" => "A concert or show with this ID does not exist"], 404);
        }

        AddressModel::create([
            "name" => $request->all()['name'],
            "city" => $request->all()['city'],
            "country" => $request->all()['country'],
            "zip" => $request->all()['zip'],
            "address" => $request->all()['address'],
            "reservation_token" => $reservationToken['id'],
        ]);

        $reservation = ReservationModel::where('reservation_token', $reservationToken['id'])->get();
        $reservationToken['reservation'] = $reservation;

        $result = [];
        foreach($reservationToken['reservation'] as $reservation){
            $newTicket = TicketModel::create([
                "code" => generateRandomString(),
                "name" => $request->all()['name'],
                "row" => $reservation['row'],
                "seat" => $reservation['seat'],
                "show" => $showId
            ]);

            foreach(ConcertController::$rows as $row){
                if($row['id'] == $newTicket['row']){
                    $newTicket['row'] = $row;
                }
            }
            // $show
            $newTicket['show'] = $show;
            $concert['location'] = LocationModel::find($concert['location']);
            $newTicket['show']['concert'] = $concert;
            $result[] = $newTicket;
        }


        return response()->json(["tickets" => $result], 201);
    }

}
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
