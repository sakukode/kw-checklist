<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ItemResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'type'       => 'items',
            'id'         => (string) $this->id,
            'attributes' => [                
                'description'    => $this->description,
                'is_completed'   => (boolean) $this->is_completed,
                'completed_at'   => $this->completed_at,
                'update_by'      => $this->update_by,
                'update_at'      => $this->update_at,
                'created_at'     => $this->created_at,
                'due'            => $this->due,
                'urgency'        => $this->urgency,      
                'assignee_id'    => $this->assignee_id,
                'task_id'        => $this->task_id,          
            ],
            'links' => [
                'self' => route('items.show', ['checklistId' => $this->checklist_id,'itemId' => $this->id]),
            ]
        ];
    }    
}
