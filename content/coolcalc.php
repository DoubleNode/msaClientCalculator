<?php
/* ----------------------------------------------
 * Calculator client
 *
 * A very simple example of interaction with 
 * a calculator server application whose actions
 * are facilitated by thrift.  Both the client
 * and server negotiate on the common interface
 * defined by calculator.thrift
 *
 *@author Ian Chan
 *@date May 10, 2010
 * ----------------------------------------------
 */
 
namespace msaCalculator;

error_reporting(E_ALL);

require_once __DIR__.'/msaCommon/php/lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__).'/.').'/msaCalculator/gen-php';
//
$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__ . '/msaCommon/php/lib');
$loader->registerDefinition('shared', $GEN_DIR);
$loader->registerDefinition('msaCalculator', $GEN_DIR);
$loader->register();

/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements. See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership. The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

use Thrift\Protocol\TBinaryProtocol;
use Thrift\transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;

// Several things might go wrong
try 
{
	// Create a thrift connection (Boiler plate)
    //if (array_search('--http', $argv)) 
    //{
        $socket = new THttpClient('192.168.99.100', 32785, 'CalculatorHandler.php');
    //} 
    //else 
    //{
    //    $socket = new TSocket('192.168.99.100', '32785');
    //}
    
    $transport = new TBufferedTransport($socket, 1024, 1024);
    $protocol  = new TBinaryProtocol($transport);
	
    // Create a calculator client
	$client = new CalculatorClient($protocol);
	
	// Open up the connection
	$transport->open();	

    //print "TP[".__FILE__.":".__LINE__."]<br />\n";
	$client->ping();
    print "ping<br />\n";
		
    $sum = $client->add(1,1);
	print "1+1=$sum<br />\n";
	
	$work = new Work();
	
	$work->op      = Operation::DIVIDE;
	$work->num1    = 1;
	$work->num2    = 0;
	
	try 
	{
	    $client->calculate(1, $work);
	    print "Whoa! We can divide by zero?<br />\n";
	} 
	catch (InvalidOperation $io) 
	{
	    print "InvalidOperation: $io->why<br />\n";
	}
	
	$work->op       = Operation::SUBTRACT;
	$work->num1    = 15;
	$work->num2    = 10;
	
	$diff = $client->calculate(1, $work);
	print "15-10=$diff<br />\n";
	
	$log = $client->getStruct(1);
	print "Log: $log->value<br />\n";
	
	// And finally, we close the thrift connection
	$transport->close();
} 
catch (TException $tx) 
{	
	// a general thrift exception, like no such server
	echo "ThriftException: ".$tx->getMessage()."<br />\n";
}

?>
