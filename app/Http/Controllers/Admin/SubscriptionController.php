<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use DataTables;
use Validator;


class SubscriptionController extends Controller
{

    //Subscription Plans list
    public function subscriptionPlans(Request $request){
        $title = "Subscription Plans";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];

        if ($request->ajax()) {
            $data = SubscriptionPlan::latest()->get();

            $table = DataTables::of($data)

                ->addIndexColumn()

                ->addColumn('plan_duration', function ($row) {
                    return $row->time_limit . ' ' . $row->duration;
                })

                ->addColumn('amount', function ($row) {
                    return '$' . $row->amount;
                })

                ->addColumn('action', function ($row) {
                    $btn = '<div class="action-width"><a href="' . url('admin/subscription/edit/' . $row->id) . '"><button class="btn btn-primary btn-sm updateData ml-2"><i class="icon-open" aria-hidden="true"></i></button></a></div>';
                    return $btn;
                })

                ->editColumn('status', function ($row) {
                    $status = ($row->status == 1) ? 'checked="checked"' : '';
                    return '<label class="switch"><input type="checkbox" ' . $status . ' class="togbtn" data-id=' . $row->id . ' id="togBtn"><div class="slider round"> <span class="on">Active</span>  <span class="off">Inactive</span></div></label>';
                })

                ->rawColumns(['plan_duration', 'action', 'status'])
                ->make(true);

            return $table;
        }
        return view('admin/subscription/index', compact('title', 'breadcrumbs'));
    }

    public function editSubscription($id){
        $title = "Edit Subscription";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];

        $subscription = SubscriptionPlan::find($id);
        if ($subscription) {
            return view('admin/subscription/edit', compact('title', 'breadcrumbs', 'subscription'));
        } else {
            return redirect('admin/subscription/plans')->with('error_msg', "Plan does not exist.");
        }
    }

    public function updateSubscription(Request $request, $id){
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required|max:400',
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
            } else {

                $subscription = SubscriptionPlan::find($id);
                $data = [
                    'name' => $request->name,
                    'description' => $request->description,
                ];


                $affected = $subscription->update($data);

                if ($affected) {
                    return ['status' => 'true', 'message' => __("Plan updated successfully.")];
                } else {
                    return ['status' => 'true', 'message' => __("Something went wrong, Please try again!")];
                }
            }
        } catch (\Exception $e) {
            return ['status' => 'false', 'message' => $e->getMessage()];
        }
    }

    public function status(Request $request)
    {
        $id = $request->id;
        $row = SubscriptionPlan::whereId($id)->first();
        $row->status = $row->status == 1 ? 0 : 1;
        $row->save();

        if($row->status){
            $message = 'Plan status changed successfully.';
        }else{
            $message = 'Plan status changed successfully.';
        }

        return response()->json(['success' => $message, 'val' => $row->status]);
    }
}
