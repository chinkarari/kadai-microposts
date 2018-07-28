<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        // すでにフォローしているか確認
        $exist = $this->is_following($userId);
        // 自分自身でないかの確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me)
        {
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            $this->followings()->detach($userId);
            return true;
        } else {
            return false;
        }
    }
    
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::WhereIn('user_id', $follow_user_ids);
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'micropost_favorite', 'user_id', 'post_id')->withTimestamps();
    }
    
    public function favorite($postId)
    {
        // すでにお気に入りに登録しているかの確認
        $exist = $this->is_favorite($postId);
        
        if ($exist) {
            // すでに起きに入りに登録している場合、何もしない。
            return false;
        } else {
            // お気に入りに登録していなければ登録する
            $this->favorites()->attach($postId);
            return true;
        }
    }
    
    public function unfavorite($postId)
    {
        // すでにお気に入りに登録しているかの確認
        $exist = $this->is_favorite($postId);
        
        if ($exist) {
            // すでにお気に入りの場合、外す
            $this->favorites()->detach($postId);
            return true;
        } else {
            // お気に入りでない場合、そのまま
            return false;
        }
    }
    
    public function is_favorite($postId)
    {
        return $this->favorites()->where('post_id', $postId)->exists();
    }
}
