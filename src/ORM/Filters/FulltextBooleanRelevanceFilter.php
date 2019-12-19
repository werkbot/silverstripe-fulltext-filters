<?php

namespace Fromholdio\FulltextFilters\ORM\Filters;

use SilverStripe\ORM\DataQuery;

class FulltextBooleanRelevanceFilter extends FulltextBooleanFilter
{
    protected $weight;
    protected $alias;

    public function __construct(?string $fullName = null, $value = false, array $modifiers = array(), int $weight = 1, string $alias="")
    {
        parent::__construct($fullName, $value, $modifiers);
        $this->setWeight($weight);
        $this->setAlias($alias);
    }

    protected function applyOne(DataQuery $query)
    {
        $query = parent::applyOne($query);
        $alias = $this->getRelevanceAlias();
        $score = $this->getScoreName();
        $select = sprintf("{$score} := MATCH (%s) AGAINST ('{$this->getValue()}' IN BOOLEAN MODE)", $this->getDbName());
        $weight = $this->getWeight();
        $select = $select . ' * ' . $weight;
        $query->selectField($select, $alias);
        return $query;
    }

    public function setAlias(string $alias)
    {
        $this->alias = $alias;
        return $this;
    }

    public function setWeight(int $weight)
    {
        $this->weight = $weight;
        return $this;
    }

    public function getWeight()
    {
        return (int) $this->weight;
    }

    public function getRelevanceAlias()
    {
        if($this->alias){
            return $this->alias;
        }else{
            return $this->getName() . 'Relevance';
        }
    }

    public function getScoreName()
    {
        return '@' . $this->getName() . 'Score';
    }
}
