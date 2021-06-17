<?php

namespace App\Forum\Transformers;

class UserTransformer extends Transformer
{
    protected $resourceName = 'user';

    public function transform($data)
    {
        return [
            'id' => $data['id'],
            'email' => $data['email'],
            'token' => $data['token'],
            'username' => $data['username'],
            'bio' => $data['bio'],
            'image' => $data['image'],
            'role' => $data->getRoleNames()[0],
			'language' => $data['language'],
            'profession' => $data['profession'],
        ];
    }
}
