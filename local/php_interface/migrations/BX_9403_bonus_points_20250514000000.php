<?php

namespace Sprint\Migration;


use Sprint\Migration\Exceptions\HelperException;

class BX_9403_bonus_points_20250514000000 extends Version
{
    protected $author = "+79205018571";

    protected $description = "";

    protected $moduleVersion = "5.0.0";

    /**
     * @return void
     * @throws HelperException
     */
    public function up(): void
    {
        $helper = $this->getHelperManager();
        $helper->Sql()->query(
            "create table wl_onec_loyalty_queue
                    (
                        ID           int auto_increment primary key,
                        METHOD       varchar(50)  null,
                        PARAMS       varchar(300)  null,
                        USER_ID      int          not null,
                        TIMESTAMP    datetime     not null,
                        IS_COMPLETED tinyint(1)   null,
                        DATE_EXEC    datetime     null,
                        RESULT       varchar(500) null,                    
                        ATTEMPT      int
                    );"
        );

        $helper->Agent()->saveAgent([
            'MODULE_ID'      => 'wl.onec_loyalty',
            'USER_ID'        => '1',
            'SORT'           => '0',
            'NAME'           => '\\WL\\OnecLoyalty\\Agents\\ProcessingQueueAgent::execute()',
            'ACTIVE'         => 'Y',
            'NEXT_EXEC'      => date('d.m.Y') . ' 00:00:00',
            'AGENT_INTERVAL' => '60',
            'IS_PERIOD'      => 'N',
            'RETRY_COUNT'    => '0',
        ]);
    }


    /**
     * @return void
     * @throws HelperException
     */
    public function down(): void
    {
        $helper = $this->getHelperManager();
        $helper->Sql()->query("drop table wl_onec_loyalty_queue;");
    }
}
