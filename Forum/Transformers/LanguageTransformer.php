<?php

namespace App\Forum\Transformers;

class LanguageTransformer extends Transformer
{
    protected $resourceName = 'language';

    public function transform($data)
    {
        return $data;
    }
}
