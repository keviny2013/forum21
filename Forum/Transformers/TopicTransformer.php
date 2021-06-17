<?php

namespace App\Forum\Transformers;

class TopicTransformer extends Transformer
{
    protected $resourceName = 'topic';

    public function transform($data)
    {

        $community = $data['community']->first() ? (new CommunityTransformer())->transform($data['community']->first()) : [];

        $topic = [
            'slug'           => $data['slug'],
            'title'          => $data['title'],
            'about'          => $data['about'],
            'facts'          => $data['facts'],
            'tagList'        => $data['tagList'],
            'createdAt'      => $data['created_at']->toAtomString(),
            'updatedAt'      => $data['updated_at']->toAtomString(),
            'favorited'      => $data['favorited'],
            'favoritesCount' => $data['favoritesCount'],
            'language'       => $data['language'],
            'community'      => $community,
            'categories'     => array_map(function ($category) {
                return [
                    'id'          => $category['id'],
                    'name'        => $category['name'],
                    'description' => $category['description'],
                    'parents'     => $category['parents'],
                    'children'    => $category['children']
                ];
            }, $data['categories']->all()),

            'author'     => [
                'username'  => $data['user']['username'],
                'bio'       => $data['user']['bio'],
                'image'     => $data['user']['image'],
                'following' => $data['user']['following'],
            ],
            'moderators' => array_map(function ($user) {
                return [
                    'username' => $user['username'],
                    'bio'      => $user['bio'],
                    'image'    => $user['image'],
                ];
            }, $data['moderators']->all()),
            'opinions'   => $data['opinions']
        ];
        $topic['moderators'][] = $topic['author'];

        $moderators = $data['moderators']->pluck('id');
        $moderators = $moderators->all();

        $user_id = auth()->id();

        if ($user_id === $data['user']['id'] || in_array($user_id, $moderators)) {
            $topic['moderatorRequests'] = array_map(function ($user) {
                return [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'bio'      => $user['bio'],
                    'image'    => $user['image'],
                ];
            }, $data['moderatorRequests']->all());
        }


        return $topic;
    }
}
