<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
   /*  protected $table = 'municipios'; */
   
    
      protected $fillable = ['name', 'code', 'codigo_dane', 'department_id'];
  
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
}
