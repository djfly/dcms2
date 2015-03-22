<?php

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\db\ActiveRecord;

class NestedSetBehavior extends Behavior
{
	public $hasManyRoots=false;
	public $rootAttribute='root';
	public $leftAttribute='lft';
	public $rightAttribute='rgt';
	public $levelAttribute='level';
	
	private $_ignoreEvent = false;
	private $_deleted = false;
	private $_id;
	
	private static $_cached;
	private static $_c = 0;
	
	public function events()
	{
		return [
			ActiveRecord::EVENT_INIT => 'afterConstruct',
			ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
			ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
			ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
			ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
		];
	}	

	/**
	 * Named scope. Gets descendants for node.
	 * @param int $depth the depth.
	 * @return CActiveRecord the owner.
	 */
	public function descendants($depth = null)
	{
		/** @var \yii\db\ActiveRecord */
		$owner = $this->owner;
		$query = $owner->find()
			->andWhere("[[$this->leftAttribute]] > :left", [':left' => $owner->{$this->leftAttribute}])
			->andWhere("[[$this->rightAttribute]] < :right", [':right' => $owner->{$this->rightAttribute}])
			->orderBy([$this->leftAttribute => SORT_ASC]);
		
		if($depth !== null){
			$query->andWhere($this->levelAttribute . '<= :depth', [':depth' => $owner->{$this->levelAttribute} + $depth]);
		}

		if($this->hasManyRoots){
			$query->andWhere("$this->rootAttribute = :root", [':root' => $owner->{$this->rootAttribute}]);
		}
		return $query;
	}

	/**
	 * Named scope. Gets children for node (direct descendants only).
	 * @return CActiveRecord the owner.
	 */
	public function children()
	{
		return $this->descendants(1);
	}

	/**
	 * Named scope. Gets ancestors for node.
	 * @param int $depth the depth.
	 * @return CActiveRecord the owner.
	 */
	public function ancestors($depth=null)
	{
		$query = $this->owner->find()
			->andWhere("[[$this->leftAttribute]] < :left", [':left' => $this->owner->{$this->leftAttribute}])
			->andWhere("[[$this->rightAttribute]] > :right", [':right' => $this->owner->{$this->rightAttribute}])
			->orderBy([$this->leftAttribute => SORT_ASC]);

		if($depth !== null){
			$query->andWhere($this->levelAttribute . '>= :depth', [':depth' => $this->owner->{$this->levelAttribute} - $depth]);
		}

		if($this->hasManyRoots){
			$query->andWhere("$this->rootAttribute = :root", [':root' => $this->owner->{$this->rootAttribute}]);
		}
		return $query;
	}

	/**
	 * Named scope. Gets root node(s).
	 * @return CActiveRecord the owner.
	 */
	public function roots()
	{
		return $query = $this->owner->find()->andWhere("[[$this->leftAttribute]] = 1");
	}

	/**
	 * Named scope. Gets parent of node.
	 * @return CActiveRecord the owner.
	 */
	public function parent()
	{
		$owner = $this->owner;
		$query = $owner->find()
			->andWhere("[[$this->leftAttribute]] < :left", [':left' => $owner->{$this->leftAttribute}])
			->andWhere("[[$this->rightAttribute]] > :right", [':right' => $owner->{$this->rightAttribute}])
			->orderBy([$this->rightAttribute => SORT_ASC]);

		if($this->hasManyRoots){
			$query->andWhere("$this->rootAttribute = :root", [':root' => $owner->{$this->rootAttribute}]);
		}
		return $query;
	}

	/**
	 * Named scope. Gets previous sibling of node.
	 * @return CActiveRecord the owner.
	 */
	public function prev()
	{
		$owner = $this->owner;
		$query = $owner->find()
			->andWhere("[[$this->rightAttribute]] = :right", [':right' => $owner->{$this->leftAttribute} - 1]);

		if($this->hasManyRoots){
			$query->andWhere("$this->rootAttribute = :root", [':root' => $owner->{$this->rootAttribute}]);
		}
		return $query;
	}

	/**
	 * Named scope. Gets next sibling of node.
	 * @return CActiveRecord the owner.
	 */
	public function next()
	{
		$owner = $this->owner;
		$query = $owner->find()
			->andWhere("[[$this->leftAttribute]] = :left", [':left' => $owner->{$this->rightAttribute} + 1]);

		if($this->hasManyRoots){
			$query->andWhere("$this->rootAttribute = :root", [':root' => $owner->{$this->rootAttribute}]);
		}
		return $query;
	}

