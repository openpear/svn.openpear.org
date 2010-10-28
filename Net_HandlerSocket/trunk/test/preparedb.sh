dir=$(cd $(dirname $0); pwd)
scp $dir/preparedb.sql takada-at@sag14:/tmp/preparedb.sql
ssh takada-at@sag14 "mysql -uhstest -ppassword -h127.0.0.1 hstest < /tmp/preparedb.sql"
