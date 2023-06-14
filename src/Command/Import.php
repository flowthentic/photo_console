<?php namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputOption, InputArgument};
use Symfony\Component\Console\Question\Question;
use App\Entity\Photo;

#[AsCommand('import')]
class Import extends Command
{
    private $action, $actName;
    
    protected function initialize(InputInterface $input, OutputInterface $output): int
    {
        Photo::$delta = new \DateInterval($input->getOption('add') ?? 'P0D');
        if ($input->getOption('rename'))
        {
            Photo::$outputFormat = '/Y/m/d/His.';
            Photo::$suffix = PATHINFO_EXTENSION;
        }
        else
        {
            Photo::$outputFormat = '/Y/m/d/';
            Photo::$suffix = PATHINFO_BASENAME;
        }
        
        if ($input->getOption('move'))
        {
            $this->action = fn($from, $to) => rename($from, $to);
            $this->actName = "Move";
        }
        else
        {
            $this->action = fn($from, $to) => copy($from, $to);
            $this->actName = "Copy";
        }
        
        Photo::$prefix = getenv('HOME').'/Pictures';
        return Command::SUCCESS;
    }
    protected function interact(InputInterface $input, OutputInterface $output): int
    {
        $iohelper = $this->getHelper('question');
        $question = sprintf('Where would you like to import your photos? [%s] ', Photo::$prefix);
        $question = new Question($question, Photo::$prefix);
        while (!is_dir($this->destination = $iohelper->ask($input, $output, $question)))
            $output->writeln("The directory $this->destination does not exist");
        return Command::SUCCESS;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try
        {
            foreach ($input->getArgument('files') as $path)
            {
                if (is_dir($path)) continue;
                $photo = new Photo($path);
                echo "$this->actName $path to $photo\n";
                $this->action($path, $photo);
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
        ->setHelp('Imports photos.')
        ->addArgument('files', InputArgument::REQUIRED | InputArgument::IS_ARRAY)
        ->addOption('move', 'm', InputOption::VALUE_NONE, 'Remove files from source location.')
        ->addOption('rename', 'N', InputOption::VALUE_NONE, 'Rename files by their time.')
        ->addOption('add', 'a', InputOption::VALUE_REQUIRED, 'Interval to add to detected creation date.')
        ->addOption('force', 'f', InputOption::VALUE_NONE, 'Include unrecognized file types.');
    }
}
