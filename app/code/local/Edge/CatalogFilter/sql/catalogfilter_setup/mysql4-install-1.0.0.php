<?php

$this->startSetup();

$this->run("
    ALTER TABLE {$this->getTable('eav/attribute')}
        ADD COLUMN `filter_type` ENUM('single', 'multiple') DEFAULT NULL
");

$this->endSetup();