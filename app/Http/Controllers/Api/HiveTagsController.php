<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;

use App\Models\HiveTag;
use Illuminate\Http\Request;

use Validator;

class HiveTagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $hive_tags = $request->user()->hive_tags()->orderBy('tag')->get();

        return $hive_tags;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag'         => 'required|string',
            'hive_id'     => 'required|integer|exists:hives,id',
            'url'         => 'nullable|url',
            'router'      => 'nullable|json',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails())
            return response()->json(['errors'=>$validator->errors()]);
        
        $user      = $request->user(); 
        $hive_tags = $user->hive_tags;
        $existing  = $hive_tags->where('tag', $request->input('tag'));

        if ($existing->count() > 0)
            return $this->update($request, $existing->first()->id);

        $hive_tag = $user->hive_tags()->create($request->all());

        return response()->json($hive_tag, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $tag)
    {
        $hive_tag = $request->user()->hive_tags()->where('tag', $tag)->first();

        return $hive_tag;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tag'         => 'required|string',
            'hive_id'     => 'required|integer|exists:hives,id',
            'url'         => 'nullable|url',
            'router'      => 'nullable|json',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails())
            return response()->json(['errors'=>$validator->errors()]);

        $hive_tag = $request->user()->hive_tags()->findOrFail($id);
        $hive_tag->update($request->all());

        return response()->json($hive_tag, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $tag)
    {
        $request->user()->hive_tags()->where('tag', $tag)->delete();

        return response()->json(null, 204);
    }
}
