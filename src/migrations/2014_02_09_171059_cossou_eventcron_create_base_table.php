<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CossouEventcronCreateBaseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('eventcron', function (Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->increments('id');

			$table->string('event', 64)->index();
			$table->text('arguments');
			$table->dateTime('execute_at')->default('0000-00-00 00:00:00');

			$table->dateTime('started_at')->nullable();
			$table->dateTime('ended_at')->nullable();

			$table->tinyInteger('status')->default(0);

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('eventcron');
	}

}