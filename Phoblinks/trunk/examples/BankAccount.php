<?php

require_once dirname(__FILE__) . '/../src/Phoblinks.php';

function p($s)
{
    print $s . "\n";
}

$cls = Phoblinks()->toDefineClass('BankAccount');

$cls->property('ballance')
    ->defaultValue(0);

$cls->begin(); // BankAccount

class BankAccount extends PhobObject
{

    function deposit($x)
    {
        $this->_ballance += $x;
    }

    function withdraw($x)
    {
        $this->_ballance = max(array(0, $this->_ballance-$x));
    }

}

$cls->end(); // BankAccount

$account = BankAccount()->make();
$account->setBallance(200);
p($account->ballance());   // 200
$account->deposit(50);
p($account->ballance());   // 250
$account->withdraw(100);
p($account->ballance());   // 150
$account->withdraw(200);
p($account->ballance());   // 0


$cls = Phoblinks()->toDefineClass('StockAccount', BankAccount());

$cls->property('numShares')
    ->defaultValue(10);

$cls->property('pricePerShare')
    ->defaultValue(30);

$cls->begin(); // StockAccount

class StockAccount extends __BankAccount__
{

    function ballance()
    {
        return $this->_numShares * $this->_pricePerShare;
    }

    function setBallance($x)
    {
        $this->_numShares = ((float)$x) / $this->_pricePerShare;
    }

}

$cls->end(); // StockAccount

$stock = StockAccount()->make();

p($stock->numShares());         // 10
p($stock->pricePerShare());     // 30
p($stock->ballance());          // 300
$stock->setBallance(150);
p($stock->numShares());         // 5

$stock->setBallance(600);
p($stock->ballance());          // 600
p($stock->numShares());         // 20
$stock->deposit(60);
p($stock->ballance());          // 660
p($stock->numShares());         // 22


