<?php
/**
 * Created by PhpStorm.
 * User: 29673
 * Date: 2018/11/15
 * Time: 15:51
 */

namespace App\Models;
use App\Models\Model;

class LotteryHistory extends Model
{
    protected $table = 'lottery_histories';

    public function coupon()
    {
        return $this->hasOne(Coupon::class,'id','coupon_id');
    }
}