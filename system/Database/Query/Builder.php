<?php

class Builder
{
    public function __construct($schemaName = '', $schemaPath = '')
    {
        $this->schemaFilePath = $schemaPath . $schemaName;

        // Where is your mysql? go find it!
        // windows: C:\\xampp\\mysql\\bin\\
        // masOS: /Applications/XAMPP/xamppfiles/bin/
        // linux: /usr/bin/
        $this->mysqlPath = "/usr/bin/";

        // Let's include the factory class
        // then we build it's method
        $this->factory = new Factory($this->mysqlPath, $this->schemaFilePath);

        // !! F A I L S A F E !!
        // This collection is to prevent unwanted 
        // changes against the database of the host specified
        // please be careful removing host, 
        // this may damage/drop/change the database
        $this->notAllowedHost = [
            "50.62.135.136"
        ];

        // database engine
        $this->engine = "InnoDB";
    }

    /**
     * Create a scaffold of our create table
     * 
     */
    public function createSchema($table, $cols, $primary)
    {
        $create_structure = "";
        $engine = $this->engine;
        $create_structure .= "CREATE TABLE `$table` (";
        foreach ($cols as $col => $datatype) {
            $create_structure .= "`$col` " . $datatype . ",";
        }
        $create_structure .= "PRIMARY KEY (`$primary`) USING BTREE
		)
		COLLATE='latin1_swedish_ci'
		ENGINE=$engine
		;";

