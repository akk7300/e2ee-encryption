<?php

namespace Akk7300\E2eeEncryption\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Spatie\Crypto\Rsa\KeyPair;

class GenerateKeyPairsCommand extends Command
{
    protected $signature = 'encryption:generate-keys';

    protected $description = 'Generate encryption keys for all rows in a specified table based on configuration';

    public function handle()
    {
        $tableName = Config::get('e2ee_encryption.table_name');

        if (empty($tableName)) {
            $this->error('Please set up your table name in the config file.');

            return;
        }

        $records = DB::table($tableName)->whereNull('public_key')->whereNull('private_key')->get();

        $records->each(function ($record) use ($tableName) {
            $keyPair = (new KeyPair())->generate();

            DB::table($tableName)
                ->where('id', $record->id)
                ->update([
                    'public_key' => $keyPair->getPublicKey(),
                    'private_key' => $keyPair->getPrivateKey(),
                ]);
        });

        $this->info('Encryption keys generated successfully for '.count($records).' records in table: '.$tableName);
    }
}
