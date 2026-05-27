<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    protected $table = 'messages';
    protected $fillable = ['conversation_id', 'from_id', 'to_id', 'content', 'type', 'status'];
    
    public function from() {
        return $this->belongsTo(User::class, 'from_id');
    }
    
    public function to() {
        return $this->belongsTo(User::class, 'to_id');
    }
    
    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }
}