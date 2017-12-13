<?php
/**
 * Created by PhpStorm.
 * User: Jie
 * Date: 2017/11/21
 * Time: 下午9:45
 */

// mysql数据库配置
$mysql = [
	'mysql_server_name' => 'mysql',
	'mysql_username' => 'root',
	'mysql_password' => 'dell_456',
	'mysql_database' => 'local',
];

$params = [
	// 个人所得税系数配置
	'tax_table' => [
		[ 'tax' => 80000, 'percent' => 0.45 ],
		[ 'tax' => 55000, 'percent' => 0.35],
		[ 'tax' => 35000, 'percent' => 0.3],
		[ 'tax' => 9000, 'percent' => 0.25],
		[ 'tax' => 4500, 'percent' => 0.2],
		[ 'tax' => 1500, 'percent' => 0.1],
		[ 'tax' => 0, 'percent' => 0.03],
	],
	// 公积金系数配置,现放弃,使用前台录入
	'housing_fund' => [
		'company' => 0.08,
		'person' => 0.08,
	],
	// 养老金系数配置
	'pension' => [
		'company' => 0.2,
		'person' => 0.08,
	],
	// 医疗保险系数配置
	'care' => [
		'company' => 0.1,
		'person' => 0.023,
	],

	// 统计系数,
	'staticsist' => 1 / 3,
];
