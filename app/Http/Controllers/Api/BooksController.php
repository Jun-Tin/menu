<?php

namespace App\Http\Controllers\Api;

use App\Models\{Book, Store};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use Carbon\Carbon;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Book $book)
    {
        return (new BookResource($book))->additional(['status' => 200]);
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
        $book->date = strtotime($request->date);
        $book->save();

        return (new BookResource($book))->additional(['status' => 200, 'message' => '创建成功！']);
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
        $book->date = strtotime($request->date);
        $book->update();

        return (new BookResource($book))->additional(['status' => 200, 'message' => '修改成功！']);
    }

    /** 【 预约状态修改按钮 】 */ 
    public function edit(Request $request, Book $book)
    {
        $book->update(['status' => 1]);
        return (new BookResource($book))->additional(['status' => 200, 'message' => '修改成功！']);
    }
}
