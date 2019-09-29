<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Checklist;
use App\Item;
use App\Http\Resources\ItemResource;
use App\Http\Resources\ItemsResource;

class ItemController extends Controller
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

    public function index(Request $request)
    {
        //get params filter&sort
        $filters = $request->get('filter');    
        $sort    = $request->get('sort');       
        $page    = $request->get('page');
        $limit   = $page ? $page['limit'] : 10;
        $offset  = $page ? $page['offset'] : 0;       
       
        return new ItemsResource(Item::pagination($limit, $offset, $sort, $filters)->get());
    }

    public function index_by_checklist($checklistId, Request $request)
    {
        //get params filter&sort
        $filters = $request->get('filter');    
        $sort    = $request->get('sort');       
        $page    = $request->get('page');
        $limit   = $page ? $page['limit'] : 10;
        $offset  = $page ? $page['offset'] : 0;

        return new ItemsResource(Item::where(['checklist_id' => $checklistId])->pagination($limit, $offset, $sort, $filters)->get());
    }

    public function show($id)
    {
        $item = Item::find($id);

        if(!$item) {
            return $this->customResponse('Not Found', 404);
        }

        $response = new ItemResource($item);

        return $response;
    }

    public function store($checklistId, Request $request)
    {
        //Return error 404 response if data was not found
        if(!Checklist::find($checklistId)) 
            return $this->customResponse('Not Found', 404);

        $this->validate($request, [            
            'data.attributes.description'   => 'required',
        ]);

        $data                       = $request->data;
        $attributes                 = $request->input('data.attributes');
        $attributes['due']          = $request->has('data.attributes.due') ? $attributes['due'] : null;
        $attributes['due']          = $attributes['due'] != null ? date("Y-m-d H:i:s", strtotime($attributes['due'])) : null;
        $attributes['checklist_id'] = $checklistId;

        $item         = Item::create($attributes);

        if($item) {
            //return created data
            return new ItemResource($item);
        }

        //Return error 500 response if updated was not successful        
        return $this->customResponse('Server Error', 500);
    }

    public function update($checklistId, $itemId, Request $request)
    {        
        $item = Item::find($itemId);
        //Return error 404 response if data was not found
        if(!$item) 
            return $this->customResponse('Not Found', 404);

        //validate request parameters
        $this->validate($request, [            
            'data.attributes.description'   => 'required',
        ]);
        
        $data       = $request->data;
        $attributes = $data['attributes'];         
        
        $attributes['due']          = Arr::has($attributes, 'due') ? $attributes['due'] : null;
        $attributes['due']          = $attributes['due'] != null ? date("Y-m-d H:i:s", strtotime($attributes['due'])) : null;
        $attributes['created_at']   = Arr::has($attributes, 'created_at') ? date("Y-m-d H:i:s", strtotime($attributes['created_at'])) : null;        
        $res = Item::find($itemId)->update($attributes);

        if($res){
            //return updated data
            return new ItemResource($item);            
        }

        //Return error 500 response if updated was not successful        
        return $this->customResponse('Server Error', 500);
    }

    public function mass_update(Request $request, $id)
    {
        //validate request parameters
        $this->validate($request, [            
            'data.*.attributes.description'   => 'required',
        ]);

        //Return error 404 response if product was not found
        if(!Checklist::find($id)) 
            return $this->customResponse('Not Found', 404);

        $data = $request->data;
        $response = [];

        foreach($data as $d) {
            if(!Item::find($d['id'])) {
                $response[] = [
                    'id' => $d['id'],
                    'action' => $d['action'],
                    'status' => 404
                ];
            } else {
                $attributes         = $d['attributes'];
                $attributes['due']  = Arr::has($attributes, 'due') ? $attributes['due'] : null;
                $attributes['due']  = $attributes['due'] != null ? date("Y-m-d H:i:s", strtotime($attributes['due'])) : null;

                $item = Item::find($d['id'])->update($attributes);    

                if($item) {
                    $response[] = [
                        'id' => $d['id'],
                        'action' => $d['action'],
                        'status' => 200
                    ];  
                } else {
                    $response[] = [
                        'id' => $d['id'],
                        'action' => $d['action'],
                        'status' => 403
                    ];
                }
            }
            
        }

        return response(['data' => $response], 200);
    }

    public function destroy($checklistId, $itemId)
    {                
        //Return error 404 response if product was not found
        if(!Item::find($itemId)) 
            return $this->customResponse('Not Found', 404);
        
        //Return 410(done) success response if delete was successful
        if(Item::find($itemId)->delete()){
            return $this->customResponse('', 204);
        }

        //Return error 400 response if delete was not successful
        return $this->customResponse('Server Error', 500);
    }

    public function complete(Request $request)
    {
        $data = $request->data;
        $response = [];

        foreach($data as $d) {
            $item = Item::find($d['item_id']);

            if($item) {
                $item->is_completed = true;
                $item->save();

                $d['id'] = $item->id;
                $d['is_completed'] = $item->is_completed;
                $d['checklist_id'] = $item->checklist_id;

                $response[] = $d;
            }
        }

        return response(['data' => $response], 200);
    }

    public function incomplete(Request $request)
    {
        $data = $request->data;
        $response = [];

        foreach($data as $d) {
            $item = Item::find($d['item_id']);

            if($item) {
                $item->is_completed = false;
                $item->save();

                $d['id'] = $item->id;
                $d['is_completed'] = $item->is_completed;
                $d['checklist_id'] = $item->checklist_id;

                $response[] = $d;
            }
        }

        return response(['data' => $response], 200);
    }

    public function customResponse($message = 'success', $status = 200)
    {
        return response(['status' =>  $status, 'message' => $message], $status);
    }

}
