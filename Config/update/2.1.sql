SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- be2bill_method
-- ---------------------------------------------------------------------

ALTER TABLE `be2bill_method` ADD `data` TEXT AFTER `method`;


SET FOREIGN_KEY_CHECKS = 1;
