<?php
declare(strict_types = 1);

namespace app\modules\graphql\schema\types;

use app\modules\graphql\schema\mutations\ExampleMutationType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\UnionType;

/**
 * Class Types
 * @package app\modules\graphql\schema\types
 */
class Types {
	public const SCHEMA_ERROR_NAME = 'ValidationErrorType';

	// Запрос, мутации, ответы
	private static ?QueryType $query = null;
	private static ?MutationType $mutation = null;

	// Валидация
	private static ?ValidationErrorType $validationError = null;
	private static ?Response $response = null;
	private static array $validationTypes = [];

	// Типы для наших сущностей
	private static ?ExampleType $example = null;
	private static ?ExampleMutationType $exampleMutationType = null;

	/**
	 * Запрос
	 * @return QueryType
	 */
	public static function query():QueryType {
		return static::$query?:static::$query = new QueryType();
	}

	/**
	 * Мутации
	 * @return MutationType
	 */
	public static function mutation():MutationType {
		return static::$mutation?:static::$mutation = new MutationType();
	}

	/**
	 * Объект валидации
	 * @return ValidationErrorType
	 */
	public static function validationError():ValidationErrorType {
		return static::$validationError?:static::$validationError = new ValidationErrorType();
	}

	/**
	 * Список объектов валидации
	 * @return Response
	 */
	public static function response():Response {
		return self::$response?:static::$response = new Response();
	}

	/**
	 * Наш Union Type, для всех моделей.
	 * Метод возвращает новый сгенерированный тип, на основе типа, который пришел в аргументе.
	 * В resolveType, в случае успеха, нам придет наш сохраненный/измененный объект.
	 * В случае ошибок валидации придет ассоциативный массив из $model->getError()
	 * @param ObjectType $type
	 * @return UnionType
	 */
	public static function validationErrorsUnionType(ObjectType $type):UnionType {
		$typeNameValidationErrorsType = $type->name.self::SCHEMA_ERROR_NAME;
		return static::$validationTypes[$typeNameValidationErrorsType]??
			static::$validationTypes[$typeNameValidationErrorsType] = new UnionType([
				'name' => $typeNameValidationErrorsType,
				'types' => [$type, static::response()],
				'resolveType' => fn($value) => static::response()
			]);
	}

	/**
	 * Запросы партнера
	 * @return ExampleType
	 */
	public static function example():ExampleType {
		return static::$example?:static::$example = new ExampleType();
	}

	/**
	 * Мутации партнера
	 * @return ExampleMutationType
	 */
	public static function exampleMutation():ExampleMutationType {
		return static::$exampleMutationType?:static::$exampleMutationType = new ExampleMutationType();
	}
}
