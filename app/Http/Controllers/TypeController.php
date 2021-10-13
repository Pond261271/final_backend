<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Type;

class TypeController extends Controller
{
    // แสดง Type ทั้งหมด
    public function getAllTypes()
    {
        $type = Type::all();

        foreach ($type as $papa) {
            $papa['service_count'] = $papa->services()->count();
            $papa['coupon_count'] = $papa->coupons()->count();
        }
        return $type;
    }

    // get 1 type
    public function getTypeAndService($id)
    {
        $type = Type::find($id);
        $type["services"] = $type->services;
        $type["employees"] = $type->employees($id)->get();

        foreach ($type["employees"] as $employee) {
            $employee["name"] = $employee->user()->value('name');
        }

        foreach ($type["services"] as $yaya){
            $yaya["coupon_count"] = $yaya->coupons()->count();
        }
        return $type;
    }

    // create type
    public function createType(Request $request)
    {
        // validator
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type_image_url' => 'required',

        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return [
                "status" => "error",
                "error" => $errors
            ];
        } else {
            $type = new Type();
            $type->name = $request->name;
            $type->type_image_url = $request->type_image_url;

            if ($type->save()) {
                return $type;
            } else {
                return
                    [
                        "status" => "error",
                        "error" => "สร้างไม่ได้"
                    ];
            }
        }
    }

    // update, แก้ไข
    public function update(Request $request, $id)
    {
        $type = Type::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type_image_url' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            return [
                "status" => "error",
                "error" => $errors
            ];
        } else {
            $type->name = $request->name;
            $type->type_image_url = $request->type_image_url;

            if ($type->save()) {
                $type['service_count'] = $type->services()->count();
                $type['coupon_count'] = $type->coupons()->count();
                return $type;
            } else {
                return
                    [
                        "status" => "error",
                        "error" => "แก้ไขไม่ได้"
                    ];
            }
        }
    }

    // delete
    public function destroy($id)
    {
        $type = Type::findOrFail($id);
        $type->services()->delete();
        $type->coupons()->delete();

        if ($type->delete()) {
            return [
                "status" => "success"
            ];
        } else {
            return [
                "status" => "error",
                "error" => "ลบไม่ได้"
            ];
        }
    }
}
