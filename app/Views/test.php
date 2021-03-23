<?php

echo "test ni : $data[user_data] <br>";
print_r($datas);
// foreach ($user_data as $userList) {
// 	echo $userList->name;
// }

foreach ($datas['user_data'] as $key => $data) {
	echo $data['name'];
}
