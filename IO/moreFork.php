<?php  
/**
 * 多进程线程模型
 * @author phachon@163.com
 */
//创建一个 socket
$server = stream_socket_server('tcp://127.0.0.1:8100', $errno, $error) or die('create server failed');

while (1) {
	$conn = stream_socket_accept($server);
	if(pthread_create() == 0) {
		//子进程
		$request = fread($conn);
		//to do
		$response = 'create tcp://127.0.0.1:8100 success';
		fwrite($response);
		fclose($conn);
		exit(0);
	}
}