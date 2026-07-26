<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Guarded(['id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that should be cast to native types.
     *
     * @see https://laravel.com/docs/master/eloquent-mutators#attribute-casting
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
        'admin_group_id' => 'integer',
        'disable_earnings' => 'integer',
        'referred_by' => 'integer',
        'social_networks' => 'array',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function image(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id')->withDefault();
    }

    public function profileImage(): string
    {
        if (!$this->image->id) {
            $hash = md5(strtolower($this->email));

            return "//2.gravatar.com/avatar/{$hash}?s=150&amp;d=mm&amp;r=g";
        }

        return asset($this->image->thumbnail());
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function statistics(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Statistic::class);
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class);
    }

    public function socialProfiles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SocialProfile::class);
    }

    public function withdraws(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Withdraw::class);
    }

    public function adminGroup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AdminGroup::class);
    }

    /**
     * The third argument is the foreign key name of the model on which you are defining the relationship,
     * while the fourth argument is the foreign key name of the model that you are joining to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'author_id', 'follower_id')->withTimestamps();
    }

    /**
     * The third argument is the foreign key name of the model on which you are defining the relationship,
     * while the fourth argument is the foreign key name of the model that you are joining to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'author_id')->withTimestamps();
    }

    public function bookmarks(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Article::class,'bookmarks','user_id','article_id');
    }

    public function likes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Article::class,'likes','user_id','article_id');
    }

    /**
     * Get the user's social network link.
     *
     * @param string $name
     * @return string
     */
    public function socialNetwork($name)
    {
        $links = $this->social_networks;

        return !empty($links[$name]) ? $links[$name] : '';
    }

    public function permalink(): string
    {
        return route('author.show', ['username' => $this->username]);
    }

    public function feed()
    {
        return route('author.feed', ['username' => $this->username]);
    }

    public function getMetaDescription()
    {
        $description = $this->description;

        return $description;
        //return $this->getTextChars($description, 160, true);
    }
}
