<?php

namespace Akk7300\E2eeEncryption\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Crypto\Rsa\KeyPair;

class GenerateKeyPairsCommand extends Command
{
    protected $signature = 'encryption:generate-keys {id? : The ID of the table row}';

    protected $description = 'Generate encryption keys for rows in a specified table based on configuration. If ID is provided, update only that row; otherwise, update all rows with null keys.';

    public function handle()
    {
        $rowId = $this->argument('id');

        $tableName = config('e2ee_encryption.table_name');
        $publicKeyColumn = config('e2ee_encryption.public_key_column');
        $privateKeyColumn = config('e2ee_encryption.private_key_column');

        if (empty($tableName)) {
            $this->error('Please set up your table name in the config file.');

            return;
        }

        if ($rowId) {
            $record = DB::table($tableName)->where('id', $rowId)->first();

            if (! $record) {
                $this->error("No record found with ID: {$rowId}");

                return;
            }

            [$privateKey, $publicKey] = (new KeyPair())->generate();

            DB::table($tableName)
                ->where('id', $rowId)
                ->update([
                    'public_key' => $publicKey,
                    'private_key' => $privateKey,
                    'updated_at' => now(),
                ]);

            $this->info("Encryption keys generated successfully for record with ID: {$rowId} in table: {$tableName}");
        } else {
            $records = DB::table($tableName)->whereNull($publicKeyColumn)->whereNull($privateKeyColumn)->get();

            $records->each(function ($record) use ($tableName) {
                [$privateKey, $publicKey] = (new KeyPair())->generate();

                DB::table($tableName)
                    ->where('id', $record->id)
                    ->update([
                        'public_key' => $publicKey,
                        'private_key' => $privateKey,
                        'updated_at' => now(),
                    ]);
            });

            $this->info('Encryption keys generated successfully for '.count($records).' records in table: '.$tableName);
        }
    }
}