	/**
	 * Create root node if multiple-root tree mode. Update node if it's not new.
	 * @param boolean $runValidation whether to perform validation.
	 * @param boolean $attributes list of attributes.
	 * @return boolean whether the saving succeeds.
	 */
	public function save($runValidation = true,$attributes = null)
	{
		$owner = $this->owner;

		if($runValidation && !$owner->validate($attributes)){
			return false;
		}

		if($owner->getIsNewRecord()){
			return $this->makeRoot($attributes);
		}

		$this->_ignoreEvent = true;
		$result = $owner->update();
		$this->_ignoreEvent = false;

		return $result;
	}

	/**
	 * Create root node if multiple-root tree mode. Update node if it's not new.
	 * @param boolean $runValidation whether to perform validation.
	 * @param boolean $attributes list of attributes.
	 * @return boolean whether the saving succeeds.
	 */
	public function saveNode($runValidation = true, $attributes = null)
	{
		return $this->save($runValidation, $attributes);
	}

	/**
	 * Deletes node and it's descendants.
	 * @return boolean whether the deletion is successful.
	 */
	public function delete()
	{
		$owner = $this->owner;

		if($owner->getIsNewRecord()){
			throw new Exception('The node cannot be deleted because it is new.');
		}

		if($this->getIsDeletedRecord()){
			throw new Exception('The node cannot be deleted because it is already deleted.');
		}

		$db = $owner->getDb();
		$extTransFlag = $db->getTransaction();

		if($extTransFlag === null){
			$transaction = $db->beginTransaction();
		}

		try{
			if($owner->isLeaf()){
				$this->_ignoreEvent = true;
				$result = $owner->delete();
				$this->_ignoreEvent = false;
			}else{
				$query = new Query();
				$query->andWhere("[[$this->leftAttribute]] >= :left", [':left' => $owner->{$this->leftAttribute}])
					->andWhere("[[$this->rightAttribute]] <= :right", [':right' => $owner->{$this->rightAttribute}]);

				if($this->hasManyRoots){
					$query->andWhere("$this->rootAttribute = :root", [':root' => $owner->{$this->rootAttribute}]);
				}
				$result = $owner->deleteAll($query->where, $query->params) > 0;
			}

			if(!$result){
				if($extTransFlag === null){
					$transaction->rollback();
				}
				return false;
			}

			$this->shiftLeftRight($owner->{$this->rightAttribute} + 1, $owner->{$this->leftAttribute} - $owner->{$this->rightAttribute} - 1);

			if($extTransFlag===null){
				$transaction->commit();
			}
			$this->correctCachedOnDelete();
		}catch(Exception $e){
			if($extTransFlag === null){
				$transaction->rollback();
			}
			throw $e;
		}
		return true;
	}

	/**
	 * Deletes node and it's descendants.
	 * @return boolean whether the deletion is successful.
	 */
	public function deleteNode()
	{
		return $this->delete();
	}

	/**
	 * Prepends node to target as first child.
	 * @param CActiveRecord $target the target.
	 * @param boolean $runValidation whether to perform validation.
	 * @param array $attributes list of attributes.
	 * @return boolean whether the prepending succeeds.
	 */
	public function prependTo($target, $runValidation = true, $attributes = null)
	{
		return $this->addNode($target, $target->{$this->leftAttribute} + 1, 1, $runValidation, $attributes);
	}

	/**
	 * Prepends target to node as first child.
	 * @param CActiveRecord $target the target.
	 * @param boolean $runValidation whether to perform validation.
	 * @param array $attributes list of attributes.
	 * @return boolean whether the prepending succeeds.
	 */
	public function prepend($target, $runValidation = true, $attributes = null)
	{
		return $target->prependTo($this->owner, $runValidation, $attributes);
	}

	/**
	 * Appends node to target as last child.
	 * @param CActiveRecord $target the target.
	 * @param boolean $runValidation whether to perform validation.
	 * @param array $attributes list of attributes.
	 * @return boolean whether the appending succeeds.
	 */
	public function appendTo($target, $runValidation = true, $attributes = null)
	{
		return $this->addNode($target, $target->{$this->rightAttribute}, 1, $runValidation, $attributes);
	}

