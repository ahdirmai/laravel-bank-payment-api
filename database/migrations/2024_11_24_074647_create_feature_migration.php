<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create 'provinsi' table
        Schema::create('provinsi', function (Blueprint $table) {
            $table->string('kodeprovinsi', 2)->primary();
            $table->string('namaprovinsi');
            $table->string('userinput')->nullable();
            $table->timestamp('tglinput')->nullable();
            $table->string('userupdate')->nullable();
            $table->timestamp('tglupdate')->nullable();
        });

        // Create 'kabupaten' table
        Schema::create('kabupaten', function (Blueprint $table) {
            $table->id();
            $table->string('kodeprovinsi', 2);
            $table->string('kodekabupaten', 2)->index(); // Adding an index to `kodekabupaten`
            $table->string('namakabupaten');
            $table->string('jeniskabupaten');
            $table->string('userinput')->nullable();
            $table->timestamp('tglinput')->nullable();
            $table->string('userupdate')->nullable();
            $table->timestamp('tglupdate')->nullable();

            $table->foreign('kodeprovinsi')->references('kodeprovinsi')->on('provinsi');
        });

        // Create 'kecamatan' table
        Schema::create('kecamatan', function (Blueprint $table) {
            $table->id();
            $table->string('kodekecamatan', 2)->index(); // Adding an index to `kodekecamatan`
            $table->string('namakecamatan');
            $table->string('kodekabupaten', 2);
            $table->string('kodeprovinsi', 2);
            $table->string('userinput')->nullable();
            $table->timestamp('tglinput')->nullable();
            $table->string('userupdate')->nullable();
            $table->timestamp('tglupdate')->nullable();

            $table->foreign('kodeprovinsi')->references('kodeprovinsi')->on('provinsi');
            $table->foreign('kodekabupaten')->references('kodekabupaten')->on('kabupaten');
        });

        // Create 'kelurahan' table
        Schema::create('kelurahan', function (Blueprint $table) {
            $table->id();
            $table->string('kodekelurahan', 4);
            $table->string('namakelurahan');
            $table->string('kodekecamatan', 2);
            $table->string('kodekabupaten', 2);
            $table->string('kodeprovinsi', 2);
            $table->string('jeniskelurahan');
            $table->timestamp('tglinput')->nullable();
            $table->string('userinput')->nullable();
            $table->timestamp('tglupdate')->nullable();
            $table->string('userupdate')->nullable();

            $table->foreign('kodeprovinsi')->references('kodeprovinsi')->on('provinsi');
            $table->foreign('kodekabupaten')->references('kodekabupaten')->on('kabupaten');
            $table->foreign('kodekecamatan')->references('kodekecamatan')->on('kecamatan');
        });

        // Create 'jenispajak' table
        Schema::create('jenispajak', function (Blueprint $table) {
            $table->string('kodepajak', 10)->primary();
            $table->string('namapajak');
            $table->timestamp('tglinput');
            $table->string('userinput');
            $table->timestamp('tglupdate')->nullable();
            $table->string('userupdate')->nullable();
            $table->string('modedata', 1);
            $table->string('metode', 1);
        });

        // Create 'objekpajak' table
        Schema::create('objekpajak', function (Blueprint $table) {
            $table->id();
            $table->string('kodeobjek', 2);
            $table->string('kodepajak', 10);
            $table->string('namaobjek');
            $table->timestamp('tglinput');
            $table->string('userinput');
            $table->timestamp('tglupdate')->nullable();
            $table->string('userupdate')->nullable();
            $table->string('modedata', 1);
            $table->decimal('persenpajak', 5, 2);

            $table->foreign('kodepajak')->references('kodepajak')->on('jenispajak');
        });

        // Create 'wajibpajak' table
        Schema::create('wajibpajak', function (Blueprint $table) {
            $table->string('npwpd', 10)->primary();
            $table->string('namawpd');
            $table->string('nik', 16);
            $table->string('namalkp');
            $table->string('alamat');
            $table->string('kodedesa', 4);
            $table->string('kodekec', 2);
            $table->string('kodekab', 2);
            $table->string('kodeprov', 2);
            $table->string('jenisw');
            $table->string('npwp', 15);
            $table->date('tgldaftar');
            $table->string('fotodok');
            $table->timestamp('tglinput');
            $table->string('userinput');
            $table->timestamp('tglupdate')->nullable();
            $table->string('userupdate')->nullable();
        });

        // Create 'dataobjekpajak' table
        Schema::create('dataobjekpajak', function (Blueprint $table) {
            $table->string('nop', 8)->primary();
            $table->string('namaobjekpajak');
            $table->string('alamat');
            $table->string('kodedesa', 4);
            $table->string('kodekec', 2);
            $table->string('notlp')->nullable();
            $table->decimal('luastempat', 10, 2);
            $table->string('statusmilik');
            $table->string('kodepajak', 10);
            $table->string('kodeobjekpajak', 2);
            $table->string('npwpd', 10);
            $table->date('tmtoperasi');
            $table->string('noppbb')->nullable();
            $table->timestamp('tglinput');
            $table->string('userinput');
            $table->timestamp('tglupdate')->nullable();
            $table->string('userupdate')->nullable();
            $table->string('fotoobjekpajak');

            $table->foreign('kodepajak')->references('kodepajak')->on('jenispajak');
            $table->foreign('npwpd')->references('npwpd')->on('wajibpajak');
        });

        // Create 'sptpd' table
        Schema::create('sptpd', function (Blueprint $table) {
            $table->string('nosptpd', 19)->primary();
            $table->string('npwpd', 10);
            $table->string('nop', 8);
            $table->string('kodepajak', 10);
            $table->string('kodeobjek', 2);
            $table->year('tahun');
            $table->string('bulan', 2);
            $table->text('uraian');
            $table->date('tgllapor');
            $table->date('tgltempo')->nullable();
            $table->string('userinput');
            $table->timestamp('tglinput');

            $table->foreign('npwpd')->references('npwpd')->on('wajibpajak');
            $table->foreign('nop')->references('nop')->on('dataobjekpajak');
            $table->foreign('kodepajak')->references('kodepajak')->on('jenispajak');
        });

        // Create 'skpd' table
        Schema::create('skpd', function (Blueprint $table) {
            $table->string('nosptpd', 19)->primary();
            $table->date('tglsptpd');
            $table->date('tglskpd');
            $table->string('kodepajak', 10);
            $table->string('kodeobjek', 2);
            $table->string('nop', 8);
            $table->string('npwpd', 10);
            $table->string('masapajak', 7);
            $table->decimal('nilaiomzet', 12, 2);
            $table->decimal('persenpajak', 5, 2);
            $table->decimal('nilaipajak', 12, 2);
            $table->timestamp('tglinput');
            $table->timestamp('tglupdate')->nullable();
            $table->string('userinput')->nullable();
            $table->string('userupdate')->nullable();

            $table->foreign('nosptpd')->references('nosptpd')->on('sptpd');
            $table->foreign('npwpd')->references('npwpd')->on('wajibpajak');
            $table->foreign('nop')->references('nop')->on('dataobjekpajak');
            $table->foreign('kodepajak')->references('kodepajak')->on('jenispajak');
        });

        // Create 'sspd' table
        Schema::create('sspd', function (Blueprint $table) {
            // $table->string('noss', 15)->primary();
            $table->string('nosptpd', 19)->primary();
            $table->string('npwpd', 10);
            $table->date('tglss');
            $table->date('masapajak');
            $table->decimal('nilaiomzet', 12, 2);
            $table->decimal('persenpajak', 5, 2);
            $table->decimal('nilaipajak', 12, 2);
            $table->decimal('nilaidenda', 12, 2);
            $table->decimal('nilaipokok', 12, 2);
            $table->string('userinput');
            $table->timestamp('tglinput');
            $table->timestamp('tglupdate')->nullable();
            $table->string('userupdate')->nullable();

            $table->foreign('nosptpd')->references('nosptpd')->on('sptpd');
            $table->foreign('npwpd')->references('npwpd')->on('wajibpajak');
        });
    }

    public function down()
    {
        // Drop tables in reverse order of creation
        Schema::dropIfExists('sspd');
        Schema::dropIfExists('skpd');
        Schema::dropIfExists('sptpd');
        Schema::dropIfExists('dataobjekpajak');
        Schema::dropIfExists('wajibpajak');
        Schema::dropIfExists('objekpajak');
        Schema::dropIfExists('jenispajak');
        Schema::dropIfExists('kelurahan');
        Schema::dropIfExists('kecamatan');
        Schema::dropIfExists('kabupaten');
        Schema::dropIfExists('provinsi');
    }
};
