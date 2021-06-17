<?php

namespace App\Forum\Transformers;

class DiscussionTransformer extends Transformer
{
    protected $resourceName = 'discussion';

    public function transform($data)
    {
        return [
            'id'              => $data['id'],
            'title'           => $data['title'],
            'goal'            => $data['goal'],
            'language'        => $data['language']['name'],
            'expiration_date' => $data['expiration_date'],
            'slug'            => $data['slug'],
            'createdAt'       => $data['created_at']->toAtomString(),
            'updatedAt'       => $data['updated_at']->toAtomString(),
            'categories'      => $this->getCategories($data),
            'author'          => [
                'username'  => $data['user']['username'],
                'bio'       => $data['user']['bio'],
                'image'     => $data['user']['image'],
                'following' => $data['user']['following'],
            ]
        ];
    }

    private function getCategories($data)
    {

        $model = $data->holder->first();

        $type = strtolower((new \ReflectionClass($model))->getShortName());


        $categories = [];

        switch ($type) {
            case 'topic':
                $categories = $model['categories']->all();
                break;
            case 'opinion':
                $categories = $model['topic']['categories']->all();
                break;
            case 'category':
                $categories = [$model];
                break;
            default:
        }

        if (count($categories) === 0) {
            return [];
        }


        return array_map(function ($category) {
            return [
                'id'          => $category['id'],
                'name'        => $category['name'],
                'description' => $category['description'],
                'parents'     => $category['parents'],
                'children'    => $category['children']
            ];
        }, $categories);
    }
}