	/**
	 * Appends target to node as last child.
	 * @param CActiveRecord $target the target.
	 * @param boolean $runValidation whether to perform validation.
	 * @param array $attributes list of attributes.
	 * @return boolean whether the appending succeeds.
	 */
	public function append($target,$runValidation = true,$attributes = null)
	{
		return $target->appendTo($this->owner, $runValidation, $attributes);
	}

	/**
	 * Inserts node as previous sibling of target.
	 * @param CActiveRecord $target the target.
	 * @param boolean $runValidation whether to perform validation.
	 * @param array $attributes list of attributes.
	 * @return boolean whether the inserting succeeds.
	 */
	public function insertBefore($target, $runValidation = true, $attributes = null)
	{
		return $this->addNode($target, $target->{$this->leftAttribute}, 0, $runValidation, $attributes);
	}

	/**
	 * Inserts node as next sibling of target.
	 * @param CActiveRecord $target the target.
	 * @param boolean $runValidation whether to perform validation.
	 * @param array $attributes list of attributes.
	 * @return boolean whether the inserting succeeds.
	 */
	public function insertAfter($target, $runValidation = true, $attributes = null)
	{
		return $this->addNode($target, $target->{$this->rightAttribute} + 1, 0, $runValidation, $attributes);
	}

	/**
	 * Move node as previous sibling of target.
	 * @param CActiveRecord $target the target.
	 * @return boolean whether the moving succeeds.
	 */
	public function moveBefore($target)
	{
		return $this->moveNode($target, $target->{$this->leftAttribute}, 0);
	}

	/**
	 * Move node as next sibling of target.
	 * @param CActiveRecord $target the target.
	 * @return boolean whether the moving succeeds.
	 */
	public function moveAfter($target)
	{
		return $this->moveNode($target, $target->{$this->rightAttribute} + 1, 0);
	}

	/**
	 * Move node as first child of target.
	 * @param CActiveRecord $target the target.
	 * @return boolean whether the moving succeeds.
	 */
	public function moveAsFirst($target)
	{
		return $this->moveNode($target, $target->{$this->leftAttribute} + 1, 1);
	}

	/**
	 * Move node as last child of target.
	 * @param CActiveRecord $target the target.
	 * @return boolean whether the moving succeeds.
	 */
	public function moveAsLast($target)
	{
		return $this->moveNode($target, $target->{$this->rightAttribute}, 1);
	}

	/**
	 * Move node as new root.
	 * @return boolean whether the moving succeeds.
	 */
	public function moveAsRoot()
	{
		$owner = $this->owner;

		if(!$this->hasManyRoots){
			throw new Exception('Many roots mode is off.');
		}

		if($owner->getIsNewRecord()){
			throw new Exception('The node should not be new record.');
		}

		if($this->getIsDeletedRecord()){
			throw new Exception('The node should not be deleted.');
		}

		if($owner->isRoot()){
			throw new Exception('The node already is root node.');
		}

		$db = $owner->getDb();
		$extTransFlag = $db->getTransaction();

		if($extTransFlag === null){
			$transaction = $db->beginTransaction();
		}

		try{
			$left = $owner->{$this->leftAttribute};
			$right = $owner->{$this->rightAttribute};
			$levelDelta = 1 - $owner->{$this->levelAttribute};
			$delta = 1 - $left;

			$owner->updateAll(
				array(
					$this->leftAttribute => new Expression($db->quoteColumnName($this->leftAttribute) . sprintf('%+d', $delta)),
					$this->rightAttribute => new Expression($db->quoteColumnName($this->rightAttribute) . sprintf('%+d', $delta)),
					$this->levelAttribute => new Expression($db->quoteColumnName($this->levelAttribute) . sprintf('%+d', $levelDelta)),
					$this->rootAttribute => $owner->getPrimaryKey(),
				),
				[
					"and",
					"[[$this->leftAttribute]] >= :left",
					"[[$this->rightAttribute]] <= :right",
					"[[$this->rootAttribute]] = :root"
				],
				[
					':left' => $left,
					':right' => $right,
					':root' => $owner->{$this->rootAttribute}
				]
			);
			$this->shiftLeftRight($right + 1, $left - $right - 1);

			if($extTransFlag === null){
				$transaction->commit();
			}
			$this->correctCachedOnMoveBetweenTrees(1, $levelDelta, $owner->getPrimaryKey());
		}catch(Exception $e){
			if($extTransFlag === null){
				$transaction->rollback();
			}
			throw $e;
		}
		return true;
	}

