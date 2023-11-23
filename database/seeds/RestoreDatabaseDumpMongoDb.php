<?php

use Illuminate\Database\Seeder;

class RestoreDatabaseDumpMongoDb extends Seeder
{
    public function run()
    {
        $DUMP_FOLDER = $this->getDumpFilePath();

        if (is_null($DUMP_FOLDER)) {
            return;
        }

        $MONGO_TARGET_HOST = env('DB_MONGO_HOST');
        $MONGO_TARGET_PORT = env('DB_MONGO_PORT');
        $MONGO_TARGET_DB   = env('DB_DATABASE');

        $dumpCommand =
            "mongorestore ".
                "--host ${MONGO_TARGET_HOST} " .
                "--port ${MONGO_TARGET_PORT} ".
                "--db ${MONGO_TARGET_DB} ".
                "${DUMP_FOLDER} " .
                "--drop > /dev/null";

        exec($dumpCommand);
    }

    private function getDumpFilePath() : ?string
    {
        $databasePath = database_path();
        $dumpPath     = '/dump/mongo';
        $dumpFilePath = $databasePath . $dumpPath;

        if (! file_exists($dumpFilePath)) {
            return null;
        }

        return $dumpFilePath;
    }
}
