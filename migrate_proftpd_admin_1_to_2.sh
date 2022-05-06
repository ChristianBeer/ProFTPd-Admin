#!/bin/bash
mysql_old_user=CHANGE_ME
mysql_old_password=CHANGE_ME
mysql_old_db=CHANGE_ME
mysql_old_host=CHANGE_ME
mysql_new_user=CHANGE_ME
mysql_new_password=CHANGE_ME
mysql_new_db=CHANGE_ME
mysql_new_host=CHANGE_ME
tmp_file=/tmp/$mysql_old_db_usertable_$$

#Dump original data to temporary file
echo "Dumping original data from $mysql_old_db to $tmp_file"
mysql -u $mysql_old_user -p$mysql_old_password -h $mysql_old_host -D $mysql_old_db -e "select * from usertable INTO OUTFILE '$tmp_file' FIELDS TERMINATED BY ','"

#Read data from temporary file
while read line ; do
	userid=$(echo $line | cut -d ',' -f 1)
	passwd=$(echo $line | cut -d ',' -f 2)
	homedir=$(echo $line | cut -d  ',' -f 3)
	shell=$(echo $line | cut -d ',' -f 4)
	uid=$(echo $line | cut -d ',' -f 5)
	gid=$(echo $line | cut -d ',' -f 6)
	login_count=$(echo $line | cut -d ',' -f 7)
	last_login=$(echo $line | cut -d ',' -f 8)
	disabled=$(echo $line | cut -d ',' -f 11)
	name=$(echo $line | cut -d ',' -f 12)
	email=$(echo $line | cut -d ',' -f 13)
	comment=''
	title=''
	company=''
	bytes_in_used=0
	bytes_out_used=0
	files_in_used=0
	files_out_used=0
	last_modified=$(date '+%F %T')
#Write values in destination DB
	mysql -u $mysql_new_user -p$mysql_new_password -h $mysql_new_host -D $mysql_new_db << EOF
insert into users values('$uid','$userid','$uid','$gid','$passwd','$homedir','$comment','$disabled','$shell','$email','$name','$title','$company','$bytes_in_used','$bytes_out_used','$files_in_used','$files_out_used','$login_count','$last_login','$last_modified');
EOF
done <$tmp_file
rm $tmp_file
