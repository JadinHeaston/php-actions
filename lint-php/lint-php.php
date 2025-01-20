<?php

//Configurable
define('TERMINAL_COLORS', [
	'default' => "\033[37m", //white
	'red' => "\033[31m",
	'green' => "\033[32m",
]);

//Parsing options.
$options = getopt(
	'',
	[
		'lint-directory:', //Directories to be iterated through and linted.
		'exclude::' //Exclude paths. Any direct matches will be skipped (including their subdirectories)
	]
);

if (count($options) > 0 === false || isset($options['lint-directory']) === false)
	exit('No `--lint-directory` provided.');
elseif ($options['lint-directory'] === false)
	exit('Invalid `lint-directory` provided. Please provide the root directory path to be linted.');

//Required option
if (is_string($options['lint-directory']))
	$lintDirectories = [$options['lint-directory']];
elseif (is_array($options['lint-directory']))
	$lintDirectories = $options['lint-directory'];

$excludedPaths = [];
if (isset($options['exclude']) === true)
{
	if (is_string($options['exclude']))
		$excludedPaths = [$options['exclude']];
	elseif (is_array($options['exclude']))
		$excludedPaths = $options['exclude'];

	//Normalizing paths.
	foreach ($excludedPaths as &$excludedPath)
	{
		$excludedPath = realpath($excludedPath);
	}
}

//Getting PHP files.
$phpFiles = getPHPFiles($lintDirectories, $excludedPaths);
sort($phpFiles);

//Linting PHP Files
$lintFailures = 0;
foreach ($phpFiles as $phpFilePath)
{
	echo 'Linting: ' . $phpFilePath . ' - ';
	$commandOutput = runCommand('php -w ' . $phpFilePath . ' | php -l');
	if ($commandOutput !== false && $commandOutput['return_value'] === 0)
		echo TERMINAL_COLORS['green'] . 'PASS' . TERMINAL_COLORS['default'];
	else
	{
		echo TERMINAL_COLORS['red'] . 'FAIL' . TERMINAL_COLORS['default'];
		++$lintFailures;
	}

	echo PHP_EOL;
}

if ($lintFailures === 0)
{
	echo TERMINAL_COLORS['green'] . 'SUCCESS: No linting errors present. (' . $lintFailures . ')', PHP_EOL;
	exit(0);
}
else
{
	echo TERMINAL_COLORS['red'] . 'FAILURE: linting errors present. (' . $lintFailures . ')', PHP_EOL;
	exit(1);
}




//Functions

function getPHPFiles(array $directoryPath, array &$excludedPaths = []): array
{
	$PHPFilePaths = [];

	foreach ($directoryPath as $directory)
	{
		//Normalizing path
		$directoryPath = realpath($directory);

		//Get directories within the directories.
		$innerDirectories = glob($directoryPath . DIRECTORY_SEPARATOR . '*', GLOB_NOSORT | GLOB_ONLYDIR);
		//Get the PHP files in this directory.
		$phpFiles = glob($directoryPath . DIRECTORY_SEPARATOR . '*.php', GLOB_NOSORT);

		if ($innerDirectories === false)
			exit('Failed to find directories within: ' . $directory);
		elseif ($innerDirectories === false)
			exit('Failed to find PHP files within: ' . $directory);

		//Remove excluded directory paths.
		$innerDirectories = array_diff($innerDirectories, $excludedPaths);
		//Remove excluded PHP paths.
		$phpFiles = array_diff($phpFiles, $excludedPaths);

		//Call this function on each remaining inner directory to recursively check deeper.
		foreach ($innerDirectories as $innerDirectory)
		{
			array_push($PHPFilePaths, ...getPHPFiles([$innerDirectory], $excludedPaths));
		}

		//Adding this directories PHP files.
		array_push($PHPFilePaths, ...$phpFiles);
	}

	return $PHPFilePaths;
}

function runCommand(string $command): array | false
{
	// Prepare the descriptors for process communication
	$descriptors = [
		0 => ['pipe', 'r'], // stdin
		1 => ['pipe', 'w'], // stdout
		2 => ['pipe', 'w'] // stderr
	];

	// Open the process
	$process = proc_open($command, $descriptors, $pipes);

	if (is_resource($process))
	{
		// Close the stdin pipe (we don't need to write to it)
		fclose($pipes[0]);

		// Read from the stdout pipe
		$stdout = stream_get_contents($pipes[1]);
		fclose($pipes[1]);

		// Read from the stderr pipe
		$stderr = stream_get_contents($pipes[2]);
		fclose($pipes[2]);

		// Close the process
		$returnValue = proc_close($process);

		// Return the result
		return [
			'stdout' => $stdout,
			'stderr' => $stderr,
			'return_value' => $returnValue
		];
	}
	else
		return false;
}
