<?php
/**
 * Created by PhpStorm.
 * User: Jie
 * Date: 2017/11/21
 * Time: 下午9:42
 */
require(__DIR__ . '/commonService.php');
class basicService extends commonService
{
	// 新增基础信息
	public function create($cash, $percent, $type, $industry, $func, $ip) {
		// 薪资大于平均值的10倍,跳过不记录
		// todo... 此参数可以加入redis,有效期设为1d,加快访问速度
		$avgCash = parent::find("SELECT avg( cash ) AS cash FROM basic");
		$avgCash = $avgCash[0] ? $avgCash[0]['cash'] : 0;
		if ($avgCash && $cash > $avgCash * 10) {
			return true;
		}
		// 若同一IP录入数据超过10条则不录入
		$ipCounts = parent::find("SELECT count( 1 ) as total FROM basic WHERE ip = ?",
			's',
			$ip);
		$ipCounts = $ipCounts[0] ? $ipCounts[0]['total'] : 0;
		if ($ipCounts > 10) {
			return true;
		}
		return parent::insert("INSERT INTO basic(cash, percent, `type`, industry, func, ip) VALUES (?,?,?,?,?,?)",
			'ddddds',
			$cash,
			$percent,
			$type,
			$industry,
			$func,
			$ip
		 );
	}

	//统计数据汇总
	public function statistics($cash, $industry, $func) {
		// 获取当前行业的平均税前金额
		$avgIndustry = parent::find("SELECT avg( cash ) AS cash FROM basic WHERE industry = ?",
			'd',
			$industry);
		$avgIndustry = $avgIndustry[0] ? $avgIndustry[0]['cash'] : 0;
		// 获取比当前行业平均金额大的行业的数量
		$overAvgIndustry = parent::find("SELECT COUNT(1) as total FROM (SELECT industry, AVG(cash) AS cash FROM basic GROUP BY industry  HAVING cash > ?) as t",
			'd',
			$avgIndustry);
		$overAvgIndustry = $overAvgIndustry[0] ? $overAvgIndustry[0]['total'] : 0;
		// 获取统计的行业总数量
		$totalIndustry = parent::find("SELECT count(DISTINCT industry) as total FROM basic");
		$totalIndustry = $totalIndustry[0] ? $totalIndustry[0]['total'] : 0;

		// 获取当前职能的记录条数
		$funcCount = parent::find("SELECT COUNT(1) as total FROM basic WHERE func = ?",
			'd',
			$func);
		$funcCount = $funcCount[0] ? $funcCount[0]['total'] : 0;

		// 获取大于当前税前金额的职能记录条数
		$overCashCount = parent::find("SELECT COUNT(1) as total FROM basic WHERE func = ? AND cash > ?",
			'd',
			$func,
			$cash);
		$overCashCount = $overCashCount[0] ? $overCashCount[0]['total'] : 0;

		// 获取去年和今年的统计数据
		$yearData = parent::find("SELECT YEAR(created) as y, AVG(cash) as cash FROM basic WHERE YEAR(created) >= ? GROUP BY y",
			'd',
			date('Y') - 1
			);
		$lastYear = 0;
		$thisYear = 0;
		foreach ($yearData as $year) {
			if ($year['y'] == date('Y') - 1){
				$lastYear = $year['cash'];
			}
			else if ($year['y'] == date('Y')){
				$thisYear = $year['cash'];
			}
		}

		return [
			'overAvgIndustry' => $overAvgIndustry, // 大于平均行业薪资的数量
			'totalIndustry' => $totalIndustry, // 行业总数量
			'funcCount' => $funcCount, //当前职能的记录条数
			'overCashCount' => $overCashCount, //大于当前职能的记录条数
			'lastYear' => $lastYear, //去年的平均薪资
			'thisYear' => $thisYear, //今年的平均薪资
		];
	}
}
