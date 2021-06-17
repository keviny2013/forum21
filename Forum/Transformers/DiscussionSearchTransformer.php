<?php

namespace App\Forum\Transformers;

use App\Forum\Parent\Parentable;

class DiscussionSearchTransformer extends DiscussionTransformer
{
    use Parentable;

    protected $resourceName = 'item';

    public function transform($data)
    {

        $discussion = parent::transform($data);

        return array_merge(
            $discussion,
            [
                'model' => self::formatModel($data, 'discussion')
            ]
        );
    }


}
