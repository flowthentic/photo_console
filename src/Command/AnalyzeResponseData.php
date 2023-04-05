<?php namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputDefinition, InputArgument};
use App\Entity\{InputLine, DataLine, Query};

// the name of the command is what users type after "php bin/console"
#[AsCommand(
    name: 'app:analyze',
    description: 'Analyzes response times.',
    hidden: false
)]
class AnalyzeCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try
        {
            // If testing get input through getStream method
            $inputStream = $input->getStream() ?? STDIN;
            Query::$input = array();
            for ($S = (int)fgets($inputStream); $S > 0; $S--)
            {
     //           $output->writeln($S);
                $line = explode(' ', fgets($inputStream));
                $line_type = array_shift($line);
                if ($line_type == 'C') // waiting timeline
                {
                    $line = new DataLine($line);
                    //append the line to the collection for querying
                    Query::$input[] = $line;
                }
                elseif ($line_type == 'D') // query
                {
                    $query = new Query($line);
                    $output->writeln($query);
                }
                else throw new \Exception("Unrecognized line type '$line_type'");
            }
            return Command::SUCCESS;
        }
        catch (\Exception $e)
        {
            $output->writeln($e);
            return Command::INVALID;
        }
    }
    protected function configure(): void
    {
        $this
            ->setHelp('Analyzes response times.')
            ->setDefinition(
                new InputDefinition([
                    new InputArgument('input', InputArgument::OPTIONAL)
                ])
            )
        ;
    }
}
?>
