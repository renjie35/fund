<?php
/**
 * Created by PhpStorm.
 * User: Jie
 * Date: 2017/11/21
 * Time: 下午9:42
 */
require_once(__DIR__ . '/../config/config.php');
class commonService
{
	public function find($query, $bindkey = '', ...$params) {
		$mysql = $GLOBALS['mysql'];
		$mysqli = mysqli_connect(
			$mysql['mysql_server_name'],
			$mysql['mysql_username'],
			$mysql['mysql_password'],
			$mysql['mysql_database']) or die("error connecting") ; //连接数据库

		if ($mysqli->connect_errno) {
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}
		try {
			$mysqli->query("set names 'utf8'"); //数据库输出编码

			$stmt = $mysqli->prepare($query);

			if ($bindkey) {
				$stmt->bind_param($bindkey, ...$params);
			}
			$stmt->execute();
			// $stmt = $mysqli->query("select * from basic limit 5");


			if($stmt instanceof mysqli_stmt)
			{
				// 获取信息
				$meta = $stmt->result_metadata();
				$fields = $meta->fetch_fields();

				$results = [];
				$ref_results = [];
				foreach ($fields as $field)
				{
					$results[$field->name] = null;
					$ref_results[] =& $results[$field->name];
				}
				call_user_func_array(array($stmt, 'bind_result'), $ref_results);

				$i = 0;
				while ($stmt->fetch())
				{
					$data[$i] = array();
					foreach ($results as $k => $v)
					{
						$data[$i][$k] = $v;
					}
					$i++;
				}
			}
			elseif($stmt instanceof mysqli_result)
			{
				while($row = $stmt->fetch_assoc())
					$data[] = $row;
			}

			$stmt->close();
		} catch (Exception $e) {
			throw $e;
		} finally {
			$mysqli->close();
		}

		return $data;
	}

	public function insert($query, $bindkey, ...$params) {
		$mysql = $GLOBALS['mysql'];
		$result = false;
		$mysqli = mysqli_connect(
			$mysql['mysql_server_name'],
			$mysql['mysql_username'],
			$mysql['mysql_password'],
			$mysql['mysql_database']) or die("error connecting") ; //连接数据库

		if ($mysqli->connect_errno) {
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}
		try {
			$mysqli->query("set names 'utf8'"); //数据库输出编码
			$stmt = $mysqli->prepare($query);
			$stmt->bind_param($bindkey, ...$params);
			$stmt->execute();

			// 获取信息
			$result = $stmt->affected_rows;
			$stmt->close();
		} catch (Exception $e) {
			throw $e;
		} finally {
			$mysqli->close();
		}

		return $result;
	}
}
