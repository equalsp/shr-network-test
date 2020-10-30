<!-- PROJECT LOGO -->
<br />
<p align="center">
  <h3 align="center">Subscribe HR Network Path Test</h3>

  <p align="center">
    Submission for Subscribe HR's network path test.<br/> 
    Assumptions have been made in the coding of the solution and unit test have been written to test helper functions.
  </p>
</p>



<!-- TABLE OF CONTENTS -->
## Table of Contents

* [About the Project](#about-the-project)
  * [Built With](#built-with)
* [Requirements](#requirements)
  * [Installation](#installation)
* [Roadmap](#roadmap)



<!-- ABOUT THE PROJECT -->
# About The Project

My submission for Subscribe HR's network path test.

### Assumptions
* CSV File provided by the user is correctly formatted and data is in the appropriate type
    * Input - Uppercase single character ([A-Z])
    * Output - Uppercase single character ([A-Z])
    * Latency limit - Unsigned integer (>0)
    * The Input and Output are not the same character (Will exit with an error message)
* Because the user is allowed to input a custom CSV file path, it was assumed that the data in the CSV could be different each time the script is run. So instead of generating and persisting a statiic routing table of values and perform a look up each time a query was made, the script was written to perform these manually  
* The file is run on a PHP capable environment
* The correct permissions have been granted for the script to access the CSV file  
* The latency is the same regardless of the direction (i.e. the latency from the Input to the Output is the same as the latency from the Output to the Input)
* The script is meant to be executed from the command line, a check (php_sapi_name() === 'cli') has been carried out to prevent the script from outputting anything from a browser

### Built With

* [PHP](https://www.php.net/)
* [PHPUnit](https://phpunit.de/) (for unit tests)


<!-- GETTING STARTED -->
# Requirements

This is a command line PHP file and needs to be run on an environment with PHP installed


### Installation

1. Clone the repo
```sh
git clone https://github.com/equalsp/shr-network-test.git
```
2. Execute from an the command line
```sh
php index.php
```
3. It will then prompt you to enter a path to the CSV file. You can use the sample.csv provided by entering:
```sh
sample.csv
```
4. It will then prompt you to enter your Input Output and Latency Limit. You can run a sample test by entering:
```sh
A F 1000
```
5. The output will be displayed in your console.
```sh
Path not found
```


Coming soon

<!-- ROADMAP -->
# Roadmap
* CSV file validation
* User input validation
* Further refactoring to optimise performance
* Allow the user to retry inputting parameters if the data fails validation 

Coming soon