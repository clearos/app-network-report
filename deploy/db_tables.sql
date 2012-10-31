DROP TABLE IF EXISTS `network`;
CREATE TABLE `network` (
    `id` bigint(20) unsigned NOT NULL auto_increment,
    `iface` varchar(16) NOT NULL,
    `rx_bytes` bigint default NULL,
    `rx_packets` bigint default NULL,
    `rx_errors` bigint default NULL,
    `rx_drop` bigint default NULL,
    `rx_rate` bigint default NULL,
    `tx_bytes` bigint default NULL,
    `tx_packets` bigint default NULL,
    `tx_errors` bigint default NULL,
    `tx_drop` bigint default NULL,
    `tx_rate` bigint default NULL,
    `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX(iface),
    INDEX(timestamp)
) ENGINE=innodb DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
