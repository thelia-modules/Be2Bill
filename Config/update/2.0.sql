
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- be2bill_transaction
-- ---------------------------------------------------------------------

ALTER TABLE `be2bill_transaction` ADD `method_name` VARCHAR(255) DEFAULT '' AFTER `transaction_id`;
ALTER TABLE `be2bill_transaction` ADD `transaction` TEXT AFTER `cardtype`;

-- ---------------------------------------------------------------------
-- be2bill_method
-- ---------------------------------------------------------------------

CREATE TABLE `be2bill_method`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `order_id` INTEGER NOT NULL,
    `method` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `FI_be2bill_method_order_id` (`order_id`),
    CONSTRAINT `fk_be2bill_method_order_id`
        FOREIGN KEY (`order_id`)
        REFERENCES `order` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
