<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('media_files', function (Blueprint $table) {
        $table->id();
        $table->string('original_name');
        $table->string('r2_path'); // Stores the path inside the R2 bucket
        $table->string('mime_type'); // e.g., video/mp4, image/jpeg
        $table->unsignedBigInteger('file_size'); // Size in bytes
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
