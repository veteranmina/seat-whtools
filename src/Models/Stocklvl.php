<?php

namespace veteranmina\Seat\ContractStock\Models;

use Illuminate\Database\Eloquent\Model;

class Stocklvl extends Model
{
    public $timestamps = true;

    protected $table = 'contractstock_stocklvls';

    protected $fillable = ['id', 'minLvl', 'fitting_id'];


    public function fitting()
    {
        return $this->hasOne('Denngarr\Seat\Fitting\Models\Fitting', 'id', 'fitting_id');
    }
}
