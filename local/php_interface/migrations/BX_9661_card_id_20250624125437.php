<?php

namespace Sprint\Migration;


class BX_9661_card_id_20250624125437 extends Version
{
    protected $author = "+79158557174";

    protected $description = "";

    protected $moduleVersion = "5.0.0";

    public function up()
    {
        $helper = $this->getHelperManager();
        $query = file_get_contents(__DIR__.'/BX_9661_card_id_20250624125437_files/script.sql');
        $helper->Sql()->query($query);
    }

    public function down()
    {
        //your code ...
    }
}
