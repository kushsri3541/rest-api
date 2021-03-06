<?php declare(strict_types=1);

$applicationDir = dirname(__DIR__, 2);

require $applicationDir . '/vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Reconmap\QueueProcessor;
use Reconmap\Services\ApplicationContainer;
use Reconmap\Services\ConfigLoader;
use Reconmap\Tasks\TaskResultProcessor;

$logger = new Logger('cron');
$logger->pushHandler(new StreamHandler($applicationDir . '/logs/application.log', Logger::DEBUG));

$config = (new ConfigLoader())->loadFromFile($applicationDir . '/config.json');
$config->update('appDir', $applicationDir);

$container = new ApplicationContainer($config, $logger);

$tasksProcessor = $container->get(TaskResultProcessor::class);

/** @var QueueProcessor $queueProcessor */
$queueProcessor = $container->get(QueueProcessor::class);
$exitCode = $queueProcessor->run($tasksProcessor, 'tasks:queue');

exit($exitCode);
