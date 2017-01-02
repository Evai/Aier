<?php

use Illuminate\Database\Eloquent\Model;

/**
 * Class Users
 */
class Users extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    //指定主键
    protected $primaryKey = 'userid';

    //指定允许批量赋值的字段
    protected $fillable = [
        'name', 'email', 'password', 'avater'
    ];

    //指定不允许批量赋值的字段
    protected $guarded = [];

    //自动维护时间戳
    public $timestamps = false;

    //定制时间戳格式
    protected $dateFormat = 'U';

    /**
     * 避免转换时间戳为时间字符串
     */
    public function fromDateTime($value)
    {
        return $value;
    }
    //将默认增加时间转化为时间戳
    protected function getDateFormat()
    {
        return time();
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','email'
    ];

}
