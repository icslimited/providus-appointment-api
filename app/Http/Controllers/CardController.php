<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        try {
            return response()->json(['success' => true, 'cards' => Card::all()], 200);
        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage())], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        return 'Execute card.store()';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $fields = $request->validate([
                'cardNumber' => 'required|unique:cards,cardNumber',
            ]);
    
            $card = Card::create($fields);

            return response()->json(['success' => true, 'card' => $card], 201);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
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
        try {
            $card = Card::findOrFail($id);

            return response()->json(['success' => true, 'card' => $card], 200);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
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
        return 'Execute card.edit()';
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
        try {
            $fields = $request->validate([
                'cardNumber' => 'unique:cards,cardNumber',
                'status' => 'string'
            ]);

            Card::find($id)->update($fields);

            return response()->json(['success' => true, 'card' => Card::find($id)], 200);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
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
        try {
            $card = Card::findOrFail($id);

            return response()->json(['success' => $card->delete()], 200);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
    }
}
