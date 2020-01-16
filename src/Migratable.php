<?php

namespace Rennokki\ElasticScout;

trait Migratable
{
    /**
     * Get the migratable alias.
     *
     * @param  string  $alias
     * @return string
     */
    public function getMigratableAlias(string $alias)
    {
        return "{$this->getName()}_{$alias}";
    }
}