	/**
	 * Determines if node is descendant of subject node.
	 * @param CActiveRecord $subj the subject node.
	 * @return boolean whether the node is descendant of subject node.
	 */
	public function isDescendantOf($subj)
	{
		$owner = $this->owner;
		$result = ($owner->{$this->leftAttribute} > $subj->{$this->leftAttribute})
		&& ($owner->{$this->rightAttribute} < $subj->{$this->rightAttribute});

		if($this->hasManyRoots){
			$result = $result && ($owner->{$this->rootAttribute} === $subj->{$this->rootAttribute});
		}
		return $result;
	}

	/**
	 * Determines if node is leaf.
	 * @return boolean whether the node is leaf.
	 */
	public function isLeaf()
	{
		$owner = $this->owner;
		return $owner->{$this->rightAttribute} - $owner->{$this->leftAttribute} === 1;
	}

	/**
	 * Determines if node is root.
	 * @return boolean whether the node is root.
	 */
	public function isRoot()
	{
		return $this->owner->{$this->leftAttribute} == 1;
	}

	/**
	 * Returns if the current node is deleted.
	 * @return boolean whether the node is deleted.
	 */
	public function getIsDeletedRecord()
	{
		return $this->_deleted;
	}

	/**
	 * Sets if the current node is deleted.
	 * @param boolean $value whether the node is deleted.
	 */
	public function setIsDeletedRecord($value)
	{
		$this->_deleted = $value;
	}

	/**
	 * Handle 'afterConstruct' event of the owner.
	 * @param CEvent $event event parameter.
	 */
	public function afterConstruct($event)
	{
		$owner = $this->owner;
		self::$_cached[get_class($owner)][$this->_id = self::$_c++] = $owner;
	}

	/**
	 * Handle 'afterFind' event of the owner.
	 * @param CEvent $event event parameter.
	 */
	public function afterfind($event)
	{
		$owner = $this->owner;
		self::$_cached[get_class($owner)][$this->_id = self::$_c++] = $owner;
	}

	/**
	 * Handle 'beforeSave' event of the owner.
	 * @param CEvent $event event parameter.
	 * @return boolean.
	 */
	public function beforeSave($event)
	{
		if($this->_ignoreEvent){
			return true;
		}else{
			throw new Exception('You should not use CActiveRecord::save() method when NestedSetBehavior attached.');
		}
	}

	/**
	 * Handle 'beforeDelete' event of the owner.
	 * @param CEvent $event event parameter.
	 * @return boolean.
	 */
	public function beforeDelete($event)
	{
		if($this->_ignoreEvent){
			return true;
		}else{
			throw new Exception('You should not use CActiveRecord::delete() method when NestedSetBehavior attached.');
		}
	}

	/**
	 * @param int $key.
	 * @param int $delta.
	 */
	private function shiftLeftRight($key, $delta)
	{
		$owner = $this->owner;
		$db = $owner->getDb();

		foreach(array($this->leftAttribute, $this->rightAttribute) as $attribute){
			$condition = ['and', "[[$attribute]] >= :key"];
			$params = [':key' => $key];
			
			if($this->hasManyRoots){
				$condition[] = "[[$this->rootAttribute]] = :$this->rootAttribute";
				$params[":$this->rootAttribute"] = $owner->{$this->rootAttribute};
			}

			$owner->updateAll(
				[$attribute => new Expression($db->quoteColumnName($attribute) . sprintf('%+d', $delta))],
				$condition,
				$params
			);
		}
	}

