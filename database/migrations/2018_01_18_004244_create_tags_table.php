<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
	public function up () {
		Schema::create('tags', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id');
			$table->string('content', 20);
			$table->softDeletes();
			$table->timestamps();
		});
	}

	public function down () {
		Schema::dropIfExists('tags');
	}
}
