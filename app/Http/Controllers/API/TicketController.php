<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\ConcertController;
use Illuminate\Http\Request;

use App\Http\Requests\TicketGetRequest;

use App\Models\ShowModel;
use App\Models\ConcertModel;
use App\Models\TicketModel;
use App\Models\LocationModel;

class TicketController extends Controller
{
    public function getTickets(TicketGetRequest $request){
        $tickets = TicketModel::where('code', $request->all()['code'])->where('name', $request->all()['name'])->get();
        if($tickets->isEmpty()){
            return response()->json(["error" => "Unauthorized"], 401);
        }
        $jsonTickets = [];
        foreach($tickets as $ticket){
            foreach(ConcertController::$rows as $row){
                if($row['id'] == $ticket['row']){
                    $ticket['row'] = $row;
                }
            }

            $ticket['show'] = ShowModel::find($ticket['show']);
            $ticket['show']['concert'] = ConcertModel::find($ticket['show']['concert']);
            $ticket['show']['concert']['location'] = LocationModel::find($ticket['show']['concert']['location']);
            $jsonTickets[] = $ticket;
        }
        return response()->json(["concert" => $jsonTickets], 200);
    }


    public function cancel(TicketGetRequest $request, $id){
        $ticketByCodeAndName = TicketModel::where('code', $request->all()['code'])->where('name', $request->all()['name'])->first();
        if($ticketByCodeAndName == null){
            return response()->json(["error" => "Unauthorized"], 401);
        }
        $ticketById = TicketModel::find($id);
        if($ticketById == null){
            return response()->json(["error" => "A ticket with this ID does not exist"], 404);
        }

        $ticketById->delete();

        return response()->json(["message" => "Successfully canceled"], 204);
    }
}
