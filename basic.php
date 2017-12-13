<?php
error_reporting(E_ALL);

require('./service/basicService.php');
require('./config/config.php');
// action
if ($_REQUEST['action'] == 'calc') {
	// 验证
	if (!$_POST['cash'] ||
		!$_POST['percent'] ||
		!( !is_nan($_POST['percent']) && intval($_POST['percent']) > 0 && intval($_POST['percent']) < 100 ) ||
		!$_POST['type'] ||
		!$_POST['industry'] ||
		!$_POST['func']
	) {
		echo json_encode([
			'success' => false,
			'code' => 422,
			'message' => '参数错误'
		]);
		exit();
	}

    // 插入新数据
	$basicService = new basicService();
	$rst = $basicService->create($_POST['cash'], $_POST['percent'], $_POST['type'], $_POST['industry'], $_POST['func'], $_SERVER["REMOTE_ADDR"]);

	// 获取统计数据
	$statistics = $basicService->statistics($_POST['cash'], $_POST['industry'], $_POST['func']);
	echo json_encode([
		'success' => !!$rst,
		'code' => 200,
		'message' => '操作成功',
		'request' => $_POST,
		'params' => $params,
		'statistics' => $statistics,
	]);
	exit();
}




