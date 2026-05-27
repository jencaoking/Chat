<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'password', 'avatar', 'role', 'status'];
    protected $hidden = ['password'];
    
    public function conversations() {
        return $this->belongsToMany(Conversation::class, 'conversation_user', 'user_id', 'conversation_id');
    }
    
    public function messages() {
        return $this->hasMany(Message::class, 'from_id');
    }
}