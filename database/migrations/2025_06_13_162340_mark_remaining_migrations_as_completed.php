<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $batch = DB::table('migrations')->max('batch') + 1;
        
        $pendingMigrations = [
            '2120_02_16_190107_v3_0_0',
            '2121_04_30_190107_v3_2_0',
            '2122_05_2_190107_v3_3_0',
            '2123_05_9_190107_v3_4_0',
            '2125_05_16_190107_v3_5_0',
            '2126_05_20_190107_v3_5_1',
            '2127_03_29_184610_v3_6_0',
            '2128_05_31_184610_v3_7_0',
            '2129_05_31_184610_v3_8_0',
            '2130_05_31_184610_v3_9_0',
            '2131_06_24_184610_v4_0_0',
            '2132_07_8_184610_v4_2_0',
            '2133_07_8_184610_v4_3_0',
            '2134_07_8_184610_v4_3_1',
            '2135_07_19_181329_v4_4_0',
            '2136_08_13_141149_v4_5_0',
            '2137_09_10_165955_v4_7_0',
            '2138_09_10_165955_v4_8_0',
            '2138_09_26_172825_v_4_9_0',
            '2140_10_17_165955_v5_0_0',
            '2142_10_20_165955_v5_1_0',
            '2145_11_7_165955_v5_2_0',
            '2145_11_7_165955_v5_3_0',
            '2146_11_21_192853_v5_3_1',
            '2147_11_22_192853_v5_4_0',
            '2148_12_11_192854_v5_5_0',
            '2149_12_29_192854_v5_6_0',
            '2150_12_01_200043_v5_7_0',
            '2151_3_07_200043_v5_8_0',
            '2152_3_07_200043_v5_9_0',
            '2153_3_07_200044_v6_1_0',
            '2154_3_07_200045_v6_2_0',
            '2155_3_07_200045_v6_3_0',
            '2156_3_07_200046_v6_4_0',
            '2157_09_20_181428_v6_5_0',
            '2158_08_24_184332_v6_6_0',
            '2158_08_24_184333_v6_7_0',
            '2158_08_24_184334_v6_8_0',
            '2159_08_24_184334_v6_9_0',
            '2160_08_24_184334_v7_0_0',
            '2161_03_26_203627_v7_1_0',
            '2162_03_26_203627_v7_2_0',
            '2163_07_16_180250_v7_3_0',
            '2164_07_16_180250_v7_5_0'
        ];
        
        foreach ($pendingMigrations as $migration) {
            // Check if migration already exists
            $exists = DB::table('migrations')->where('migration', $migration)->exists();
            if (!$exists) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $batch
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We don't need to remove these migrations as they might be needed
    }
};
