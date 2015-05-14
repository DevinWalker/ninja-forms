<?php if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'nf-upgrade-handler-register', 'add_nf_database_migrations', 10, 1 );
function add_nf_database_migrations( $upgrades ) {
    $upgrades[] = new NF_Upgrade_Database_Migrations();
    return $upgrades;
}

final class NF_Upgrade_Database_Migrations extends NF_Upgrade
{

    public $name = 'database_migrations';

    public $priority = "0.0.1";

    public $description = 'The database needs to be updated to support the new version.';

    public function loading()
    {
        $already_run = get_option( 'nf_database_migrations', false );

        $this->total_steps = ( $already_run ) ? 0 : 1;
    }

    public function step( $step )
    {
        $this->createObjectTable();
        $this->createObjectMetaTable();
        $this->createObjectRelationshipsTable();
    }

    public function complete()
    {
        update_option( 'nf_database_migrations', true);
    }

    /*
     * PRIVATE METHODS
     */

    private function createObjectTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . NF_OBJECTS_TABLE_NAME . " (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `type` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;";

        dbDelta( $sql );
    }

    private function createObjectMetaTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS ". NF_OBJECT_META_TABLE_NAME . " (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `object_id` bigint(20) NOT NULL,
        `meta_key` varchar(255) NOT NULL,
        `meta_value` longtext NOT NULL,
        PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;";

        dbDelta( $sql );
    }

    private function createObjectRelationshipsTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . NF_OBJECT_RELATIONSHIPS_TABLE_NAME . " (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `child_id` bigint(20) NOT NULL,
        `parent_id` bigint(20) NOT NULL,
        `child_type` varchar(255) NOT NULL,
        `parent_type` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
        ) DEFAULT CHARSET=utf8;";

        dbDelta( $sql );
    }
}
