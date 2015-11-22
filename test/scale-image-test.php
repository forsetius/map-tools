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
    ['-s test1.png -o #g#_test_#i#.png -w 4096 -h 2048 -g #g#',0],
    ['-s test2.png -o #g#_test_#i#.png -w 4096 -g #g#',0],
    ['-s test1.png -o #g#_test_#i#.png -h 2048 -g #g#',0],
    ['-s test1.png -o #g#_test_#i#.png -w 1024 -h 512 -g #g#',0],
    ['-s test2.png -o #g#_test_#i#.png -w 1024 -g #g#',0],
    ['-s test1.png -o #g#_test_#i#.png -h 512 -g #g#',0],
    ['-s test2.png -o #g#_test_#i#.png -w error -g #g#',SyntaxException::INVALID_VALUE],
    ['-s test2.png -o #g#_test_#i#.png -w - -g #g#',SyntaxException::INVALID_VALUE],
    ['-s test2.png -o #g#_test_#i#.png -w * -g #g#',SyntaxException::INVALID_VALUE],
    ['-s test2.png -o #g#_test_#i#.png -h error -g #g#',SyntaxException::INVALID_VALUE],
    ['-s test2.png -o #g#_test_#i#.png -h - -g #g#',SyntaxException::INVALID_VALUE],
    ['-s test2.png -o #g#_test_#i#.png -h * -g #g#',SyntaxException::INVALID_VALUE],
);
?>