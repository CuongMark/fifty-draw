<?php


namespace Angel\Fd\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $table_angel_fd_ticket = $setup->getConnection()->newTable($setup->getTable('angel_fd_ticket'));

        $table_angel_fd_ticket->addColumn(
            'ticket_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_angel_fd_ticket->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => False,'unsigned' => true],
            'Product Id'
        );

        $table_angel_fd_ticket->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => False],
            'Customer Id'
        );

        $table_angel_fd_ticket->addColumn(
            'start',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => False],
            'Start Number'
        );

        $table_angel_fd_ticket->addColumn(
            'end',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => False],
            'End Number'
        );

        $table_angel_fd_ticket->addColumn(
            'price',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => False,'precision' => 12,'scale' => 4],
            'Price'
        );

        $table_angel_fd_ticket->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            ['default' => Table::TIMESTAMP_INIT, 'nullable' => false],
            [],
            'Created At'
        );

        $table_angel_fd_ticket->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['default' => Table::TIMESTAMP_INIT_UPDATE,'nullable' => false],
            'Updated At'
        );

        $table_angel_fd_ticket->addColumn(
            'credit_transaction_id',
            Table::TYPE_INTEGER,
            null,
            [],
            'Store Credit Transaction Id'
        );

        $table_angel_fd_ticket->addColumn(
            'invoice_item_id',
            Table::TYPE_INTEGER,
            null,
            [],
            'Invoice Item Id'
        );

        $table_angel_fd_ticket->addColumn(
            'admin_id',
            Table::TYPE_INTEGER,
            null,
            [],
            'Admin Staff Id'
        );
        
        $table_angel_fd_ticket->addColumn(
            'serial',
            Table::TYPE_TEXT,
            255,
            [],
            'Serial Number'
        );

        $table_angel_fd_ticket->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            [],
            'status'
        );
        
        $table_angel_fd_ticket->addIndex(
            $setup->getIdxName(
                'angel_fd_ticket',
                ['serial'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['serial'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );

        $table_angel_fd_ticket->addIndex(
            $setup->getIdxName(
                'angel_fd_ticket',
                ['product_id', 'start'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['product_id', 'start'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );

        $table_angel_fd_ticket->addForeignKey(
            $setup->getFkName('angel_fd_ticket', 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id',
            $setup->getTable('catalog_product_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        );

        //Your install script

        $setup->getConnection()->createTable($table_angel_fd_ticket);
    }
}
