<?php

class Factory
{
    public function __construct($mysqlPath, $filePath)
    {
        $this->mysqlPath = $mysqlPath;
        $this->filePath = $filePath;
    }

    /**
     * This is the collection of tables
     * with preset datas
     * 
     */
    public function tableWithPresets()
    {
        return [
            "tbl_menu",
            "tbl_gchart_main",
            "tbl_assign_chart_of_account",
            "tbl_doctype",
            "tbl_emp_class",
            "tbl_package",
            "tbl_productmaster",
            "tbl_product_category",
            "tbl_user_privilege_item"
        ];
    }

    /**
     * This is the options we used in dump
     * 
     */
    public function optionDump()
    {
        return "--single-transaction --skip-add-drop-table --skip-add-locks --skip-comments --skip-set-charset --tz-utc";
    }

    /**
     * This is the option where the dump should go
     * 
     */
    public function resultFile()
    {
        return '--result-file="' . $this->filePath . '"';
    }

    /**
     * the database from variables
     * 
     */
    public function database()
    {
        return $GLOBALS['config']['mysql']['orig_db_main'];
    }

    /**
     * the host from variables
     * 
     */
    public function host()
    {
        return $GLOBALS['config']['mysql']['host'];
    }

    /**
     * the user from variables
     * 
     */
    public function user()
    {
        return $GLOBALS['config']['mysql']['username'];
    }

    /**
     * the password from variables
     * 
     */
    public function password()
    {
        return $GLOBALS['config']['mysql']['password'];
    }

    /**
     * the connection we used to connect to mysql
     * 
     */
    public function conn()
    {
        return '--host="' . $this->host() . '" --user="' . $this->user() . '" --password="' . $this->password() . '"';
    }

    /**
     * this will manufacture our import structure
     * 
     */
    public function importSql()
    {
        return $this->mysqlPath . 'mysql ' . $this->conn() . ' ' . $this->database() . ' < ' . $this->filePath;
    }

    /**
     * this will manufacture our dump structure
     * this one has no data it only dump
     * table schema
     * 
     */
    public function sqlDumpNodata()
    {
        return $this->mysqlPath . "mysqldump " . $this->optionDump() . " " . $this->conn() . " " . $this->database() . " --routines " . $this->resultFile() . " --no-data";
    }

    /**
     * this will manufacture our dump structure
     * this one has data that we specify in
     * tableWithPresets method
     * 
     */
    public function sqlDump($isPrune)
    {
        $hasPresets = implode(" ", $this->tableWithPresets());
        $presetTables = ($isPrune != "prune") ? $hasPresets . " tbl_migrations" : $hasPresets;

        return $this->mysqlPath . "mysqldump " . $this->optionDump() . " " . $this->conn() . " --no-create-info --skip-triggers " . $this->database() . " " . $presetTables . " >> " . $this->filePath;
    }
}
