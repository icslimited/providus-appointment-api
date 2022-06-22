<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
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
            return response()->json(['success' => true, 'departments' => Department::all()], 200);
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
                'name' => 'required|unique:departments,name',
                'description' => 'string'
            ]);
    
            $department = Department::create($fields);

            return response()->json(['success' => true, 'department' => $department], 201);

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
            $department = Department::findOrFail($id);

            return response()->json(['success' => true, 'department' => $department], 200);

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
                'name' => 'unique:departments,name',
                'description' => 'string|nullable'
            ]);

            Department::find($id)->update($fields);

            return response()->json(['success' => true, 'department' => Department::find($id)], 200);

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
            $department = Department::findOrFail($id);

            return response()->json(['success' => $department->delete()], 200);

        } catch (Exception $ex) {
            return response()->json(['success' => false, 'errors' => array('message' => $ex.getMessage()) ], 500);
        }
    }
}
