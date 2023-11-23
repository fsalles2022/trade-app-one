<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHierarchySequenceTrigger extends Migration
{
    public function up()
    {
        DB::unprepared('
            
            CREATE TRIGGER sequence_generator BEFORE INSERT ON `hierarchies`
            
            FOR EACH ROW
            
            BEGIN
                IF (`NEW`.`parent` IS NULL) THEN
                    SET @SEQ = (SELECT COUNT(*) + 1 FROM `hierarchies` WHERE `parent` IS NULL);
                    SET NEW.sequence = @SEQ;
                ELSE
                    SET @SEQ = (SELECT COUNT(*) + 1 FROM `hierarchies` WHERE `parent` = `NEW`.`parent`);
                    SET @SEQ_ID = (SELECT AUTO_INCREMENT FROM `information_schema`.TABLES WHERE TABLE_NAME = \'hierarchies\' AND table_schema = DATABASE());
                    SET @SEQ_PARENT = (SELECT `sequence` FROM `hierarchies` WHERE `id` = `NEW`.`parent`);
                    SET NEW.sequence = CONCAT(@SEQ_PARENT, \'.\', @SEQ);
              END IF;
            END;;
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS sequence_generator');
    }
}
