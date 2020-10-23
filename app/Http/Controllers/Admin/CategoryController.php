<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Policies\CategoryPolicies;
use App\Models\Category;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use DB;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // if (\Gate::forUser(\Auth::guard('admin')->user())->allows('super')) {
            return Category::latest()->paginate(5);
        // }
        // return abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request , [
            'name' => 'bail|required|min:3'
        ]);
        return Category::create($request->all());
    }
    public function multiDelete(Request $request)
    {
     if (Gate::forUser(\Auth::guard('admin')->user())->allows('super')) {
        try {
            Gate::authorize('super');
            DB::beginTransaction();
            foreach ($request->all() as $category) {
                Category::find($category['id'])->delete();
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
        }
     }
      return abort(403);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $category;
    }
    public function upload(Request $request){
        $this->validate($request,[
            'file' => 'required|mimes:jpg,jpeg,png|image'
        ]);
       $picName = time().'.'.$request->file->extension();
       $request->file->move(public_path('uploads/categories'),$picName);
       return $picName;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $this->validate($request , [
            'name' => 'bail|required|min:3'
        ]);

        return $category->update($request->except('id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        return $category->delete();
    }
}
