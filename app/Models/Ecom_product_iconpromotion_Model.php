<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ecom_product_iconpromotion_Model extends Model
{   
    /**
    * Indicates if the model should be timestamped.
    *
    * @var bool
    */
    protected $table = 'ecom_product_iconpromotion';
    protected $primaryKey = 'flight_id';
    protected $fillable = [
        'id',
        'name',
        'icon_img',
        'status',
        'create_dtm',
        'create_by',
        'update_by'
    ];

    function get($id = '') {
        $data = Ecom_product_iconpromotion_Model::select('*');
        if($id != ''){
            $data->where('id', $id);
        }
        $res = $data->get();
        return $res;
    }
}
