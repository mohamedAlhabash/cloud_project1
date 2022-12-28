<?php

namespace App\Helper;

use Aws\CloudWatch\CloudWatchClient;
use Aws\Exception\AwsException;
use Carbon\Carbon;

class CloudWatchHelper
{

    function putMetricData(
        $cloudWatchClient,
        $cloudWatchRegion,
        $namespace,
        $metricData
    ) {
        try {
            $result = $cloudWatchClient->putMetricData([
                'Namespace' => $namespace,
                'MetricData' => $metricData
            ]);

            if (isset($result['@metadata']['effectiveUri'])) {
                if (
                    $result['@metadata']['effectiveUri'] ==
                    'https://monitoring.' . $cloudWatchRegion . '.amazonaws.com'
                ) {
                    return 'Successfully published datapoint(s).';
                } else {
                    return 'Could not publish datapoint(s).';
                }
            } else {
                return 'Error: Could not publish datapoint(s).';
            }
        } catch (AwsException $e) {
            return 'Error: ' . $e->getAwsErrorMessage();
        }
    }

    function putTheMetricData($metricName, $value)
    {
        $namespace = 'cloudProject';
        $metricData = [
            [
                'MetricName' => $metricName,
                'Timestamp' => now(), // 11 May 2020, 20:26:58 UTC.
                'Unit' => 'Count',
                'Value' => $value
            ]
        ];

        $cloudWatchRegion = 'us-east-1';
        $cloudWatchClient = new CloudWatchClient([
            'region' => $cloudWatchRegion,
            'version' => '2010-08-01'
        ]);

        return $this->putMetricData(
            $cloudWatchClient,
            $cloudWatchRegion,
            $namespace,
            $metricData
        );
    }

    function getMetricStatistics(
        $cloudWatchClient,
        $namespace,
        $metricName,
        $startTime,
        $endTime,
        $Statistics,
        $period,
    ) {
        try {
            $result = $cloudWatchClient->getMetricStatistics([
                'Namespace' => $namespace,
                'MetricName' => $metricName,
                'StartTime' => $startTime,
                'EndTime' => $endTime,
                'Statistics' => $Statistics,
                'Period' => $period,
            ]);

            $message = '';

            if (isset($result['@metadata']['effectiveUri'])) {
                $message .= 'For the effective URI at ' .
                    $result['@metadata']['effectiveUri'] . "\n\n";

                if ((isset($result['Datapoints'])) and
                    (count($result['Datapoints']) > 0)
                ) {
                    $message .= "Datapoints found:\n\n";

                    foreach ($result['Datapoints'] as $datapoint) {
                        foreach ($datapoint as $key => $value) {
                            $message .= $key . ' = ' . $value . "\n";
                        }

                        $message .= "\n";
                    }
                } else {
                    $message .= 'No datapoints found.';
                }
            } else {
                $message .= 'No datapoints found.';
            }

            return $message;
        } catch (AwsException $e) {
            return 'Error: ' . $e->getAwsErrorMessage();
        }
    }
    function getTheMetricStatistics($metricName, $startTime)
    {
        // Average number of Amazon EC2 vCPUs every 5 minutes within
        // the past 3 hours.
        $namespace = 'cloudProject';
        $metricName = $metricName;
        $startTime = Carbon::now()->subMinutes($startTime); //Carbon::now()->subMinutes(30);
        $endTime = Carbon::now();
        $period = 300; // Seconds. (5 minutes = 300 seconds.)
        $Statistics = array('Average');
        $cloudWatchClient = new CloudWatchClient([
            //'profile' => 'default',
            // 'credentials' => [
            //     'key' => env('AWS_ACCESS_KEY_ID'),
            //     'secret' => env('AWS_SECRET_ACCESS_KEY'),
            // ],
            'region' => 'us-east-1',
            'version' => '2010-08-01'
        ]);

        return $this->getMetricStatistics($cloudWatchClient, $namespace, $metricName, $startTime, $endTime, $Statistics, $period);
    }

}


