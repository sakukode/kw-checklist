<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TemplateResource extends Resource
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
            'type'       => 'templates',
            'id'         => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'checklist' => [
                    'due_unit' => $this->checklist->due_unit,
                    'description' => $this->checklist->description,
                    'due_interval' => $this->checklist->due_interval
                ],
                'items' => $this->checklist->items->makeHidden([     
                    "id",               
                    "is_completed",
                    "completed_at",
                    "due",                    
                    "created_by",
                    "updated_by",
                    "assignee_id",
                    "task_id",
                    "checklist_id",
                    "created_at",
                    "updated_at",
                    "deleted_at"
                ])
            ],
            'links'      => [
                'self' => route('templates.show', ['templateId' => $this->id]),
            ],
        ];
    }
}
