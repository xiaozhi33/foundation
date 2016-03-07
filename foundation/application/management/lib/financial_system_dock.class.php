<?php
    require_once 'mssql_db_class.php';
    /**
     * Class financial_system_dock_interface
     */
    class financial_system_dock_interface
    {
        static $_instance = false;
        private function __construct()
        {
            $this->get_instance();
        }

        public function get_instance()
        {
            if(self::$_instance && !is_object(self::$_instance))
            {
                self::$_instance = new mssql_db_lib();
            }
        }
    }