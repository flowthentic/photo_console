<?php namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AnalyzeResponseDataTest extends KernelTestCase
{
    public function testProvidedExample()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:analyze');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs([<<<customer_provided_input
7
C 1.1 8.15.1 P 15.10.2012 83
C 1 10.1 P 01.12.2012 65
C 1.1 5.5.1 P 01.11.2012 117
D 1.1 8 P 01.01.2012-01.12.2012
C 3 10.2 N 02.10.2012 100
D 1 * P 8.10.2012-20.11.2012
D 3 10 P 01.12.2012

customer_provided_input
        ]);
        $commandTester->execute([]);
        $commandTester->assertCommandIsSuccessful();

        $this->assertEquals($commandTester->getDisplay(), <<<expected_output
83
100
-

expected_output
        );
    }
}

?>
