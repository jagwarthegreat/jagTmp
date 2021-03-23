<?php

class DatabaseMigrationRepository
{
	public function __construct($sqlfileName, $table)
	{
		$this->queryBuilder = new Builder($sqlfileName);
		$this->table = $table;
	}

	/**
	 * Get the completed migrations.
	 * 
	 */
	public function getRan()
	{
		$completed = [];
		$migrations = $this->queryBuilder->selectLoop("migrations", $this->table);
		if (count($migrations) > 0) {
			foreach ($migrations as $done) {
				$completed[] = $done[0];
			}
		}

		return $completed;
	}

	/**
	 * Get the completed migrations by batch.
	 * 
	 * 
	 */
	public function getRanBatch()
	{
		$migration_list = [];
		$batch = $this->getLastBatchNumber();
		$migrations = $this->queryBuilder->selectLoop("migrations", $this->table, "batch = '$batch' ORDER BY id DESC");
		if (count($migrations) > 0) {
			foreach ($migrations as $list) {
				$migration_list[] = $list[0];
			}
		}

		return $migration_list;
	}

	/**
	 * Create the migration repository data store.
	 * 
	 */
	public function createRepository()
	{
		// The migrations table is responsible for keeping track of which of the
		// migrations have actually run for the application. We'll create the
		// table to hold the migration file's path as well as the batch ID.
		$response = $this->queryBuilder->raw("CREATE TABLE `tbl_migrations` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`migrations` TEXT NOT NULL COLLATE 'latin1_swedish_ci',
			`batch` INT(11) NOT NULL,
			PRIMARY KEY (`id`) USING BTREE
		)
		COLLATE='latin1_swedish_ci'
		ENGINE=InnoDB
		;");

		return ($response[val]) ? nl2br("Migration table created successfully.\n") : nl2br("Error creating migration table.\n");
	}

	/**
	 * Get the last migration batch number.
	 * 
	 */
	public function getLastBatchNumber()
	{
		$batch_num = $this->queryBuilder->select("batch", $this->table, "id > 0 ORDER BY batch DESC LIMIT 1");
		return $batch_num[0];
	}

	/**
	 * Get the next migration batch number.
	 *
	 */
	public function getNextBatchNumber()
	{
		return $this->getLastBatchNumber() + 1;
	}

	/**
	 * Log that a migration was run.
	 * 
	 */
	public function log($migration_name, $migration_batch)
	{
		$this->queryBuilder->insert($this->table, ['migrations' => $migration_name, 'batch' => $migration_batch]);
	}

	/**
	 * Remove a migration from the log.
	 * 
	 */
	public function delete($migration_name, $migration_batch)
	{
		$this->queryBuilder->delete($this->table, "migrations = '$migration_name' AND batch = '$migration_batch'");
	}

	/**
	 * Let's capture the mode/behaviour of our migration
	 * we'll then create the schema
	 * 
	 */
	public function schemaScaffold($mode, $table, $updown, $primary, $type)
	{
		$schema = "";
		if ($type === "UP") {
			if (strtoupper($mode) === "NEW") {
				$schema .= $this->queryBuilder->createSchema($table, $updown, $primary);
			}

			if (strtoupper($mode) === "DROP") {
				$schema .= $this->queryBuilder->dropSchema($table);
			}
		} else {

			// this will reverse the migration
			// if the file mode == NEW then we will drop the table
			if (strtoupper($mode) === "NEW") {
				$schema .= $this->queryBuilder->dropSchema($table);
			}

			// When the file mode == DROP then we will create the table
			if (strtoupper($mode) === "DROP") {
				$schema .= $this->queryBuilder->createSchema($table, $updown, $primary);
			}
		}

		if (strtoupper($mode) === "CHANGE") {
			$schema .= $this->queryBuilder->alterSchema($table, $updown);
		}

		if (strtoupper($mode) === "RENAMETABLE") {
			$schema .= $this->queryBuilder->renameTableSchema($updown);
		}

		return $schema;
	}
}
