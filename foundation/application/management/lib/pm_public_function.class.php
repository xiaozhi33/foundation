<?php
    class pm_public_function
    {
        protected $orm;
        public function __construct()
        {
            $this->orm = ORM::getInstance();
        }
        /**
         * @param $pm_id
         * @param $rate_id
         */
        public function save_rete($pm_id, $rate_id){
            try{
                if(!empty($pm_id) && !empty($rate_id)){
                    $this->orm->createDAO("");
                }
            }catch(Exception $e){
                throw $e;
            }
        }
    }
?>