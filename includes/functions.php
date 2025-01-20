<?php

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
