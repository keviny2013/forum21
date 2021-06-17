<?php

namespace App\Forum\Transformers;

class OpinionTransformer extends Transformer
{
    protected $resourceName = 'opinion';

    public function transform($data)
    {
        $opinion = [
            'id'         => $data['id'],
            'title'      => $data['title'],
            'about'      => $data['about'],
            'solution'   => $data['solution'],
            'slug'       => $data['slug'],
            'createdAt'  => $data['created_at']->toAtomString(),
            'updatedAt'  => $data['updated_at']->toAtomString(),
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
        ];
        array_push($opinion['moderators'], $opinion['author']);

        $moderators = $data['moderators']->pluck('id');
        $moderators = $moderators->all();

        $user_id = auth()->id();

        if ($user_id === $data['user']['id'] || in_array($user_id, $moderators)) {
            $opinion['moderatorRequests'] = array_map(function ($user) {
                return [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'bio'      => $user['bio'],
                    'image'    => $user['image'],
                ];
            }, $data['moderatorRequests']->all());
        }
        return $opinion;
    }
}
