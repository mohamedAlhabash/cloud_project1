<?php
namespace App\Helper;

use Aws\Ec2\Ec2Client;

class Ec2pool{

    public $ec2 = null;

    public function __construct(){
        $this->getec2Instance();
    }

    public function getec2Instance(){
        $this->ec2 = Ec2Client::factory(array(
            'credentials'=>[
                'key'    => env('AWS_EC2_KEY'),
                'secret' => env('AWS_EC2_SECRET_KEY'),
            ],
            'region' => env('AWS_DEFAULT_REGION'),
            'version'=>env('AWS_EC2_VERSION')
        ));
        return $this->ec2;
    }

    public function get_number_of_instances(){
        $result = $this->getInstancesInfo();

        $count = 0;
        foreach($result['Reservations'] as $instances)
        {
            foreach($instances['Instances'] as $instance)
            {
                if($instance["State"]["Code"] == 16 || $instance["State"]["Code"] == 0){
                    $count++;
                }
            }
        }
        return $count;
    }

    public function getInstancesInfo()
    {
        return $this->ec2->describeInstances();
    }

    public function create_instance_from_Image(){
        $number_of_instances = $this->get_number_of_instances();
        if($number_of_instances < 8 ){
            $instance_name = "mem-cache-copy" . $number_of_instances;
            $result = $this->ec2->runInstances([
                    'ImageId' => 'ami-0afa731581bb9ae8e',
                    'InstanceType' => 't2.micro',
                    'KeyName' => "Cloud_test2_key",
                    'SecurityGroupIds' => ['sg-0b70ac19d17e8e77a', 'sg-0a988dfb1f6fbd63f', 'sg-0cf850da5f25eb08b'],
                    'MinCount' => 1,
                    'MaxCount' => 1,
                    'TagSpecifications '=>[
                        'ResourceType'  => 'instance',
                        'Tags'          => [
                            'Key' => 'Name',
                            'Value'=> $instance_name
                        ],
                    ],
            ]);
        }
    }

    public function stop_instance(){
        $instance_id = $this->selectInstance();
        if($instance_id != false){
            $result = $this->ec2->stopInstances(array(
                'InstanceIds' => [$instance_id],
            ));
        }
    }

    public function terminate_instance(){
        $instance_id = $this->selectInstance();
        if($instance_id != false){
            $result = $this->ec2->terminateInstances(array(
                'InstanceIds' => array($instance_id),
            ));
            return $result;
        }
        return false;
    }

    public function get_instance_by_name($name){
        $result = $result = $this->ec2->describeInstances(array(
            "Filters" => array(
                array(
                    "Name" => "tag:environment",
                    "Values" => array(
                        $name
                    )
                )
            )
        ));
        return $result;
    }

    public function selectInstance()
    {
        $count = $this->get_number_of_instances();
        if($count > 1){
            $result = $this->getInstancesInfo();
            foreach($result['Reservations'] as $instances) {
                foreach ($instances['Instances'] as $instance) {
                    if($instance["InstanceId"] == env('AWS_MAIN_EC2_INSTANCE')){
                        continue;
                    }
                    if($instance["State"]["Code"] == 16 || $instance["State"]["Code"] == 0) {
                        return $instance["InstanceId"];
                    }
                }
            }
        }
        return false;
    }
}
