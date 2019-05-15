<?php

namespace Fundbox\Bundle\FundboxCheckoutBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class FundboxCheckoutBundleInstaller implements Installation
{
    /**
     * {@inheritDoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateOroIntegrationTransportTable($schema);

        /** Tables generation **/
        $this->createFundboxCheckoutTransLabelTable($schema);
        $this->createFundboxCheckoutShortLabelTable($schema);

        /** Foreign keys generation **/
        $this->addFundboxCheckoutTransLabelForeignKeys($schema);
        $this->addFundboxCheckoutShortLabelForeignKeys($schema);
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updateOroIntegrationTransportTable(Schema $schema)
    {
        $table = $schema->getTable('oro_integration_transport');

        $table->addColumn('fbx_environment', 'string', ['notnull' => false, 'length' => 50]);
        $table->addColumn('fbx_production_public_key', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('fbx_production_private_key', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('fbx_test_public_key', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('fbx_test_private_key', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('fbx_log_enabled', 'boolean', ['default' => '0', 'notnull' => false]);
        $table->addColumn('fbx_payment_action', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('fbx_minimum_order', 'integer', ['notnull' => false, 'default' => 10]);
        $table->addColumn('fbx_maximum_order', 'integer', ['notnull' => false, 'default' => 100000]);
    }

    /**
     * Create fbx_trans_label table
     *
     * @param Schema $schema
     */
    protected function createFundboxCheckoutTransLabelTable(Schema $schema)
    {
        $table = $schema->createTable('fbx_trans_label');
        $table->addColumn('transport_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->setPrimaryKey(['transport_id', 'localized_value_id']);
        $table->addIndex(['transport_id'], 'idx_13476d069909c13f', []);
        $table->addUniqueIndex(['localized_value_id'], 'uniq_13476d06eb576e89');
    }

    /**
     * Create fbx_short_label table
     *
     * @param Schema $schema
     */
    protected function createFundboxCheckoutShortLabelTable(Schema $schema)
    {
        $table = $schema->createTable('fbx_short_label');
        $table->addColumn('transport_id', 'integer', []);
        $table->addColumn('localized_value_id', 'integer', []);
        $table->addUniqueIndex(['localized_value_id'], 'uniq_2c81a8dceb576e89');
        $table->addIndex(['transport_id'], 'idx_2c81a8dc9909c13f', []);
        $table->setPrimaryKey(['transport_id', 'localized_value_id']);
    }

    /**
     * Add fbx_trans_label foreign keys.
     *
     * @param Schema $schema
     */
    protected function addFundboxCheckoutTransLabelForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('fbx_trans_label');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_integration_transport'),
            ['transport_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }

    /**
     * Add fbx_short_label foreign keys.
     *
     * @param Schema $schema
     */
    protected function addFundboxCheckoutShortLabelForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('fbx_short_label');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_fallback_localization_val'),
            ['localized_value_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_integration_transport'),
            ['transport_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'CASCADE']
        );
    }
}
