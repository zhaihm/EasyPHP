# EasyPHP
Light-weight PHP framework Version 0.1

### Introduction
This framework is developed for small website, which does not have performance concerns.

It uses a light-weight database interface called **Medoo**, which supports MySQL, MSSQL, Oracle... It also supports Memcache.

### Architecture
* **config:** where the config files locate
* **controller:** where controllers locate
* **library:** where the framework files locate, like DB and Memcache interfaces
* **service:** service is a layer which implements business logics used in more than one controller
* **sql:** exported sql scripts
* **tools:** tools like DB cleaner, ...
* **unit_test:** unit test files

### How to add controllers
A controller could be added by adding a file to controller directory.

### How to add actions
An action could be added by adding a function to a related controller

### How to do unit test
You could simply add your unit test file to **unit_test** directory. *$commands* stands for actions in controllers. Each item in *params* could be seen as a test case. *url* will be called when running unit test, and *params* will be passed into it.

You should run command like this:

`cd unit_test`

`php test_user_url.php login`

You could also run all test cases in one line:

`php test_user_url.php all`

Then all tests will run automatically.