        return $create_structure;
    }

    /**
     * Create a scaffold of our alter table
     * 
     */
    public function alterSchema($table, $cols)
    {
        $countCols = 1;
        $alter_structure = "";
        $alter_structure .= "ALTER TABLE `$table` ";
        foreach ($cols as $datatype) {
            $putComma = ($countCols < count($cols)) ? ", " : " ";
            $alter_structure .= $datatype . $putComma;
            $countCols++;
        }
        $alter_structure .= ";";

        return $alter_structure;
    }

    public function renameTableSchema($cols)
    {
        $scaffold = "";
        foreach ($cols as $from => $to) {
            $scaffold .= "ALTER TABLE `$from` ";
            $scaffold .= "RENAME TO `$to`;";
        }
        return $scaffold;
    }

    /**
     * Create a scaffold of our drop table
     * 
     */
    public function dropSchema($table)
    {
        return "DROP TABLE IF EXISTS `$table`;";
    }

    /**
     * reference function @fm_functions.php
     * by Reno C.
     */
    function insert($table_name, $form_data, $last_id = 'N')
    {
        $fields = array_keys($form_data);

        $sql = "INSERT INTO " . $table_name . "
	    (`" . implode('`,`', $fields) . "`)
	    VALUES('" . implode("','", $form_data) . "')";

        $return_insert = mysql_query($sql) or die(mysql_error());
        $lastID = mysql_insert_id();

        if ($last_id == 'Y') {
            if ($return_insert) {
                return $lastID;
            } else {
                return 0;
            }
        } else {
            if ($return_insert) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * reference function @fm_functions.php
     * by Reno C.
     */
    public function select($type, $table, $params = '')
    {
        $inject = ($params == '') ? "" : "WHERE $params";
        $select_query = mysql_query("SELECT $type FROM $table $inject") or die(mysql_error());
        $fetch = mysql_fetch_array($select_query);
        return $fetch;
    }

    /**
     * reference function @fm_functions.php
     * by Reno C.
     */
    public function selectLoop($type, $table, $params = '')
    {
        $inject = ($params == '') ? "" : "WHERE $params";
        $fetch = mysql_query("SELECT $type FROM $table $inject") or die(mysql_error());
        while ($row = mysql_fetch_array($fetch)) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * reference function @fm_functions.php
     * by Reno C.
     */
    public function update($table_name, $form_data, $where_clause = '')
    {
        $whereSQL = '';
        if (!empty($where_clause)) {
            if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
                $whereSQL = " WHERE " . $where_clause;
            } else {
                $whereSQL = " " . trim($where_clause);
            }
        }
        $sql = "UPDATE " . $table_name . " SET ";
        $sets = array();
        foreach ($form_data as $column => $value) {
            $sets[] = "`" . $column . "` = '" . $value . "'";
        }
        $sql .= implode(', ', $sets);
        $sql .= $whereSQL;

        $return_query = mysql_query($sql);
        if ($return_query) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * reference function @fm_functions.php
     * by Reno C.
     */
    public function delete($table_name, $where_clause = '')
    {
        $whereSQL = '';
        if (!empty($where_clause)) {
            if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
                $whereSQL = " WHERE " . $where_clause;
            } else {
                $whereSQL = " " . trim($where_clause);
            }
        }
        $sql = "DELETE FROM " . $table_name . $whereSQL;

        $return_delete = mysql_query($sql);

        if ($return_delete) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * reference function @fm_functions.php
     * by Reno C.
     */
    public function raw($query)
    {
        $is = mysql_query($query);
        $data = ['val' => $is, 'error' => mysql_error()];
        return $data;
    }

    /**
     * check if migarations table exist in our database
     * 
     */
    public function tableExist($table)
    {
        $database = $this->factory->database();
        $fetch = mysql_fetch_array(mysql_query("SELECT COUNT(TABLE_NAME) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$database' AND TABLE_NAME = '$table'"));
        return ($fetch[0] > 0) ? 1 : 0;
    }

    /**
     * this will drop a single table
     * set FOREIGN_KEY_CHECKS = 0
     * 
     */
    public function dropTable($table)
    {
        $this->foreignKeyChecks();
        $this->raw("DROP TABLE `$table`;");
    }

    /**
     * this will drop a single table
     * if exist set FOREIGN_KEY_CHECKS = 0
     * 
     */
    public function dropIfExists($table)
    {
        $this->foreignKeyChecks();
        $this->raw("DROP TABLE IF EXISTS `$table`;");
    }

    /**
     * set FOREIGN_KEY_CHECKS = 0
     * 
     */
    public function foreignKeyChecks()
    {
        $this->raw("SET FOREIGN_KEY_CHECKS=0;");
    }

    /**
     * this will drop all the tables in the database
     * also drops the views
     * 
     */
    public function dropAllTables()
    {
        $tables = $this->allTables();
        if (count($tables) > 0) {
            foreach ($tables as $table) {
                $this->dropIfExists($table);
            }
            $this->dropViews();
        }

        return nl2br("Dropped all tables successfully.\n");
    }

    /**
     * store all tables of a database to an array
     * 
     */
    public function allTables()
    {
        $tables_arr = [];
        $database = $this->factory->database();
        $loop_all_table = $this->selectLoop("TABLE_NAME", "INFORMATION_SCHEMA.TABLES", "TABLE_SCHEMA = '$database' AND TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME ASC");
        if (count($loop_all_table) > 0) {
            foreach ($loop_all_table as $tbl) {
                $tables_arr[] = $tbl[0];
            }
        }

        return $tables_arr;
    }

    /**
     * this will execute the stored database schema
     * 
     */
    public function importStoredDatabaseSchema()
    {
        $output = null;
        $retval = null;
        $command = $this->factory->importSql();
        exec($command, $output, $retval);

        return $retval;
    }

    /**
     * Initial dump structure
     * 
     */
    public function schemaDump($option)
    {
        $output = null;
        $retval = null;
        $buildDump = $this->factory->sqlDumpNodata();
        exec($buildDump, $output, $retval);

        // we include the table that has presets data
        $migrationsData = $this->factory->sqlDump($option);
        exec($migrationsData);

        return $retval;
    }

    public function isAllowed()
    {
        return (!in_array($this->factory->host(), $this->notAllowedHost)) ? 1 : 0;
    }

    public function mysqlHost()
    {
        return $this->factory->host();
    }
}
