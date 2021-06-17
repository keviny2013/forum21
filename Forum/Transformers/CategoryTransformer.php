<?php

namespace App\Forum\Transformers;

class CategoryTransformer extends Transformer
{
    protected $resourceName = 'category';

    public function transform($data)
    {
        return [
            'id'          => $data['id'],
            'name'        => $data['name'],
            'description' => $data['description'],
            'parent_id'   => $data['parent_id'],
            'parents'     => $data['parents'],
            'children'    => $data['children']
        ];
    }
}
