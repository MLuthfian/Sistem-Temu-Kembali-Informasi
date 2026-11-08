<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category');
            $table->string('source_label')->nullable();
            $table->string('badge_protocol')->nullable();
            $table->text('image_url')->nullable();
            $table->text('summary')->nullable();
            $table->text('definition')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('causes')->nullable();
            $table->text('self_care_steps')->nullable();
            $table->text('red_flags')->nullable();
            $table->text('when_to_doctor')->nullable();
            $table->text('prevention')->nullable();
            $table->text('references_text')->nullable();
            $table->string('last_reviewed')->nullable();
            $table->timestamps();
        });

        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('authors')->nullable();
            $table->integer('year')->default(2024);
            $table->string('journal_name');
            $table->string('index_rank');
            $table->string('doi_url')->nullable();
            $table->string('topic')->nullable();
            $table->string('license')->nullable();
            $table->string('verified_date')->nullable();
            $table->text('abstract')->nullable();
            $table->integer('chunks_count')->default(0);
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('publisher');
            $table->integer('year')->default(2024);
            $table->string('url')->nullable();
            $table->string('topic')->nullable();
            $table->string('layer')->default('Lapisan 1 (Kemenkes)');
            $table->string('license')->nullable();
            $table->string('verified_date')->nullable();
            $table->string('status')->default('Terverifikasi');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_id')->nullable()->constrained('documents')->onDelete('cascade');
            $table->string('judul_dokumen');
            $table->string('sumber')->nullable();
            $table->string('url')->nullable();
            $table->string('topik')->nullable();
            $table->string('lapisan')->default('Lapisan 1 (Kemenkes)');
            $table->string('lisensi')->nullable();
            $table->integer('tahun')->default(2024);
            $table->text('text');
            $table->timestamps();
        });

        Schema::create('emergency_rules', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->unique();
            $table->string('severity')->default('CRITICAL');
            $table->string('action');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable();
            $table->text('query');
            $table->boolean('is_emergency')->default(false);
            $table->boolean('is_forbidden')->default(false);
            $table->boolean('is_fallback')->default(false);
            $table->string('category')->nullable();
            $table->float('top_score')->default(0.0);
            $table->integer('citations_count')->default(0);
            $table->text('response_preview')->nullable();
            $table->timestamps();
        });

        Schema::create('app_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_configs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('emergency_rules');
        Schema::dropIfExists('chunks');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('journals');
        Schema::dropIfExists('diseases');
    }
};

