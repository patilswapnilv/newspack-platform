<?php
/**
 * Builder in charge of handling the DB operations using a model as the input.
 *
 * @since 6.0.0
 */

namespace TEC\Events\Custom_Tables\V1\Models;

use Generator;
use InvalidArgumentException;
use TEC\Common\Configuration\Configuration;
use Tribe__Cache as Cache;
use Tribe__Cache_Listener as Cache_Triggers;

/**
 * Class Builder
 *
 * @since   6.0.0
 *
 * @package TEC\Events\Custom_Tables\V1\Models
 */
class Builder {

	/**
	 * @var int Flag to validate the Builder::upsert was an insert. Note - This is dependent on whether the MySQL
	 *      CLIENT_FOUND_ROWS flag is set or not.
	 */
	const UPSERT_DID_INSERT = 1;

	/**
	 * @var int Flag to validate the Builder::upsert was an update. Note - This is dependent on whether the MySQL
	 *      CLIENT_FOUND_ROWS flag is set or not.
	 */
	const UPSERT_DID_UPDATE = 2;

	/**
	 * @var int Flag to validate the Builder::upsert made no changes. Note - This is dependent on whether the MySQL
	 *      CLIENT_FOUND_ROWS flag is set or not.
	 */
	const UPSERT_DID_NOT_CHANGE = 0;

	/**
	 * A class-wide query execution toggle that will prevent the execution
	 * of SQL queries across all instances of the the Builder.
	 *
	 * @since 6.0.0
	 *
	 * @var bool
	 */
	private static $class_execute_queries = true;

	/**
	 * Whether the results of fetch methods should be cached for the duration of the request or not.
	 * When active results will be memoized using the SQL query as key in the non-persistent cache (i.e. memoized).
	 *
	 * @since 6.11.1
	 *
	 * @var bool
	 */
	private static bool $use_query_cache = true;

	/**
	 * The size of the batch the Builder should use to fetch
	 * Models in unbound query methods like `find_all`.
	 *
	 * Set statically to affect any instance of the Builder.
	 *
	 * @since 6.0.0
	 *
	 * @var int
	 */
	protected $batch_size = 1000;

	/**
	 * The type of output that should be used to format the result set elements.
	 *
	 * @since 6.0.0
	 *
	 * @var string
	 */
	private $output_format;

	/**
	 * An instance to the Model that is using this builder class.
	 *
	 * @since 6.0.0
	 *
	 * @var Model model The model that is using this class.
	 */
	private $model;

	/**
	 * Base operation running in SQL; by default is a `SELECT *` statement
	 *
	 * @since 6.0.0
	 *
	 * @var string operation
	 */
	private $operation = 'SELECT *';

	/**
	 * Group of all the different where clauses combined with an AND boolean.
	 *
	 * @since 6.0.0
	 *
	 * @var string[] The WHERE clauses.
	 */
	private $wheres = [];

	/**
	 * The list of args in an associative array of the query params for each where clause.
	 *
	 * @since 6.1.3
	 *
	 * @var array<<string,mixed>>
	 */
	private $where_args = [];

	/**
	 * Variable holding the value used to limit the results from the Query.
	 *
	 * @since 6.0.0
	 *
	 * @var  int|null limit
	 */
	private $limit;

	/**
	 * Variable holding the value used to offset the results from the Query.
	 *
	 * @since 6.0.0
	 *
	 * @var  int|null $offset
	 */
	private $offset;

	/**
	 * Variable holding the values of order used to construct the SQL query.
	 *
	 * @since 6.0.0
	 *
	 * @var array<array<string,mixed>> order
	 */
	private $order = [];

	/**
	 * Flag to indicate the moment the builder class has an invalid query.
	 *
	 * @since 6.0.0
	 *
	 * @var bool invalid
	 */
	private $invalid = false;

	/**
	 * List of all the valid operators to use when running comparisons with SQL.
	 *
	 * @since 6.0.0
	 *
	 * @var string[] operators
	 */
	public $operators = [
		'=',
		'<',
		'>',
		'<=',
		'>=',
		'!=',
		'<>',
	];

	/**
	 * List of all the queries executed by this instance of the builder class.
	 *
	 * @since 6.0.0
	 *
	 * @var array<string> An array of multiple queries.
	 */
	private $queries = [];

	/**
	 * If the queries should be executed or not against the Database.
	 *
	 * @since 6.0.0
	 *
	 * @var bool execute_queries
	 */
	private $execute_queries = true;

	/**
	 * An array with all the available inner joins.
	 *
	 * @since 6.0.0
	 *
	 * @var array<array<string>> joins
	 */
	private $joins = [];

	/**
	 * Builder constructor.
	 *
	 * @since 6.0.0
	 *
	 * @param Model $model The model using this builder.
	 */
	public function __construct( Model $model ) {
		$this->model      = $model;
		$this->batch_size = function_exists( 'tec_query_batch_size' ) ? tec_query_batch_size( __METHOD__ ) : 50;
	}

	/**
	 * Sets the class-wide queries execution toggle that will enable or
	 * disable the execution of queries overriding the per-instance value of the
	 * `$execute_queries` flag.
	 *
	 * @since 6.0.0
	 *
	 * @param bool $class_execute_queries Whether to enable or disable the execution
	 *                                    of queries class-wide.
	 *
	 * @see Builder::enable_query_execution() to set the flag on a per-instance basis
	 */
	public static function class_enable_query_execution( $class_execute_queries ) {
		self::$class_execute_queries = $class_execute_queries;
	}

	/**
	 * Get an instance to this builder class.
	 *
	 * @since 6.0.0
	 *
	 * @return $this An instance to this builder class.
	 */
	public function builder_instance() {
		return $this;
	}

	/**
	 * If this builder class has invalid queries or not.
	 *
	 * @since 6.0.0
	 *
	 * @return bool True if this builder has an invalid query, false otherwise.
	 */
	public function has_invalid_queries() {
		return $this->invalid;
	}

	/**
	 * Returns an array of strings with all the SQL queries generated by this builder class.
	 *
	 * @since 6.0.0
	 *
	 * @return array<string> An array of the current Builder queries.
	 */
	public function queries() {
		return $this->queries;
	}

