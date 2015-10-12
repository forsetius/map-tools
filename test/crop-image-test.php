<?php
use pl\forseti\cli\SyntaxException;
use pl\forseti\maptools\CapabilityException;
$tasks = array(
    ['',SyntaxException::REQUIRED_VALUE],
    ['-s "test.png" -g #g#',0],
    ['-s test.png -g #g#',0],
    ['-s test.jpg -g #g#',0],
    ['-s test.bmp -g #g#',CapabilityException::UNSUPPORTED_FORMAT],
    ['-s test.txt -g #g#',CapabilityException::UNSUPPORTED_FORMAT],
    ['-s test.png -g error',CapabilityException::UNSUPPORTED_LIBRARY],
    ['-s',SyntaxException::REQUIRED_VALUE],
    ['-s *',CapabilityException::UNSUPPORTED_FORMAT],
    ['-s /',CapabilityException::UNSUPPORTED_FORMAT],
    ['-s test.jpg -o #g#_test_#i#.jpg -g #g#',0],
    ['-s test.jpg -o #g#_test_#i#.png -g #g#',0],
    ['-s test.jpg -o #g#_test_#i#.bmp -g #g#',CapabilityException::UNSUPPORTED_FORMAT],
    ['-s test.jpg -o *',CapabilityException::UNSUPPORTED_FORMAT],
    ['-s test.jpg -o /',CapabilityException::UNSUPPORTED_FORMAT],
    ['-s test1.png -o #g#_test_#i#.png -g #g#',0],
    ['-s test2.png -o #g#_test_#i#.png -g #g#',0],
    ['-s test.png -g #g# -v=0',0],
    ['-s test.png -g #g# -v',0],
    ['-s test.png -v=error',SyntaxException::INVALID_VALUE],
    ['-s test.png -v=5',SyntaxException::VALUE_OUT_OF_BOUNDS],
    ['-s test.png -v=-5',SyntaxException::INVALID_VALUE],
    ['-s test.png -v=5-',SyntaxException::INVALID_VALUE],
    ['-s test.png -v=-',SyntaxException::INVALID_VALUE],
    ['-s test.png -v=*',SyntaxException::INVALID_VALUE],
    ['-s test.png --help',0],
    ['-s test.png -v --help',0],
    ['-s test.png -d -g #g#',0],
    ['-s test1.png -d -g #g#',0],
    ['-s test2.png -d -g #g#',0],
    ['-s test1.png -o #g#_test_#i#.png -l 0 -t 20 -r 20 -b 20 -g #g#',0],
    ['-s test1.png -o #g#_test_#i#.png -l 40 -t 20 -b 20 -g #g#',0],
    ['-s test1.png -o #g#_test_#i#.png -l error -t 20 -r 20 -b 20 -g #g#',SyntaxException::INVALID_VALUE],
    ['-s test1.png -o #g#_test_#i#.png -l 0 -t error -r 20 -b 20 -g #g#',SyntaxException::INVALID_VALUE],
    ['-s test1.png -o #g#_test_#i#.png -l 0 -t 20 -r error -b 20 -g #g#',SyntaxException::INVALID_VALUE],
    ['-s test1.png -o #g#_test_#i#.png -l 0 -t 20 -r 20 -b error -g #g#',SyntaxException::INVALID_VALUE],
    ['-s test.png -o #g#_test_#i#.png -c -t512 -r512 -g #g#',0],
    ['-s test.png -o #g#_test_#i#.png -c=1024 -t512 -r512 -g #g#',0],
    ['-s test.png -o #g#_test_#i#.png -c=512 -t512 -r512 -g #g#',0],
    ['-s test.png -o #g#_test_#i#.png -c=-512 -t512 -r512 -g #g#',SyntaxException::INVALID_VALUE],
    ['-s test.png -o #g#_test_#i#.png -c=error -t512 -r512 -g #g#',SyntaxException::INVALID_VALUE],
);
?>