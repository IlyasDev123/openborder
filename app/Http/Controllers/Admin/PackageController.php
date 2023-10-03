<?php

namespace App\Http\Controllers\Admin;

use App\Models\Package;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages = Package::orderBy('sort_position', 'ASC')->get();
        return view('admin.package.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.package.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => '',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:max_width=1000,max_height=1000',
        ]);

        if ($validator->fails()) {
            return \redirect()->back()->with('invalid', $validator->getMessageBag()->getMessages());
        }
        $image = addFile($request->image, 'package_image/');
        Package::create([
            'name' => $request->name,
            'description' => $request->description,
            'package_name_es' => $request->package_name_es??null,
            'package_description_es' => $request->package_description_es??null,
            'image' => $image,
            'sort_position' => 0,

        ]);

        return redirect(route('package.index'));
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
        $package = Package::find($id);

        return view('admin.package.edit', compact('package'));
    }

    /**
     * activePackage
     *
     * @param  mixed $request
     * @return void
     */
    public function activePackage(Request $request)
    {
        $package = Package::findOrfail($request->id);
        $package->update(['status' => $request->status]);
        return redirect()->route('package.index')
            ->with('success', 'Package Activate Successfully!');
    }

    /**
     * deactivePackage
     *
     * @param  mixed $request
     * @return void
     */
    public function deactivatePackage(Request $request)
    {
        $package = Package::findOrfail($request->id);
        $package->update(['status' => $request->status]);
        return redirect()->route('package.index')
            ->with('success', 'Package Deactivate Successfully!');
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
         $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => '',
        ]);

        if ($validator->fails()) {
            return \redirect()->back()->with('invalid', $validator->getMessageBag()->getMessages());
        }
        $packagePlan = Package::findOrfail($id);
        try {
            $data = $request->except('_token');

            if ($request->file('image')) {
                $image = addFile($data['image'], 'package_image/');
            } else {
                $image = $packagePlan->image;
            }
            $data['image'] = $image;

            $packagePlan->update($data);

            return redirect()->route('package.index')
            ->with('success', 'Package  Update successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $package = Package::find($request->id);
        try {

            if ($package->image) {
                File::delete(public_path($package->image));
            }
            $package->delete();
            return redirect()->route('package.index')
                ->with('success', 'Deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to  deleted plan.');
        }
    }

    public function sortedPositionSave(Request $request)
    {

        $packages = Package::get();

        foreach ($packages as $package) {
            foreach ($request->order as $order) {
                if ($order['id'] == $package->id) {
                    $package->update(['sort_position' => $order['position']]);
                }
            }
        }

        return sendSuccess("success", $package);
    }
}