	/**
	 * Method to enable query execution or not.
	 *
	 * @since 6.0.0
	 *
	 * @param bool $execute_queries If the query should be executed or not against the Database.
	 */
	public function enable_query_execution( $execute_queries = true ) {
		$this->execute_queries = $execute_queries;

		return $this;
	}

	/**
	 * Sets the size of the batch the Builder should use to fetch models in unbound query methods like `find_all`.
	 *
	 * @since 6.0.0
	 *
	 * @param int $size    The size of the batch the Builder should use to fetch
	 *                     Models in unbound query methods like `find_all`.
	 *
	 * @return Builder The instance to the current class.
	 */
	public function set_batch_size( $size = 100 ) {
		$this->batch_size = $size;

		return $this;
	}

	/**
	 * Insert a new row or update one if already exists.
	 *
	 * @since 6.1.3 Integration with memoization.
	 * @since 6.0.0
	 *
	 * @param array<string>            $unique_by A list of columns that are marked as UNIQUE on the database.
	 * @param array<string,mixed>|null $data      The data to be inserted or updated into the table.
	 *
	 * @return false|int The rows affected flag or false on failure.
	 */
	public function upsert( array $unique_by, array $data = null ) {
		if ( empty( $unique_by ) ) {
			throw new InvalidArgumentException( 'A series of unique column needs to be specified.' );
		}

		// If no input was provided use the model as input.
		if ( $data === null ) {
			$model = $this->model;
			$model->validate();
		} else {
			if ( empty( $data ) ) {
				return false;
			}

			$columns = array_keys( $data );
			// Make sure the required key is part of the data to be inserted in.
			foreach ( $unique_by as $column ) {
				if ( ! in_array( $column, $columns, true ) ) {
					throw new InvalidArgumentException( "The column '{$column}' must be part of the data array" );
				}
			}

			$model = $this->set_data_to_model( $data );
			$model->validate( array_keys( $data ) );
		}

		if ( $model->is_invalid() ) {
			do_action(
				'tribe_log',
				'error',
				implode( ' : ', $model->errors() ),
				[
					'method' => __METHOD__,
					'line'   => __LINE__,
					'model'  => get_class( $model ),
				]
			);

			return false;
		}

		list( $formatted_data, $format ) = $model->format();

		// No data to be inserted.
		if ( empty( $formatted_data ) ) {
			return false;
		}

		$placeholder_values = $this->create_placeholders( $formatted_data, $format );

		if ( empty( $placeholder_values ) ) {
			return false;
		}

		global $wpdb;

		$update_sql   = [];
		$update_value = [];
		foreach ( $formatted_data as $column => $value ) {
			if ( in_array( $column, $unique_by, true ) ) {
				continue;
			}
			$value_placeholder = $format[ $column ] ?? '%s';
			$update_sql[]      = "{$column}={$value_placeholder}";
			$update_value[]    = $value;
		}
		$update_assignment_list = $wpdb->prepare( implode( ', ', $update_sql ), ...$update_value );

		$columns = implode( ',', array_keys( $formatted_data ) );

		$sql = "INSERT INTO {$wpdb->prefix}{$this->model->table_name()} ($columns) VALUES($placeholder_values) ON DUPLICATE KEY update {$update_assignment_list}";
		$sql = $wpdb->prepare( $sql, ...$this->create_replacements_values( $formatted_data ) );

		$this->queries[] = $sql;

		if ( $this->execute_queries && self::$class_execute_queries ) {
			/*
			 * Depending on the db implementation, it could not run updates and return `0`.
			 * We need to make sure it does not return exactly boolean `false`.
			 */
			$result = $wpdb->query( $sql );
			if ( $result === false ) {
				do_action(
					'tribe_log',
					'debug',
					'Builder: query failure.',
					[
						'source' => __CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__,
						'trace'  => debug_backtrace( 2, 5 ), // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
						'error'  => $wpdb->last_error,
					]
				);
			}

			// If we have a cache, let's clear it.
			// It may be either a static call or on an instance, handle both.
			if ( $data !== null ) {
				// Attempt to generate a cache key by the upsert key.
				foreach ( $unique_by as $field ) {
					$value = $data[ $field ] ?? null;
					$key   = self::generate_cache_key( $model, $field, $value );

					// Invalidate the caches.
					$cache = tribe_cache();
					$cache->delete( $key, Cache_Triggers::TRIGGER_SAVE_POST );
					$cache->set_last_occurrence( Cache_Triggers::TRIGGER_SAVE_POST );
				}
			} else {
				$model->flush_cache();
			}

			return $result;
		}

		return 0;
	}

	/**
	 * Add operation to insert new records inside of the table that is used from the current model. A single entry can
	 * be set here as an array of key => value pairs, where the key is the column being saved and the value is the
	 * value intended to be saved.
	 *
	 * A bulk insert is also supported, only rows of the same size can be inserted, an array of arrays representing
	 * each
	 * the column to be inserted, all rows should be the same length of columns and values as the rest of the rows
	 * inside of the data, otherwise the operation is won't complete.
	 *
	 * @since 6.0.0
	 *
	 * @param array<string, mixed>|array<array<string,mixed> $data The data that is being inserted.
	 *
	 * @return int The number of affected rows.
	 */
	public function insert( array $data ) {
		// No data or operation was inserted.
		if ( empty( $data ) ) {
			return 0;
		}

		// If the first element is not an array make sure to wrap it around an array.
		if ( ! is_array( reset( $data ) ) ) {
			$data = [ $data ];
		}

		// @todo make this filterable?
		$insert_batch_size = $this->batch_size;
		$result            = 0;
		global $wpdb;

		$wpdb->suppress_errors( true );

		do {
			$this_batch_data = array_splice( $data, 0, $insert_batch_size );
			$validated       = $this->validate_rows( $this_batch_data );
			if ( empty( $validated['columns'] ) || empty( $validated['placeholders'] ) || empty( $validated['values'] ) ) {
				return 0;
			}

			$columns      = $validated['columns'];
			$placeholders = $validated['placeholders'];

			$sql             = "INSERT INTO {$wpdb->prefix}{$this->model->table_name()} ($columns) VALUES $placeholders";
			$sql             = $wpdb->prepare( $sql, ...$validated['values'] );
			$this->queries[] = $sql;
			if ( $this->execute_queries ) {
				$query_result = $this->query( $sql );
				$result      += (int) $query_result;
			}
		} while ( count( $data ) );

		$wpdb->suppress_errors( false );

		return $result;
	}

