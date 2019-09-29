<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ChecklistResources extends Resource
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
        $attributes = [
            'object_domain' => $this->object_domain,
            'object_id'     => (string) $this->object_id,
            'description'   => $this->description,            
            'is_completed'  => (boolean) $this->is_completed,
            'completed_at'  => $this->completed_at,
            'update_by'     => $this->update_by,
            'created_by'    => $this->created_by,
            'update_at'     => $this->update_at,
            'created_at'    => $this->created_at,
            'due'           => $this->due,
            'urgency'       => $this->urgency,            
            $this->mergeWhen($request->get('include') == "items", [
                'items' => $this->items
            ])
        ];

        return [
            'type'       => 'checklists',
            'id'         => (string) $this->id,
            'attributes' => $attributes,
            'links'      => [
                'self' => route('checklists.show', ['checklistId' => $this->id]),
            ],
        ];
    }
}
