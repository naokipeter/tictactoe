<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BoardController extends Controller
{
    /**
     * Get next board
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get board
        $board = $request->input('board');

        return response($board);
    }

    private function 
}
