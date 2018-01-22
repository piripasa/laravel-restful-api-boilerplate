<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
	use SoftDeletes;

	protected $fillable = [ 'content' ];

	public function articles () {
		return $this->belongsToMany(Article::class);
	}
}
