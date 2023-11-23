<?php

use Illuminate\Database\Seeder;

class RestoreDatabaseDumpSeeder extends Seeder
{
    public function run()
    {
        $DUMP_FILE_PATH = $this->getDumpFilePath();

        if(is_null($DUMP_FILE_PATH)) {
            return;
        }

        $DB_USERNAME    = env('DB_USERNAME');
        $DB_HOST        = env('DB_HOST');
        $DB_PASSWORD    = env('DB_PASSWORD');
        $DB_DATABASE    = env('DB_DATABASE');

        $dumpCommand =
            "mysql " .
            "--host='${DB_HOST}' " .
            "--user='${DB_USERNAME}' " .
            "--password='${DB_PASSWORD}' " .
            "'${DB_DATABASE}' < '${DUMP_FILE_PATH}'";

        exec($dumpCommand);
    }

    private function getDumpFilePath() : ?string
    {
        $databasePath = database_path();
        $dumpPath     = '/dump/backup.sql';
        $dumpFilePath = $databasePath . $dumpPath;

        if (!file_exists($dumpFilePath)) {
            return null;
        }

        return $dumpFilePath;
    }

}