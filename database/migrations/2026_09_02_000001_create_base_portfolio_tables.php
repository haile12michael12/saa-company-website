<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // Password reset tokens
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // Sessions table
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        // General settings
        if (!Schema::hasTable('general_settings')) {
            Schema::create('general_settings', function (Blueprint $table) {
                $table->id();
                $table->string('logo')->nullable();
                $table->string('footer_logo')->nullable();
                $table->string('favicon')->nullable();
                $table->timestamps();
            });
        }

        // SEO settings
        if (!Schema::hasTable('seo_settings')) {
            Schema::create('seo_settings', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->text('keywords')->nullable();
                $table->timestamps();
            });
        }

        // Heroes
        if (!Schema::hasTable('heroes')) {
            Schema::create('heroes', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('sub_title')->nullable();
                $table->string('btn_text')->nullable();
                $table->string('btn_url')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }

        // Typer titles
        if (!Schema::hasTable('typer_titles')) {
            Schema::create('typer_titles', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->timestamps();
            });
        }

        // Services
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable()->unique();
                $table->string('icon')->nullable();
                $table->string('image')->nullable();
                $table->text('description');
                $table->longText('long_description')->nullable();
                $table->json('features')->nullable();
                $table->string('price')->nullable();
                $table->timestamps();
            });
        }

        // Abouts
        if (!Schema::hasTable('abouts')) {
            Schema::create('abouts', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->longText('description')->nullable();
                $table->string('image')->nullable();
                $table->string('resume')->nullable();
                $table->timestamps();
            });
        }

        // Categories for portfolio
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // Portfolio items
        if (!Schema::hasTable('portfolio_items')) {
            Schema::create('portfolio_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->string('image')->nullable();
                $table->string('title');
                $table->string('slug')->nullable()->unique();
                $table->longText('description');
                $table->string('client')->nullable();
                $table->string('website')->nullable();
                $table->timestamps();
            });
        }

        // Portfolio section settings
        if (!Schema::hasTable('portfolio_section_settings')) {
            Schema::create('portfolio_section_settings', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('sub_title')->nullable();
                $table->timestamps();
            });
        }

        // Skill section settings
        if (!Schema::hasTable('skill_section_settings')) {
            Schema::create('skill_section_settings', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('sub_title')->nullable();
                $table->timestamps();
            });
        }

        // Skill items
        if (!Schema::hasTable('skill_items')) {
            Schema::create('skill_items', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->integer('percent')->default(0);
                $table->timestamps();
            });
        }

        // Experiences
        if (!Schema::hasTable('experienaces')) {
            Schema::create('experienaces', function (Blueprint $table) {
                $table->id();
                $table->string('image')->nullable();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->timestamps();
            });
        }

        // Feedbacks / Reviews
        if (!Schema::hasTable('feedback')) {
            Schema::create('feedback', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('position')->nullable();
                $table->text('description');
                $table->unsignedTinyInteger('rating')->default(5);
                $table->boolean('is_featured')->default(true);
                $table->timestamps();
            });
        }

        // Feedback section settings
        if (!Schema::hasTable('feedback_section_settings')) {
            Schema::create('feedback_section_settings', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('sub_title')->nullable();
                $table->timestamps();
            });
        }

        // Blog categories
        if (!Schema::hasTable('blog_categories')) {
            Schema::create('blog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // Blogs
        if (!Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category')->constrained('blog_categories')->cascadeOnDelete();
                $table->string('image')->nullable();
                $table->string('title');
                $table->string('slug')->nullable()->unique();
                $table->longText('description');
                $table->timestamps();
            });
        }

        // Blog section settings
        if (!Schema::hasTable('blog_section_settings')) {
            Schema::create('blog_section_settings', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('sub_title')->nullable();
                $table->timestamps();
            });
        }

        // Contact section settings
        if (!Schema::hasTable('contact_section_settings')) {
            Schema::create('contact_section_settings', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->text('sub_title')->nullable();
                $table->timestamps();
            });
        }

        // Footer social links
        if (!Schema::hasTable('footer_social_links')) {
            Schema::create('footer_social_links', function (Blueprint $table) {
                $table->id();
                $table->string('icon');
                $table->string('url');
                $table->timestamps();
            });
        }

        // Footer info
        if (!Schema::hasTable('footer_infos')) {
            Schema::create('footer_infos', function (Blueprint $table) {
                $table->id();
                $table->text('info')->nullable();
                $table->string('copy_right')->nullable();
                $table->string('powered_by')->nullable();
                $table->timestamps();
            });
        }

        // Footer contact info
        if (!Schema::hasTable('footer_contact_infos')) {
            Schema::create('footer_contact_infos', function (Blueprint $table) {
                $table->id();
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->timestamps();
            });
        }

        // Footer useful links
        if (!Schema::hasTable('footer_useful_links')) {
            Schema::create('footer_useful_links', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('url');
                $table->timestamps();
            });
        }

        // Footer help links
        if (!Schema::hasTable('footer_help_links')) {
            Schema::create('footer_help_links', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('url');
                $table->timestamps();
            });
        }

        // FAQs table
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category')->default('General');
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('footer_help_links');
        Schema::dropIfExists('footer_useful_links');
        Schema::dropIfExists('footer_contact_infos');
        Schema::dropIfExists('footer_infos');
        Schema::dropIfExists('footer_social_links');
        Schema::dropIfExists('contact_section_settings');
        Schema::dropIfExists('blog_section_settings');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('feedback_section_settings');
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('experienaces');
        Schema::dropIfExists('skill_items');
        Schema::dropIfExists('skill_section_settings');
        Schema::dropIfExists('portfolio_section_settings');
        Schema::dropIfExists('portfolio_items');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('abouts');
        Schema::dropIfExists('services');
        Schema::dropIfExists('typer_titles');
        Schema::dropIfExists('heroes');
        Schema::dropIfExists('seo_settings');
        Schema::dropIfExists('general_settings');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
