<?php
/**
 * Processor file for [[+packageName]] extra
 *
 * Copyright [[+copyright]] by [[+author]] [[+email]]
 * Created on [[+createdon]]
 *
[[+license]]
 *
 * @package [[+packageNameLower]]
 * @subpackage processors
 */

/* @var $modx modX */


class mc_ProcessorTypeProcessor extends modObjectGetListProcessor {
    public $classKey = 'modmc_Element';
    public $languageTopics = array('mc_packageNameLower:default');
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'ASC';


   /**
     * Iterate across the data
     *
     * @param array $data
     * @return array
     */
    public function iterate(array $data) {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $object) {
            if ($this->checkListPermission && $object instanceof modAccessibleObject && !$object->checkPolicy('list')) continue;
			
            $objectArray = $this->prepareRow($object);
			
            if (!empty($objectArray) && is_array($objectArray)) {
				$objectArray['cls'] = 'pupdate premove';
                $list[] = $objectArray;
                $this->currentIndex++;
            }
        }
        $list = $this->afterIteration($list);
		
        return $list;
    }
	
    /**
     * Get the data of the query
     * @return array
     */
    public function getData() {
		
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        /* query for chunks */
        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey,$c);
        $c = $this->prepareQueryAfterCount($c);

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey,$this->getProperty('sortAlias',$sortClassKey),'',array($this->getProperty('sort')));
        if (empty($sortKey)) $sortKey = $this->getProperty('sort');
		$dir = $this->getProperty('dir');
		$dir = empty($dir) ? $this->defaultSortDir : $dir;
        $c->sortby($sortKey,$dir);
        if ($limit > 0) {
            $c->limit($limit,$start);
        }

		
        $data['results'] = $this->modx->getCollection($this->classKey,$c);
		
        return $data;
    }
}
return 'mc_ProcessorTypeProcessor';
