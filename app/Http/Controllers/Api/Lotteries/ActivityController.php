<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/12/19
 * Time: 11:09
 */

namespace App\Http\Controllers\Api\Lotteries;


use App\Http\Controllers\Controller;
use App\Models\FanLottery;
use App\Models\LotteryActivity;
use App\Models\LotteryPrize;
use App\Services\Token;

class ActivityController extends Controller
{
    public function index()
    {
        $date=LotteryActivity::first();
        return response()->json(['status' => 'success', 'data' =>$date]);
    }

    public function store()
    {
        $data=request()->all();
        if(LotteryActivity::create($data)) {
            return response()->json(['status' => 'success', 'msg' => '新增成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '新增失败！']);
    }

    public function update()
    {
        $data = request()->all();
        if(LotteryActivity::where('id', request()->activity)->update($data)) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }

    public function destroy()
    {
        if(LotteryActivity::where('id', request()->activity)->delete()) {
            return response()->json(['status' => 'success', 'msg' => '删除成功！']);
        }

        return response()->json(['status' => 'error', 'msg' => '删除失败！']);
    }

    public function wxShow()
    {
        $fan_id=Token::getUid();
        $activity_id=request()->activity;
        $activity=LotteryActivity::find($activity_id);
        $prizes=LotteryPrize::where('activity_id',$activity_id)->orderBy('orderby_lev', 'asc')->with('coupon')->get();
        $turn=$prizes->count()+1;
        if (!$activity->turn_img){
            $turn_image='https://'.request()->server('HTTP_HOST').
                '/img/lotteries/n'.$turn.'.png';
        }
        if ($activity->status==0){
            return response()->json(["status"=>"success","data"=>'活动未开']);
        }
        $fan_data=FanLottery::firstOrCreate(['fan_id'=>$fan_id,'activity_id'=>$activity_id]
            ,['number'=>'1']);
        return response()->json(["status"=>"success","data"=>compact('fan_data','activity','prizes','turn_image')]);
    }

    public function addFanLottery()
    {
        $fan_id=request()->fan_id;
        $number=request()->number;
        $activity_id=request()->activity_id;
        $save=FanLottery::updateOrCreate(['fan_id'=>$fan_id,'activity_id'=>$activity_id],
                                          ['number'=>$number]);
        if($save) {
            return response()->json(['status' => 'success', 'msg' => '更新成功！']);
        }
        return response()->json(['status' => 'error', 'msg' => '更新失败！']);
    }
}