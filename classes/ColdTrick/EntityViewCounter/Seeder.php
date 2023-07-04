<?php

namespace ColdTrick\EntityViewCounter;

use Elgg\Database\Clauses\OrderByClause;
use Elgg\Database\QueryBuilder;
use Elgg\Database\Seeds\Seed;
use Elgg\Database\Update;

/**
 * Seed views with the seeded entities
 */
class Seeder extends Seed {
	
	/**
	 * @var array supported types for seeding
	 */
	protected $supported_types;
	
	/**
	 * {@inheritDoc}
	 */
	public function seed() {
		$this->advance($this->getCount());
		
		$exclude = [];
		while ($this->getCount() < $this->limit) {
			$entity = $this->getRandomEntity($exclude);
			if (!$entity instanceof \ElggEntity) {
				// no more to fetch
				break;
			}
			
			$user_guids = [];
			for ($i = 0; $i < $this->faker()->numberBetween(5, 25); $i++) {
				$user = $this->getRandomUser($user_guids);
				$user_guids[] = $user->guid;
				
				$annotation_id = $entity->annotate(ENTITY_VIEW_COUNTER_ANNOTATION_NAME, '__faker', ACCESS_PUBLIC, $user->guid);
				if (empty($annotation_id)) {
					continue;
				}
				
				$annotation = elgg_get_annotation_from_id($annotation_id);
				$this->backdateView($annotation, $entity);
			}
			
			// cache the count for faster response
			$entity->entity_view_count = $entity->countAnnotations(ENTITY_VIEW_COUNTER_ANNOTATION_NAME);
			
			$this->advance();
		}
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function unseed() {
		$options = [
			'annotation_name_value_pairs' => [
				[
					'name' => ENTITY_VIEW_COUNTER_ANNOTATION_NAME,
					'value' => '__faker',
					'case_sensitive' => false,
					'type' => ELGG_VALUE_STRING,
				],
			],
			'limit' => false,
			'batch' => true,
			'batch_inc_offset' => false,
			'count' => true,
		];
		
		// set the correct count on the progressbar as it's wrong by default
		$count = elgg_get_annotations($options);
		unset($options['count']);
		
		$this->progress->setMaxSteps($count);
		
		/* @var $annotations \ElggBatch */
		$annotations = elgg_get_annotations($options);
		
		/* @var $annotation \ElggAnnotation */
		foreach ($annotations as $annotation) {
			if ($annotation->delete()) {
				$this->log("Deleted entity view {$annotation->id}");
			} else {
				$this->log("Failed to delete entity view {$annotation->id}");
				$annotations->reportFailure();
				continue;
			}
			
			$this->advance();
		}
	}
	
	/**
	 * {@inheritDoc}
	 */
	public static function getType(): string {
		return 'entity_view_counter';
	}
	
	/**
	 * {@inheritDoc}
	 */
	protected function getCountOptions(): array {
		return [
			'wheres' => [
				function (QueryBuilder $qb, $main_alias) {
					// can't use annotation_name_value_pairs because of join bug
					// @see https://github.com/Elgg/Elgg/issues/14405
					$ann = $qb->joinAnnotationTable($main_alias);
					
					return $qb->merge([
						$qb->compare("{$ann}.name", '=', ENTITY_VIEW_COUNTER_ANNOTATION_NAME, ELGG_VALUE_STRING),
						$qb->compare("{$ann}.value", '=', '__faker', ELGG_VALUE_STRING),
					]);
				},
			],
		];
	}
	
	/**
	 * Get the supported types to seed view for
	 *
	 * @return array
	 */
	protected function getSupportedTypes(): array {
		if (isset($this->supported_types)) {
			return $this->supported_types;
		}
		
		$setting = elgg_get_plugin_setting('entity_types', 'entity_view_counter');
		if (!empty($setting)) {
			$filtered = [];
			$setting = json_decode($setting, true);
			foreach ($setting as $type => $subtypes) {
				if (empty($subtypes) || !is_array($subtypes)) {
					continue;
				}
				
				$filtered[$type] = [];
				foreach ($subtypes as $subtype => $enabled) {
					if (empty($enabled)) {
						continue;
					}
					
					$filtered[$type][] = $subtype;
				}
			}
			
			$this->supported_types = $filtered;
		} else {
			$this->supported_types = elgg_entity_types_with_capability('searchable');
		}
		
		return $this->supported_types;
	}
	
	/**
	 * Get a random entity to seed views on
	 *
	 * @param array $excluded_guids excluded GUIDs (previously seeded)
	 *
	 * @return null|\ElggEntity
	 */
	protected function getRandomEntity(array $excluded_guids = []): ?\ElggEntity {
		$excluded_guids[] = 0;
		
		$entities = elgg_get_entities([
			'type_subtype_pairs' => $this->getSupportedTypes(),
			'metadata_names' => ['__faker'],
			'wheres' => [
				function (QueryBuilder $qb, $main_alias) use ($excluded_guids) {
					return $qb->compare("{$main_alias}.guid", 'NOT IN', $excluded_guids, ELGG_VALUE_GUID);
				},
				function (QueryBuilder $qb, $main_alias) {
					$ann = $qb->subquery('annotations');
					$ann->select('entity_guid')
						->where($qb->compare('name', '=', ENTITY_VIEW_COUNTER_ANNOTATION_NAME, ELGG_VALUE_STRING))
						->andWhere($qb->compare('value', '=', '__faker', ELGG_VALUE_STRING));
					
					return $qb->compare("{$main_alias}.guid", 'NOT IN', $ann->getSQL());
				}
			],
			'limit' => 1,
			'order_by' => new OrderByClause('RAND()', null),
		]);
		
		return empty($entities) ? null : $entities[0];
	}
	
	/**
	 * Backdate the view
	 *
	 * @param \ElggAnnotation $annotation view annotation
	 * @param \ElggEntity     $entity     viewed entity
	 *
	 * @return void
	 */
	protected function backdateView(\ElggAnnotation $annotation, \ElggEntity $entity): void {
		$since = $this->create_since;
		$this->setCreateSince($entity->time_created);
		
		$update = Update::table('annotations');
		$update->set('time_created', $update->param($this->getRandomCreationTimestamp(), ELGG_VALUE_TIMESTAMP))
			->where($update->compare('id', '=', $annotation->id, ELGG_VALUE_ID));
		
		elgg()->db->updateData($update);
		
		$this->setCreateSince($since);
	}
}
