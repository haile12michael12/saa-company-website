<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogSectionSettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactSectionSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\FeedbackSectionSettingController;
use App\Http\Controllers\Admin\FooterContactInfoController;
use App\Http\Controllers\Admin\FooterHelpLinkController;
use App\Http\Controllers\Admin\FooterInfoController;
use App\Http\Controllers\Admin\FooterSocialLinkController;
use App\Http\Controllers\Admin\FooterUsefulLinkController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\HeroController;
use App\Http\Controllers\Admin\PortfolioItemController;
use App\Http\Controllers\Admin\PortfolioSectionSetting;
use App\Http\Controllers\Admin\PortfolioSectionSettingController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SeoSettingController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SkillItemController;
use App\Http\Controllers\Admin\SkillSectionSettingController;
use App\Http\Controllers\Admin\TyperTitleContoller;
use App\Http\Controllers\Frontend\HomeController;
use App\Models\FooterInfo;
use App\Models\SkillSectionSetting;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** Frontend Public Routes (Browse without authentication) */

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services.index');
Route::get('/services/{service}', [HomeController::class, 'showService'])->name('services.show');
Route::get('/portfolio', [HomeController::class, 'portfolio'])->name('portfolio.index');
Route::get('/portfolio/{portfolio}', [HomeController::class, 'showPortfolio'])->name('portfolio.show');
Route::get('portfolio-details/{id}', [HomeController::class, 'showPortfolio'])->name('show.portfolio');
Route::get('/blog', [HomeController::class, 'blog'])->name('blog.index');
Route::get('blogs', [HomeController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [HomeController::class, 'showBlog'])->name('blog.show');
Route::get('blog-details/{id}', [HomeController::class, 'showBlog'])->name('show.blog');
Route::get('/contact', [HomeController::class, 'contactPage'])->name('contact.page');
Route::post('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/quote-request', [HomeController::class, 'quoteRequest'])->name('quote.request');
Route::post('/quote-request', [HomeController::class, 'submitQuoteRequest'])->name('quote.submit');
Route::get('/book-consultation', [HomeController::class, 'bookConsultation'])->name('consultation.book');
Route::post('/book-consultation', [HomeController::class, 'submitBookConsultation'])->name('consultation.submit');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq.index');
Route::get('/reviews', [HomeController::class, 'reviews'])->name('reviews.index');
Route::post('/reviews', [HomeController::class, 'submitReview'])->name('reviews.submit');
Route::get('/ai-assistant', [HomeController::class, 'aiAssistant'])->name('ai.assistant');
Route::post('/ai-assistant/chat', [HomeController::class, 'aiAssistantChat'])->name('ai.assistant.chat');
Route::get('resume/download', [AboutController::class, 'resumeDownload'])->name('resume.download');



/** Admin Routes */

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('dashboard',[DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function(){

    /** Hero Route */
    Route::resource('hero', HeroController::class);
    Route::resource('typer-title', TyperTitleContoller::class);

    /** Service Route */
    Route::resource('service', ServiceController::class);

    /** About Route */
    Route::resource('about', AboutController::class);

    /** Portfolio Category Route */
    Route::resource('category', CategoryController::class);

    /** Portfolio Item Route */
    Route::resource('portfolio-item', PortfolioItemController::class);

    /** Portfolio Section Setting Route */
    Route::resource('portfolio-section-setting', PortfolioSectionSettingController::class);

    /** Skill Section Setting Route */
    Route::resource('skill-section-setting', SkillSectionSettingController::class);

    /** Skill Items Route */
    Route::resource('skill-item', SkillItemController::class);

    /** Skill Items Route */
    Route::resource('experience', ExperienceController::class);

    /** Feedback Route */
    Route::resource('feedback', FeedbackController::class);

    /** Feedback Section Setting Route */
    Route::resource('feedback-section-setting', FeedbackSectionSettingController::class);

    /** Blog Category Route */
    Route::resource('blog-category', BlogCategoryController::class);

    /** Blog Route */
    Route::resource('blog', BlogController::class);

    /** Blog Section Setting Route */
    Route::resource('blog-section-setting', BlogSectionSettingController::class);

    /** Contact Section Setting Route */
    Route::resource('contact-section-setting', ContactSectionSettingController::class);

    /** Footer Social Route */
    Route::resource('footer-social', FooterSocialLinkController::class);

    /** Footer Info Route */
    Route::resource('footer-info', FooterInfoController::class);

    /** Footer Contact Info Route */
    Route::resource('footer-contact-info', FooterContactInfoController::class);

    /** Footer Useful Links Route */
    Route::resource('footer-useful-links', FooterUsefulLinkController::class);

    /** Footer Help Links Route */
    Route::resource('footer-help-links', FooterHelpLinkController::class);

    /** Settings Route */
    Route::get('settings', SettingController::class)->name('settings.index');

    /** General setting Route */
    Route::resource('general-setting', GeneralSettingController::class);

    /** Seo setting Route */
    Route::resource('seo-setting', SeoSettingController::class);

});
