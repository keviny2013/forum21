<?php

namespace App\Forum\Transformers;

use App\Forum\Parent\Parentable;

class OpinionSearchTransformer extends OpinionTransformer
{
    use Parentable;

    protected $resourceName = 'item';

    public function transform($data)
    {
        $opinion = parent::transform($data);
        return array_merge($opinion, [
            'categories' => array_map(function ($category) {
                return [
                    'id'          => $category['id'],
                    'name'        => $category['name'],
                    'description' => $category['description'],
                    'parents'     => $category['parents'],
                    'children'    => $category['children']
                ];
            }, $data['topic']['categories']->all()),
            'language'   => $data['topic']['language'],
            'model'      => self::formatModel($data, 'opinion')
        ]);
    }
}
