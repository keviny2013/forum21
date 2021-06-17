<?php

namespace App\Forum\Transformers;

use App\Forum\Parent\Parentable;
use Illuminate\Support\Facades\Storage;

class CommunityTransformer extends Transformer
{
    protected $resourceName = 'community';

    public function transform($data)
    {


       $community = [
            'id'           => $data['id'],
            'slug'         => $data['slug'],
            'title'        => $data['title'],
            'about'        => $data['about'],
            'private'      => (boolean)$data['private'],
            'readonly'     => (boolean)$data['readonly'],
            'image'        => Storage::disk('s3')->url($data['image']),
            'createdBy'    => $data['user'],
            'membersCount' => $data['members']->count(),
            'topicsCount'  => $data['topics']->count(),
            'joined'       => (boolean)$data['joined'],
            'createdAt'    => $data['created_at']->toAtomString(),
            'updatedAt'    => $data['updated_at']->toAtomString(),
            'language_id'  => $data['language_id'],
            'location'     => $data['location'],
            'profession'   => $data['profession'],
        ];

        $moderators = $data['moderators']->pluck('id');
        $moderators = $moderators->all();

        $user_id = auth()->id();

        if ($user_id === $data['user']['id'] || in_array($user_id, $moderators)) {
            $community['joinRequests'] = array_map(function ($user) {
                return [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'bio'      => $user['bio'],
                    'image'    => $user['image'],
                ];
            }, $data['joinRequests']->all());
        }

       return $community;
    }

}
