<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
	protected $fillable = [
        'name', 'description', 'user_id',
    ];

	public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function getApiModel()
    {
        return [
            'id'=> $this->id,
            'name'=> $this->name,
            'description'=> $this->description
        ];
    }
}
