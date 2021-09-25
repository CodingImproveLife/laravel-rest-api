<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Database\Eloquent\Collection;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Collection|News[]
     */
    public function index()
    {
        return News::all();
    }
}
