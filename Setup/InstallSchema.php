<?php
/**
 * KiwiCommerce
 *
 * Do not edit or add to this file if you wish to upgrade to newer versions in the future.
 * If you wish to customise this module for your needs.
 * Please contact us https://kiwicommerce.co.uk/contacts.
 *
 * @category   KiwiCommerce
 * @package    KiwiCommerce_InventoryLog
 * @copyright  Copyright (C) 2018 KiwiCommerce Ltd (https://kiwicommerce.co.uk/)
 * @license    https://kiwicommerce.co.uk/magento2-extension-license/
 */

namespace KiwiCommerce\InventoryLog\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\DB\Ddl\Trigger;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \Magento\Framework\DB\Ddl\TriggerFactory
     */
    public $triggerFactory;

    /**
     * InstallSchema constructor.
     * @param \Magento\Framework\DB\Ddl\TriggerFactory $triggerFactory
     */
    public function __construct(
        \Magento\Framework\DB\Ddl\TriggerFactory $triggerFactory
    ) {
        $this->triggerFactory = $triggerFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $setup->getConnection()->addColumn($setup->getTable('cataloginventory_stock_item'), "ukey", [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'ukey',
            'size'=>200
        ]);

        $tableName = $setup->getTable('kiwicommerce_stock_movement');
        $tableKiwiCommerceInventoryLogMovement = $setup->getConnection()->newTable($tableName);
        $tableKiwiCommerceInventoryLogMovement->addColumn(
            'movement_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true],
            'Entity ID'
        )->addColumn(
            'stock_item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false,'unsigned' => true],
            'stock_item_id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'product_id'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true,'nullable' => true],
            'user_id'
        )->addColumn(
            'username',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            40,
            ['nullable' => true],
            'username'
        )->addColumn(
            'is_admin',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['default' => '0','unsigned' => true,'nullable' => true],
            'Is_admin'
        )->addColumn(
            'current_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false,'precision' => 12,'scale' => 4],
            'current_qty'
        )->addColumn(
            'qty_movement',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false,'precision' => 12,'scale' => 4],
            'qty_movement'
        )->addColumn(
            'old_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false,'precision' => 12,'scale' => 4],
            'old_qty'
        )->addColumn(
            'is_in_stock',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['default' => '0','unsigned' => true],
            'is_in_stock'
        )->addColumn(
            'message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'message'
        )->addColumn(
            'ukey',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            200,
            [],
            'ukey'
        )->addColumn(
            'ip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            [],
            'ip'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'created_at'
        );
        $setup->getConnection()->createTable($tableKiwiCommerceInventoryLogMovement);

        /*Sql Trigger code start*/
        $triggerName = 'stock_before_update';
        $event = Trigger::EVENT_UPDATE;

        $trigger = $this->triggerFactory->create()
            ->setName($triggerName)
            ->setTime(Trigger::TIME_BEFORE)
            ->setEvent($event)
            ->setTable($setup->getTable('cataloginventory_stock_item'));

        $trigger->addStatement($this->buildStatement($event, $setup));

        $setup->getConnection()->dropTrigger($trigger->getName());
        $setup->getConnection()->createTrigger($trigger);

        $triggerName = 'stock_after_insert';
        $event = Trigger::EVENT_INSERT;
        $trigger = $this->triggerFactory->create()
            ->setName($triggerName)
            ->setTime(Trigger::TIME_AFTER)
            ->setEvent($event)
            ->setTable($setup->getTable('cataloginventory_stock_item'));

        $trigger->addStatement($this->buildStatement($event, $setup));

        $setup->getConnection()->dropTrigger($trigger->getName());
        $setup->getConnection()->createTrigger($trigger);
        /*sql trigger code end*/

        $setup->endSetup();
    }
    public function buildStatement($event, $setup)
    {

        switch ($event) {
            case Trigger::EVENT_INSERT:
                $triggerSql  = "DECLARE	isEnable SMALLINT(5);\n";
                $triggerSql .= "SELECT 1 INTO isEnable FROM ".$setup->getTable('core_config_data')." WHERE path = 'inventory_log/general/inventory_enabled' and value = 1;\n";
                $triggerSql .= "IF (isEnable = 1) THEN\n";
                $triggerSql .= "IF (NEW.qty IS NOT NULL) THEN\n";
                $triggerSql .= "IF (NEW.ukey IS NULL) THEN\n";
                $triggerSql .= "INSERT INTO ".$setup->getTable('kiwicommerce_stock_movement')."(stock_item_id,product_id,current_qty,qty_movement,old_qty,is_in_stock,message,ukey)  VALUES  (NEW.item_id,NEW.product_id,NEW.qty,NEW.qty,0,NEW.is_in_stock,\"Stock updated by direct query Insert\",NEW.ukey);\n";
                $triggerSql .= "END IF;\n";
                $triggerSql .= "END IF;\n";
                $triggerSql .= "END IF;\n";
                return $triggerSql;
            case Trigger::EVENT_UPDATE:
                $triggerSql  = "DECLARE	isEnable SMALLINT(5);\n";
                $triggerSql .= "DECLARE qty_movement DECIMAL(12,0);\n";
                $triggerSql .= "SELECT 1 INTO isEnable FROM ".$setup->getTable('core_config_data')." WHERE path = 'inventory_log/general/inventory_enabled' and value = 1;\n";
                $triggerSql .= "IF (isEnable = 1) THEN\n";
                $triggerSql .= "IF (NEW.qty IS NOT NULL) THEN\n";
                $triggerSql .= "IF (NEW.ukey IS NULL OR NEW.ukey = OLD.ukey ) THEN\n";
                $triggerSql .= "SET qty_movement = NEW.qty-OLD.qty;\n";
                $triggerSql .= "IF (qty_movement != 0 AND NEW.qty != OLD.qty) THEN\n";
                $triggerSql .= "INSERT INTO ".$setup->getTable('kiwicommerce_stock_movement')."(stock_item_id,product_id,current_qty,qty_movement,old_qty,is_in_stock,message,ukey) VALUES (NEW.item_id,NEW.product_id,NEW.qty,qty_movement,OLD.qty,NEW.is_in_stock,\"Stock updated by direct query\",OLD.ukey);\n";
                $triggerSql .= "END IF;\n";
                $triggerSql .= "END IF;\n";
                $triggerSql .= "END IF;\n";
                $triggerSql .= "END IF;\n";
                return $triggerSql;
            default:
                return '';
        }
    }
}
