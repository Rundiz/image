# Unit testing.

Recomended PHPUnit version 10.

Run command `phpunit` for test all units.  
Run command `phpunit --testsuite "dependent","imgprocess-gd"` to test some test suites with dependency.  
Run command `phpunit --exclude-testsuite "imgprocess-gd","imgprocess-imagick"` to exclude some test suite.