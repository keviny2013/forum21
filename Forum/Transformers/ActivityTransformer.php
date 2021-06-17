<?php

namespace App\Forum\Transformers;

use App\Forum\Parent\Parentable;

class ActivityTransformer extends Transformer
{
    use Parentable;

    protected $resourceName = 'activity';

    public function transform($data)
    {

        $type = self::getTypeFromModel($data['subject_type']);

        $model = self::formatModel($data['subject'], $type);

        return [
            'id'         => $data['id'],
            'event_type' => $data['description'],
            'created_at' => $data['created_at']->toAtomString(),
            'model'      => $model,
        ];
    }

}