	/**
	 * Perform updates against a model that already exists on the database.
	 *
	 * @since 6.1.3 Integration with memoization.
	 * @since 6.0.0
	 *
	 * @param array|null $data    If the data is null the data of the model would be used to set an update, otherwise
	 *                            an array of `column => value` are used to construct the series of updates to perform
	 *                            against this model.
	 *
	 * @return bool|int False if the operation was unsuccessfully
	 */
	public function update( array $data = null ) {
		// Invalid on a where clause or previous value.
		if ( $this->invalid ) {
			return false;
		}

		if ( $data === null ) {
			$model = $this->model;
			$model->validate();
		} else {
			if ( empty( $data ) ) {
				return false;
			}

			$model = $this->set_data_to_model( $data );
			$model->validate( array_keys( $data ) );
		}

		if ( $model->is_invalid() ) {
			$this->invalid = true;

			return false;
		}

		list( $formatted_data, $format ) = $model->format();

		if ( empty( $formatted_data ) ) {
			return false;
		}

		$columns             = [];
		$replacements_values = [];
		foreach ( $formatted_data as $column => $value ) {
			if ( array_key_exists( $column, $format ) ) {
				$columns[]             = "`{$column}` = {$format[$column]}";
				$replacements_values[] = $value;
				continue;
			}

			if ( $value === null ) {
				$columns[] = "`{$column}` = NULL";
			}
		}

		global $wpdb;

		$this->operation = "UPDATE {$wpdb->prefix}{$this->model->table_name()}";

		$pieces = [
			$this->operation,
			'SET ' . $wpdb->prepare( implode( ', ', $columns ), $replacements_values ),
		];

		$where = $this->get_where_clause();

		if ( $where !== '' ) {
			$pieces[] = $where;
		}

		$sql = implode( "\n", $pieces );

		$this->queries[] = $sql;

		// Trigger the save post cache invalidation.
		tribe_cache()->set_last_occurrence( Cache_Triggers::TRIGGER_SAVE_POST );

		// If we have a cache, let's clear it.
		$model->flush_cache();

		return $this->execute_queries ? $this->query( $sql ) : false;
	}

	/**
	 * Run a delete operation against an existing model if the model has not been persisted on the DB the operation
	 * will fail.
	 *
	 * @since 6.1.3 Integration with memoization.
	 * @since 6.0.0
	 *
	 * @return int The number of affected rows.
	 */
	public function delete() {
		$this->operation = 'DELETE';

		global $wpdb;
		$sql = $this->get_sql();

		// If the query is invalid, don't delete anything.
		if ( $this->invalid ) {
			return 0;
		}

		$this->queries[] = $sql;
		$result          = $this->execute_queries ? $this->query( $sql ) : false;

		// If an error happen or no row was updated by the query above.
		if ( $result === false || (int) $result === 0 ) {
			return 0;
		}

		// Invalidate the query cache.
		$cache = tribe_cache();
		$cache->set_last_occurrence( Cache_Triggers::TRIGGER_SAVE_POST );

		foreach ( $this->where_args as $args ) {
			$field = $args['field'] ?? null;
			$value = $args['value'] ?? null;
			// Not a valid item.
			if ( ! $field || ! $value ) {
				continue;
			}
			$key = self::generate_cache_key( $this->model, $field, $value );

			// Invalidate the caches.
			$cache->delete( $key, Cache_Triggers::TRIGGER_SAVE_POST );
		}
		$this->model->reset();

		return absint( $result );
	}

	/**
	 * Find an instance of the model in the database using a specific value and column if no column is specified
	 * the primary key is used.
	 *
	 * @since 6.1.3 Added memoization behind a feature flag (default on).
	 * @since 6.0.0
	 *
	 * @param mixed|array<mixed> $value  The value, or values, of the column we are looking for.
	 * @param string|null        $column The name of the column used to compare against, primary key if not defined.
	 *
	 * @return Model|null Returns a single record where if the model is found, `null` otherwise.
	 */
	public function find( $value, $column = null ) {
		$column ??= $this->model->primary_key_name();
		$conf     = tribe( Configuration::class );

		// Memoize disabled?
		if ( $conf->get( 'TEC_NO_MEMOIZE_CT1_MODELS' ) ) {
			return $this->where( $column, $value )->first();
		}

		// Check if we memoized this instance.
		$key  = self::generate_cache_key( $this->model, $column, $value );
		$data = tribe_cache()->get( $key, Cache_Triggers::TRIGGER_SAVE_POST, null, Cache::NON_PERSISTENT );

		if ( $data ) {
			$model_class       = get_class( $this->model );
			$result            = new $model_class( $data );
			$result->cache_key = $key;

			return $result;
		}

		// Not memoized, fetch it.
		$result = $this->where( $column, $value )->first();
		if ( $result ) {
			// Store on model so we can use it to cache bust later.
			$result->cache_key = $key;

			tribe_cache()->set( $key, $result->to_array(), Cache::NON_PERSISTENT, Cache_Triggers::TRIGGER_SAVE_POST );
		}

		return $result;
	}

	/**
	 * Generates a cache key for this particular model instance.
	 *
	 * @since 6.1.3
	 *
	 * @param Model  $model The instance we are generating a cache key for.
	 * @param string $field The field we are searching / caching by.
	 * @param mixed  $value The value we are searching / caching with.
	 *
	 * @return string
	 */
	public static function generate_cache_key( Model $model, $field, $value ): string {
		$value = ! is_string( $value ) ? serialize( $value ) : $value;

		return $field . $value . get_class( $model );
	}

