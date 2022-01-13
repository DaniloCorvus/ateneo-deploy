<?php

namespace App\Http\Controllers;

use App\Models\WaitingList;
use App\Response;
use App\Services\WaitingListService;
use App\Traits\ApiResponser;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaitingListController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->success(WaitingListService::index());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display statistics of waiting lists.
     *
     * @return \Illuminate\Http\Response
     */
    public function statistics()
    {
        //
        $stats = [];
        $stats['topAwait'] = WaitingListService::getTopAwaitBySpeciality();
        $stats['lastAppointment'] = WaitingListService::getLastAppointment();
        $stats['averageTime'] = WaitingListService::averageTime();
        $stats['averageTime'] =   $stats['averageTime']->time ? CarbonInterval::seconds($stats['averageTime']->time)->cascade()->forHumans() : 0;
        return $this->success($stats);
    }


    public function cancellWaitingAppointment()
    {
        return $this->success(

            WaitingList::whereId(request()->get('id'))->update(
                [
                    'state' => 'Cancelado',
                    'message_cancell' => request()->get('message', 'sin mensaje')
                ]
            )

        );
    }
}
