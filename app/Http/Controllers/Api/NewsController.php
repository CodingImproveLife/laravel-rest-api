<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\StoreRequest;
use App\Http\Requests\News\UpdateRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(News::class, 'news', ['except' => ['index', 'show', 'store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return NewsResource::collection(News::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @param News $news
     * @return NewsResource
     */
    public function store(StoreRequest $request, News $news)
    {
        $news->title = $request['title'];
        $news->content = $request['content'];
        $news->user_id = Auth::id();
        $news->category_id = $request['category_id'];
        $news->save();

        return new NewsResource($news);
    }

    /**
     * Display the specified resource.
     *
     * @param News $news
     * @return NewsResource
     */
    public function show(News $news)
    {
        return new NewsResource($news);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param News $news
     * @return NewsResource
     */
    public function update(UpdateRequest $request, News $news)
    {
        $news->update($request->all());
        return new NewsResource($news);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param News $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(News $news)
    {
        $news->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
