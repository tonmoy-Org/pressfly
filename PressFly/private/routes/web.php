<?php

use App\Http\Controllers as Base;
use App\Http\Controllers\Admin as Admin;
use App\Http\Controllers\Member as Member;
use Illuminate\Support\Facades\Route;

Route::get('/ajax-element', function () {
    $element = (string)request()->query('element');

    $elements = [
        'block1',
        'block2',
        'block3',
        'block4',
        'block5',
        'block6',
        'block7',
        'block8',
        'block9',
        'block10',
        'grid1',
        'grid2',
        'grid3',
        'grid4',
        'grid5',
    ];

    if (!in_array($element, $elements)) {
        return null;
    }

    $attributes = [
        'cats' => (string)request()->query('cats', ''),
        'per_page' => (int)request()->query('per_page', 6),
        'order_by' => (string)request()->query('orderby', 'published_at'),
        'order' => (string)request()->query('order', 'desc'),
        'summary_length' => (int)request()->query('excerpt', null),
        'page' => (int)request()->query('page', 1),
        'pagination' => (string)request()->query('pagination', 'numeric'),
    ];

    return call_user_func([\App\Helpers\Elements::class, $element], $attributes);
})->name('ajax.element');

Route::group([], function () {
    // Authentication Routes...
    Route::match(['get', 'post'], '/login', [Base\AuthController::class, 'login'])->name('login');
    Route::get('logout', [Base\AuthController::class, 'logout'])->name('logout');

    // Registration Routes...
    Route::match(['get', 'post'], '/register', [Base\AuthController::class, 'register'])->name('register');

    Route::get('email-verify/{username}/{key}', [Base\AuthController::class, 'emailVerify'])->name('email.verify');

    Route::match(['get', 'post'], '/sms-verify', [Base\AuthController::class, 'smsVerify'])->name('sms.verify');
    Route::get('/sms-resend', [Base\AuthController::class, 'smsResend'])->name('sms.resend');

    Route::match(['get', 'post'], 'password/reset/{username?}/{key?}', [Base\AuthController::class, 'resetPassword'])
        ->name('password.reset');

    Route::get('/auth/{provider}', [Base\AuthController::class, 'redirectToSocialProvider'])->name('social.login');
    Route::get('/auth/{provider}/callback', [Base\AuthController::class, 'handleSocialProviderCallback'])
        ->name('social.callback');
});

// Install Routes
Route::name('')->group(function () {
    Route::get('/install', [Base\InstallController::class, 'index'])->name('install.index');
    Route::match(['get', 'post'], '/install/database', [Base\InstallController::class, 'database'])
        ->name('install.database');
    Route::get('/install/data', [Base\InstallController::class, 'data'])->name('install.data');
    Route::match(['get', 'post'], '/install/admin', [Base\InstallController::class, 'admin'])->name('install.admin');
    Route::get('/install/finish', [Base\InstallController::class, 'finish'])->name('install.finish');
});

