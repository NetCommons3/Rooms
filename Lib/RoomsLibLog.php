<?php
/**
 * ルーム削除時の関連テーブル削除処理に関するライブラリ
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('Shell', 'Console/Command');

/**
 * ルーム削除時の関連テーブル削除処理に関するライブラリ
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\Rooms\Lib
 */
class RoomsLibLog {

/**
 * シェル開始時間
 *
 * @var float
 */
	private static $__shellStartTime;

/**
 * 処理開始時間
 *
 * @var float
 */
	private static $__procStartTime;

/**
 * debugとしてログ出力
 *
 * @param Shell|null $Shell 実行シェル
 * @param string $message 出力するメッセージ
 * @param int $indentLevel インデントレベル
 * @return void
 */
	public static function debugLog($Shell, $message, $indentLevel = 1) {
		if (! empty($Shell)) {
			$Shell->out(
				"<debug>" . str_repeat('    ', $indentLevel) . "{$message}</debug>",
				1,
				Shell::VERBOSE
			);
		}
	}

/**
 * infoとしてログ出力
 *
 * @param Shell|null $Shell 実行シェル
 * @param string $message 出力するメッセージ
 * @param int $indentLevel インデントレベル
 * @return void
 */
	public static function infoLog($Shell, $message, $indentLevel = 1) {
		if (! empty($Shell)) {
			$Shell->out(
				"<info>" . str_repeat('    ', $indentLevel) . "{$message}</info>",
				1,
				Shell::VERBOSE
			);
		}
	}

/**
 * successとしてログ出力
 *
 * @param Shell|null $Shell 実行シェル
 * @param string $message 出力するメッセージ
 * @param int $indentLevel インデントレベル
 * @return void
 */
	public static function successLog($Shell, $message, $indentLevel = 1) {
		if (! empty($Shell)) {
			$Shell->out(
				"<success>" . str_repeat('    ', $indentLevel) . "{$message}</success>",
				1,
				Shell::VERBOSE
			);
		}
	}

/**
 * シェル開始のログ出力
 *
 * @param Shell|null $Shell 実行シェル
 * @return void
 */
	public static function shellStartLog($Shell) {
		self::$__shellStartTime = microtime(true);
		if (! empty($Shell)) {
			$Shell->out(sprintf(
				"[SHELL START %s] Memory=%s",
				date('Y-m-d H:i:s'),
				self::getMemoryUsage()
			));
		}
	}

/**
 * シェル終了のログ出力
 *
 * @param Shell|null $Shell 実行シェル
 * @return void
 */
	public static function shellEndLog($Shell) {
		$endTime = microtime(true);
		if (! empty($Shell)) {
			$Shell->out(sprintf(
				"[SHELL E N D %s] Time=%.4f, Memory=%s",
				date('Y-m-d H:i:s'),
				($endTime - self::$__shellStartTime),
				self::getMemoryUsage()
			));
		}
	}

/**
 * 処理開始のログ出力
 *
 * @param Shell|null $Shell 実行シェル
 * @param string $message 出力するメッセージ
 * @param int $indentLevel インデントレベル
 * @return void
 */
	public static function processStartLog($Shell, $message = '', $indentLevel = 1) {
		self::$__procStartTime = microtime(true);
		if (! empty($Shell)) {
			$Shell->out(sprintf(
				str_repeat('    ', $indentLevel) . "[PROCESS START %s] %s Memory=%s",
				date('Y-m-d H:i:s'),
				$message,
				self::getMemoryUsage()
			));
		}
	}

/**
 * 処理終了のログ出力
 *
 * @param Shell|null $Shell 実行シェル
 * @param string $message 出力するメッセージ
 * @param int $indentLevel インデントレベル
 * @return void
 */
	public static function processEndLog($Shell, $message = '', $indentLevel = 1) {
		$endTime = microtime(true);
		if (! empty($Shell)) {
			$Shell->out(sprintf(
				str_repeat('    ', $indentLevel) . "[PROCESS E N D %s] %s Time=%.4f, Memory=%s",
				date('Y-m-d H:i:s'),
				$message,
				($endTime - self::$__procStartTime),
				self::getMemoryUsage()
			));
		}
	}

/**
 * メモリー使用量取得 単位付きで取得
 *
 * @return string
 * @see GetMemoryUsageComponent::execute() からコードをコピペ
 */
	public static function getMemoryUsage() {
		$size = memory_get_usage();
		return self::__formatMemory($size);
	}

/**
 * getPeakMemoryUsage
 *
 * @return string
 */
	public static function getPeakMemoryUsage() {
		$size = memory_get_peak_usage();
		return self::__formatMemory($size);
	}

/**
 * メモリ使用量のフォーマット
 *
 * @param int $size memory使用量
 * @return string
 */
	private static function __formatMemory($size) {
		$byte = 1024;	// バイト
		$mb = pow($byte, 2);	// メガバイト
		//$gb = pow($byte, 3);	// ギガバイト

		switch(true){
			//case $size >= $gb:
			//	$target = $gb;
			//	$unit = 'GB';
			//	break;
			case $size >= $mb:
				$target = $mb;
				$unit = 'MB';
				break;
			default:
				$target = $byte;
				$unit = 'KB';
				break;
		}

		$newSize = round($size / $target, 3);
		$fileSize = number_format($newSize, 3, '.', ',') . $unit;

		return $fileSize;
	}

}
