<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Templates;
use App\TemplatesCategories;
use DB;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $templates = Templates::get();

        return $templates;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate incoming request
        $request->validate([
            'title' => 'required|max:30',
            'description' => 'required|max:50'
        ]);

        // define request
        $title = $request->input('title');
        $description = $request->input('description');
        $categories = json_decode($request->input('categories'), true);


        // store request in DB
        $templateId = Templates::insertGetId(
            ['title' => $title, 'description' => $description, 'amount' => 0]
        );

        // format request
        $templateCategories = $this->prepareCategories($categories, $templateId);

        // store request in DB
        DB::table('templates_categories')->insert($templateCategories);


        // confirm it exists in the table
        $exists = Templates::where('id', $templateId)->exists();

        return json_encode($exists);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $templateId
     * @param  array $categories
     * @return array $templateCategories
     */
    public function prepareCategories($categories, $templateId) {
        $templateCategories = array();

        foreach($categories as $category){
            $templateCategories[] = array('template_id' => $templateId, 'category_id' => $category);
        }

        return $templateCategories;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ids)
    {
        //
        $templates = Templates::join('templates_categories', 'templates.id', '=', 'templates_categories.template_id')
            ->whereIn('category_id', $this->filterIntegerRequest($ids))
            ->get();

        return $templates;
    }

    /**
     * Filter integer based requests
     * Only numbers and commas are allowed
     * No duplicate
     *
     *
     * @param  int  $ids
     * @return array $set of integers
     */

    public function filterIntegerRequest($ids) {

        $ids = json_decode($ids, true);

        $set = array();
        foreach($ids as $id) {
            if(is_int($id)) {
                $set[] = $id;
            }
        }

        return $set;
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
}
