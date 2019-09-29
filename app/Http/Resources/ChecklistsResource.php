<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

use App\Checklist;

class ChecklistsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => ChecklistResource::collection($this->collection),
        ];
    }    

    public function with($request)
    {                        
        //get all query string params
        $query_str = "";
        $query_str = urldecode($request->getQueryString());
        $query_str = $query_str != "" ? "?".$query_str : $query_str;
        // get page query string
        $page   = $request->has('page') ? $request->get('page') : ['limit' => 10, 'offset' => 0];
        $limit  = $page['limit'];
        $offset = $page['offset'];

        //total rows
        $total = Checklist::count();
        //total pages
        $total_pages = ceil($total / $limit);        
        //current page
        $current_page = ceil(($offset) / $limit) + 1;

        //generate links
        $links = [
            'first'=> '', 
            'last' => '', 
            'next' => '', 
            'prev' => ''
        ];

        foreach($links as $name => $url) {

            switch ($name) {
                case 'first':
                    $current_offset = 0;     
                    $page['offset'] = $current_offset;
                    $page_param = rawurldecode(http_build_query(['page' => $page]));
                    $url = $query_str != "" ? preg_replace('/page\[limit]=\d*&page\[offset]=\d*/i', $page_param, $query_str) : $query_str."?".$page_param;

                    $links[$name] = route('checklists.index').$url;               
                    break;
                case 'last':
                    $current_offset = $total > $limit ? ($total_pages -1) * $limit : 0;  
                    $page['offset'] = $current_offset;
                    $page_param = rawurldecode(http_build_query(['page' => $page]));
                    $url = $query_str != "" ? preg_replace('/page\[limit]=\d*&page\[offset]=\d*/i', $page_param, $query_str) : $query_str."?".$page_param;

                    $links[$name] = route('checklists.index').$url;                  
                    break;
                case 'next':
                    $current_offset =  $current_page * $limit;
                    $page['offset'] = $current_offset;
                    $page_param = rawurldecode(http_build_query(['page' => $page]));
                    $url = $query_str != "" ? preg_replace('/page\[limit]=\d*&page\[offset]=\d*/i', $page_param, $query_str) : $query_str."?".$page_param;

                    $links[$name] = $current_page < $total_pages ? route('checklists.index').$url : null;
                    break;
                case 'prev':
                    $current_offset =  (($current_page - 1) * $limit) - $limit;
                    $page['offset'] = $current_offset;
                    $page_param = rawurldecode(http_build_query(['page' => $page]));
                    $url = $query_str != "" ? preg_replace('/page\[limit]=\d*&page\[offset]=\d*/i', $page_param, $query_str) : $query_str."?".$page_param;

                    $links[$name] = $current_page > 1 ? route('checklists.index').$url : null;
                    break;
                default:
                    # code...
                    break;
            }            
        }
                
        return [
            'meta' => [
                'count' => $this->count(),
                'total' => $total,
            ],
            'links' => [
                'first' => $links['first'],
                'last'  => $links['last'],
                'next'  => $links['next'],
                'prev'  => $links['prev'],
            ]
        ];
    }
}