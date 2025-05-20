<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SellingTip;
use Illuminate\Support\Facades\Storage;
use DataTables;
use Illuminate\Support\Facades\Validator;

class SellingTipController extends Controller
{
    public function index(Request $request)
    {
        $title = "Selling Tips Articles";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];
    
        if ($request->ajax()) {
            $query = SellingTip::latest();
    
            if ($request->has('status') && $request->status !== null) {
                $query->where('status', (int)$request->status);
            }
    
            $articles = $query->get();
    
            return Datatables::of($articles)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = ($row->status == 1) ? 'checked' : '';
    
                    return '<label class="switch">
                                <input type="checkbox" ' . $status . ' class="toggle-status" data-id="' . $row->id . '">
                                <div class="slider round">
                                    <span class="on">Active</span>  
                                    <span class="off">Inactive</span>
                                </div>
                            </label>';
                })
                ->editColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset('public/storage/article_images/' . $row->image) . '" width="50" class="img-thumbnail">';
                    }
                    return 'No Image';
                })
                ->editColumn('action', function ($row) {
                    return '<div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $row->id . '">
                                    <a class="dropdown-item" href="' . route('admin.selling_tips.edit', $row->id) . '">Edit</a>
                                    <a class="dropdown-item delete_selling" href="javascript:void(0)" data-id="' . $row->id . '">Delete</a>
                                </div>
                            </div>';
                })
                ->rawColumns(['status', 'image', 'action'])
                ->make(true);
        }
    
        return view('admin.selling_tips.index', compact('title', 'breadcrumbs'));
    }
    
    

    public function create()
    {
        return view('admin.selling_tips.create');
    }
    
    public function store(Request $request)
    {
        if ($request->ajax() && $request->isMethod('post')) {
            try {
                // Validate input
                $validator = Validator::make($request->all(), [
                    'title' => 'required|string|max:255|unique:selling_tips,title',
                    'description' => 'required',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ], [
                    'title.required' => 'The title field is required.',
                    'title.unique' => 'The title has already been taken.',
                    'description.required' => 'The description field is required.',
                    'image.image' => 'The file must be an image.',
                    'image.mimes' => 'Only JPEG, PNG, JPG, and GIF formats are allowed.',
                    'image.max' => 'The image size must not exceed 2MB.'
                ]);
    
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->messages()], 422);
                }
    
                // Handle image upload
                $imageName = null;
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('article_images', 'public');
                    $imageName = str_replace('article_images/', '', $imagePath);
                }
    
                // Save the article
                $sellingTip = new SellingTip();
                $sellingTip->title = $request->title;
                $sellingTip->description = $request->description;
                $sellingTip->image = $imageName;
                $sellingTip->status = 1;
                $sellingTip->save();
    
                return response()->json(['status' => 'true', 'message' => 'Selling Tip added successfully.']);
    
            } catch (\Exception $e) {
                return response()->json(['status' => 'false', 'message' => $e->getMessage()], 500);
            }
        }
    
        return redirect()->route('admin.selling_tips.index')->with('error', 'Invalid request.');
    }
    

    public function edit($id)
    {
        $title = "Edit Selling Tip";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];
    
        $sellingTip = SellingTip::findOrFail($id);
        return view('admin.selling_tips.edit', compact('title', 'breadcrumbs', 'sellingTip'));
    }
    
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'       => 'required|string|max:255|unique:selling_tips,title,' . $id . ',id',
                'description' => 'required',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'title.required' => 'The title field is required.',
                'title.unique'   => 'This title has already been taken.',
                'description.required' => 'The description field is required.',
                'image.image'    => 'The file must be an image.',
                'image.mimes'    => 'Allowed image types are jpeg, png, jpg, gif.',
                'image.max'      => 'The image size must not exceed 2MB.'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()], 422);
            }
    
            $sellingTip = SellingTip::findOrFail($id);
            $imageName = $sellingTip->image; // Keep existing image if not updated
    
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('article_images', 'public');
                $imageName = str_replace('article_images/', '', $imagePath);
            }
    
            $sellingTip->update([
                'title'       => $request->title,
                'description' => $request->description,
                'image'       => $imageName,
            ]);
    
            return response()->json(['status' => 'true', 'message' => "Selling Tip updated successfully."]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function deleteArticle(SellingTip $sellingTip)
    {
        if ($sellingTip->image) {
            Storage::disk('public')->delete($sellingTip->image);
        }
        $sellingTip->delete();

        return redirect()->route('admin.selling_tips.index')->with('success', 'Article deleted successfully.');
    }

    public function toggleStatus(SellingTip $sellingTip)
    {
        $sellingTip->status = !$sellingTip->status;
        $sellingTip->save();

        return response()->json(['success' => 'Status updated successfully.', 'status' => $sellingTip->status]);
    }

    public function status(Request $request)
    {
        $id = $request->id;
        $row = SellingTip::whereId($id)->first();
        $row->status = $row->status == 1 ? 0 : 1;
        $row->save();

        if($row->status){
            $driving_data['link'] = '<h2>Hello, ' . $row->title . '</h2>';
            $driving_data['link'] .= '<h3>Article has been activated.</h3>';
        }else{
            $driving_data['link'] = '<h2>Hello, ' . $row->title . '</h2>';
            $driving_data['link'] .= '<h3>Article has been deactivated.</h3>';

        }

        return response()->json(['success' => 'Article status changed successfully.', 'val' => $row->status]);
    }
    
    

}
