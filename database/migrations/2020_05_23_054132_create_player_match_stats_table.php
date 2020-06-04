<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerMatchStatsTable extends Migration
{
	public function up()
	{
		Schema::create('player_match_stats', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->timestamps();

			$table->string('side');
			$table->string('phase');

			$table->integer('enemy_kills');
			$table->integer('enemy_assists');
			$table->integer('enemy_flash_assists');
			$table->integer('deaths');
			$table->integer('deaths_traded');
			$table->integer('enemy_damage');
			$table->integer('enemy_utility_damage');
			$table->integer('enemy_trade_kills');
			$table->integer('enemy_headshot_kills');
			$table->integer('kast_rounds');
			$table->integer('0_kill_rounds')->unsigned();
			$table->integer('1_kill_rounds')->unsigned();
			$table->integer('2_kill_rounds')->unsigned();
			$table->integer('3_kill_rounds')->unsigned();
			$table->integer('4_kill_rounds')->unsigned();
			$table->integer('5_kill_rounds')->unsigned();
			$table->integer('enemies_flashed')->unsigned();
			$table->double('enemies_flashed_duration', 8, 4)->unsigned();
			$table->integer('plants')->unsigned();
			$table->integer('defuses')->unsigned();
			$table->integer('mvps')->unsigned();
			$table->bigInteger('time_alive_ms')->unsigned();
			$table->bigInteger('max_alive_time_ms')->unsigned();

			$table->integer('team_kills');
			$table->integer('team_assists');
			$table->integer('team_flash_assists');
			$table->integer('team_damage');
			$table->integer('teammates_flashed')->unsigned();
			$table->double('teammates_flashed_duration', 8, 4)->unsigned();

			$table->integer('one_vs_1_scenarios')->default(0);
			$table->integer('one_vs_1_wins')->default(0);
			$table->integer('one_vs_1_kills')->default(0);
			$table->integer('one_vs_1_deaths')->default(0);

			$table->integer('one_vs_2_scenarios')->default(0);
			$table->integer('one_vs_2_wins')->default(0);
			$table->integer('one_vs_2_kills')->default(0);
			$table->integer('one_vs_2_deaths')->default(0);

			$table->integer('one_vs_3_scenarios')->default(0);
			$table->integer('one_vs_3_wins')->default(0);
			$table->integer('one_vs_3_kills')->default(0);
			$table->integer('one_vs_3_deaths')->default(0);

			$table->integer('one_vs_4_scenarios')->default(0);
			$table->integer('one_vs_4_wins')->default(0);
			$table->integer('one_vs_4_kills')->default(0);
			$table->integer('one_vs_4_deaths')->default(0);

			$table->integer('one_vs_5_scenarios')->default(0);
			$table->integer('one_vs_5_wins')->default(0);
			$table->integer('one_vs_5_kills')->default(0);
			$table->integer('one_vs_5_deaths')->default(0);

			$table->integer('enemy_kills_ak47')->default(0);
			$table->integer('enemy_kills_aug')->default(0);
			$table->integer('enemy_kills_awp')->default(0);
			$table->integer('enemy_kills_axe')->default(0);
			$table->integer('enemy_kills_bayonet')->default(0);
			$table->integer('enemy_kills_bizon')->default(0);
			$table->integer('enemy_kills_cz75a')->default(0);
			$table->integer('enemy_kills_deagle')->default(0);
			$table->integer('enemy_kills_elite')->default(0);
			$table->integer('enemy_kills_famas')->default(0);
			$table->integer('enemy_kills_fists')->default(0);
			$table->integer('enemy_kills_fiveseven')->default(0);
			$table->integer('enemy_kills_g3sg1')->default(0);
			$table->integer('enemy_kills_galilar')->default(0);
			$table->integer('enemy_kills_glock')->default(0);
			$table->integer('enemy_kills_hammer')->default(0);
			$table->integer('enemy_kills_hegrenade')->default(0);
			$table->integer('enemy_kills_hkp2000')->default(0);
			$table->integer('enemy_kills_inferno')->default(0);
			$table->integer('enemy_kills_knife_bowie')->default(0);
			$table->integer('enemy_kills_knife_butterfly')->default(0);
			$table->integer('enemy_kills_knife_canis')->default(0);
			$table->integer('enemy_kills_knife_cord')->default(0);
			$table->integer('enemy_kills_knife_css')->default(0);
			$table->integer('enemy_kills_knife_falchion')->default(0);
			$table->integer('enemy_kills_knife_flip')->default(0);
			$table->integer('enemy_kills_knife_gut')->default(0);
			$table->integer('enemy_kills_knife_gypsy_jackknife')->default(0);
			$table->integer('enemy_kills_knife_karambit')->default(0);
			$table->integer('enemy_kills_knife_m9_bayonet')->default(0);
			$table->integer('enemy_kills_knife_outdoor')->default(0);
			$table->integer('enemy_kills_knife_push')->default(0);
			$table->integer('enemy_kills_knife_skeleton')->default(0);
			$table->integer('enemy_kills_knife_stiletto')->default(0);
			$table->integer('enemy_kills_knife_survival_bowie')->default(0);
			$table->integer('enemy_kills_knife_t')->default(0);
			$table->integer('enemy_kills_knife_tactical')->default(0);
			$table->integer('enemy_kills_knife_ursus')->default(0);
			$table->integer('enemy_kills_knife_widowmaker')->default(0);
			$table->integer('enemy_kills_knife')->default(0);
			$table->integer('enemy_kills_knifegg')->default(0);
			$table->integer('enemy_kills_m4a1_silencer_off')->default(0);
			$table->integer('enemy_kills_m4a1_silencer')->default(0);
			$table->integer('enemy_kills_m4a1')->default(0);
			$table->integer('enemy_kills_m249')->default(0);
			$table->integer('enemy_kills_mac10')->default(0);
			$table->integer('enemy_kills_mag7')->default(0);
			$table->integer('enemy_kills_mp5sd')->default(0);
			$table->integer('enemy_kills_mp7')->default(0);
			$table->integer('enemy_kills_mp9')->default(0);
			$table->integer('enemy_kills_negev')->default(0);
			$table->integer('enemy_kills_nova')->default(0);
			$table->integer('enemy_kills_p90')->default(0);
			$table->integer('enemy_kills_p250')->default(0);
			$table->integer('enemy_kills_revolver')->default(0);
			$table->integer('enemy_kills_sawedoff')->default(0);
			$table->integer('enemy_kills_scar20')->default(0);
			$table->integer('enemy_kills_sg556')->default(0);
			$table->integer('enemy_kills_snowball')->default(0);
			$table->integer('enemy_kills_spanner')->default(0);
			$table->integer('enemy_kills_ssg08')->default(0);
			$table->integer('enemy_kills_taser')->default(0);
			$table->integer('enemy_kills_tec9')->default(0);
			$table->integer('enemy_kills_ump45')->default(0);
			$table->integer('enemy_kills_usp_silencer_off')->default(0);
			$table->integer('enemy_kills_usp_silencer')->default(0);
			$table->integer('enemy_kills_world')->default(0);
			$table->integer('enemy_kills_worldspawn')->default(0);
			$table->integer('enemy_kills_xm1014')->default(0);

			$table->uuid('match_id')->index();
			$table->foreign('match_id')->references('id')->on('matches')->onDelete('cascade');

			$table->uuid('player_id')->index();
			$table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
		});
	}

	public function down()
	{
		Schema::dropIfExists('player_match_stats');
	}
}