	/**
	 * Get an array of models that match with the criteria provided.
	 *
	 * @since 6.0.0
	 *
	 * @param string       $column    The column name to look for.
	 * @param array<mixed> $in_values An array of values to test against the database.
	 *
	 * @return Builder
	 */
	public function where_in( $column, array $in_values = [] ) {
		$result = $this->prepare_list_of_values( $column, $in_values );

		if ( $this->invalid || empty( $result ) ) {
			return $this;
		}

		if ( empty( $result['placeholders'] ) || empty( $result['values'] ) ) {
			return $this;
		}

		global $wpdb;

		$placeholders       = implode( ',', $result['placeholders'] );
		$where_args         = [
			'field'          => $column,
			'operator'       => 'IN',
			'prepare_format' => $result['placeholders'],
			'value'          => $result['values'],
		];
		$this->where_args[] = $where_args;
		$this->wheres[]     = $wpdb->prepare( "(`{$column}` IN ({$placeholders}))", $result['values'] );

		return $this;
	}

	/**
	 * Append a new "NOT IN()" clause to the builder of where clauses.
	 *
	 * @since 6.0.0
	 *
	 * @param string       $column        The name of the column to compare against.
	 * @param array<mixed> $not_in_values The list of values used in the comparison.
	 *
	 * @return $this
	 */
	public function where_not_in( $column, array $not_in_values = [] ) {
		$result = $this->prepare_list_of_values( $column, $not_in_values );

		if ( $this->invalid || empty( $result ) ) {
			return $this;
		}

		if ( empty( $result['placeholders'] ) || empty( $result['values'] ) ) {
			return $this;
		}

		global $wpdb;

		$placeholders       = implode( ',', $result['placeholders'] );
		$where_args         = [
			'field'          => $column,
			'operator'       => 'NOT IN',
			'prepare_format' => $result['placeholders'],
			'value'          => $result['values'],
		];
		$this->where_args[] = $where_args;
		$this->wheres[]     = $wpdb->prepare( "(`{$column}` NOT IN ({$placeholders}))", $result['values'] );

		return $this;
	}

	/**
	 * Prepare, sanitize and validate a list of values against the model validators.
	 *
	 * @since 6.0.0
	 *
	 * @param string       $column      The name of the column we are validating.
	 * @param array<mixed> $list_values An array with the values that are compared against this model column.
	 *
	 * @return array<array<string>, array<mixed>> An associative array with the placeholders and values generated from
	 *                              the validations.
	 */
	private function prepare_list_of_values( $column, $list_values ) {
		$placeholders  = [];
		$values        = [];
		$this->invalid = false;

		foreach ( $list_values as $value ) {
			$model = $this->set_data_to_model( [ $column => $value ] );
			if ( ! $model->enable_single_validation( $column )->validate( [ $column ] ) ) {
				$this->invalid = true;
				continue;
			}
			list( $data, $format ) = $model->format();
			if ( empty( $data ) || empty( $data[ $column ] ) ) {
				$this->invalid = true;
				continue;
			}
			if ( array_key_exists( $column, $format ) ) {
				$placeholders[] = $format[ $column ];
				$values[]       = $data[ $column ];
				continue;
			}

			if ( $data[ $column ] === null ) {
				$placeholders[] = $column;
				$values[]       = 'NULL';
			}
		}

		return compact( 'placeholders', 'values' );
	}

	/**
	 * Checks the value and columns requested for a GET operation on the
	 * Model to make sure they are coherent and valid.
	 *
	 * @since 6.0.0
	 *
	 * @param mixed|array<mixed> $value  The value, or values, of the column we are looking for.
	 * @param string|null        $column The name of the column used to compare against, primary key if not defined.
	 *
	 * @return array<mixed>|false Either an array containing the column, data and format, in this order; or `false` to
	 *                            indicate the value and column are not coherent and valid. The data and format values
	 *                            will be array if the input `$value` is an array.
	 */
	private function check_find_value_column( $value, $column = null ) {
		$column      ??= $this->model->primary_key_name();
		$data_buffer   = [];
		$format_buffer = [];

		foreach ( (array) $value as $val ) {
			$model = $this->set_data_to_model( [ $column => $val ] );

			if ( ! $model->validate( [ $column ] ) ) {
				return false;
			}

			list( $data, $format ) = $model->format();

			if ( empty( $data ) || empty( $data[ $column ] ) || empty( $format ) || empty( $format[ $column ] ) ) {
				return false;
			}

			$data_buffer[]   = $data;
			$format_buffer[] = $format;
		}

		$data   = is_array( $value ) ? $data_buffer : reset( $data_buffer );
		$format = is_array( $value ) ? $format_buffer : reset( $format_buffer );

		return [ $column, $data, $format ];
	}

	/**
	 * Finds all the Model instances matching a set of values for a column.
	 *
	 * The method will query the database for matching Models in batches of fixed size
	 * that will be hidden from the client code.
	 *
	 * @since 6.0.0
	 *
	 * @param mixed|array<mixed> $value     The value, or values, to find the matches for.
	 * @param string|null        $column    The column to search the Models by, or `null` to use the Model
	 *                                      primary column.
	 *
	 * @return Generator<Model>|null A generator that will return all matching Model instances
	 *                               hiding the batched query logic.
	 */
	public function find_all( $value, $column = null ) {
		if ( false === $column_data_format = $this->check_find_value_column( $value, $column ) ) {
			// Nothing to return.
			return;
		}

		list( $column, $data, $format ) = $column_data_format;

		$operator = is_array( $value ) ? 'IN' : '=';
		$compare  = is_array( $value ) ? implode( ',', array_column( $format, $column ) ) : $format[ $column ];
		$data     = is_array( $value ) ? array_column( $data, $column ) : $data;

		// Build our order by string.
		$order_by = $this->get_order_by_clause();

		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->prefix}{$this->model->table_name()} WHERE `{$column}` {$operator} ({$compare}) {$order_by} LIMIT %d";

		$batch_size    = min( absint( $this->batch_size ), 5000 );
		$semi_prepared = $wpdb->prepare( $sql, array_merge( (array) $data, [ $batch_size ] ) );
		$model_class   = get_class( $this->model );
		// Start with no results.
		$results = [];
		$offset  = 0;
		$found   = 0;
		do {
			if ( empty( $results ) ) {
				// Run a fetch if we're out of results to return, maybe get some results.
				$results = $wpdb->get_results( $semi_prepared . " OFFSET {$offset}", ARRAY_A );
				if ( $results === false || $wpdb->last_error ) {
					do_action(
						'tribe_log',
						'debug',
						'Builder: query failure.',
						[
							'source' => __METHOD__ . ':' . __LINE__,
							'trace'  => debug_backtrace( 2, 5 ), // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
						'error'      => $wpdb->last_error,
						]
					);
				}

				$offset += $batch_size;
				$found   = count( $results );
				$results = array_reverse( $results );
			}

			// Get a result from the fetch.
			$result = array_pop( $results );

			if ( null === $result ) {
				// No more results.
				break;
			}

			// Yield a model instance.
			yield new $model_class( $result );
		} while ( $found > 0 );