	/**
	 * @param CActiveRecord $target.
	 * @param int $key.
	 * @param int $levelUp.
	 * @param boolean $runValidation.
	 * @param array $attributes.
	 * @return boolean.
	 */
	private function addNode($target, $key, $levelUp, $runValidation, $attributes)
	{
		$owner = $this->owner;

		if(!$owner->getIsNewRecord()){
			throw new Exception('The node cannot be inserted because it is not new.');
		}

		if($this->getIsDeletedRecord()){
			throw new Exception('The node cannot be inserted because it is deleted.');
		}

		if($target->getIsDeletedRecord()){
			throw new Exception('The node cannot be inserted because target node is deleted.');
		}

		if($owner->equals($target)){
			throw new Exception('The target node should not be self.');
		}

		if(!$levelUp && $target->isRoot()){
			throw new Exception('The target node should not be root.');
		}

		if($runValidation && !$owner->validate()){
			return false;
		}

		if($this->hasManyRoots){
			$owner->{$this->rootAttribute} = $target->{$this->rootAttribute};
		}

		$db = $owner->getDb();
		$extTransFlag = $db->getTransaction();

		if($extTransFlag === null){
			$transaction = $db->beginTransaction();
		}

		try{
			$this->shiftLeftRight($key, 2);
			$owner->{$this->leftAttribute} = $key;
			$owner->{$this->rightAttribute} = $key + 1;
			$owner->{$this->levelAttribute} = $target->{$this->levelAttribute} + $levelUp;
			$this->_ignoreEvent = true;
			$result = $owner->insert($attributes);
			$this->_ignoreEvent = false;

			if(!$result){
				if($extTransFlag === null){
					$transaction->rollback();
				}
				return false;
			}

			if($extTransFlag === null){
				$transaction->commit();
			}
			$this->correctCachedOnAddNode($key);
		}catch(Exception $e){
			if($extTransFlag === null){
				$transaction->rollback();
			}
			throw $e;
		}
		return true;
	}

	/**
	 * @param array $attributes.
	 * @return boolean.
	 */
	private function makeRoot($attributes)
	{
		$owner = $this->owner;
		$owner->{$this->leftAttribute} = 1;
		$owner->{$this->rightAttribute} = 2;
		$owner->{$this->levelAttribute} = 1;

		if($this->hasManyRoots){
			$db = $owner->getDb();
			$extTransFlag = $db->getTransaction();

			if($extTransFlag === null){
				$transaction = $db->beginTransaction();
			}

			try{
				$this->_ignoreEvent = true;
				$result = $owner->insert($attributes);
				$this->_ignoreEvent = false;

				if(!$result){
					if($extTransFlag === null){
						$transaction->rollback();
					}
					return false;
				}

				$pk = $owner->{$this->rootAttribute} = $owner->getPrimaryKey();
				$owner->updateAll([$this->rootAttribute => $pk], [$owner->primaryKey()[0] => $pk]);

				if($extTransFlag === null){
					$transaction->commit();
				}
			}catch(Exception $e){
				if($extTransFlag === null){
					$transaction->rollback();
				}
				throw $e;
			}
		}else{
			if($owner->roots()->exists()){
				throw new Exception('Cannot create more than one root in single root mode.');
			}

			$this->_ignoreEvent = true;
			$result = $owner->insert($attributes);
			$this->_ignoreEvent = false;

			if(!$result){
				return false;
			}
		}
		return true;
	}

