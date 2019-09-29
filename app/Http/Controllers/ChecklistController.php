<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Checklist;
use App\Http\Resources\ChecklistResource;
use App\Http\Resources\ChecklistsResource;

class ChecklistController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return ArticlesResource
     */
    public function index(Request $request)
    {                    
        //get params filter&sort
        $filters = $request->get('filter');    
        $sort    = $request->get('sort');       
        $page    = $request->get('page');
        $limit   = $page ? $page['limit'] : 10;
        $offset  = $page ? $page['offset'] : 0;       
       
        return new ChecklistsResource(Checklist::pagination($limit, $offset, $sort, $filters)->get());
    }

    public function show(Request $request, $id)
    {
        $checklist = Checklist::find($id);        

        if(!$checklist) {
            return $this->customResponse('Not Found', 404);
        }

        $response = new ChecklistResource($checklist);

        return $response;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'data.attributes.object_domain' => 'required',
            'data.attributes.object_id'     => 'required',
            'data.attributes.description'   => 'required',
        ]);

        $data              = $request->data;
        $attributes        = $data['attributes'];
        $attributes['due'] = Arr::has($attributes, 'due') ? $attributes['due'] : null;
        $attributes['due'] = $attributes['due'] != null ? date("Y-m-d H:i:s", strtotime($attributes['due'])) : $attributes['due'];
        $checklist         = Checklist::create($attributes);

        if($checklist) {
            $items = Arr::has($attributes, 'items') ? $attributes['items'] : null;
            
            if ($items) {
                $checklist_items = [];
                foreach ($items as $item) {
                    $checklist_items[] = [
                        'description' => $item,
                        'task_id'     => $attributes['task_id'],
                    ];
                }

                $checklist->items()->createMany($checklist_items);
            }

            return new ChecklistResource($checklist);      
        }

        return $this->customResponse('Server Error', 500);

    }

    public function update($checklistId, Request $request)
    {
        //validate request parameters
        $this->validate($request, [
            'data.attributes.object_domain' => 'required',
            'data.attributes.object_id'     => 'required',
            'data.attributes.description'   => 'required',
        ]);

        //Return error 404 response if data was not found
        if(!Checklist::find($checklistId)) 
            return $this->customResponse('Not Found', 404);
        
        $data       = $request->data;
        $attributes = $data['attributes'];         
        
        $attributes['due']          = Arr::has($attributes, 'due') ? $attributes['due'] : null;
        $attributes['due']          = $attributes['due'] != null ? date("Y-m-d H:i:s", strtotime($attributes['due'])) : null;
        $attributes['created_at']   = Arr::has($attributes, 'created_at') ? date("Y-m-d H:i:s", strtotime($attributes['created_at'])) : null;
        
        
        $res = Checklist::find($checklistId)->update($attributes);

        if($res){
            //return updated data
            $checklist = Checklist::find($checklistId);
            return new ChecklistResource($checklist);        
        }

        //Return error 500 response if updated was not successful        
        return $this->customResponse('Server Error', 500);
    }

    public function destroy($id)
    {        
        //Return error 404 response if product was not found
        if(!Checklist::find($id)) 
            return $this->customResponse('Not Found', 404);
        
        //Return 204(done) success response if delete was successful
        if(Checklist::find($id)->delete()){
            return $this->customResponse('', 204);
        }

        //Return error 400 response if delete was not successful
        return $this->customResponse('Server Error', 500);
    }   

    public function customResponse($message = 'success', $status = 200)
    {
        return response(['status' =>  $status, 'message' => $message], $status);
    }

}
