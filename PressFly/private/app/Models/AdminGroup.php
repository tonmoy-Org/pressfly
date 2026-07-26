<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminGroup extends Model
{
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @see https://laravel.com/docs/master/eloquent-mutators#attribute-casting
     *
     * @var array
     */
    protected $casts = [
        'permissions' => 'array',
    ];

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public static function getAllPermissions(): array
    {
        return [
            'super_admin' => 'admin.dashboard',
            'dashboard' => 'admin.dashboard',
            'article_view' => 'admin.articles.index',
            'article_create' => 'admin.articles.index',
            'article_edit' => 'admin.articles.index',
            'article_delete' => 'admin.articles.index',
            'article_new_approve' => 'admin.articles.indexNewPending',
            'article_update_approve' => 'admin.articles.indexUpdatePending',
            'comment_view' => 'admin.comments.index',
            'comment_edit' => 'admin.comments.index',
            'comment_delete' => 'admin.comments.index',
            'category_view' => 'admin.categories.index',
            'category_create' => 'admin.categories.index',
            'category_edit' => 'admin.categories.index',
            'category_delete' => 'admin.categories.index',
            'tag_view' => 'admin.tags.index',
            'tag_create' => 'admin.tags.index',
            'tag_edit' => 'admin.tags.index',
            'tag_delete' => 'admin.tags.index',
            'page_view' => 'admin.pages.index',
            'page_create' => 'admin.pages.index',
            'page_edit' => 'admin.pages.index',
            'page_delete' => 'admin.pages.index',
            'file_view' => 'admin.files.index',
            'file_upload' => 'admin.files.index',
            'file_edit' => 'admin.files.index',
            'file_delete' => 'admin.files.index',
            'homepage' => 'admin.pages.homepage',
            'menu' => 'admin.menus.index',
            'sidebar' => 'admin.sidebars.index',
            'ad_view' => 'admin.ads.index',
            'ad_create' => 'admin.ads.index',
            'ad_edit' => 'admin.ads.index',
            'ad_delete' => 'admin.ads.index',
            'payout_rates' => 'admin.prices',
            'withdrawal_requests_view' => 'admin.withdraws.index',
            'withdrawal_requests_manage' => 'admin.withdraws.index',
            'withdrawal_methods' => 'admin.withdraws.methods',
            'user_view' => 'admin.users.index',
            'user_create' => 'admin.users.index',
            'user_edit' => 'admin.users.index',
            'user_delete' => 'admin.users.index',
            'user_referrals' => 'admin.users.referrals',
            'settings' => 'admin.options.index',
            'settings_style' => 'admin.options.style',
            'settings_language' => 'admin.language.index',
            'settings_system_info' => 'admin.options.system',
        ];
    }
}