// Public Routes
Route::name('')->group(function () {
    Route::get('/', [Base\HomeController::class, 'index'])->name('homepage');

    Route::post('/visitor-check', [Base\VisitorCheckController::class, 'index'])->name('visitor-check');

    Route::get('/feed', [Base\HomeController::class, 'feed'])->name('feed');

    Route::get('/ref/{username}', [Base\UserController::class, 'ref'])->name('referral.url');

    Route::get('/login-as', [Base\UserController::class, 'loginAsUser'])->name('login.as');

    Route::post('/article/go', [Base\ArticleController::class, 'go'])->name('article-go');

    Route::get('/category/{slug}-{category}', [Base\CategoryController::class, 'show'])
        ->where(['slug' => '(.+)', 'category' => '[0-9]+'])->name('category.show');
    Route::get('/category/{slug}-{category}/feed', [Base\CategoryController::class, 'feed'])
        ->where(['slug' => '(.+)', 'category' => '[0-9]+'])->name('category.feed');

    Route::get('/tag/{slug}-{tag}', [Base\TagController::class, 'show'])
        ->where(['slug' => '(.+)', 'tag' => '[0-9]+'])->name('tag.show');
    Route::get('/tag/{slug}-{tag}/feed', [Base\TagController::class, 'feed'])
        ->where(['slug' => '(.+)', 'tag' => '[0-9]+'])->name('tag.feed');

    Route::get('/page/{slug}', [Base\PageController::class, 'show'])->name('page.show');

    Route::post('/comment/store', [Base\CommentController::class, 'store'])->name('comment.add');
    Route::post('/reply/store', [Base\CommentController::class, 'replyStore'])->name('reply.add');

    Route::get('/author/{username?}', [Base\AuthorController::class, 'show'])->name('author.show');
    Route::get('/author/{username}/feed', [Base\AuthorController::class, 'feed'])->name('author.feed');
    Route::post('/author/{username}/follow', [Base\AuthorController::class, 'follow'])->name('author.follow');
    Route::post('/author/{username}/unFollow', [Base\AuthorController::class, 'unFollow'])->name('author.unfollow');

    Route::post('/newsletter/subscribe', [Base\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::match(['get', 'post'], '/search', [Base\SearchController::class, 'index'])->name('search');

    Route::get('/contact', [Base\ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact/process', [Base\ContactController::class, 'process'])->name('contact.process');

    Route::get('/sitemap', [Base\SitemapController::class, 'index'])->name('sitemap');
    Route::get('/sitemap.xml', [Base\SitemapController::class, 'index'])->name('sitemap.xml');

    Route::get('/{slug}-{article}', [Base\ArticleController::class, 'show'])
        ->where(['slug' => '(.+)', 'article' => '[0-9]+'])->name('article.show');

    Route::post('upload/editor', [Base\UploadController::class, 'editor'])->name('upload.editor');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/upgrade', [Admin\UpgradeController::class, 'index'])->name('upgrade');
    Route::post('/upgrade/process', [Admin\UpgradeController::class, 'process'])->name('upgrade.process');

    Route::get('/activation', [Admin\ActivationController::class, 'index'])->name('activation');
    Route::post('/activation/process', [Admin\ActivationController::class, 'process'])->name('activation.process');

    Route::resource('ads', Admin\AdController::class)->except(['show']);

    Route::match(['get', 'post'], '/articles/pay/{article}', [Admin\ArticleController::class, 'pay'])
        ->name('articles.pay');

    Route::match(['get', 'post'], '/articles/index/new-pending', [Admin\ArticleController::class, 'indexNewPending'])
        ->name('articles.indexNewPending');
    Route::get('/articles/{article}/new-pending/edit', [Admin\ArticleController::class, 'newPendingEdit'])
        ->where(['article' => '[0-9]+'])->name('articles.newPendingEdit');
    Route::put('/articles/{article}/new-pending/process', [Admin\ArticleController::class, 'newPendingProcess'])
        ->where(['article' => '[0-9]+'])->name('articles.newPendingProcess');
    Route::match(['get', 'post'],
        '/articles/index/update-pending',
        [Admin\ArticleController::class, 'indexUpdatePending'])->name('articles.indexUpdatePending');
    Route::get('/articles/{article}/update-pending/edit', [Admin\ArticleController::class, 'updatePendingEdit'])
        ->where(['article' => '[0-9]+'])->name('articles.updatePendingEdit');
    Route::put('/articles/{article}/update-pending/process', [Admin\ArticleController::class, 'updatePendingProcess'])
        ->where(['article' => '[0-9]+'])->name('articles.updatePendingProcess');

    Route::resource('articles', Admin\ArticleController::class)->except(['show']);

    Route::resource('comments', Admin\CommentController::class)->except(['show']);

    Route::resource('categories', Admin\CategoryController::class)->except(['show']);
    Route::resource('tags', Admin\TagController::class)->except(['show']);

    Route::get('pages/homepage', [Admin\PageController::class, 'homepage'])->name('pages.homepage');
    Route::post('pages/homepage/store', [Admin\PageController::class, 'homepageStore'])->name('pages.homepage.store');
    Route::resource('pages', Admin\PageController::class)->except(['show']);

    //Route::get('/files', [Admin\FileController::class, 'index'])->name('files.index');
    Route::resource('files', Admin\FileController::class)->except(['show']);

    Route::get('withdraws', [Admin\WithdrawController::class, 'index'])->name('withdraws.index');
    Route::match(['get', 'post'], 'withdraws/methods', [Admin\WithdrawController::class, 'methods'])
        ->name('withdraws.methods');
    Route::get('withdraws/{withdraw}', [Admin\WithdrawController::class, 'show'])->whereNumber('withdraw')
        ->name('withdraws.show');
    Route::post('withdraws/{withdraw}/approve', [Admin\WithdrawController::class, 'approve'])->whereNumber('withdraw')
        ->name('withdraws.approve');
    Route::post('withdraws/{withdraw}/complete', [Admin\WithdrawController::class, 'complete'])->whereNumber('withdraw')
        ->name('withdraws.complete');
    Route::post('withdraws/{withdraw}/cancel', [Admin\WithdrawController::class, 'cancel'])->whereNumber('withdraw')
        ->name('withdraws.cancel');

    Route::get('/menus', [Admin\MenuController::class, 'index'])->name('menus.index');
    Route::post('/add-menu-item', [Admin\MenuController::class, 'addMenuItem'])->name('menu.item.add');
    Route::post('/menus/create', [Admin\MenuController::class, 'create'])->name('menus.create');
    Route::put('/menus/{menu}/edit', [Admin\MenuController::class, 'edit'])->whereNumber('menu')
        ->name('menus.edit');
    Route::delete('/menus/{menu}/destroy', [Admin\MenuController::class, 'destroy'])->whereNumber('menu')
        ->name('menus.destroy');

    Route::get('/sidebars', [Admin\SidebarController::class, 'index'])->name('sidebars.index');
    Route::post('/add-widget', [Admin\SidebarController::class, 'addWidget'])->name('sidebar.widget.add');
    Route::post('/sidebars/create', [Admin\SidebarController::class, 'create'])->name('sidebars.create');
    Route::put('/sidebars/{sidebar}/edit', [Admin\SidebarController::class, 'edit'])->whereNumber('sidebar')
        ->name('sidebars.edit');
    Route::delete('/sidebars/{sidebar}/destroy', [Admin\SidebarController::class, 'destroy'])->whereNumber('sidebar')
        ->name('sidebars.destroy');

    Route::match(['get', 'post'], '/users/referrals', [Admin\UsersController::class, 'referrals'])
        ->name('users.referrals');
    Route::resource('users', Admin\UsersController::class);

    Route::resource('admin-groups', Admin\AdminGroupController::class)->except(['show']);

    Route::match(['get', 'post'], '/options', [Admin\OptionController::class, 'index'])->name('options.index');
    Route::post('/options/test-sms', [Admin\OptionController::class, 'testSms'])->name('options.testSms');
    Route::match(['get', 'post'], '/options/style', [Admin\OptionController::class, 'style'])->name('options.style');
    Route::match(['get', 'post'], '/payout-rates', [Admin\OptionController::class, 'prices'])->name('prices');
    Route::get('/options/system', fn() => view('admin.options.system'))->name('options.system');

    Route::get('/language', [Admin\LanguageController::class, 'index'])->name('language.index');
    Route::post('/language/create', [Admin\LanguageController::class, 'create'])->name('language.create');
    Route::delete('/language/{language}/destroy', [Admin\LanguageController::class, 'destroy'])
        ->name('language.destroy');
    Route::post('/language/{language}/sync', [Admin\LanguageController::class, 'sync'])->name('language.sync');
    Route::post('/translation/update', [Admin\LanguageController::class, 'translationUpdate'])
        ->name('translation.update');
    Route::delete('/translation/delete', [Admin\LanguageController::class, 'translationDelete'])
        ->name('translation.delete');
});

// Member Routes
Route::prefix('member')->name('member.')->middleware(['role:admin,member'])->group(function () {
    Route::get('/', [Member\DashboardController::class, 'index'])->name('dashboard');

    Route::get('feed', [Member\UserController::class, 'feed'])->name('feed');

    Route::resource('articles', Member\ArticleController::class)->except(['show']);

    Route::get('/bookmarks', [Member\BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmark/{article}/add', [Member\BookmarkController::class, 'add'])->name('bookmark.add');
    Route::delete('/bookmark/{article}/remove', [Member\BookmarkController::class, 'remove'])->name('bookmark.remove');

    Route::get('/likes', [Member\LikeController::class, 'index'])->name('likes.index');
    Route::post('/like/{article}/add', [Member\LikeController::class, 'add'])->name('like.add');
    Route::delete('/like/{article}/remove', [Member\LikeController::class, 'remove'])->name('like.remove');

    Route::get('withdraws', [Member\WithdrawController::class, 'index'])->name('withdraws.index');
    Route::post('withdraws/request', [Member\WithdrawController::class, 'request'])->name('withdraws.request');

    Route::get('referrals', [Member\UserController::class, 'referrals'])->name('referrals');

    Route::match(['get', 'post'], 'username', [Member\UserController::class, 'setUsername'])->name('set.username');

    Route::match(['get', 'post'], 'settings', [Member\UserController::class, 'settings'])->name('settings');

    Route::post('email-change', [Member\UserController::class, 'emailChangeRequest'])->name('email.change.request');

    Route::get('email-change/{username}/{key}', [Member\UserController::class, 'emailChangeProcess'])
        ->name('email.change.process');

    Route::post('password-change', [Member\UserController::class, 'passwordChange'])->name('password.change');
});