	/**
	 * @param CActiveRecord $target.
	 * @param int $key.
	 * @param int $levelUp.
	 * @return boolean.
	 */
	private function moveNode($target, $key, $levelUp)
	{
		$owner = $this->owner;

		if($owner->getIsNewRecord()){
			throw new Exception('The node should not be new record.');
		}

		if($this->getIsDeletedRecord()){
			throw new Exception('The node should not be deleted.');
		}

		if($target->getIsDeletedRecord()){
			throw new Exception('The target node should not be deleted.');
		}

		if($owner->equals($target)){
			throw new Exception('The target node should not be self.');
		}

		if($target->isDescendantOf($owner)){
			throw new Exception('The target node should not be descendant.');
		}

		if(!$levelUp && $target->isRoot()){
			throw new Exception('The target node should not be root.');
		}

		$db = $owner->getDb();
		$extTransFlag = $db->getTransaction();

		if($extTransFlag === null){
			$transaction = $db->beginTransaction();
		}

		try{
			$left = $owner->{$this->leftAttribute};
			$right = $owner->{$this->rightAttribute};
			$levelDelta = $target->{$this->levelAttribute} - $owner->{$this->levelAttribute} + $levelUp;

			if($this->hasManyRoots && $owner->{$this->rootAttribute} !== $target->{$this->rootAttribute}){
				foreach([$this->leftAttribute, $this->rightAttribute] as $attribute){
					$owner->updateAll(
						[$attribute => new Expression($db->quoteColumnName($attribute) . sprintf('%+d', $right - $left + 1))],
						['and', "[[$attribute]] >= :key", $this->rootAttribute . '= :root'],
						[':key' => $key, ':root' => $target->{$this->rootAttribute}]
					);
				}

				$delta = $key - $left;

				$owner->updateAll(
					[
						$this->leftAttribute => new Expression($db->quoteColumnName($this->leftAttribute).sprintf('%+d',$delta)),
						$this->rightAttribute => new Expression($db->quoteColumnName($this->rightAttribute).sprintf('%+d',$delta)),
						$this->levelAttribute => new Expression($db->quoteColumnName($this->levelAttribute).sprintf('%+d',$levelDelta)),
						$this->rootAttribute => $target->{$this->rootAttribute},
					],
					[
						"and",
						"[[$this->leftAttribute]] >= :left",
						"[[$this->rightAttribute]] <= :right",
						"[[$this->rootAttribute]] = :root"
					],
					[
						':left' => $left,
						':right' => $right,
						':root' => $owner->{$this->rootAttribute}
					]
				);

				$this->shiftLeftRight($right + 1, $left - $right - 1);
				if($extTransFlag === null){
					$transaction->commit();
				}
				$this->correctCachedOnMoveBetweenTrees($key, $levelDelta, $target->{$this->rootAttribute});
			}else{
				$delta = $right - $left + 1;
				$this->shiftLeftRight($key, $delta);

				if($left >= $key){
					$left += $delta;
					$right += $delta;
				}

				$query = new Query();
				$query->andWhere("[[$this->leftAttribute]] >= :left", [':left' => $left])
					->andWhere("[[$this->rightAttribute]] <= :right", [':right' => $right]);
				
				if($this->hasManyRoots){
					$query->andWhere("$this->rootAttribute = :root", [':root' => $owner->{$this->rootAttribute}]);
				}

				$owner->updateAll(
					[$this->levelAttribute => new Expression($db->quoteColumnName($this->levelAttribute) . sprintf('%+d', $levelDelta))],
					$query->where,
					$query->params
				);

				foreach([$this->leftAttribute, $this->rightAttribute] as $attribute){
					$query = new Query();
					$query->andWhere("[[$attribute]] >= :left", [':left' => $left])
						->andWhere("[[$attribute]] <= :right", [':right' => $right]);

					if($this->hasManyRoots){
						$query->andWhere("$this->rootAttribute = :root", [':root' => $owner->{$this->rootAttribute}]);
					}
					$owner->updateAll(
						[$attribute => new Expression($db->quoteColumnName($attribute) . sprintf('%+d', $key - $left))],
						$query->where,
						$query->params
					);
				}
				$this->shiftLeftRight($right + 1, -$delta);

				if($extTransFlag === null){
					$transaction->commit();
				}
				$this->correctCachedOnMoveNode($key, $levelDelta);
			}
		}catch(Exception $e){
			if($extTransFlag === null){
				$transaction->rollBack();
			}
			throw $e;
		}
		return true;
	}

	/**
	 * Correct cache for {@link NestedSetBehavior::delete()} and {@link NestedSetBehavior::deleteNode()}.
	 */
	private function correctCachedOnDelete()
	{
		$owner = $this->owner;
		$left = $owner->{$this->leftAttribute};
		$right = $owner->{$this->rightAttribute};
		$key = $right + 1;
		$delta = $left - $right - 1;

		foreach(self::$_cached[get_class($owner)] as $node){
			if($node->getIsNewRecord() || $node->getIsDeletedRecord()){
				continue;
			}
			if($this->hasManyRoots && $owner->{$this->rootAttribute} !== $node->{$this->rootAttribute}){
				continue;
			}
			if($node->{$this->leftAttribute} >= $left && $node->{$this->rightAttribute} <= $right){
				$node->setIsDeletedRecord(true);
			}else{
				if($node->{$this->leftAttribute} >= $key){
					$node->{$this->leftAttribute} += $delta;
				}
				if($node->{$this->rightAttribute} >= $key){
					$node->{$this->rightAttribute} += $delta;
				}
			}
		}
	}

