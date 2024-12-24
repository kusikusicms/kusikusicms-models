<?php

namespace KusikusiCMS\Models\Support;

use Illuminate\Database\Eloquent\Collection;
use KusikusiCMS\Models\EntityContent;

class EntityContentCollection extends Collection
{
    /**
     * Methods
     */
    public function flattenByField () {
        return $this->reduce(function (array $carry, EntityContent $content) {
            $carry[$content->field] = $content->text;
            return $carry;
        }, []);
    }
}

