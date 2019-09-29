<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checklist extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'object_domain',
        'object_id',
        'due',
        'due_interval',
        'due_unit',
        'urgency',
        'description',
        'completed_at',
        'updated_by',
        'updated_at',
        'created_at',
        'urgency',
        'is_completed',
    ];

    protected $filterable = [
        'due',
        'created_by',
        'updated_by',
        'object_domain',
        'object_id',
        'is_completed',
        'urgency',
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

    /**
     * Override parent boot and Call deleting item
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($users) {
            foreach ($users->items()->get() as $item) {
                $item->delete();
            }
        });
    }

    /**
     * Get the items for the checklists.
     */
    public function items()
    {
        return $this->hasMany('App\Item');
    }
}
