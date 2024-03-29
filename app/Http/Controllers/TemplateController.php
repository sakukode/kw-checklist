<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use App\Checklist;
use App\Template;

use App\Http\Resources\TemplateResource;
use App\Http\Resources\TemplatesResource;

class TemplateController extends Controller
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
       
        return new TemplatesResource(Template::pagination($limit, $offset, $sort, $filters)->get());
    }


    public function show($id)
    {
        $template = Template::find($id);

        if(!$template) {
            return $this->customResponse('Not Found', 404);
        }

        $response = new TemplateResource($template);

        return $response;        
    }

    public function store(Request $request)
    {        
        $data = $request->data;
        $name = $request->input('data.attributes.name');
        
        //create template
        $template = Template::create(['name' => $name]);

        if($template) {
            $data['id'] = $template->id;

            // create related checklist
            if($request->has('data.attributes.checklist')) {
                $payload_checklist = $request->input('data.attributes.checklist');
                $checklist = $template->checklist()->create($payload_checklist);

                if($checklist) {
                    // create related items
                    if($request->has('data.attributes.items')) {
                        $payload_items = $request->input('data.attributes.items');
                        $checklist->items()->createMany($payload_items);
                    }
                }
            }            

            return response(['data' => $data], 201);
        }
    }

    public function update($templateId, Request $request)
    {           
        $template = Template::find($templateId);
        //Return error 404 response if data was not found
        if(!$template) 
            return $this->customResponse('Not Found', 404);
        
        $data = $request->data;
        $name = $request->input('data.attributes.name');        
        
        $res = Template::find($templateId)->update(['name' => $name]);

        if($res) {            
            $data['id'] = $template->id;            

            return response(['data' => $data], 200);
        }

        //Return error 500 response if updated was not successful        
        return $this->customResponse('Server Error', 500);
    }

    public function destroy($id)
    {        
        //Return error 404 response if product was not found
        if(!Template::find($id)) 
            return $this->customResponse('Not Found', 404);
        
        //Return 204(done) success response if delete was successful
        if(Template::find($id)->delete()){
            return $this->customResponse('', 204);
        }

        //Return error 400 response if delete was not successful
        return $this->customResponse('Server Error', 500);
    }  

    public function assigns(Request $request, $id)
    {
        
    } 

    public function customResponse($message = 'success', $status = 200)
    {
        return response(['status' =>  $status, 'message' => $message], $status);
    }

}
