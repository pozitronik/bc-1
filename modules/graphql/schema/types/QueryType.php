<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use app\models\sys\users\Users;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

/**
 * Class QueryType
 * @package app\schema
 */
class QueryType extends ObjectType {
	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct([
			'fields' => [
				/**
				 * query {
				 *        examples {
				 *            id,
				 *            username
				 *        }
				 *    }
				 */
				'examples' => [
					'type' => Type::listOf(Types::example()),
					'args' => [
						'search' => Type::string(),
					],
					'resolve' => function($root, $args) {
						return [
							new Users(['id' => $r1 = mt_rand(1, 10), 'username' => "hello$r1"]),
							new Users(['id' => $r2 = mt_rand(11, 20), 'username' => "hello$r2"]),
						];
					}
				],
				/**
				 * query {
				 *        example(id: 10) {
				 *            id,
				 *            username
				 *        },
				 *    }
				 */
				'example' => [
					'type' => Types::example(),
					'args' => [
						'id' => Type::nonNull(Type::int()),
					],
					'resolve' => function($root, $args) {
						return new Users(['id' => $args['id'], 'username' => 'hello5']);
					},
				],
			],
		]);
	}
}