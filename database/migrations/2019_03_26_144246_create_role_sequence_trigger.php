<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRoleSequenceTrigger extends Migration
{
    public function up()
    {
        DB::unprepared('
            
            CREATE TRIGGER role_sequence_generator BEFORE INSERT ON `roles`
            
            FOR EACH ROW
            
            BEGIN
                IF (`NEW`.`parent` IS NULL) THEN
                    SET @SEQ = (SELECT COUNT(*) + 1 FROM `roles` WHERE `parent` IS NULL);
                    SET NEW.sequence = @SEQ;
                ELSE
                    SET @SEQ = (SELECT COUNT(*) + 1 FROM `roles` WHERE `parent` = `NEW`.`parent`);
                    SET @SEQ_PARENT = (SELECT `sequence` FROM `roles` WHERE `id` = `NEW`.`parent`);
                    SET NEW.sequence = CONCAT(@SEQ_PARENT, \'.\', @SEQ);
              END IF;
            END;;
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS role_sequence_generator');
    }
}
