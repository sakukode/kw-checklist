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

    protected $filterable = [
        'name',        
    ];

    /**
     * Filter data
     * @param  [type] $query   [description]
     * @param  [type] $filters [description]
     * @return [type]          [description]
     */
    public function scopeFilter($query, $filters)
    {
        $fields = $this->filterable;
     
        if($filters != null) {
            foreach($filters as $field => $opt) {            
                $cond = key($opt);
                $val = $opt[$cond];

                if(in_array($field, $fields)) {
                    switch ($cond) {
                        case 'is':
                            $query->where($field, $val);
                            break;
                        case 'between':
                            $dates = explode(",", $val);
                            $start_date = date("Y-m-d H:i:s", strtotime($dates[0]));
                            $end_date = date("Y-m-d H:i:s", strtotime($dates[1]));
                            $query->whereBetween($field, [$start_date, $end_date]);
                        default:
                            # code...
                            break;
                    }

                }
            }
        }
    }


    /**
     * Sort Data
     * @param  [type] $query [description]
     * @param  [type] $sort [description]
     * @return [type]        [description]
     */
    public function scopeSort($query, $sort)
    {
        if ($sort != null) {                        
            $sort_column  = $sort[0] == "-" ? ltrim($sort, '-') : $sort;
            $sort_order   = $sort[0] == "-" ? "desc" : "asc";

            $query->orderBy($sort_column, $sort_order);
        }

        return $query;
    }

    public function scopePagination($query, $limit = 10, $offset = 0, $sort = null, $filters = null)
    {        
        $query->filter($filters)            
              ->limit($limit)
              ->offset($offset)
              ->sort($sort);              
        
        return $query;        
    }
}