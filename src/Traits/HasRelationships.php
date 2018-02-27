<?php

namespace Bread\Traits;

use Bread\BreadFacade;
use TCG\Voyager\Database\Schema\SchemaManager;

trait HasRelationships
{
    public function getRelationshipsAttribute()
    {
        if (!isset($this->relationships)) {
            return null;
        }
        $relationships = [];
        foreach ($this->relationships as $relationship) {
            $method = $this->$relationship();
            $relationships[$relationship] = $method;
        }
        return $relationships;
    }

    public function getRelationshipContent($relationship, $method, $visible_rows)
    {
        $type = get_unqualified_class($relationship);
        $pivot = [];

        foreach ($visible_rows as $row) {
            $fields = parse_field_name($row->field);
            if ($fields['type'] == 'pivot') {
                $pivot[] = $fields['attribute'];
            }
        }

        if ($type == 'BelongsTo') {
            $name = $relationship->getRelation();
            $content = $this->$name();
        } else if ($type == 'BelongsToMany') {
            $name = $relationship->getRelationName();
            $content = $this->$name()->withPivot(array_unique($pivot));
        } else if ($type == 'HasOne' || $type == 'HasMany') {
            $content = $this->$method();
        }

        if (isset($content)) {
            return $content->get();
        }
        return [];
    }
}