	/**
	 * Correct cache for {@link NestedSetBehavior::addNode()}.
	 * @param int $key.
	 */
	private function correctCachedOnAddNode($key)
	{
		$owner = $this->owner;
		foreach(self::$_cached[get_class($owner)] as $node){
			if($node->getIsNewRecord() || $node->getIsDeletedRecord()){
				continue;
			}
			if($this->hasManyRoots && $owner->{$this->rootAttribute} !== $node->{$this->rootAttribute}){
				continue;
			}
			if($owner === $node){
				continue;
			}
			if($node->{$this->leftAttribute} >= $key){
				$node->{$this->leftAttribute} += 2;
			}
			if($node->{$this->rightAttribute} >= $key){
				$node->{$this->rightAttribute} += 2;
			}
		}
	}

	/**
	 * Correct cache for {@link NestedSetBehavior::moveNode()}.
	 * @param int $key.
	 * @param int $levelDelta.
	 */
	private function correctCachedOnMoveNode($key, $levelDelta)
	{
		$owner = $this->owner;
		$left = $owner->{$this->leftAttribute};
		$right = $owner->{$this->rightAttribute};
		$delta = $right - $left + 1;

		if($left >= $key){
			$left += $delta;
			$right += $delta;
		}

		$delta2 = $key - $left;

		foreach(self::$_cached[get_class($owner)] as $node){
			if($node->getIsNewRecord() || $node->getIsDeletedRecord()){
				continue;
			}
			if($this->hasManyRoots && $owner->{$this->rootAttribute} !== $node->{$this->rootAttribute}){
				continue;
			}
			if($node->{$this->leftAttribute} >= $key){
				$node->{$this->leftAttribute} += $delta;
			}
			if($node->{$this->rightAttribute} >= $key){
				$node->{$this->rightAttribute} += $delta;
			}
			if($node->{$this->leftAttribute} >= $left && $node->{$this->rightAttribute} <= $right){
				$node->{$this->levelAttribute} += $levelDelta;
			}
			if($node->{$this->leftAttribute} >= $left && $node->{$this->leftAttribute} <= $right){
				$node->{$this->leftAttribute} += $delta2;
			}
			if($node->{$this->rightAttribute} >= $left && $node->{$this->rightAttribute} <= $right){
				$node->{$this->rightAttribute} += $delta2;
			}
			if($node->{$this->leftAttribute} >= $right + 1){
				$node->{$this->leftAttribute} -= $delta;
			}
			if($node->{$this->rightAttribute} >= $right + 1){
				$node->{$this->rightAttribute} -= $delta;
			}
		}
	}

	/**
	 * Correct cache for {@link NestedSetBehavior::moveNode()}.
	 * @param int $key.
	 * @param int $levelDelta.
	 * @param int $root.
	 */
	private function correctCachedOnMoveBetweenTrees($key, $levelDelta, $root)
	{
		$owner = $this->owner;
		$left = $owner->{$this->leftAttribute};
		$right = $owner->{$this->rightAttribute};
		$delta = $right - $left + 1;
		$delta2 = $key - $left;
		$delta3 = $left - $right - 1;

		foreach(self::$_cached[get_class($owner)] as $node)
		{
			if($node->getIsNewRecord() || $node->getIsDeletedRecord()){
				continue;
			}
			if($node->{$this->rootAttribute} === $root){
				if($node->{$this->leftAttribute} >= $key){
					$node->{$this->leftAttribute} += $delta;
				}
				if($node->{$this->rightAttribute} >= $key){
					$node->{$this->rightAttribute} += $delta;
				}
			}else if($node->{$this->rootAttribute} === $owner->{$this->rootAttribute}){
				if($node->{$this->leftAttribute} >= $left && $node->{$this->rightAttribute} <= $right){
					$node->{$this->leftAttribute} += $delta2;
					$node->{$this->rightAttribute} += $delta2;
					$node->{$this->levelAttribute} += $levelDelta;
					$node->{$this->rootAttribute} = $root;
				}else{
					if($node->{$this->leftAttribute} >= $right + 1){
						$node->{$this->leftAttribute} += $delta3;
					}
					if($node->{$this->rightAttribute} >= $right + 1){
						$node->{$this->rightAttribute} += $delta3;
					}
				}
			}
		}
	}

	/**
	 * Destructor.
	 */
	public function __destruct()
	{
		unset(self::$_cached[get_class($this->owner)][$this->_id]);
	}
}