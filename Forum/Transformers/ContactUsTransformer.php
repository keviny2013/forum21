<?php

namespace App\Forum\Transformers;

class ContactUsTransformer extends Transformer
{
    protected $resourceName = 'contact';

    public function transform($data)
    {
        return $data;
    }
}
