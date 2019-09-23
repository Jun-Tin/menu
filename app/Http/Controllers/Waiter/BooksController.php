<?php

namespace App\Http\Controllers\Waiter;

use App\Models\{Book, Store};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Book $book)
    {

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
    public function store(Request $request, Book $book)
    {
        $book->fill($request->all());
        $book->date      = strtotime($request->date);
        $book->meal_time = strtotime($request->meal_time);
        $book->lock_in   = strtotime($request->lock_in);
        $book->lock_out  = strtotime($request->lock_out);

        $store = Store::find($request->store_id);
        $book->type  = $store->checkTimeArea($book->meal_time);

        $first = $book::where('date',$book->date)->where('type',$book->type)->where('place_id',$request->place_id)->first();

        if ($first) {
            return response()->json(['error' => ['message' => ['预约已存在！']], 'status' => 401]);
        }

        $book->save();

        return (new BookResource($book))->additional(['status' => 200, 'message' => '创建成功！']);
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
    public function update(Request $request, Book $book)
    {
        $book->fill($request->all());
        $book->date      = strtotime($request->date);
        $book->meal_time = strtotime($request->meal_time); 
        $book->lock_in   = strtotime($request->lock_in);
        $book->lock_out  = strtotime($request->lock_out);

        $store = Store::find($request->store_id);
        $book->type  = $store->checkTimeArea($book->meal_time);

        $first = $book::where('date',$book->date)->where('type',$book->type)->where('place_id',$request->place_id)->first();

        if ($first) {
            return response()->json(['error' => ['message' => ['预约已存在！']], 'status' => 401]);
        }

dd($book);
        $book->update($book);

        return (new BookResource($book))->additional(['status' => 200, 'message' => '创建成功！']);
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
}
