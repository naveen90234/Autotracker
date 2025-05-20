<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use DataTables;
use Validator;

class EmailTemplateController extends Controller
{

    public $page_url = 'email-templates';
    public $model = EmailTemplate::class;
    public $page_title = 'Email Template';

    public function index(Request $request)
    {
        $title = $this->page_title;
        $url   = $this->page_url;
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];

        if ($request->ajax()) {
            $data = $this->model::latest()->get();
            $table = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="action-width"><a href="' . route('admin.edit-email-templates',['id'=>$row->id]) . '" title="Edit email template" ><button class="btn btn-primary btn-sm updateData"><i class="icon-open" aria-hidden="true"></i></button></a></div>';
                    return $btn;
                })
                ->editColumn('created_at', function ($row) {
                    return '<td>' . $row->created_at->format('m/d/Y') . '</td>';
                })

                ->rawColumns(['action', 'status', 'created_at'])
                ->make(true);

            return $table;
        }
        return view('admin/'.$this->page_url.'/index', compact('title', 'breadcrumbs','url'));
    }

    public function edit($id)
    {
        $url   = $this->page_url;
        $title = "Edit Email Template";
        $breadcrumbs = [
            ['name' => $this->page_title, 'relation' => 'link','url' => route('admin.email-templates')],
            ['name' => $title, 'relation' => 'Current', 'url' => ''],
        ];

        $data = $this->model::find($id);
        if ($data) {
            return view('admin/'.$this->page_url.'/edit', compact('title', 'breadcrumbs', 'data','url'));
        } else {
            // return abort(404, 'Page not found.');
            return redirect('admin/'.$this->page_url.'')->with('error_msg', "Something went wrong.");
        }
    }

    public function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            'content' => 'required',
            'subject' => 'required',
        ]);

        if ($validator->fails()) {
            foreach ($validator->messages()->getMessages() as $field_name => $messages) {
            if (!isset($firstError))
                $firstError = $messages[0];
                $error[$field_name] = $messages[0];
            }

            return response()->json(array(
                'errors' => $validator->messages()
            ), 422);
        }

        $appPage = $this->model::whereId($id)->first();

        if ($appPage) {
            $appPage->title   = $request->get('title');
            $appPage->subject = $request->get('subject');
            $appPage->content = $request->get('content');
            $appPage->save();

            return ['status' => 'true', 'message' => "Email Template successfully updated."];
        } else {
            return ['status' => 'true', 'message' => "Something went wrong, Please try again!"];
        }
    }
}
