<?php
/**
 * File: Validator.php
 * Project: PHPDI
 * File Created: Saturday, 17th February 2018 3:29:32 pm
 * Author: Ivan Grigorov
 * Contact:  ivangrigorov9 at gmail.com
 * -----
 * Last Modified: Sunday, 4th March 2018 6:18:06 pm
 * Modified By: Ivan Grigorov
 * -----
 * License: MIT
 */

//define("FILE_LOCATION", dirname(__FILE__));
require_once(dirname(__FILE__)."/../Errors/ObjectParametersExceptions.php");
require_once(dirname(__FILE__)."/../Errors/GlobalExceptions.php");
require_once(dirname(__FILE__)."/../Errors/WorkflowErrors.php");
require_once(dirname(__FILE__)."/../Lib/Config.php");


use ObjectParametersExceptions as ObjectParametersExceptions; 
use GlobalExceptions as CustomGlobalExceptions;
use WorkflowErrors as WorkflowErrors;


final class Validator {

    public static function CheckForValidInjectionWithParameters($injectionConfig, $inputParams) {
        if (!isset($injectionConfig) || !isset($inputParams)) {
            throw new \CustomGlobalExceptions\ParameterNotGIvenException();
        }
        if (!isset($injectionConfig["params"])) {
            throw new \ObjectParametersExceptions\MissingParametersException();
        }
        foreach($injectionConfig["params"] as $key => $value) {
            $correctNameIsGiven = false;
            foreach($inputParams["params"] as $inputparam) {
                if ($key === $inputparam["name"]) {
                    if (!isset($inputparam["value"]) && !isset($value["defaultValue"])) {
                        throw new \ObjectParametersExceptions\MissingValueForParametersException($inputparam["name"]);
                    }
                    $correctNameIsGiven = true;
                }
            } 
            if (!$correctNameIsGiven) {
                throw new \ObjectParametersExceptions\MissingNameInParametersException($inputparam["name"]);
            }
        }
    }

    public static function checkCorrectParametersForInstantiation($paramConfig, $inputConfig) {
        $countOfConfigParams = count($paramConfig);
        $countedParams = 0;
        if (!($countOfConfigParams === count($inputConfig))) {
            // Make custom Exception
            throw new Exception("Invalid count of given parameters");
        }
        foreach($paramConfig as $param) {
            foreach($inputConfig["params"] as $inputParam) {
                if ($param->getName() === $inputParam["name"]) {
                    $countedParams++;
                }
            }
        }
        if ($countOfConfigParams === $countedParams) {
            return true;
        }
        return false;

    }

    public static function checkIfFileExists($filePath) {
        if (!file_exists($filePath)) {
            $exception = new  \WorkflowErrors\FileNotFoundException($filePath);
            throw $exception;
        }
    }

    public static function checkIfConstantIsDefined($constantName) {
        if ($constantName === null) {
            return false;
        }
        return true;
    }

    public static function checkIfInterfaceIsLoaded($interface) {
        if (!interface_exists($interface) && Config::CHECK_FOR_INTERFACE) {
            return false;
        }
        return true;

    } 
}