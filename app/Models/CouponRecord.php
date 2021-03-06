<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;

class CouponRecord extends Model
{

    public static function boot() 
    {
        parent::boot();
        
        static::addGlobalScope('date', function(Builder $builder) {
            $builder->where('end_time', '>=', date('Y-m-d',time()));
        });

    }

    public function coupon() 
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    public function fan() 
    {
        return $this->belongsTo(Fan::class);
    }

    //发放优惠券给用户
    public function grantCoupon(int $coupon_id, int $uid)
    {
        $data = [];     
        $time = Coupon::getTime($coupon_id);
        $data['start_time'] = $time['start'];
        $data['end_time'] = $time['end'];
        $data['uid'] = $uid;
        if(CouponRecord::create($data)) {
            return true;
        } 

        return false;
    }

    //获取用户可用的优惠券
    public static function getUserHasCoupons(int $uid)
    {
        if(!isset($uid)) {
            return '用户ID不能为空';
        }

        return self::where(['fan_id' => $uid, 'status' => 0])->with(['coupon'])->get()->toArray();
    }

    //获取用户已使用和已过期的优惠券
    public static function getUserCouponsByUsed(int $uid)
    {
        if(!isset($uid)) {
            return '用户ID不能为空';
        }

        return self::where(['fan_id' => $uid])->whereIn('status', [-1, 1])->with(['coupon'])->withoutGlobalScope('data')->get()->toArray();
    }

    //获取用户符合条件的优惠券    
    public static function getUserAccordCoupons($uid, $total_price) {
        $result = [];
        $records = self::getUserHasCoupons($uid);
        foreach($records as $record) {
            if($record['coupon']['use_price'] <= $total_price) {
                $result[] = $record;
            }
        }
        return $result;
    }

    public static function use($id)
    {
        if(self::where('id', $id)->update(['status' => 1, 'use_time' => date('Y-m-d H:i:s', time())])){
            return 'success';
        }

        return 'error';
    }

    public static function getCouponById($id) {
        return self::where(['id' => $id])->with(['coupon'])->get();
    }
}
