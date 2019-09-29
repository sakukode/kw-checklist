<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name',        
    ];    

    /**
     * Get the checklists for the checklists.
     */
    public function checklist()
    {
        return $this->hasOne('App\Checklist');
    }
}