		// We're done.
		return;
	}

	/**
	 * Limit the results from a query to a single result and return the first instance if available otherwise null.
	 *
	 * @since 6.0.0
	 *
	 * @return Model|array|null The requested model in the required format, or `null` if the model could not be found.
	 */
	public function first() {
		$results = $this->limit( 1 )->get();

		if ( empty( $results ) ) {
			return null;
		}

		$result = reset( $results );

		switch ( $this->output_format ) {
			case OBJECT:
			default:
				return $result instanceof $this->model ? $result : null;
			case ARRAY_N:
				return is_array( $result ) ? array_values( $result ) : null;
			case ARRAY_A:
				return is_array( $result ) ? $result : null;
		}
	}

	/**
	 * Execute a COUNT() call against the DB using the provided query elements.
	 *
	 * @since 6.0.0
	 *
	 * @param string|null $column_name The name of the column used for the count, '*` otherwise.
	 *
	 * @return int
	 */
	public function count( $column_name = null ) {
		if ( $this->invalid ) {
			return 0;
		}

		global $wpdb;

		if ( $column_name === null ) {
			$this->operation = 'SELECT COUNT(*)';
		} else {
			$this->operation = $wpdb->prepare( 'SELECT COUNT(%s)', $column_name );
		}

		// If the query is invalid, don't return a single result.
		if ( $this->invalid ) {
			return 0;
		}

		$sql             = $this->get_sql();
		$this->queries[] = $sql;

		$result = $wpdb->get_var( $sql );
		if ( $result === false || $wpdb->last_error ) {
			do_action(
				'tribe_log',
				'debug',
				'Builder: query failure.',
				[
					'source' => __METHOD__ . ':' . __LINE__,
					'trace'  => debug_backtrace( 2, 5 ), // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
					'error'  => $wpdb->last_error,
				]
			);
		}

		return (int) $result;
	}

	/**
	 * Run a query and return the results directly from $wpdb->query().
	 *
	 * @since 6.3.1
	 *
	 * @param string $query The SQL query to run on the database.
	 *
	 * @return bool|int|mixed|\mysqli_result|resource|null The query result or null.
	 */
	protected function query( string $query ) {
		global $wpdb;
		$result = null;
		if ( $this->execute_queries ) {
			$result = $wpdb->query( $query );
			if ( $result === false || $wpdb->last_error ) {
				do_action(
					'tribe_log',
					'debug',
					'Builder: query failure.',
					[
						'source' => __METHOD__ . ':' . __LINE__,
						'trace'  => debug_backtrace( 2, 5 ), // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
						'error'  => $wpdb->last_error,
					]
				);
			}
		}

		return $result;
	}

	/**
	 * Execute an EXISTS() call using the created query as subquery of the EXISTS function.
	 *
	 * @since 6.0.0
	 * @return bool True If the query has at least 1 result available, false otherwise.
	 */
	public function exists() {
		if ( $this->invalid ) {
			return false;
		}

		global $wpdb;

		$this->operation = 'SELECT *';

		// If the query is invalid, don't return a single result.
		if ( $this->invalid ) {
			return false;
		}

		$subquery = $this->get_sql();

		$sql             = "SELECT * FROM `{$wpdb->prefix}{$this->model->table_name()}` WHERE EXISTS ($subquery)";
		$this->queries[] = $sql;

		if ( $this->execute_queries ) {
			return (bool) $wpdb->get_var( $sql );
		}

		return false;
	}

	/**
	 * Create a join clause with the single builder method.
	 *
	 * @since 6.0.0
	 *
	 * @param string $table_name           The name of the table to join.
	 * @param string $left_column          The field on the table to join.
	 * @param string $current_model_column The field on the current model to join against with.
	 *
	 * @return $this
	 */
	public function join( $table_name, $left_column, $current_model_column ) {
		if ( $this->invalid ) {
			return $this;
		}

		global $wpdb;

		$parts = [
			"JOIN `{$table_name}`",
			"ON `{$wpdb->prefix}{$this->model->table_name()}`.$current_model_column = `{$table_name}`.$left_column",
		];

		$this->joins[] = $parts;

		return $this;
	}

	/**
	 * Select all the rows that match with the query.
	 *
	 * @since 6.0.0
	 * @return Model[]
	 */
	public function get() {
		global $wpdb;

		$this->operation = 'SELECT *';

		// If the query is invalid, don't return a single result.
		if ( $this->invalid ) {
			return [];
		}

		$sql             = $this->get_sql();
		$this->queries[] = $sql;
		$results         = [];

		if ( $this->execute_queries ) {
			$results = self::$use_query_cache ?
				tribe_cache()->get( $sql, Cache_Triggers::TRIGGER_SAVE_POST, null, Cache::NON_PERSISTENT )
				: null;

			if ( null === $results ) {
				$results = $wpdb->get_results(
					$sql,
					ARRAY_A
				);

				if ( self::$use_query_cache ) {
					tribe_cache()->set( $sql, $results, Cache::NON_PERSISTENT, Cache_Triggers::TRIGGER_SAVE_POST );
				}
			}

			if ( $results === false || $wpdb->last_error ) {
				do_action(
					'tribe_log',
					'debug',
					'Builder: query failure.',
					[
						'source' => __CLASS__ . ' ' . __METHOD__ . ' ' . __LINE__,
						'trace'  => debug_backtrace( 2, 5 ), // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
					'error'      => $wpdb->last_error,
					]
				);
			}
		}

		if ( ARRAY_A === $this->output_format ) {
			return $results;
		}

		if ( ARRAY_N === $this->output_format ) {
			return array_map( 'array_values', $results );
		}

		return $this->create_collection( $results );
	}

	/**
	 * Compiles the current order by statements if any exist and returns the entire `ORDER BY` clause.
	 *
	 * @since 6.0.13
	 *
	 * @return string The compiled ORDER BY clause.
	 */
	private function get_order_by_clause(): string {
		$compiled_order_by = '';
		foreach ( $this->order as $order ) {
			$compiled_order_by .= '`' . $order['column'] . '` ' . $order['order'] . ', ';
		}
		$compiled_order_by = ! empty( $compiled_order_by ) ? 'ORDER BY ' . trim( $compiled_order_by, ', ' ) : '';

		return $compiled_order_by;
	}

	/**
	 * Get all the pieces of the SQL constructed to used against the DB.
	 *
	 * @since 6.0.0
	 * @return string
	 */
	public function get_sql() {
		// If this query is already invalid return an empty string.
		if ( $this->invalid ) {
			return '';
		}

		global $wpdb;
		$pieces = [
			$this->operation,
			"FROM `{$wpdb->prefix}{$this->model->table_name()}`",
		];

		foreach ( $this->joins as $joins ) {
			foreach ( $joins as $line ) {
				$pieces[] = $line;
			}
		}

		$where = $this->get_where_clause();
		if ( $where !== '' ) {
			$pieces[] = $where;
		}

		$order_by = $this->get_order_by_clause();
		if ( $order_by !== '' ) {
			$pieces[] = $order_by;
		}

		if ( isset( $this->limit ) ) {
			$pieces[] = $wpdb->prepare( 'LIMIT %d', (int) $this->limit );
		}

		if ( isset( $this->offset ) ) {
			$pieces[] = $wpdb->prepare( 'OFFSET %d', (int) $this->offset );
		}

		return implode( "\n", $pieces );
	}

	/**
	 * Get the SQL with the where clauses, uses the `where_in` first if specified along side with a  where clause
	 * if no where clause is specified the `primary_key` of the model is used to construct a where clause.
	 *
	 * @since 6.0.0
	 *
	 * @return string An empty string if the WHERE clause is invalid, or a valid SQL with the where clause.
	 */
	private function get_where_clause() {
		if ( ! empty( $this->wheres ) ) {
			return 'WHERE ' . implode( ' AND ', $this->wheres );
		}

		// Add a where clause with the primary key of the model if no where was specified.
		$pk = $this->model->primary_key_name();
		if ( isset( $this->model->{$pk} ) ) {
			$this->wheres     = [];
			$this->where_args = [];
			$this->where( $pk, $this->model->{$pk} );

			if ( empty( $this->wheres ) ) {
				$this->invalid = true;

				return '';
			}

			return 'WHERE ' . implode( ' AND ', $this->wheres );
		}

		return '';
	}

	/**
	 * Add the available where clauses on the model.
	 *
	 * @since 6.0.0
	 *
	 * @param string      $column   The name of the column
	 * @param string|null $operator The operator to use against to compare or the value
	 * @param string|null $value    The value to compare against with.
	 *
	 * @return $this
	 */
	public function where( $column, $operator = null, $value = null ) {
		$this->invalid = false;
		$where_args    = null;

		// If only 2 arguments are provided use the second argument as the value and assume the operator is "="
		if ( func_num_args() === 2 ) {
			$value    = $operator;
			$operator = '=';
		}

		if ( $this->invalid_operator( $operator ) ) {
			$this->invalid = true;

			return $this;
		}

		$model = $this->set_data_to_model( [ $column => $value ] );

		if ( ! $model->enable_single_validation( $column )->validate( [ $column ] ) ) {
			$this->invalid = true;

			return $this;
		}

		list( $data, $format ) = $model->format();

		if ( empty( $data ) || ! array_key_exists( $column, $data ) ) {
			$this->invalid = true;

			return $this;
		}

		if ( array_key_exists( $column, $format ) ) {
			global $wpdb;
			$format = $format[ $column ];

			$where_args = [
				'field'          => $column,
				'operator'       => $operator,
				'prepare_format' => $format,
				'value'          => $data[ $column ],
			];

			$this->where_args[] = $where_args;
			$this->wheres[]     = $wpdb->prepare( "(`{$column}` {$operator} {$format})", $data[ $column ] );

			return $this;
		}

		if ( $value === null ) {
			$where_args         = [
				'field'    => $column,
				'operator' => $operator,
				'value'    => null,
			];
			$this->where_args[] = $where_args;
			$this->wheres[]     = "(`{$column}` {$operator} NULL)";
		}

		return $this;
	}

	/**
	 * Detect if the operator is allowed or not.
	 *
	 * @since 6.0.0
	 *
	 * @param string $operator The operator to compare against with.
	 *
	 * @return bool If the operator is invalid or not.
	 */
	private function invalid_operator( $operator ) {
		return ! in_array( $operator, $this->operators, true );
	}

	/**
	 * Allow to define the clause for order by on the Query.
	 *
	 * @since 6.0.0
	 * @since 6.0.13 Can accept multiple order by statements. Previously `order_by()` would only use the last statement specified.
	 *
	 * @param string|null $column The name of the column to order by, if not provided fallback to the primary key name
	 * @param string      $order  The type of order for the results.
	 *
	 * @return $this
	 */
	public function order_by( $column = null, $order = 'ASC' ) {
		if ( in_array( strtoupper( $order ), [ 'ASC', 'DESC' ], true ) ) {
			$this->order[] = [
				'column' => $column ?? $this->model->primary_key_name(),
				'order'  => $order,
			];
		}

		return $this;
	}

	/**
	 * Set the limit for the current Query.
	 *
	 * @since 6.0.0
	 *
	 * @param int $limit The limit to apply to the current query.
	 *
	 * @return $this Instance to the current class.
	 */
	public function limit( $limit ) {
		$limit = (int) $limit;

		if ( $limit >= 0 ) {
			$this->limit = $limit;
		}

		return $this;
	}

	/**
	 * Set the offset for the current query.
	 *
	 * @since 6.0.0
	 *
	 * @param int $offset The offset applied to the current query.
	 *
	 * @return $this Instance to the current class.
	 */
	public function offset( $offset ) {
		$this->offset = max( 0, (int) $offset );

		return $this;
	}

	/**
	 * Create an array with a list of placeholders to be replaced, if the column has been defined in the format
	 * we just use the format specified on the format array, if the format has not been specified, check if the
	 * value was set as `null and if so define "NULL" as the placeholder value when passing it to the `$wpdb->prepare`
	 * call.
	 *
	 * @since 6.0.0
	 *
	 * @param array<string, mixed> $data   An associative array where the keys represent the columns and the value the
	 *                                     expected value to be saved.
	 * @param array<string,string> $format An array with the key as the column and the value as the format of the
	 *                                     column.
	 *
	 * @return string
	 */
	private function create_placeholders( array $data, array $format ) {
		$placeholder_values = [];
		foreach ( $data as $column => $value ) {
			if ( array_key_exists( $column, $format ) ) {
				$placeholder_values[] = $format[ $column ];
				continue;
			}

			if ( $value === null ) {
				$placeholder_values[] = 'NULL';
				continue;
			}
		}

		// Not the same number of columns to be inserted.
		if ( count( $placeholder_values ) !== count( array_keys( $data ) ) ) {
			return '';
		}

		return implode( ',', $placeholder_values );
	}

	/**
	 * Create an array with all the raw values that are going to be replaced, `null` values are skipped
	 * as those should be inserted directly as part of the `values() list instead of using `$wpdb->prepare`.
	 *
	 * @since 6.0.0
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	private function create_replacements_values( array $data ) {
		$values = [];
		foreach ( $data as $column => $value ) {
			if ( $value === null ) {
				continue;
			}
			$values[] = $value;
		}

		return $values;
	}

	/**
	 * Validate a series of rows inside of a nested array.
	 *
	 * @since 6.0.0
	 *
	 * @param array<array<string, mixed>> $data The data to be validated.
	 *
	 * @return array<string, mixed> An array with the columns, placeholders and values to be inserted.
	 */
	private function validate_rows( array $data ) {
		$columns     = '';
		$formatting  = [];
		$list_values = [];

		foreach ( $data as $row ) {
			$model = $this->set_data_to_model( $row );
			$model->validate();
			if ( $model->is_invalid() ) {
				continue;
			}

			list( $values, $format ) = $model->format();

			if ( empty( $values ) || empty( $format ) ) {
				continue;
			}

			// Just set the columns values the first time.
			if ( empty( $columns ) ) {
				$keys    = array_map(
					static function ( $column ) {
						return "`{$column}`";
					},
					array_keys( $values )
				);
				$columns = implode( ',', $keys );
			}

			// Ignore all values that were set as NULL.
			$values_to_be_inserted = array_filter(
				array_values( $values ),
				static function ( $value ) {
					return $value !== null;
				}
			);

			// Append all the values, into a single array to flat the values into a single array.
			array_push( $list_values, ...$values_to_be_inserted );

			$pieces = [];
			foreach ( $values as $column => $value ) {
				if ( array_key_exists( $column, $format ) ) {
					$pieces[] = $format[ $column ];
					continue;
				}

				if ( $value === null ) {
					$pieces[] = 'NULL';
					continue;
				}
			}
			$formatting[] = '(' . implode( ',', $pieces ) . ')';
		}

		return [
			'columns'      => $columns,
			'placeholders' => implode( ',', $formatting ),
			'values'       => $list_values,
		];
	}

	/**
	 * If an instance already exists refresh the values by querying the same value against the DB.
	 *
	 * @since 6.1.3 Integration with memoization.
	 * @since 6.0.0
	 *
	 * @return Model
	 */
	public function refresh() {
		$pk = $this->model->primary_key_name();
		if ( ! isset( $this->model->{$pk} ) ) {
			return $this->model;
		}

		// If we have a cache, let's clear it.
		$this->model->flush_cache();
		$model = $this->find( $this->model->{$pk}, $pk );

		if ( $model === null ) {
			$this->model->reset();
		}

		foreach ( $model->to_array() as $column => $value ) {
			$this->model->{$column} = $value;
		}
	}

	/**
	 * Setup the dynamic properties of a model using an array.
	 *
	 * @since 6.0.0
	 *
	 * @param array<string, mixed> $data An array that is going to be used to setup the data of a model.
	 *
	 * @return   Model  An instance of a model
	 */
	private function set_data_to_model( array $data = [] ) {
		$data        = array_merge( $this->model->to_array(), $data );
		$model_class = get_class( $this->model );

		return new $model_class( $data );
	}

	/**
	 * Create an array of model instances to get the benefits of a model.
	 *
	 * @since 6.0.0
	 *
	 * @param array|null $raw The result from a `$wpdb->get_results` call.
	 *
	 * @return array<Model> An array with the models with the raw results.
	 */
	private function create_collection( array $raw = null ) {
		if ( $raw === null ) {
			return [];
		}

		$model_class = get_class( $this->model );

		$results = [];

		foreach ( $raw as $result ) {
			$results[] = new $model_class( $result );
		}

		return $results;
	}

	/**
	 * Adds a raw WHERE clause to the SQL statement being built.
	 *
	 * @since 6.0.0
	 * @param string $query   The SQL clause to be prepared using the `wpdb::prepare()`
	 *                        method and placeholder format.
	 * @param mixed  ...$args A set of arguments that should be used to prepare the SQL
	 *                        statement.
	 *
	 * @return $this A reference to the query builder object, for chaining purposes.
	 */
	public function where_raw( $query, ...$args ) {
		global $wpdb;
		$where_args         = [
			'operator' => 'raw',
			'value'    => $query,
		];
		$this->where_args[] = $where_args;
		$this->wheres[]     = '(' . $wpdb->prepare( $query, ...$args ) . ')';

		return $this;
	}

	/**
	 * Sets the output format that should be used to format the result(s) of a SELECT
	 * Model query.
	 *
	 * @since 6.0.0
	 *
	 * @param string $output One of `OBJECT`, `ARRAY_A` or `ARRAY_N`. Note that `OBJECT`
	 *                       will build and return instances of the Model.
	 *
	 * @return $this A reference to the query builder object, for chaining purposes.
	 */
	public function output( $output = OBJECT ) {
		if ( ! in_array( $output, [ OBJECT, ARRAY_A, ARRAY_N ], true ) ) {
			throw new InvalidArgumentException( 'Output not supported, use one of ARRAY_A, ARRAY_N or OBJECT.' );
		}

		$this->output_format = $output;

		return $this;
	}

	/**
	 * Fetches all the matching results for the query.
	 *
	 * The method will handle querying the database in batches, running bound queries
	 * to support unbound fetching.
	 *
	 * @since 6.0.0
	 *
	 * @return Generator<Model|array> A generator of either this Model instances or arrays, depending on
	 *                                the selected output format.
	 */
	public function all() {
		$query_offset   = (int) $this->offset;
		$query_limit    = $this->limit ?: PHP_INT_MAX;
		$running_offset = $query_offset;
		/** @var \wdpb $wpdb */
		global $wpdb;
		$running_limit = $query_limit;
		$running_tally = 0;
		$found_rows    = (int) $wpdb->get_var( $this->get_count_rows_sql() );

		if ( $found_rows === 0 ) {
			// Nothing to return.
			return;
		}

		// The found rows value does take into account the offset, include it here.
		$found_results = $found_rows - $query_offset;

		do {
			$this->limit     = min( $this->batch_size, $running_limit );
			$this->offset    = $running_offset;
			$running_limit  -= $this->batch_size;
			$running_offset += $this->batch_size;
			$batch_results   = $this->get();
			foreach ( $batch_results as $batch_result ) {
				// Yields with a set key to avoid calls to `iterator_to_array` overriding the values on each pass.
				yield $running_tally++ => $batch_result;
			}
		} while ( $running_tally < $found_results && $running_tally < $query_limit );
	}

	/**
	 * Bulk updates instances of the Model.
	 *
	 * Since MySQL does not come with a bulk update feature, this code will actually
	 * delete the existing model entries and re-insert them, by primary key, using the
	 * updated data.
	 *
	 * @since 6.1.3 Integration with memoization.
	 * @since 6.0.0
	 *
	 * @param array<Model>|array<array<string,mixed>> $models Either a list of Model
	 *                                                        instances to update, or a
	 *                                                        set of models in array format.
	 *
	 * @return int The number of updated rows.
	 */
	public function upsert_set( array $models = [] ) {
		if ( ! count( $models ) ) {
			return 0;
		}

		global $wpdb;
		$table          = $wpdb->prefix . $this->model->table_name();
		$primary_key    = $this->model->primary_key_name();
		$expected_count = count( $models );
		$keys           = wp_list_pluck( $models, $primary_key );

		$deleted = 0;
		do {
			$batch         = array_splice( $keys, 0, $this->batch_size );
			$keys_interval = implode( ',', array_map( 'absint', $batch ) );
			$deleted      += $this->query( "DELETE FROM {$table} WHERE {$primary_key} IN ({$keys_interval})" );

			// If we have a cache, let's clear it.
			foreach ( $models as $model ) {
				if ( $model instanceof Model ) {
					$model->flush_cache();
				}
			}
		} while ( count( $keys ) );

		if ( $deleted !== $expected_count ) {
			// There might be legit reasons, like another process running on the same table, but let's log it.
			do_action(
				'tribe_log',
				'warning',
				'Mismatching number of deletions.',
				[
					'source'      => __CLASS__,
					'slug'        => 'delete-in-upsert-set',
					'table'       => $table,
					'primary_key' => $primary_key,
					'expected'    => $expected_count,
					'deleted'     => $deleted,
				]
			);
		}

		$updates = $models;
		// Here we make the assumptions the models will not be mixed bag, but either all arrays or all Models.
		if ( ! is_array( reset( $models ) ) ) {
			$updates = array_map(
				static function ( Model $model ) {
					return $model->to_array();
				},
				$models
			);
		}

		$inserted = $this->insert( $updates );

		if ( $inserted !== $expected_count ) {
			// There might be legit reasons, like another process running on the same table, but let's log it.
			do_action(
				'tribe_log',
				'warning',
				'Mismatching number of insertions.',
				[
					'source'      => __CLASS__,
					'slug'        => 'delete-in-upsert-set',
					'table'       => $table,
					'primary_key' => $primary_key,
					'expected'    => $expected_count,
					'inserted'    => $inserted,
				]
			);
		}

		return $inserted;
	}

	/**
	 * Gets the results and plucks a field from each.
	 *
	 * @since 6.0.1
	 *
	 * @param string $field The field to pluck.
	 *
	 * @return array The plucked values.
	 */
	public function pluck( string $field ): array {
		return wp_list_pluck( $this->get(), $field );
	}

	/**
	 * Maps from the results of the query to a new array using the callback.
	 *
	 * @since 6.0.1
	 *
	 * @param callable $callback The callback to use to map the results.
	 *
	 * @return array The mapped results.
	 */
	public function map( callable $callback ): array {
		return array_map( $callback, $this->get() );
	}

	/**
	 * Returns the SQL query to fetch the number of found rows.
	 *
	 * This builds a query without LIMIT that uses `SELECT COUNT(*)` as
	 * recommended by MySQL in place of using `SQL_CALC_FOUND_ROWS`.
	 *
	 * @see   https://dev.mysql.com/doc/refman/8.4/en/information-functions.html#function_found-rows
	 *
	 * @since 6.11.1
	 *
	 * @return string The SQL query to fetch the number of found rows.
	 */
	private function get_count_rows_sql(): string {
		// If this query is already invalid return an empty string.
		if ( $this->invalid ) {
			return '';
		}

		global $wpdb;
		$pieces = [
			'SELECT COUNT(*)',
			"FROM `{$wpdb->prefix}{$this->model->table_name()}`",
		];

		foreach ( $this->joins as $joins ) {
			foreach ( $joins as $line ) {
				$pieces[] = $line;
			}
		}

		$where = $this->get_where_clause();
		if ( $where !== '' ) {
			$pieces[] = $where;
		}

		$order_by = $this->get_order_by_clause();
		if ( $order_by !== '' ) {
			$pieces[] = $order_by;
		}

		return implode( "\n", $pieces );
	}

	/**
	 * Controls whether the Builder class should use the query cache in the fetch methods or not.
	 *
	 * @since 6.11.1
	 *
	 * @param bool $use_query_cache Whether the Builder class should use the query cache in the fetch methods or not.
	 */
	public static function use_query_cache( bool $use_query_cache ) {
		self::$use_query_cache = $use_query_cache;
	}
}
