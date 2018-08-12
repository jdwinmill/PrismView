<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Templates extends Controller
{
    /**
     *  index for main creativeEngine view
     */

    public function index() {
        return view('creativeEngine');
    }

}
