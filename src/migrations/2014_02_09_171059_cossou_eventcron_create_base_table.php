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
			$table->string('queue', 30)->index();
			$table->date('date');

			$table->date('runned_at');

			$table->string('started_at', 18);
			$table->string('ended_at', 18);

			$table->text('arguments');
			$table->boolean('processed')->default(FALSE);

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